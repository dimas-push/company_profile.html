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


    <!-- <h1>Tambah Produk</h1>
    <a href="show.php">Lihat data produk</a>
    <form action="./backend/create.php" method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="input nama produk">
        <input type="number" name="price" placeholder="input harga produk">
        <input type="file" name="image">
        <input type="submit" value="simpan" name="submit">
    </form> -->

    <div class="container position-absolute top-50 start-50 translate-middle">
        <h1 class="text-center">Tambah Produk</h1>
        <a href="show.php">Lihat Data Produk</a>
        <form action="./backend/create.php" method="post" enctype="multipart/form-data" class="row">
            <div class="mb-3">
                <div class="col">
                    <label for="formGroupExampleInput2" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" name="name" placeholder="Input Nama Produk">
                </div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput2" class="form-label">Harga Produk</label>
                <input type="number" class="form-control" name="price" placeholder="Input Harga Produk">
            </div>
            <div class="mb-3">
                <input type="file" class="form-control" name="image">
            </div>
            <div class="mb-3">
                <input type="submit" value="simpan" name="submit" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>

</html>