# Aplikasi Kasir Laundry

Aplikasi web untuk mengelola transaksi laundry, dibuat dengan PHP dan MySQL.
Ada dua level pengguna, yaitu admin dan petugas. [Tambahkan satu kalimat tentang
kenapa kamu membuatnya, misalnya tugas sekolah atau terinspirasi usaha laundry.]

## Fitur

**Admin**
- Dashboard berisi ringkasan pendapatan, grafik pendapatan harian, dan distribusi status cucian
- Kelola data petugas, jenis cucian, dan akun
- Laporan pemasukan keuangan dan laporan pekerjaan berdasarkan rentang tanggal

**Petugas**
- Tambah dan ubah data customer
- Buat transaksi, lalu ubah statusnya (Proses, Dicuci, Disetrika, Selesai)
- Cetak invoice transaksi

## Teknologi

- PHP (native) dan MySQL / MariaDB
- Bootstrap 5.3 dan Chart.js
- XAMPP sebagai server lokal

## Tampilan

![Halaman login](screenshots/01-login.png)
![Dashboard admin](screenshots/02-dashboard-admin.png)
![Distribusi status](screenshots/02b-distribusi-status.png)
![Data petugas](screenshots/03-data-petugas.png)
![Laporan pemasukan](screenshots/04-laporan-pemasukan.png)
![Data transaksi](screenshots/05-transaksi.png)
![Invoice](screenshots/06-invoice.png)
![Data customer](screenshots/07-data-customer.png)

## Cara Menjalankan

1. Pasang XAMPP, lalu nyalakan Apache dan MySQL.
2. Salin folder proyek ini ke `C:\xampp\htdocs\` dengan nama `laundry`.
3. Buka `http://localhost/phpmyadmin`, buat database baru bernama `laundry`.
4. Pilih database itu, klik tab Import, lalu pilih file `database/laundry.sql`.
5. Buka `http://localhost/laundry/admin/index.php` di browser.

## Akun Demo

| Level   | Username | Password |
|---------|----------|----------|
| Admin   | admin    | 1        |
| Petugas | Budi     | [isi password Budi] |

Semua data (nama, nomor telepon, transaksi) adalah data dummy.

## Yang Aku Pelajari

- [Tulis 2-3 hal dengan kata-katamu sendiri, misalnya: membuat relasi antar
  tabel, membuat laporan berdasarkan rentang tanggal, atau memakai Chart.js]
- Hal yang masih ingin kuperbaiki: query masih ditulis langsung (rawan SQL
  injection) dan password masih memakai MD5. Rencananya diganti dengan
  prepared statement dan `password_hash()`.
  
  Transaksi dummy bertanggal 28 Oktober sampai 3 November 2026, jadi dashboard
dan laporan paling terisi kalau dilihat pada rentang tanggal itu.