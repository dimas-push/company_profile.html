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

6. Keluar, lalu masuk kembali supaya perubahan peran berlaku.

Laragon bisa memakai pengaturan koneksi bawaan. Jika database berada di server lain, atur `EDUSCOPE_DB_HOST`, `EDUSCOPE_DB_USER`, `EDUSCOPE_DB_PASSWORD`, dan `EDUSCOPE_DB_NAME` pada lingkungan PHP. Buat akun database khusus aplikasi dengan akses secukupnya.

Apache perlu izin menulis ke folder `uploads`. File SQL tidak membuat akun contoh; jangan simpan data akun asli atau hash kata sandi di file tersebut.
