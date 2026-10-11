# API Mobile SIREPO - Panduan Flutter

Dokumen ini ringkas untuk konsumsi Flutter.

## 1. Base URL
Gunakan base URL dari `.env` (`APP_BASE_URL`).

Contoh:
- `http://localhost/sirepo-v1`

Semua endpoint di bawah diawali:
- `{{BASE_URL}}/api/mobile/...` (disarankan untuk Flutter)
- Beberapa endpoint juga tersedia di `{{BASE_URL}}/api/...` (alias umum)

## 2. Format Response
Sukses:
```json
{
  "success": true,
  "data": { }
}
```

Error:
```json
{
  "success": false,
  "message": "Pesan error"
}
```

## 3. Authentication
### Login Mahasiswa
- `POST /api/mobile/login/mahasiswa`
- body JSON:
```json
{
  "email": "mahasiswa@mail.com",
  "password": "password123"
}
```

### Login Dosen
- `POST /api/mobile/login/dosen`
- body JSON sama seperti mahasiswa.

### Ambil Profil Ringkas User Login
- `GET /api/me`
- Header: `Authorization: Bearer <access_token>`

### Logout
- `POST /api/logout`
- Header: `Authorization: Bearer <access_token>`

## 4. Lupa Password (API)
### Request Reset
- `POST /api/mobile/forgot-password/request`
- body JSON:
```json
{
  "role": "mahasiswa",
  "email": "user@mail.com"
}
```
`role`: `mahasiswa` atau `dosen`.

### Verifikasi Token Reset
- `GET /api/mobile/forgot-password/verify/{token}`

### Reset Password
- `POST /api/mobile/forgot-password/reset`
- body JSON:
```json
{
  "token": "TOKEN_DARI_EMAIL",
  "password": "passwordBaru123",
  "password_confirmation": "passwordBaru123"
}
```

Catatan:
- Link reset web tetap tersedia: `/reset-password/{token}`.
- Pada environment non-production, response request reset bisa berisi `debug_token` untuk testing.

## 5. Beranda dan Repository Publik
### Beranda Mobile (Public)
- `GET /api/mobile/public/home?limit=6`

### List Repository Publik
- `GET /api/mobile/public/repositories`
- Query filter yang didukung:
  - `q` (pencarian)
  - `author`
  - `tahun`
  - `prodi_id` atau `program_studi_id`
  - `mk_id` atau `mata_kuliah_id`
  - `jenis` atau `jenis_karya`
  - `page`, `per_page`

Contoh:
- `GET /api/mobile/public/repositories?q=sistem&author=Ahmad&tahun=2024&prodi_id=1&mk_id=2&jenis=skripsi&page=1&per_page=10`

### Detail Repository
- `GET /api/mobile/public/repositories/{id}`

Troubleshooting cepat jika masih `repository tidak ditemukan`:
- Pastikan value yang dikirim benar-benar ID repository (bukan `bookmark_id`).
- Log URL final sebelum request, contoh:
  - `GET /api/mobile/public/repositories/12`
- Hindari kirim object/map ke path; kirim string/int ID saja.

## 6. List Kategori
### Kategori Lengkap
- `GET /api/mobile/public/categories`

### Lookup Lengkap (kategori + data pendukung)
- `GET /api/mobile/public/lookups`

Kategori yang tersedia:
- program studi
- mata kuliah
- tahun
- jenis karya
- author

## 7. Bookmark CRUD
Header wajib: `Authorization: Bearer <access_token>`

### Mahasiswa
- List: `GET /api/mobile/mahasiswa/bookmarks?page=1&per_page=20`
- Detail: `GET /api/mobile/mahasiswa/bookmarks/{bookmark_id}`
- Create: `POST /api/mobile/mahasiswa/bookmarks`
- Delete: `DELETE /api/mobile/mahasiswa/bookmarks/{bookmark_id}`
- Toggle (opsional): `POST /api/mobile/mahasiswa/bookmarks/toggle`

### Dosen
- List: `GET /api/mobile/dosen/bookmarks?page=1&per_page=20`
- Detail: `GET /api/mobile/dosen/bookmarks/{bookmark_id}`
- Create: `POST /api/mobile/dosen/bookmarks`
- Delete: `DELETE /api/mobile/dosen/bookmarks/{bookmark_id}`
- Toggle (opsional): `POST /api/mobile/dosen/bookmarks/toggle`

Body create/toggle:
```json
{
  "repository_id": 12
}
```
atau
```json
{
  "slug": "judul-repository"
}
```

Catatan payload bookmark:
- `bookmark_id` = ID record bookmark (untuk hapus bookmark endpoint).
- `id` / `repository_id` = ID repository (pakai ini untuk buka detail repository).

## 8. Profil (Mobile)
Header wajib: `Authorization: Bearer <access_token>`

### Mahasiswa
- Read: `GET /api/mobile/mahasiswa/profile`
- Update: `PUT /api/mobile/mahasiswa/profile` (boleh `POST`/`PATCH`)

Body update mahasiswa:
```json
{
  "nama_lengkap": "Nama Mahasiswa",
  "email": "mahasiswa@mail.com",
  "username": "usernamebaru",
  "password": "opsional_password_baru"
}
```

### Dosen
- Read: `GET /api/mobile/dosen/profile`
- Update: `PUT /api/mobile/dosen/profile` (boleh `POST`/`PATCH`)

Body update dosen:
```json
{
  "nama_lengkap": "Nama Dosen",
  "email": "dosen@mail.com",
  "username": "usernamebaru",
  "nidn_nip": "0123456789",
  "password": "opsional_password_baru"
}
```

## 9. Endpoint Umum (opsional)
- `GET /api/profile` -> profil user login (role-aware)
- `PUT /api/profile` -> update profil user login (role-aware)

## 10. Contoh Integrasi Flutter (package:http)
```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiClient {
  final String baseUrl;
  String? token;

  ApiClient(this.baseUrl);

  Map<String, String> _headers({bool auth = false}) {
    final h = {'Content-Type': 'application/json'};
    if (auth && token != null) h['Authorization'] = 'Bearer $token';
    return h;
  }

  Future<void> loginMahasiswa(String email, String password) async {
    final res = await http.post(
      Uri.parse('$baseUrl/api/mobile/login/mahasiswa'),
      headers: _headers(),
      body: jsonEncode({'email': email, 'password': password}),
    );

    final json = jsonDecode(res.body);
    if (res.statusCode >= 400 || json['success'] != true) {
      throw Exception(json['message'] ?? 'Login gagal');
    }

    token = json['data']['access_token'];
  }

  Future<Map<String, dynamic>> getRepositories({String? q, int page = 1}) async {
    final uri = Uri.parse('$baseUrl/api/mobile/public/repositories')
        .replace(queryParameters: {
      'page': '$page',
      if (q != null && q.isNotEmpty) 'q': q,
    });

    final res = await http.get(uri, headers: _headers());
    final json = jsonDecode(res.body);
    if (res.statusCode >= 400 || json['success'] != true) {
      throw Exception(json['message'] ?? 'Gagal ambil repository');
    }
    return json['data'];
  }

  Future<Map<String, dynamic>> getMyProfileMahasiswa() async {
    final res = await http.get(
      Uri.parse('$baseUrl/api/mobile/mahasiswa/profile'),
      headers: _headers(auth: true),
    );

    final json = jsonDecode(res.body);
    if (res.statusCode >= 400 || json['success'] != true) {
      throw Exception(json['message'] ?? 'Gagal ambil profil');
    }

    return json['data']['user'];
  }
}
```

## 11. Ringkasan cepat endpoint wajib sesuai request
- Login mahasiswa: `POST /api/mobile/login/mahasiswa`
- Login dosen: `POST /api/mobile/login/dosen`
- Lupa password: 
  - `POST /api/mobile/forgot-password/request`
  - `GET /api/mobile/forgot-password/verify/{token}`
  - `POST /api/mobile/forgot-password/reset`
- Beranda: `GET /api/mobile/public/home`
- List repository + search/filter: `GET /api/mobile/public/repositories`
- Detail repository: `GET /api/mobile/public/repositories/{id}`
- List kategori: `GET /api/mobile/public/categories`
- Bookmark CRUD: `/api/mobile/{mahasiswa|dosen}/bookmarks`
- Profil (read/update): `/api/mobile/{mahasiswa|dosen}/profile`

## 12. Endpoint Baru (Detail + Kategori + Hasil Kategori)
### Detail Repository (siap page detail Flutter)
- `GET /api/mobile/public/repositories/{id}`

Tambahan field penting pada `data.item`:
- `identifier` (selalu `id` untuk mobile; pakai field ini saat navigate ke detail)
- `identifier_type` (`id`, fallback `slug` hanya untuk data legacy)
- `detail_url`
- `download_url`
- `web_url`
- `has_file`

Tambahan:
- `data.related` berisi rekomendasi repository terkait.

### Data Page Kategori (struktur siap render UI)
- `GET /api/mobile/public/categories/page`

Response utama:
```json
{
  "success": true,
  "data": {
    "groups": [
      {
        "type": "program_studi",
        "title": "Program Studi",
        "items": [
          { "value": "1", "label": "Teknik Informatika", "total": 12 }
        ]
      }
    ]
  }
}
```

### Hasil dari Kategori
- `GET /api/mobile/public/categories/{type}/{value}/repositories?page=1&per_page=10`

`type` yang didukung:
- `program_studi`
- `mata_kuliah`
- `tahun`
- `jenis_karya`
- `author`

Contoh:
- `/api/mobile/public/categories/program_studi/1/repositories`
- `/api/mobile/public/categories/tahun/2025/repositories`
- `/api/mobile/public/categories/jenis_karya/skripsi/repositories`

## 13. Implementasi Flutter: Service + 3 Page
### API service (http)
```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class PublicRepositoryApi {
  final String baseUrl;
  PublicRepositoryApi(this.baseUrl);

  Future<Map<String, dynamic>> getRepositoryDetail(String repositoryId) async {
    final uri = Uri.parse('$baseUrl/api/mobile/public/repositories/$repositoryId');
    final res = await http.get(uri);
    final json = jsonDecode(res.body) as Map<String, dynamic>;
    if (res.statusCode >= 400 || json['success'] != true) {
      throw Exception(json['message'] ?? 'Gagal ambil detail repository');
    }
    return json['data'] as Map<String, dynamic>;
  }

  Future<List<Map<String, dynamic>>> getCategoryGroups() async {
    final uri = Uri.parse('$baseUrl/api/mobile/public/categories/page');
    final res = await http.get(uri);
    final json = jsonDecode(res.body) as Map<String, dynamic>;
    if (res.statusCode >= 400 || json['success'] != true) {
      throw Exception(json['message'] ?? 'Gagal ambil kategori');
    }
    return List<Map<String, dynamic>>.from(json['data']['groups'] ?? const []);
  }

  Future<Map<String, dynamic>> getRepositoriesByCategory({
    required String type,
    required String value,
    int page = 1,
    int perPage = 10,
  }) async {
    final uri = Uri.parse(
      '$baseUrl/api/mobile/public/categories/$type/$value/repositories',
    ).replace(queryParameters: {
      'page': '$page',
      'per_page': '$perPage',
    });

    final res = await http.get(uri);
    final json = jsonDecode(res.body) as Map<String, dynamic>;
    if (res.statusCode >= 400 || json['success'] != true) {
      throw Exception(json['message'] ?? 'Gagal ambil hasil kategori');
    }
    return json['data'] as Map<String, dynamic>;
  }
}
```

### 1) RepositoryDetailPage
```dart
class RepositoryDetailPage extends StatefulWidget {
  final String repositoryId;
  const RepositoryDetailPage({super.key, required this.repositoryId});

  @override
  State<RepositoryDetailPage> createState() => _RepositoryDetailPageState();
}

class _RepositoryDetailPageState extends State<RepositoryDetailPage> {
  late Future<Map<String, dynamic>> future;

  @override
  void initState() {
    super.initState();
    future = PublicRepositoryApi('http://localhost/sirepo-v1')
        .getRepositoryDetail(widget.repositoryId);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Detail Repository')),
      body: FutureBuilder<Map<String, dynamic>>(
        future: future,
        builder: (context, snap) {
          if (snap.connectionState != ConnectionState.done) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snap.hasError) return Center(child: Text('${snap.error}'));

          final item = snap.data!['item'] as Map<String, dynamic>;
          final related = List<Map<String, dynamic>>.from(
            snap.data!['related'] ?? const [],
          );

          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              Text(item['judul'] ?? '-', style: Theme.of(context).textTheme.titleLarge),
              const SizedBox(height: 8),
              Text('${item['author'] ?? '-'} • ${item['tahun'] ?? '-'}'),
              const SizedBox(height: 16),
              Text(item['abstrak'] ?? 'Tidak ada abstrak'),
              const SizedBox(height: 20),
              Text('Repository Terkait', style: Theme.of(context).textTheme.titleMedium),
              ...related.map((r) => ListTile(
                    title: Text(r['judul'] ?? '-'),
                    subtitle: Text(r['author'] ?? '-'),
                  )),
            ],
          );
        },
      ),
    );
  }
}
```

### 2) CategoryPage (daftar kategori)
```dart
class CategoryPage extends StatefulWidget {
  const CategoryPage({super.key});

  @override
  State<CategoryPage> createState() => _CategoryPageState();
}

class _CategoryPageState extends State<CategoryPage> {
  late Future<List<Map<String, dynamic>>> future;

  @override
  void initState() {
    super.initState();
    future = PublicRepositoryApi('http://localhost/sirepo-v1').getCategoryGroups();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Kategori Repository')),
      body: FutureBuilder<List<Map<String, dynamic>>>(
        future: future,
        builder: (context, snap) {
          if (snap.connectionState != ConnectionState.done) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snap.hasError) return Center(child: Text('${snap.error}'));

          final groups = snap.data ?? const [];
          return ListView.builder(
            itemCount: groups.length,
            itemBuilder: (context, i) {
              final group = groups[i];
              final title = group['title'] ?? '-';
              final type = group['type'] ?? '';
              final items = List<Map<String, dynamic>>.from(group['items'] ?? const []);
              return ExpansionTile(
                title: Text(title),
                children: items.map((item) {
                  return ListTile(
                    title: Text(item['label'] ?? '-'),
                    trailing: Text('${item['total'] ?? 0}'),
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => CategoryResultPage(
                            type: type.toString(),
                            value: item['value'].toString(),
                            label: item['label'].toString(),
                          ),
                        ),
                      );
                    },
                  );
                }).toList(),
              );
            },
          );
        },
      ),
    );
  }
}
```

### 3) CategoryResultPage (hasil kategori)
```dart
class CategoryResultPage extends StatefulWidget {
  final String type;
  final String value;
  final String label;
  const CategoryResultPage({
    super.key,
    required this.type,
    required this.value,
    required this.label,
  });

  @override
  State<CategoryResultPage> createState() => _CategoryResultPageState();
}

class _CategoryResultPageState extends State<CategoryResultPage> {
  late Future<Map<String, dynamic>> future;

  @override
  void initState() {
    super.initState();
    future = PublicRepositoryApi('http://localhost/sirepo-v1')
        .getRepositoriesByCategory(type: widget.type, value: widget.value);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Kategori: ${widget.label}')),
      body: FutureBuilder<Map<String, dynamic>>(
        future: future,
        builder: (context, snap) {
          if (snap.connectionState != ConnectionState.done) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snap.hasError) return Center(child: Text('${snap.error}'));

          final items = List<Map<String, dynamic>>.from(
            snap.data!['items'] ?? const [],
          );

          if (items.isEmpty) {
            return const Center(child: Text('Belum ada repository di kategori ini.'));
          }

          return ListView.separated(
            itemCount: items.length,
            separatorBuilder: (_, __) => const Divider(height: 1),
            itemBuilder: (context, i) {
              final item = items[i];
              return ListTile(
                title: Text(item['judul'] ?? '-'),
                subtitle: Text('${item['author'] ?? '-'} • ${item['tahun'] ?? '-'}'),
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (_) => RepositoryDetailPage(
                        repositoryId: item['id'].toString(),
                      ),
                    ),
                  );
                },
              );
            },
          );
        },
      ),
    );
  }
}
```
