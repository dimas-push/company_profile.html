<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

<body>

    <div class="container mt-5">
        <h1>Data produk</h1>
        <table border="" class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama produk</th>
                    <th>harga</th>
                    <th>gambar produk</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require './config/db.php';

                $products = mysqli_query($db_connect, "SELECT * FROM products");
                $no = 1;

                while ($row = mysqli_fetch_assoc($products)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['name']; ?></td>
                        <td><?= $row['price']; ?></td>
                        <!-- <td><img src="<?= $row['image']; ?>" width="100"></td> -->
                        <td><a href="<?= $row['image']; ?>" target="_blank">unduh</a></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id']; ?>">Edit</a>
                            <a href="delete.php?id=<?= $row['id']; ?>">Hapus</a>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>

</html>