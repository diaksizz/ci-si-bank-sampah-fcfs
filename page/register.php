<?php
    require_once("../system/config/koneksi.php");

    function generateRandomNin() {
      $randomNumber = mt_rand(10000, 99999); // Generate a random 5-digit number
      return "NSB" . $randomNumber;
  }

    if(isset($_POST['simpan'])){
        $nin = $format;
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $rt = mysqli_real_escape_string($conn, $_POST['rt']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        // $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Secure password hashing

        // $sql = mysqli_query($conn, "SELECT * FROM nasabah WHERE nin = '$nin'");

        // if (mysqli_fetch_array($sql) > 0) {
        //     echo "<script>
        //             alert('Maaf akun sudah terdaftar');
        //           </script>";
        //     echo "<meta http-equiv='refresh' content='0; url=http://localhost/bsk09/page/admin.php?page=data-nasabah-full'>";
        //     return FALSE;      
        // }
        $nin = generateRandomNin(); 

        $insert = mysqli_query($conn, "INSERT INTO nasabah (nin, nama, rt, alamat, telepon, email, password) VALUES ('$nin','$nama','$rt','$alamat','$telepon','$email','$password')");

        if ($insert) {
            echo "<script>
                    alert('Selamat berhasil input data!');
                  </script>";
        } else {
            echo "<script>
                    alert('Terjadi kesalahan saat input data!');
                  </script>";
        }

        echo "<meta http-equiv='refresh' content='0; url=http://localhost/bsk09/page/login.php'>";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Register</title>
    <link rel="stylesheet" href="../asset/internal/css/style_2.css">
    <link href="http://fonts.googleapis.com/css?family=Cookie:700" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../asset/internal/img/img-local/favicon.ico">
    <style>
        .center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            background: var(--bg);
            border-radius: 10px;
            box-shadow: 10px 10px 15px rgba(0, 0, 0, 0.5);
        }

        .center h1 {
            text-align: center;
            padding: 20px 0;
            color: var(--primary);
        }

        .center form {
            padding: 0 40px;
            box-sizing: border-box;
        }
    </style>
</head>
<body class="login">
    <div class="center">
        <form action="" method="post" enctype="multipart/form-data">
            <h1>Registrasi Nasabah Bank Sampah</h1>
            <div class="row justify-content-center align-items-center">
                <div class="col">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" id="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="telepon" class="form-label">No HP</label>
                        <input type="number" name="telepon" class="form-control" id="telepon" required>
                    </div>
                    <div class="mb-3">
                        <label for="rt" class="form-label">RT</label>
                        <input type="number" name="rt" class="form-control" id="rt" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" id="alamat" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                    </div>
                </div>
                <div class="mt-5 mb-7" style="margin-top: 5%; margin-bottom: 5%;">
                    <button type="submit" name="simpan" class="btn btn-primary">Daftar</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
