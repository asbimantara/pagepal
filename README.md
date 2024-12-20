# PagePal - Aplikasi Tracking Buku

Aplikasi web untuk melacak progress membaca buku menggunakan PHP dan MongoDB.

## Fitur

- Tracking progress membaca buku
- Manajemen koleksi buku
- Statistik membaca
- Catatan untuk setiap buku
- Rating buku yang sudah selesai dibaca

## Teknologi

- PHP 8.1.10
- MongoDB
- HTML/CSS/JavaScript
- Font Awesome
- Composer

## Instalasi

1. Clone repository
```bash
git clone https://github.com/username/pagepal.git
cd pagepal
```

2. Install dependencies
```bash
composer install
```

3. Setup MongoDB
- Buat database bernama `bookTracker`
- Copy `.env.example` ke `.env`
- Sesuaikan konfigurasi MongoDB di `.env`

4. Jalankan aplikasi
```bash
php -S localhost:8000
```

5. Buka browser dan akses `http://localhost:8000`

## Struktur Folder

```
pagepal/
├── assets/          # CSS, JS, dan gambar
├── config/          # Konfigurasi database
├── layouts/         # Header dan footer
├── pages/           # Halaman aplikasi
└── uploads/         # Upload gambar profil
```

## Kontribusi

1. Fork repository
2. Buat branch baru
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## Lisensi

MIT License
