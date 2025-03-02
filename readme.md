# Sistem Manajemen Pasien Rumah Sakit

Aplikasi web untuk manajemen data pasien rumah sakit dengan fitur CRUD, antrian, dan pemeriksaan dokter.

## Fitur

- ✅ Manajemen data pasien (CRUD)
- ✅ Sistem antrian pasien
- ✅ Panel dokter untuk pemeriksaan
- ✅ Validasi form client & server side
- ✅ Generate laporan pemeriksaan PDF
- ✅ Multi panel (Resepsionis, Dokter, Arsip)

## Teknologi

- PHP 8.2
- MySQL/MariaDB
- Bootstrap 5.1
- Font Awesome 6.0
- TCPDF (PDF Generator)

## Struktur Database

### Tabel `pasien`
- `id_pasien` (Primary Key)
- `Nama`
- `Jenis_kelamin`
- `Tanggal_lahir`
- `No_telepon`
- `Alamat`
- `Keluhan_pasien`

### Tabel `antrian_pasien`
- `id_antrian` (Primary Key)
- `id_pasien` (Foreign Key)
- `tanggal_antrian`
- `status` (menunggu/diperiksa/selesai)
- `waktu_daftar`

### Tabel `pemeriksaan`
- `id_pemeriksaan` (Primary Key)
- `id_antrian` (Foreign Key)
- `diagnosa`
- `resep`
- `catatan_dokter`
- `waktu_pemeriksaan`

## Instalasi

1. Clone repository ini
```bash
git clone https://github.com/username/sistem-manajemen-pasien.git
```

2. Import database
```bash
mysql -u root -p < pasien.sql
```

3. Konfigurasi database di `App/Config/Database.php`
```php
private $host = "localhost";
private $username = "root";
private $password = "";
private $database = "db_pasien";
```

4. Jalankan di web server (contoh: XAMPP)
```
http://localhost/pasien/
```

## Struktur Proyek

```
├── App/
│   └── Config/
│       └── Database.php
├── src/
│   └── Model/
│       ├── Model.php
│       └── Pasien.php
├── view/
│   ├── add.php
│   ├── update.php
│   ├── delete.php
│   ├── index.php
│   └── antrian.php
├── dokter/
│   ├── index.php
│   └── generate_pdf.php
├── tcpdf/
├── autoload.php
└── pasien.sql
```

## Implementasi OOP

### 1. Encapsulation
```php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "db_pasien";
    private $conn;

    public function getConnection() {
        return $this->conn;
    }
}
```

### 2. Inheritance
```php
class Model {
    protected $db;
    protected $table;

    public function getAll() {...}
    public function getById($id) {...}
    public function deleteById($id) {...}
}

class Pasien extends Model {
    public function tambahPasien($data) {...}
    public function updatePasien($id, $data) {...}
}
```

## Validasi

### Client-side
- Validasi format ID (hanya angka)
- Validasi nama (hanya huruf)
- Validasi nomor telepon (10-15 digit)
- Validasi tanggal lahir (tidak boleh masa depan)
- Validasi alamat & keluhan (minimal 5 karakter)

### Server-side
- Validasi menggunakan PHP
- Prepared statements untuk SQL injection prevention
- Sanitasi input

## Screenshots

[Screenshot aplikasi akan ditambahkan di sini]

## Kontribusi

1. Fork repository
2. Buat branch fitur baru (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -am 'Menambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## Lisensi

[MIT License](LICENSE)

## Author

- Nama: [Nama Anda]
- Email: [Email Anda]
- GitHub: [@username] 