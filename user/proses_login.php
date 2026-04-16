<?php
session_start();
require "konek.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$query = "SELECT * FROM akun WHERE username='$username'";
$result = mysqli_query($konek, $query);
$data = mysqli_fetch_assoc($result);

if ($data) {

    // kalau password belum di-hash pakai ini:
    if ($password == $data['password']) {

        $_SESSION['username'] = $data['username'];
        $_SESSION['id_akun'] = $data['id_akun'];

        echo "<script>
                alert('Login berhasil!');
                window.location.href='home1.php';
              </script>";
        exit;

    } else {
        echo "<script>
                alert('Password salah!');
                window.location.href='login_user.php';
              </script>";
        exit;
    }

} else {
    echo "<script>
            alert('Username tidak ditemukan!');
            window.location.href='login_user.php';
          </script>";
    exit;
}
?>