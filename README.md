# EDUSCOPE

Website profil bimbingan belajar dan kursus. Pengunjung dapat melihat program yang tersedia dan mengirim pertanyaan melalui formulir kontak. Pengelola masuk ke area admin untuk memperbarui data kelas dan menangani pesan yang diterima.

## Suasana belajar

<p align="center">
  <img src="img/eduscope-hero.png" alt="Ilustrasi kegiatan belajar dan berkarya di EDUSCOPE" width="49%">
  <img src="img/eduscope-classroom.png" alt="Ilustrasi belajar bersama guru dan teman sekelas" width="49%">
</p>

## Fitur

- Halaman profil dan katalog program dengan informasi sasaran peserta, durasi, jadwal, biaya, dan gambar kelas.
- Formulir kontak untuk pertanyaan calon peserta.
- Pendaftaran akun, login, logout, dan halaman profil.
- Area admin untuk menambah, mengubah, dan menghapus kelas serta mengelola pesan masuk.
- Pembagian hak akses antara pengguna dan admin.

Database awal berisi tujuh program contoh. Jadwal dan biaya belum diisi, sehingga pengelola dapat memperbaruinya setelah informasinya tersedia.

## Teknologi

- **PHP native** untuk halaman server, autentikasi, validasi formulir, dan proses data.
- **MySQL/MariaDB** untuk menyimpan akun, program, dan pesan kontak.
- **HTML, CSS, dan JavaScript** untuk antarmuka responsif dan interaksi di browser. JavaScript ditulis tanpa framework.
- **Apache** sebagai web server; konfigurasi lokal project disiapkan untuk Laragon.

Project menggunakan `mysqli` untuk koneksi database. Tidak ada framework PHP atau paket Composer yang perlu dipasang.

## Alur aplikasi

1. Pengunjung membuka halaman utama, membaca informasi EDUSCOPE, lalu melihat daftar kelas.
2. Pengunjung mengirim pertanyaan lewat formulir kontak. Pesan disimpan di tabel `inquiries`.
3. Calon peserta membuat akun. Akun baru mendapat peran `user` secara otomatis dan dapat digunakan untuk masuk serta melihat halaman profil.
4. Pengelola dengan peran `admin` membuka area admin untuk mengelola kelas dan membaca atau menghapus pesan.

Akun admin tidak dibuat otomatis melalui pendaftaran. Setelah membuat akun, pengelola perlu mengubah perannya lewat database.

## Struktur project

```text
backend/          Proses formulir login, registrasi, kontak, dan pengelolaan kelas
config/           Koneksi database, deteksi environment, sesi, CSRF, dan helper aplikasi
partials/         Komponen halaman yang digunakan bersama
img/              Aset gambar untuk halaman website
uploads/          Gambar kelas yang diunggah melalui area admin
storage/          Penyimpanan runtime untuk pembatasan percobaan dan sesi cadangan
index.php         Halaman utama dan formulir kontak
show.php          Katalog kelas
login.php         Halaman masuk
register.php      Halaman pendaftaran
profile.php       Halaman profil pengguna
create.php        Form tambah kelas untuk admin
edit.php          Form ubah kelas untuk admin
inquiries.php     Daftar pesan kontak untuk admin
pemweb-db.sql     Pembuatan database, tabel, dan data kelas awal
style.css         Gaya umum website
home.css          Gaya halaman utama
auth.js           Interaksi untuk menampilkan atau menyembunyikan kata sandi
courses.js        Interaksi katalog kelas
```

Database memiliki tiga tabel utama: `users` menyimpan akun dan peran, `courses` menyimpan informasi kelas, sedangkan `inquiries` menyimpan pertanyaan dari formulir kontak.

## Menjalankan secara lokal

1. Jalankan Apache dan MySQL melalui Laragon.
2. Buka phpMyAdmin, lalu import file `pemweb-db.sql`. Script membuat database `pemweb-db`, tabel, dan tujuh kelas contoh.
3. Buka `http://localhost/company_profile.html/`.
4. Buat akun melalui halaman **Daftar**. Akun tersebut akan memiliki peran `user`.
5. Untuk memberi akses admin, jalankan query berikut di phpMyAdmin dan ganti alamat email dengan email akun yang dibuat:

   ```sql
   UPDATE users SET role = 'admin' WHERE email = 'alamat-admin-anda@example.com';
   ```

   Hak akses admin dibaca dari database setiap kali halaman admin diakses.

Di Laragon lokal, aplikasi memakai koneksi bawaan: host `localhost`, user `root`, kata sandi kosong, dan database `pemweb-db`.

## Konfigurasi server

Project memerlukan PHP dengan ekstensi `mysqli` dan `fileinfo`. PHP perlu dapat menulis ke folder `uploads` dan `storage/rate-limits`; folder sementara sistem juga digunakan untuk menyimpan sesi jika tersedia.

Untuk deployment, atur environment berikut pada server PHP:

```text
EDUSCOPE_ENV=production
EDUSCOPE_DB_HOST=alamat-database
EDUSCOPE_DB_USER=user-database
EDUSCOPE_DB_PASSWORD=kata-sandi-database
EDUSCOPE_DB_NAME=nama-database
```

Di luar localhost, aplikasi menganggap environment sebagai production kecuali `EDUSCOPE_ENV` diatur ke `local` atau `development`. Gunakan akun database khusus dengan kata sandi yang kuat; konfigurasi production menolak user `root` atau kata sandi kosong. Jangan masukkan kredensial database ke repository.

Gunakan HTTPS di server publik karena cookie sesi production memakai atribut `Secure`. Aturan header keamanan di `.htaccess` memerlukan `mod_headers`; jika menggunakan Nginx, pasang aturan setara pada konfigurasi server. Atur `upload_max_filesize` minimal `5M` dan `post_max_size` sekitar `6M` agar unggahan gambar kelas dapat diproses.

## Penanganan data dan keamanan

- Kata sandi disimpan sebagai hash melalui `password_hash()` dan diperiksa dengan `password_verify()`.
- Formulir memakai token CSRF, dan query database menggunakan prepared statement.
- Percobaan login, pendaftaran, dan pengiriman pesan dibatasi berdasarkan alamat IP yang diterima PHP.
- Gambar kelas dibatasi maksimal 5 MB dan diperiksa tipe MIME-nya sebelum disimpan.
- Data pembatasan permintaan berada di `storage/rate-limits`; jangan unggah isi runtime folder tersebut ke repository.

Pesan kontak berisi data pribadi. Tetapkan masa simpan yang sesuai dan hapus pesan ketika tidak lagi diperlukan.
