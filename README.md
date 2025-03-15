# Laravel Project Setup

Ikuti langkah-langkah berikut untuk menjalankan proyek Laravel di lingkungan lokal Anda:

## 📥 1. Clone Project

Jalankan perintah berikut di terminal untuk meng-clone repository:

```bash
git clone <URL_REPOSITORY>
cd <NAMA_FOLDER_PROJECT>
```

## 🗄️ 2. Buat Database & Import SQL

1. Buat database baru di MySQL.
2. Import file `.sql` yang telah disediakan ke dalam database tersebut (gunakan `db_rental_ps.sql`).

## 🔧 3. Konfigurasi Environment

1. Ubah nama file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

2. Generate **app_key**:

```bash
php artisan key:generate
```

3. Sesuaikan pengaturan database di file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username_mysql
DB_PASSWORD=password_mysql
```

4. Isi MIDTRANS API Key (gunakan token sandbox dari Midtrans):

```env
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
```

## ▶️ 4. Menjalankan Aplikasi

Jalankan perintah berikut untuk memulai aplikasi:

```bash
php artisan serve
```

Akses aplikasi di browser melalui:

```
http://localhost:8000
```

## 🔑 5. Kredensial Login

### Admin
- **Username**: admin
- **Password**: admin

### Customer
- **Username**: radhit
- **Password**: 123

---

💡 Pastikan semua dependensi telah di-install menggunakan perintah berikut jika diperlukan:

```bash
composer install
```

Jika Anda mengalami masalah, pastikan layanan MySQL berjalan dan konfigurasi `.env` sudah sesuai.

