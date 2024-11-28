<?php

require './../config/db.php';

if (isset($_POST['submit'])) {
    global $db_connect;

    // Ambil data dari form
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $tempImage = $_FILES['image']['tmp_name'];

    // Generate nama file unik
    $randomFilename = time() . '-' . md5(rand()) . '-' . $image;

    // Set direktori upload
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/pertemuan-6/uploads/';

    // Periksa apakah folder uploads ada, jika tidak, buat foldernya
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Buat folder dengan izin tulis
    }

    // Tentukan jalur penyimpanan file
    $uploadPath = $uploadDir . $randomFilename;

    // Pindahkan file ke folder tujuan
    if (move_uploaded_file($tempImage, $uploadPath)) {
        // Simpan path relatif ke database
        $query = "INSERT INTO products (name, price, image) 
                  VALUES ('$name', '$price', 'pertemuan-6/uploads/$randomFilename')";
        if (mysqli_query($db_connect, $query)) {
            echo "Berhasil upload dan data disimpan ke database.";
        } else {
            echo "Gagal menyimpan data ke database: " . mysqli_error($db_connect);
        }
    } else {
        echo "Gagal upload file. Pastikan folder uploads memiliki izin tulis.";
    }
}
