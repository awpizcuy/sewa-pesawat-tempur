# (Nama Project: Penyewaan Pesawat Tempur)

## Nama Kelompok
JET505

## Nama Team
- Hafidz Fadilah Tinardi - Backend Developer
- Muhamad Avrisad Garin Rahaguna - Frontend Developer

## Nama Project
Website Penyewaan Pesawat Tempur

## List Fitur
- Autentikasi (Register, Login, Logout)
- Manajemen Profil User
- CRUD Unit (Pesawat) oleh Admin
- CRUD Kategori oleh Admin
- Manajemen User oleh Admin
- Sistem Peminjaman (Maks 2 unit, maks 5 hari)
- Sistem Pengembalian (Hanya Admin)
- Perhitungan Denda
- Riwayat Peminjaman
- Pencarian Unit

# Proyek Sewa Pesawat Tempur

Sebuah web *full-stack* yang dibangun menggunakan framework Laravel 11 untuk mengelola sistem penyewaan unit (pesawat tempur). Proyek ini mencakup REST API yang aman dan antarmuka frontend dasar menggunakan Laravel Blade.

## 🌟 Fitur Utama

Proyek ini menyediakan sistem berbasis peran dengan dua level akses: **Admin** dan **Anggota (User)**.

### 🛡️ Autentikasi (Publik)
* Registrasi Pengguna Baru
* Login Pengguna (API & Web)
* Logout (menggunakan Laravel Sanctum untuk token API)

### 👤 Fitur Anggota (User)
* **Profil:** Melihat dan memperbarui informasi profil pribadi.
* **Unit:** Melihat daftar dan mencari unit (pesawat) yang tersedia.
* **Sewa (Rental):**
    * Membuat pesanan sewa baru.
    * Melihat riwayat sewa pribadi (aktif dan selesai).
    * Melakukan proses pengembalian unit.
    * Membayar denda jika pengembalian terlambat.
* **Chat:** Berkomunikasi dengan admin terkait transaksi sewa tertentu.

### ⚙️ Fitur Admin
* **Manajemen Pengguna (CRUD):** Kemampuan untuk menambah, melihat, mengedit, dan menghapus pengguna.
* **Manajemen Unit (CRUD):** Mengelola data master unit (pesawat), termasuk stok dan status.
* **Manajemen Kategori (CRUD):** Mengelola kategori untuk unit.
* **Manajemen Sewa:**
    * Melihat semua data transaksi sewa dari semua pengguna.
    * Memproses dan mengonfirmasi pengembalian unit oleh pengguna.
    * Mengubah status sewa (misal: 'rented', 'returned').
    * Melihat riwayat sewa per pengguna.
* **Chat:** Membalas pesan dari pengguna terkait transaksi sewa.

## 🛠️ Tech Stack

* **Framework Backend:** **Laravel 11**
* **Bahasa:** **PHP 8.2+**
* **Database:** **MySQL**
* **Autentikasi API:** **Laravel Sanctum**
* **Frontend:** **Laravel Blade**
* **Asset Bundling:** **Vite**
* **Manajemen Dependensi:** Composer (PHP) & NPM (JavaScript)

## 🚀 Cara Instalasi dan Menjalankan Proyek

1.  **Clone Repository**
    ```bash
    git clone https://github.com/awpizcuy/sewa-pesawat-tempur.git
    ```

2.  **Instal Dependensi PHP**
    ```bash
    composer install
    ```

3.  **Instal Dependensi Node.js**
    ```bash
    npm install
    ```

4.  **Siapkan Environment File**
    Salin file `.env.example` menjadi `.env`.
    ```bash
    cp .env.example .env
    ```

5.  **Generate Kunci Aplikasi Laravel**
    ```bash
    php artisan key:generate
    ```

6.  **Konfigurasi Database**
    Buat sebuah database baru di MySQL (misal: `sewa_pesawat_db`). Kemudian, buka file `.env` dan sesuaikan pengaturan `DB_*` Anda.
    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=sewa_pesawat_db
    DB_USERNAME=root
    DB_PASSWORD=password_anda
    ```

7.  **Jalankan Migrasi dan Seeder Database**
    Perintah ini akan membuat semua struktur tabel dan mengisinya dengan data awal (termasuk akun admin dan anggota).
    ```bash
    php artisan migrate --seed
    ```

8.  **Jalankan Server**
    * Buka terminal pertama untuk menjalankan server Vite (frontend assets):
        ```bash
        npm run dev
        ```
    * Buka terminal kedua untuk menjalankan server Laravel (backend):
        ```bash
        php artisan serve
        ```

9.  **Akses Aplikasi**
    Aplikasi Anda sekarang berjalan di `http://127.0.0.1:8000`.

## 🔑 Akun Default

Setelah menjalankan `migrate --seed`, Anda dapat login menggunakan akun berikut:

* **Admin:**
    * **Email:** `admin@proyek.com`
    * **Password:** `password123`
* **Anggota:**
    * **Email:** `anggota@proyek.com`
    * **Password:** `password123`

## 🗃️ Struktur Database

Tabel-tabel utama dalam database ini adalah:

* `users`: Menyimpan data pengguna, termasuk `role` ('admin', 'anggota').
* `units`: Menyimpan data unit (pesawat), termasuk `stock` dan `status` ('available', 'rented', 'maintenance').
* `categories`: Menyimpan kategori untuk unit.
* `rentals`: Menyimpan data transaksi sewa, menghubungkan `user_id` dan `unit_id`. Menyimpan `rent_date`, `due_date`, `return_date`, `status` ('rented', 'returned', 'overdue'), dan `fine_amount`.
* `chat_messages`: Menyimpan pesan chat yang terkait dengan `rental_id` dan `user_id`, dengan penanda `sender` ('user', 'admin').

## 🗺️ Rute (Halaman Web)

Berikut adalah rute web utama yang disediakan oleh `routes/web.php`:

| Halaman            | Path                  | Keterangan                             |
| ------------------ | --------------------- | -------------------------------------- |
| Landing            | `/`                   | Halaman utama                          |
| Login              | `/login`              | Halaman login                          |
| Register           | `/register`           | Halaman registrasi                     |
| Daftar Unit        | `/units`              | (Anggota) Halaman melihat unit         |
| Profil             | `/profile`            | (Anggota) Halaman profil pengguna      |
| Sewa Saya          | `/my-rentals`         | (Anggota) Halaman riwayat sewa         |
| Buat Sewa Baru     | `/rentals/new`        | (Anggota) Halaman form sewa            |
| Admin Dashboard    | `/admin/dashboard`    | (Admin) Dashboard admin                |
| Admin Manajemen User | `/admin/users`        | (Admin) Manajemen data pengguna        |
| Admin Manajemen Unit | `/admin/units`        | (Admin) Manajemen data unit            |
| Admin Manajemen Kategori | `/admin/categories`   | (Admin) Manajemen data kategori      |
| Admin Manajemen Sewa | `/admin/rentals`      | (Admin) Manajemen data sewa          |

## 🔌 Endpoint API

Berikut adalah daftar endpoint API utama yang disediakan oleh `routes/api.php`. Semua rute Anggota & Admin dilindungi oleh `auth:sanctum`.

### Rute Publik (Autentikasi)
| Method | Endpoint    | Deskripsi                 |
| :---   | :---------- | :------------------------ |
| `POST` | `/register` | Registrasi pengguna baru. |
| `POST` | `/login`    | Login dan dapatkan token. |
| `POST` | `/logout`   | Hapus token (Perlu Auth). |

### Rute Anggota (Perlu Autentikasi)
| Method | Endpoint                       | Deskripsi                        |
| :---   | :----------------------------- | :------------------------------- |
| `GET`  | `/profile`                     | Dapat profil pengguna.           |
| `PUT`  | `/profile`                     | Update profil pengguna.          |
| `GET`  | `/units`                       | Dapat semua unit.                |
| `GET`  | `/units/search`                | Cari unit.                       |
| `POST` | `/rentals`                     | Buat pesanan sewa baru.          |
| `GET`  | `/my-rentals`                  | Dapat daftar sewa (aktif).       |
| `GET`  | `/my-rentals/history`          | Dapat riwayat sewa (selesai).    |
| `POST` | `/my-rentals/{rental}/pay-fine`| Bayar denda untuk sewa.          |
| `POST` | `/my-rentals/{rental}/return`| Proses pengembalian unit.        |
| `GET`  | `/chat/{rentalId}/messages`    | Dapat pesan chat u/ sewa.        |
| `POST` | `/chat/{rentalId}/send`        | Kirim pesan chat u/ sewa.        |

### Rute Admin (Perlu Autentikasi & Role Admin)
| Method  | Endpoint                         | Deskripsi                                   |
| :---    | :------------------------------- | :------------------------------------------ |
| `GET`   | `/admin/users`                   | Dapat semua pengguna.                       |
| `POST`  | `/admin/users`                   | Buat pengguna baru.                         |
| `GET`   | `/admin/users/{user}`            | Tampilkan 1 pengguna.                       |
| `PUT`   | `/admin/users/{user}`            | Update 1 pengguna.                          |
| `DELETE`| `/admin/users/{user}`            | Hapus 1 pengguna.                           |
| `GET`   | `/admin/units`                   | Dapat semua unit (resource).                |
| `POST`  | `/admin/units`                   | Buat unit baru (resource).                  |
| `...`   | `/admin/units/{unit}`            | (GET, PUT, DELETE) Manajemen 1 unit.        |
| `GET`   | `/admin/categories`              | Dapat semua kategori (resource).            |
| `...`   | `/admin/categories/{category}`   | (POST, GET, PUT, DELETE) Manajemen kategori. |
| `GET`   | `/admin/rentals`                 | Dapat semua data sewa.                      |
| `POST`  | `/admin/rentals/{rental}/return` | Konfirmasi pengembalian oleh admin.         |
| `PATCH` | `/admin/rentals/{rental}/status` | Update status sewa (misal: 'overdue').      |
| `GET`   | `/admin/users/{userId}/history`  | Lihat riwayat sewa spesifik per pengguna.   |
| `GET`   | `/admin/chat/{rentalId}/messages`| (Admin) Dapat pesan chat u/ sewa.           |
| `POST`  | `/admin/chat/{rentalId}/send`    | (Admin) Kirim pesan chat u/ sewa.           |
