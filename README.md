# PagePal

Aplikasi tracking buku menggunakan PHP dan MongoDB

## Persyaratan

- PHP >= 7.4
- MongoDB
- Composer
- MongoDB PHP Extension

## Instalasi

1. Clone repository
```bash
git clone https://github.com/asbimantara/pagepal.git
cd pagepal
```

2. Install dependencies
```bash
composer install
```

3. Konfigurasi MongoDB
- Buat database baru bernama `bookTracker`
- Sesuaikan konfigurasi koneksi di `config/database.php`

4. Jalankan aplikasi
```bash
php -S localhost:8000
```

5. Buka browser dan akses `http://localhost:8000`