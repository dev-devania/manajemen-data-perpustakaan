# Manajemen Data Perpustakaan
Sistem Peminjaman Buku (MVP) berbasis **PHP + MySQL**.

## Fitur
- Login (Admin / Petugas)
- Kelola Data Buku (CRUD dan sort)
- Kelola Data Anggota/Peminjam (CRUD dan sort)
- Transaksi Peminjaman & Pengembalian
- UI Dark/Light Mode + Dashboard

## Requirement
- PHP 8.x
- MySQL / MariaDB
- Laragon / XAMPP (< Laragon)
- (Optional) HeidiSQL untuk cek database

## Cara Menjalankan (Local)
1. Clone / download repo ini.
2. Pindahkan folder project ke:
   - Laragon: `D:\laragon\www\NAMA_FOLDER_PROJECT`
   - XAMPP: `C:\xampp\htdocs\NAMA_FOLDER_PROJECT`
3. Buat database di MySQL: `perpustakaan_mvp`
4. Import database:
   - Jalankan file `schema.sql` (atau import via HeidiSQL/phpMyAdmin).
5. Buat file config:
   - Copy `config.ex.php` jadi `config.php`
   - Edit isi `config.php` sesuai host/user/password MySQL anda
6. Jalankan di browser:
   - `http://localhost/NAMA_FOLDER_PROJECT/login.php`

## Struktur File Singkat
- `index.php` : halaman dashboard
- `login.php`, `login_process.php`, `logout.php` : autentikasi
- `users.php`, `users_save.php` : kelola user (admin)
- `app.js` : logic UI (search/sort/modal)
- `style.css` : styling
- `assets/` : logo/gambar
- `schema.sql` : struktur database
