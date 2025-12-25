# 📖 Dokumentasi Lengkap PagePal

## Daftar Isi
- [Halaman Landing](#halaman-landing)
- [Autentikasi](#autentikasi)
- [Dashboard](#dashboard)
- [Koleksi Buku](#koleksi-buku)
- [Tambah Buku](#tambah-buku)
- [Detail Buku](#detail-buku)
- [Update Progress](#update-progress)
- [Profil](#profil)
- [Statistik](#statistik)

---

## Halaman Landing

Halaman utama yang menyambut pengunjung dengan informasi tentang PagePal.

![Landing Page](screenshots/landing%20page.png)

**Fitur:**
- Penjelasan singkat tentang aplikasi
- Tombol "Mulai Sekarang" untuk mendaftar
- Tombol "Sudah Punya Akun?" untuk login

---

## Autentikasi

### Login

![Login](screenshots/login.png)

**Fitur:**
- Login dengan email dan password
- Link ke halaman registrasi

### Registrasi

![Register](screenshots/sign%20up.png)

**Fitur:**
- Daftar dengan nama, email, dan password
- Validasi input
- Link ke halaman login

---

## Dashboard

![Dashboard](screenshots/dashboard.png)

**Fitur:**
- Ringkasan statistik membaca
- Quote motivasi harian
- Akses cepat ke fitur utama

---

## Koleksi Buku

![Koleksi Buku](screenshots/laman%20koleksi%20buku.png)

**Fitur:**
- Daftar semua buku dalam koleksi
- Filter berdasarkan status (Belum Mulai, Sedang Dibaca, Selesai)
- Pencarian buku
- Klik buku untuk melihat detail

---

## Tambah Buku

![Tambah Buku](screenshots/laman%20tambah%20buku.png)

**Fitur:**
- Input judul, penulis, jumlah halaman
- Pilih cover default (6 pilihan)
- Upload cover custom (max 5MB)
- Validasi input

---

## Detail Buku

![Detail Buku](screenshots/laman%20detail%20buku.png)

**Fitur:**
- Informasi lengkap buku
- Progress membaca dengan progress bar
- Ganti cover (default atau upload)
- Tambah catatan (max 3 per buku)
- Rating buku (untuk buku yang sudah selesai)
- Hapus buku

---

## Update Progress

![Update Progress](screenshots/laman%20update%20progres%20membaca.png)

**Fitur:**
- Update halaman yang sedang dibaca
- Otomatis update status:
  - Belum Mulai → Sedang Dibaca (saat mulai baca)
  - Sedang Dibaca → Selesai (saat sampai halaman terakhir)

---

## Profil

![Profil](screenshots/laman%20profile.png)

**Fitur:**
- Update foto profil (max 5MB, JPG/PNG)
- Update nama dan email
- Ganti password
- Statistik membaca ringkas

---

## Statistik

![Statistik](screenshots/laman%20statistik.png)

**Fitur:**
- Total buku dalam koleksi
- Buku yang sedang dibaca
- Buku yang sudah selesai
- Total halaman yang sudah dibaca
- Progress keseluruhan

---

## Teknologi yang Digunakan

| Teknologi | Kegunaan |
|-----------|----------|
| PHP 8.2 | Backend server |
| MongoDB Atlas | Database cloud |
| Cloudinary | Penyimpanan gambar cloud |
| Render | Hosting aplikasi |
| HTML/CSS/JS | Frontend |
| Font Awesome | Icons |

---

## Deployment

Aplikasi ini di-deploy menggunakan:
- **Render** - Web service dengan Docker
- **MongoDB Atlas** - Database cluster M0 (free tier)
- **Cloudinary** - Image storage (free tier)

Live URL: [https://pagepal-rrua.onrender.com](https://pagepal-rrua.onrender.com)
