<?php
require './config/db.php';

// Mendapatkan ID produk dari parameter URL
$id = $_GET['id'];

// Ambil data produk berdasarkan ID
$product = mysqli_query($db_connect, "SELECT * FROM products WHERE id = $id");
$row = mysqli_fetch_assoc($product);

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image']; // Jika ingin mengubah gambar juga

    // Update data produk
    mysqli_query($db_connect, "UPDATE products SET name='$name', price='$price', image='$image' WHERE id=$id");

    // Redirect ke halaman data produk
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

<body>

    <div class="container">
        <div class="position-absolute top-50 start-50 translate-middle">
            <h1 class="text-center">Edit Produk</h1>
            <form method="post">
                <div class="row">
                    <div class="col-auto">
                        <label>Nama Produk:</label>
                        <input type="text" name="name" value="<?= $row['name']; ?>" required class="form-label"><br><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-auto">
                        <label>Harga:</label>
                        <input type="text" name="price" value="<?= $row['price']; ?>" required
                            class="form-label"><br><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-auto">
                        <label>Gambar URL:</label>
                        <input type="text" name="image" value="<?= $row['image']; ?>" class="form-label"><br><br>
                    </div>
                </div>
                <button type="submit" name="update" class="btn btn-primary">Update</button>
            </form>
            <a href="index.php">Kembali ke Data Produk</a>
        </div>
    </div>
    </div>
</body>

</html>