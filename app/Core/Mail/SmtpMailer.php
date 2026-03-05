<?php

namespace App\Core\Mail;

use RuntimeException;

class SmtpMailer
{
    private array $config;
    /** @var resource|null */
    private $socket = null;

    public function __construct(?array $config = null)
    {
        if ($config !== null) {
            $this->config = $config;
            return;
        }

        $appConfig = require __DIR__ . '/../../../config/config.php';
        $this->config = $appConfig['mail'] ?? [];
    }

    public function send(string $toEmail, string $subject, string $body, ?string $toName = null): bool
    {
        $host       = trim((string) ($this->config['host'] ?? ''));
        $port       = (int) ($this->config['port'] ?? 465);
        $username   = trim((string) ($this->config['username'] ?? ''));
        $password   = (string) ($this->config['password'] ?? '');
        $fromEmail  = trim((string) ($this->config['from_address'] ?? $username));
        $fromName   = trim((string) ($this->config['from_name'] ?? 'SIREPO'));
        $encryption = strtolower(trim((string) ($this->config['encryption'] ?? 'ssl')));
        $timeout    = (int) ($this->config['timeout'] ?? 15);

        if ($host === '' || $username === '' || $password === '' || $fromEmail === '') {
            throw new RuntimeException('Konfigurasi SMTP belum lengkap.');
        }

        $transport = $encryption === 'ssl' ? 'ssl://' : 'tcp://';
        $remote = $transport . $host . ':' . $port;

        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
        if (!$socket) {
            throw new RuntimeException('Koneksi SMTP gagal: ' . $errstr . ' (' . $errno . ')');
        }

        $this->socket = $socket;
        stream_set_timeout($this->socket, $timeout);

        try {
            $this->expect([220]);
            $this->command('EHLO localhost', [250]);

            if ($encryption === 'tls') {
                $this->command('STARTTLS', [220]);
                if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new RuntimeException('Gagal mengaktifkan TLS pada koneksi SMTP.');
                }
                $this->command('EHLO localhost', [250]);
            }

            $this->command('AUTH LOGIN', [334]);
            $this->command(base64_encode($username), [334]);
            $this->command(base64_encode($password), [235]);

            $this->command('MAIL FROM:<' . $fromEmail . '>', [250]);
            $this->command('RCPT TO:<' . trim($toEmail) . '>', [250, 251]);
            $this->command('DATA', [354]);

            $message = $this->buildMessage($fromEmail, $fromName, $toEmail, (string) $toName, $subject, $body);
            $this->write($message . "\r\n.\r\n");
            $this->expect([250]);

            $this->command('QUIT', [221]);
            return true;
        } finally {
            if (is_resource($this->socket)) {
                fclose($this->socket);
            }
            $this->socket = null;
        }
    }

    private function buildMessage(
        string $fromEmail,
        string $fromName,
        string $toEmail,
        string $toName,
        string $subject,
        string $body
    ): string {
        $from = $this->formatAddress($fromEmail, $fromName);
        $to = $this->formatAddress($toEmail, $toName);

        $headers = [
            'From: ' . $from,
            'To: ' . $to,
            'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
            'Date: ' . date(DATE_RFC2822),
            'Message-ID: <' . bin2hex(random_bytes(12)) . '@sirepo.local>',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ];

        $normalizedBody = $this->normalizeLineEndings($body);
        $normalizedBody = preg_replace('/^\./m', '..', $normalizedBody) ?? $normalizedBody;

        return implode("\r\n", $headers) . "\r\n\r\n" . $normalizedBody;
    }

    private function formatAddress(string $email, string $name): string
    {
        $safeEmail = trim($email);
        $safeName = trim(str_replace(['"', "\r", "\n"], '', $name));
        if ($safeName === '') {
            return '<' . $safeEmail . '>';
        }

        return '=?UTF-8?B?' . base64_encode($safeName) . '?= <' . $safeEmail . '>';
    }

    private function command(string $command, array $expectedCodes): string
    {
        $this->write($command . "\r\n");
        return $this->expect($expectedCodes);
    }

    private function write(string $data): void
    {
        if (!is_resource($this->socket)) {
            throw new RuntimeException('Koneksi SMTP tidak tersedia.');
        }

        $written = fwrite($this->socket, $data);
        if ($written === false) {
            throw new RuntimeException('Gagal menulis data ke server SMTP.');
        }
    }

    private function expect(array $expectedCodes): string
    {
        if (!is_resource($this->socket)) {
            throw new RuntimeException('Koneksi SMTP tidak tersedia.');
        }

        $response = '';
        while (($line = fgets($this->socket, 515)) !== false) {
            $response .= $line;
            if (strlen($line) < 4 || $line[3] === ' ') {
                break;
            }
        }

        if ($response === '') {
            throw new RuntimeException('Server SMTP tidak mengirim respons.');
        }

        $code = (int) substr($response, 0, 3);
        if (!in_array($code, $expectedCodes, true)) {
            throw new RuntimeException('SMTP error [' . $code . ']: ' . trim($response));
        }

        return $response;
    }

    private function normalizeLineEndings(string $text): string
    {
        return preg_replace("/\r\n|\r|\n/", "\r\n", $text) ?? $text;
    }
}
