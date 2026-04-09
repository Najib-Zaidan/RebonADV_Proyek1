<?php
session_start();
require "konek.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);
$redirect = $_POST['redirect'];

// ambil data dari tabel akun
$query = "SELECT * FROM akun WHERE username='$username'";
$result = mysqli_query($konek, $query);
// $data = mysqli_fetch_assoc($result);

// if (!$result) {
//     die("Query error: " . mysqli_error($konek));
// }

$data = mysqli_fetch_assoc($result);

// cek password

if ($data && password_verify($password, $data['password'])) {

    $_SESSION['username'] = $data['username'];

    // arahkan sesuai tujuan
    if ($redirect == "form") {
        header("Location: form.php");
    } else {
        header("Location: home1.php");
    }
    exit;

} else {
    echo "<script>
            alert('Login gagal!');
            window.location='login_user.php';
          </script>";
}
?>