# 📚 PagePal - Aplikasi Tracking Buku

<div align="center">

![PagePal Banner](docs/screenshots/landing%20page.png)

**Teman setia perjalanan membacamu** - Aplikasi web untuk melacak progress membaca buku dengan fitur lengkap.

[![Live Demo](https://img.shields.io/badge/Live%20Demo-PagePal-6c63ff?style=for-the-badge)](https://pagepal-rrua.onrender.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php)](https://php.net)
[![MongoDB](https://img.shields.io/badge/MongoDB-Atlas-47A248?style=flat-square&logo=mongodb)](https://mongodb.com)

</div>

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| 📖 **Track Progress** | Catat halaman yang sudah dibaca dan lihat progressmu |
| 📝 **Catatan Membaca** | Buat catatan untuk setiap buku (max 3 per buku) |
| 📊 **Statistik** | Lihat statistik membacamu secara detail |
| ⭐ **Rating** | Beri rating untuk buku yang sudah selesai |
| 👤 **Profil** | Kelola profil dan foto profil |
| ☁️ **Cloud Storage** | Foto tersimpan di Cloudinary (persisten) |

---

## 📸 Screenshots

<details>
<summary><b>🏠 Landing Page & Auth</b></summary>

### Landing Page
![Landing Page](docs/screenshots/landing%20page.png)

### Login
![Login](docs/screenshots/login.png)

### Register
![Register](docs/screenshots/sign%20up.png)

</details>

<details>
<summary><b>📚 Fitur Utama</b></summary>

### Dashboard
![Dashboard](docs/screenshots/dashboard.png)

### Koleksi Buku
![Koleksi Buku](docs/screenshots/laman%20koleksi%20buku.png)

### Tambah Buku
![Tambah Buku](docs/screenshots/laman%20tambah%20buku.png)

### Detail Buku
![Detail Buku](docs/screenshots/laman%20detail%20buku.png)

### Update Progress
![Update Progress](docs/screenshots/laman%20update%20progres%20membaca.png)

</details>

<details>
<summary><b>📊 Profil & Statistik</b></summary>

### Profil
![Profil](docs/screenshots/laman%20profile.png)

### Statistik
![Statistik](docs/screenshots/laman%20statistik.png)

</details>

---

## 🛠️ Teknologi

- **Backend:** PHP 8.2
- **Database:** MongoDB Atlas
- **Storage:** Cloudinary
- **Hosting:** Render
- **Frontend:** HTML, CSS, JavaScript
- **Icons:** Font Awesome

---

## 🚀 Instalasi Lokal

### Prerequisites
- PHP 8.1+ dengan extension MongoDB
- Composer
- MongoDB (lokal atau Atlas)

### Steps

```bash
# 1. Clone repository
git clone https://github.com/asbimantara/pagepal.git
cd pagepal

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
# Edit .env dengan konfigurasi MongoDB Anda

# 4. Jalankan server
php -S localhost:8000

# 5. Buka http://localhost:8000
```

---

## 📁 Struktur Folder

```
pagepal/
├── assets/           # CSS, JS, images
│   ├── css/
│   ├── js/
│   └── images/
├── config/           # Database & session config
├── docs/             # Documentation & screenshots
├── layouts/          # Header & footer
├── pages/            # Application pages
└── uploads/          # User uploads (local)
```

---

## 🌐 Live Demo

Aplikasi ini sudah di-deploy dan bisa diakses di:

**🔗 [https://pagepal-rrua.onrender.com](https://pagepal-rrua.onrender.com)**

> ⚠️ **Note:** Free tier Render akan sleep setelah tidak ada aktivitas 15 menit. Request pertama mungkin memerlukan waktu ~30 detik untuk wake up.

---

## 📖 Dokumentasi Lengkap

Lihat dokumentasi lengkap dengan semua screenshot di folder [`docs/`](docs/).

---

## 🤝 Kontribusi

1. Fork repository
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## 📄 Lisensi

MIT License - Lihat [LICENSE](LICENSE) untuk detail.

---

<div align="center">

**Made with ❤️ by [Surya Bimantara](https://github.com/asbimantara)**

</div>
