# Muezza Store

Muezza Store adalah aplikasi e-commerce yang dibangun menggunakan framework [Laravel](https://laravel.com/). 

## 📋 Fitur Utama
- **Katalog Produk:** Menampilkan daftar produk dengan detail lengkap.
- **Keranjang Belanja:** Memudahkan pengguna dalam mengelola produk yang akan dibeli.
- **Checkout & Pembayaran:** Proses transaksi yang aman dan mudah.
- **Manajemen Pesanan:** Pelacakan status pesanan.
- **Panel Admin:** Dashboard untuk mengelola produk, kategori, dan pesanan.

## 🚀 Prasyarat

Sebelum menjalankan proyek ini, pastikan sistem Anda telah menginstal:
- PHP >= 8.2
- Composer
- Node.js & NPM
- Database MySQL atau SQLite

## 🛠️ Instalasi & Konfigurasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal Anda:

1. **Clone repository ini**
   ```bash
   git clone https://github.com/hidayaturromadhan/muezzastore.git
   cd muezzastore
   ```

2. **Install dependensi PHP dan Node.js**
   ```bash
   composer install
   npm install
   ```

3. **Salin file environment**
   ```bash
   cp .env.example .env
   ```
   *Catatan: Pastikan Anda menyesuaikan konfigurasi database (DB_*) di dalam file `.env`.*

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Jalankan Migrasi Database dan Seeder (jika ada)**
   ```bash
   php artisan migrate --seed
   ```

6. **Build aset frontend (Vite)**
   ```bash
   npm run build
   # atau jika dalam tahap development
   npm run dev
   ```

7. **Jalankan local server**
   ```bash
   php artisan serve
   ```

Aplikasi sekarang dapat diakses melalui browser di alamat: `http://localhost:8000`

## 🔌 Konfigurasi Layanan Eksternal (API & Integrasi)

Aplikasi ini mungkin membutuhkan integrasi dengan layanan eksternal (misal: Payment Gateway, RajaOngkir, Email SMTP). **Untuk alasan keamanan, kami tidak menyertakan API Key asli di dalam repositori ini.**

Bagi pengembang yang ingin menjalankan fitur ini, silakan tambahkan kunci API Anda sendiri pada file `.env`:

```env
# Contoh Konfigurasi (Tambahkan di file .env Anda)
PAYMENT_GATEWAY_CLIENT_KEY=your_client_key_here
PAYMENT_GATEWAY_SERVER_KEY=your_server_key_here

SHIPPING_API_KEY=your_shipping_api_key_here
```
> **⚠️ PENTING:** Jangan pernah mengetikkan API Key, Secret Key, atau Password asli Anda ke dalam file `README.md` atau `github`. Selalu gunakan variabel environment (`.env`).

## 📡 API Documentation (Endpoints)

Jika Anda sedang mengembangkan aplikasi mobile atau frontend terpisah, aplikasi ini menyediakan beberapa REST API Endpoints. 

*(Daftar endpoint di bawah ini bersifat publik / tidak menyertakan data sensitif. Autentikasi dilakukan menggunakan Bearer Token).*

| Method | Endpoint | Deskripsi | Auth Required |
| --- | --- | --- | --- |
| `GET` | `/api/products` | Mendapatkan daftar semua produk | No |
| `GET` | `/api/products/{id}` | Mendapatkan detail spesifik produk | No |
| `POST` | `/api/login` | Autentikasi user dan mendapatkan token | No |
| `GET` | `/api/user` | Mendapatkan profil user yang sedang login | Yes |
| `POST` | `/api/checkout` | Memproses data transaksi/pembelian | Yes |

*Catatan: Dokumentasi API lengkap (Postman Collection / Swagger) dapat di-generate di lingkungan lokal Anda masing-masing, atau diberikan secara terpisah oleh administrator jaringan.*

## 🔒 Keamanan & Praktik Terbaik

- **Jangan pernah** mempublikasikan file `.env`. File ini sudah dimasukkan ke dalam `.gitignore` secara default oleh Laravel.
- **Kredensial API:** Jangan menaruh API key secara hardcode di dalam file PHP atau Javascript. Selalu panggil via `env('API_KEY')` di Laravel.
- Pastikan folder `vendor/` dan `node_modules/` tidak ikut ter-commit (sudah diatur di `.gitignore`).
- Folder `storage/` dan `bootstrap/cache/` harus memiliki izin tulis (write permissions) di server produksi.

## 🛡️ Panduan untuk Tim Keamanan & Pentest (Penetration Testing)

Bagi tim keamanan (*Security Auditor* atau *Pentester*) yang ditugaskan untuk menguji sistem ini, berikut adalah informasi arsitektur dan batasan pengujian (*Rules of Engagement*):

### 1. Scope Pengujian (Ruang Lingkup)
- **In-Scope:** Seluruh logika bisnis pada domain utama (Autentikasi, Manajemen Keranjang, Checkout, dan Panel Admin).
- **Out-of-Scope:** Layanan pihak ketiga seperti Payment Gateway (misal Midtrans/Xendit) atau API Ekspedisi (RajaOngkir). Harap **tidak** melakukan *stress test* (DDoS) atau manipulasi pada endpoint pihak ketiga.

### 2. Teknologi yang Digunakan (Tech Stack)
- **Framework:** Laravel (PHP)
- **Database:** MySQL / SQLite
- **Frontend:** Blade Templating Engine (dengan integrasi Vite/NPM)
- **Autentikasi:** Laravel Session / Sanctum (untuk API Token)

### 3. Peran Pengguna (User Roles)
Untuk menguji kerentanan *Broken Access Control* (seperti IDOR atau *Privilege Escalation*), aplikasi ini memiliki pembagian peran sebagai berikut:
1. **Guest (Unauthenticated):** Dapat melihat katalog produk, tetapi tidak dapat melakukan checkout.
2. **Buyer / User (Authenticated):** Dapat menambah barang ke keranjang, melakukan checkout, dan melihat riwayat transaksinya sendiri.
3. **Admin:** Memiliki akses ke Dashboard Admin (`/admin`), dapat memanipulasi data produk, kategori, dan melihat seluruh data transaksi pengguna.

### 4. Lingkungan Pengujian
Pastikan pengujian dilakukan di lingkungan **Staging / Development** dan bukan di Production. Tim keamanan dapat menggunakan Seeder (langkah instalasi ke-5) untuk menghasilkan *dummy data* dan akun *test* yang diperlukan.

---

## 📜 Lisensi

Proyek ini menggunakan lisensi [MIT License](https://opensource.org/licenses/MIT). Silakan lihat file LICENSE untuk detail lebih lanjut.
