<?php
session_start();
require "konek.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);
// $redirect = $_POST['redirect'];

// ambil data dari tabel akun
$query = "SELECT * FROM akun WHERE username='$username'";
$result = mysqli_query($konek, $query);
// $data = mysqli_fetch_assoc($result);

if (!$result) {
    die("Query error: " . mysqli_error($konek));
}

$data = mysqli_fetch_assoc($result);

// cek password

if ($data && password_verify($password, $data['password'])) {
    $_SESSION['username'] = $data['username'];

    echo "<script>
            alert('Login berhasil!');
            window.location='home1.php';
          </script>";
} else {
    echo "<script>
            alert('Login gagal!');
            window.location='../login_user.html';
          </script>";
}
?>