# EDUSCOPE

Situs bimbel dan kursus. Pengunjung bisa melihat kelas yang tersedia dan mengirim pertanyaan; pengelola bisa memperbarui daftar kelas serta membaca pesan yang masuk.

## Menjalankan di Laragon

1. Nyalakan Apache dan MySQL.
2. Buka phpMyAdmin, lalu import `pemweb-db.sql`. File ini membuat tabel yang dibutuhkan dan menambahkan tujuh kelas EDUSCOPE jika belum ada. Jadwal serta biaya belum diisi karena informasinya perlu dikonfirmasi.
3. Kunjungi `http://localhost/company_profile.html/`.
4. Daftarkan akun lewat halaman **Daftar**. Akun baru dibuat sebagai pengguna biasa.
5. Jika akun tersebut perlu mengelola situs, ubah perannya menjadi `admin` lewat phpMyAdmin. Ganti email contoh di bawah dengan email akun tadi:

   ```sql
   UPDATE users SET role = 'admin' WHERE email = 'alamat-admin-anda@example.com';
   ```

6. Buka kembali halaman admin. Hak pengelola diperiksa ulang dari database setiap kali halaman admin dibuka.

Laragon lokal di `localhost` atau `127.0.0.1` bisa memakai pengaturan koneksi bawaan. Di host lain, aplikasi menganggap lingkungan sebagai production kecuali `EDUSCOPE_ENV=local` atau `development` ditetapkan. Untuk deployment, atur `EDUSCOPE_ENV=production` dan isi `EDUSCOPE_DB_HOST`, `EDUSCOPE_DB_USER`, `EDUSCOPE_DB_PASSWORD`, serta `EDUSCOPE_DB_NAME` pada lingkungan PHP. Production menolak user `root` dan kata sandi kosong. Buat akun database khusus aplikasi dengan hak secukupnya.

Apache/PHP perlu izin menulis ke folder `uploads` dan `storage/rate-limits`. Folder `storage` menyimpan pembatasan percobaan login dan formulir; jangan publikasikan isinya. File upload dibatasi 5 MB, jadi atur `upload_max_filesize` minimal `5M` dan `post_max_size` sedikit lebih besar, misalnya `6M`.

Project ini membutuhkan PHP dengan ekstensi `mysqli` dan `fileinfo`. Pembatasan berlaku per alamat IP yang diterima PHP (`REMOTE_ADDR`): 20 percobaan masuk per 15 menit, 20 pendaftaran per jam, dan 12 pesan per 10 menit. Jika memakai reverse proxy, atur sumber alamat klien hanya dari proxy tepercaya; jangan memakai header `X-Forwarded-For` mentah.

Untuk website publik, gunakan HTTPS. Cookie sesi production selalu memakai atribut `Secure`; pastikan pengunjung membuka situs lewat HTTPS. Aturan header keamanan pada `.htaccess` memerlukan `mod_headers`; server Nginx memerlukan konfigurasi setara di virtual host.

Pesan pengunjung sekarang bisa dihapus oleh pengelola. Tentukan masa simpan yang sesuai dengan kebutuhan EDUSCOPE dan hapus pesan yang tidak lagi diperlukan. File SQL tidak membuat akun contoh; jangan simpan data akun asli atau hash kata sandi di file tersebut.
