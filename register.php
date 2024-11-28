<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- Link Boostrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</head>

<body>

    <form action="./backend/register.php" method="POST"
        class="container position-absolute top-50 start-50 translate-middle row">
        <div class="">
            <h2>Register</h2>
            <div class="mb-3 col-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" placeholder="Masukan Nama Anda" name="name">
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
            </div>
        </div>
        <div class="mb-3">
            <div class="col-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" placeholder="Masukan Password Anda" name="password">
            </div>
        </div>
        <div class="mb-3">
            <div class="col-3">
                <label for="confirm" class="form-label">Konfirmasi Password</label>
                <input type="password" name="confirm" placeholder="Konfirmasi Password Anda" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <input type="submit" value="login" name="submit" class="btn btn-primary">
            </div>
        </div>
    </form>


</body>

</html>