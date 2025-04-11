# Kredit BNI - Sistem Manajemen Kredit Sederhana

## Deskripsi
**Kredit BNI** adalah sistem manajemen data berbasis PHP native yang digunakan untuk mengelola data kredit, angsuran, dan berbagai fitur penunjang seperti absensi, user management, dan merchant. Proyek ini ditujukan untuk membantu proses pengelolaan kredit dengan sistematis, terstruktur, dan mendukung multiuser.

---

## Fitur Utama
- **Multiuser (Admin & Operator)**
- **Manajemen Data Kredit**
  - Kredit Biasa
  - Kredit Rumah
  - Angsuran Kredit
- **Manajemen Data Merchant**
- **Manajemen Data Looser** *(akan terhubung dengan Gmail)*
- **Absensi Pegawai** *(opsional dengan fitur GPS)*
- **Manajemen Kinerja Pegawai**
- **Manajemen User**
- **Pengaturan Aplikasi (Identitas, Logo, Sistem)**
- **Upload file bukti pembayaran** (dengan batasan ukuran)

---

## Struktur Folder
```
kredit_bni/
├── admin/
│   ├── dashboard.php
│   ├── header.php, navbar.php, sidebar.php, footer.php
│   ├── data_kredit/, data_angsuran_kredit/, data_kredit_rumah/
│   ├── data_looser/, data_merchant/, data_absensi_karyawan/
│   ├── data_kinerja_karyawan/, data_user/
├── operator/
│   ├── dashboard.php
│   ├── struktur serupa admin (akses terbatas)
├── config/
│   └── koneksi.php
├── assets/
│   ├── css/, js/, img/
├── uploads/
├── index.php (login)
├── logout.php
```

---

## Teknologi
- PHP Native (no framework)
- MySQL/MariaDB
- HTML5, CSS3, JavaScript
- Optional: LeafletJS atau Google Maps (untuk fitur lokasi)

---

## Instalasi
1. Clone/download repository ini.
2. Buat database dengan nama `kredit_bni`.
3. Import file SQL yang disediakan.
4. Atur file `config/koneksi.php` sesuai konfigurasi database Anda.
5. Jalankan melalui localhost/XAMPP/Laragon.

---

## Catatan Pengguna
- Admin memiliki akses penuh.
- Operator hanya memiliki akses terbatas pada data kredit, angsuran, dan absensi.
- Pastikan file upload tidak melebihi batas maksimal 5 MB.

---

## Pengembangan Selanjutnya
- Integrasi Gmail API untuk data Looser
- GPS Absensi berbasis lokasi
- Sistem Penjadwalan Shift RSUD
- Laporan PDF & Export Excel

---

## Lisensi
Project ini dikembangkan sebagai kebutuhan internal dan bebas digunakan serta dimodifikasi untuk pengembangan sistem manajemen serupa.

---

## Kontributor
- Developer: Ahmad Fauzi (Progresif_project)
- Instansi: Progresif_Project

