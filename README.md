# Sistem Peminjaman Buku Perpustakaan — Universitas Gunadarma

Aplikasi sistem informasi peminjaman buku perpustakaan berbasis web yang dirancang dan dikembangkan untuk memenuhi spesifikasi studi kasus **Technical Test Aptitude Universitas Gunadarma**.

---

## 📌 Ringkasan Sistem & Peran Pengguna (Role-Based Access Control)

Aplikasi menerapkan sistem pembatasan akses berbasis peran (*Role-Based Access Control / RBAC*) yang diisolasi ketat di level backend:

### 1. Peran Admin (`admin` - Petugas Perpustakaan)
- **Koleksi & Katalog Buku**: Manajemen CRUD lengkap data buku (judul, penulis, tahun terbit, kategori, stok, serta unggah berkas sampul buku).
- **Pencarian Data Buku**: Fitur pencarian buku pada tabel koleksi berdasarkan judul, penulis, maupun kategori.
- **Persetujuan Peminjaman (*Approval Workflow*)**:
  - Meninjau permintaan peminjaman baru (`pending`).
  - Menyetujui (*approve*) pinjaman dengan pengurangan stok otomatis dan aman dalam `DB::transaction`.
  - Menolak (*reject*) pinjaman dengan menyertakan alasan tertulis spesifik (contoh: stok fisik rusak, kuota habis) yang otomatis diteruskan sebagai notifikasi ke peminjam.
- **Konfirmasi Pengembalian**: Memverifikasi pengembalian buku (`return_requested` menjadi `dikembalikan`) dengan pemulihan stok buku seketika dalam transaksi database.
- **Manajemen Kategori**: CRUD kategori buku dengan validasi integritas (kategori yang memiliki buku aktif tidak dapat dihapus sembarangan).
- **Data Anggota Perpustakaan**: Memantau daftar anggota terdaftar beserta Nomor Pokok Mahasiswa (NPM), alamat email, dan statistik keaktifan pinjaman.
- **Riwayat Semua Peminjaman**: Tabel riwayat peminjaman dengan pencarian multi-parameter (nama peminjam, email, NPM, ID pinjaman, judul buku) serta modal rincian transaksi lengkap.

### 2. Peran Peminjam (`peminjam` - Mahasiswa / Pemustaka)
- **Katalog Buku & Live Search**: Menelusuri koleksi buku dengan pencarian instan (AJAX Live Search dengan teknik *debounce* 300ms tanpa memuat ulang halaman) dan filter dropdown kategori di panel samping kiri.
- **Dukungan Dual Login (NPM atau Email)**: Mahasiswa dapat masuk (*login*) menggunakan alamat email terdaftar maupun Nomor Pokok Mahasiswa (NPM).
- **Modal Detail Interaktif**: Menampilkan sinopsis lengkap, data bibliografi, ketersediaan stok, dan pemilih tanggal pengembalian (*date range picker* maksimal 14 hari).
- **Pusat Notifikasi (*Notification Center*)**: Ikon lonceng pada bilah navigasi dengan *badge counter* pesan belum dibaca. Menampilkan konfirmasi persetujuan, alasan penolakan pinjaman, dan pembaruan status yang langsung mengarahkan pengguna ke halaman riwayat.
- **Alur Pengembalian**: Mengajukan pengembalian buku yang sedang dipinjam untuk diverifikasi petugas.
- **Riwayat Peminjaman Pribadi**: Menampilkan status seluruh peminjaman yang diajukan (`pending`, `dipinjam`, `return_requested`, `dikembalikan`, `ditolak`) yang terproteksi oleh Laravel Policy.

---

## 🛡️ Standar Keamanan Backend & Kepatuhan Rubrik Evaluasi

1. **Proteksi Akses URL Backend (HTTP 403 Forbidden)**:
   - Pengguna dengan peran `peminjam` yang mencoba mengakses URL rute admin (`/admin/*`) secara sengaja maupun tidak sengaja akan **langsung ditolak dengan status HTTP 403 Forbidden** melalui middleware `CheckRole` (bukan sekadar pengalihan diam-diam).
2. **Proteksi Eskalasi Hak Akses (*Privilege Escalation Prevention*)**:
   - Kolom `role` dikunci pada saat registrasi. Pendaftaran akun publik baru selalu otomatis mengunci peran sebagai `peminjam`.
3. **Otorisasi Laravel Policy**:
   - `LoanPolicy` diterapkan pada level controller untuk menjamin bahwa mahasiswa hanya dapat melihat dan mengajukan pengembalian untuk transaksi peminjaman miliknya sendiri.
4. **Validasi Stok Server-Side & Transaksi Database Atomic**:
   - Pengecekan ketersediaan stok buku wajib dilakukan langsung di server backend sebelum entri pinjaman dicatat. Seluruh manipulasi stok dibungkus dalam `DB::transaction()` guna mencegah inkonsistensi data atau *race condition*.
5. **Integritas Relasi Data Basis Data**:
   - Buku yang sedang berstatus dipinjam atau memiliki transaksi aktif tidak dapat dihapus oleh admin.
   - Kategori yang sedang memuat buku dilindungi dari penghapusan mendadak.
6. **Form Request Server-Side Validation**:
   - Seluruh input formulir (buku, login, registrasi) divalidasi menggunakan class Form Request (`StoreBookRequest`, `UpdateBookRequest`, `LoginRequest`).
7. **Bebas Komentar AI / Penjelas Alur**:
   - Seluruh berkas Controller, Model, dan Blade view dibersihkan dari komentar deskriptif alur logika buatan AI, hanya menyisakan penanda fitur standar yang ringkas dan profesional.

---

## 🎨 Tampilan UI/UX & Desain Antarmuka

- **Tipografi Bersih & Modern**: Menggunakan Google Font **Open Sans** di seluruh antarmuka aplikasi.
- **Ikon Vektor Ringan**: Seluruh elemen visual menggunakan **Inline SVG** yang tajam, responsif, dan bebas ketergantungan CDN eksternal.
- **Responsivitas Perangkat Seluler**: Dilengkapi bilah navigasi adaptif dengan menu drawer hamburger pada resolusi ponsel, serta tabel data yang otomatis bertransformasi menjadi kartu ringkas (*compact card stack*) di layar kecil.
- **Desain Warna Tegas**: Header banner manajemen menggunakan solid blue (*sky-600*) dengan kontras tipografi putih yang jelas dan nyaman dibaca.

---

## 🔑 Kredensial Akun Pengujian (Demo Accounts)

Data akun pengujian telah disediakan secara otomatis melalui Database Seeder (`php artisan db:seed`):

| Peran | Kredensial Login (NPM atau Email) | Kata Sandi | Keterangan |
|---|---|---|---|
| **Admin Perpustakaan** | `admin@gunadarma.ac.id` | `password` | Dibuat otomatis via `AdminSeeder` |
| **Mahasiswa (Demo)** | `mahasiswa@gunadarma.ac.id` *(atau NPM: `50421001`)* | `password` | Akun mahasiswa dengan data pinjaman |
| **Mahasiswa (Faiz)** | `alfanofaiz@gmail.com` *(atau NPM: `50421002`)* | `password` | Akun mahasiswa aktif |
| **Peminjam Baru** | Registrasi Publik (`/register`) | Bebas (min. 8 karakter) | Otomatis memiliki role `peminjam` |

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### 1. Prasyarat Sistem
- **PHP** >= 8.2 (Mendukung PHP 8.2 dan PHP 8.3)
- **Composer** (Manajer paket dependensi PHP)
- **Node.js** (v18 atau lebih baru) & **NPM**
- Ekstensi PHP: `pdo_sqlite` (atau `pdo_mysql` jika menggunakan MySQL)

### 2. Langkah-Langkah Pemasangan

```bash
# 1. Masuk ke direktori proyek
cd Project-PeminjamanBuku

# 2. Pasang dependensi backend PHP
composer install

# 3. Pasang dependensi frontend JavaScript & CSS
npm install

# 4. Salin berkas konfigurasi environment
cp .env.example .env

# 5. Generate Application Encryption Key
php artisan key:generate

# 6. Jalankan Migrasi Database dan Seeder Data Awal
php artisan migrate:fresh --seed

# 7. Kompilasi Aset Frontend (Tailwind CSS & JavaScript)
npm run build

# 8. Jalankan Server Aplikasi Laravel
php artisan serve
```

Aplikasi dapat langsung diakses melalui peramban web pada alamat:
👉 **`http://127.0.0.1:8000`**

---

## 🛠️ Stack Teknologi

- **Backend Framework**: Laravel 11 (PHP 8.3)
- **Autentikasi**: Laravel Breeze (Blade Stack dengan kustomisasi Dual Login Email/NPM)
- **Frontend / Styling**: Tailwind CSS, Alpine.js, Google Font Open Sans
- **Pencarian Asinkron**: Vanilla JavaScript Fetch API dengan Debounce 300ms
- **Database**: SQLite (dapat dialihkan ke MySQL / MariaDB / PostgreSQL melalui berkas `.env`)

---

## 📝 Catatan Teknis & Penanganan Masalah Khusus

1. **Penanganan Cross-Host Session Cookies pada Redirect Notifikasi**:
   - *Masalah*: Saat admin membuat notifikasi, helper `route()` dapat menyimpan URL absolut seperti `http://localhost:8000/peminjam/loans`. Jika mahasiswa mengakses web melalui `http://127.0.0.1:8000`, browser berpindah origin, mengakibatkan sesi cookie tidak terbaca dan memicu redirect tidak terduga ke halaman `/login`.
   - *Solusi*: Seluruh URL tindakan notifikasi dinormalisasi menjadi path relatif (`/peminjam/loans`) baik di level database maupun di `NotificationController`, memastikan sesi pengguna tetap utuh tanpa pernah ter-logout.
2. **Dual Identifier Authentication (Email atau NPM)**:
   - *Masalah*: Mahasiswa lebih terbiasa menggunakan Nomor Pokok Mahasiswa (NPM) untuk masuk ke sistem kampus.
   - *Solusi*: Mengkustomisasi `LoginRequest` untuk mendeteksi apakah input berformat email (`filter_var`) atau string numerik NPM, lalu mencocokkannya ke kolom yang sesuai di basis data secara transparan.
3. **Pemberitahuan Penolakan Buku yang Informatif**:
   - *Masalah*: Peminjam sering bingung mengapa permintaan pinjamannya tidak diproses atau hilang.
   - *Solusi*: Menyediakan modal penolakan bagi petugas untuk memilih atau mengetik alasan pembatalan. Alasan ini dicatat di kolom `rejection_note` tabel `loans` dan dikirimkan seketika sebagai notifikasi bertipe `danger` ke akun mahasiswa terkait.
