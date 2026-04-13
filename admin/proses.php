<?php 
session_start();
require 'konek.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($konek, $_POST['username']);
    $password = mysqli_real_escape_string($konek, $_POST['password']);

    $query = mysqli_query($konek, "SELECT * FROM akun WHERE username='$username' AND password='$password' AND role='admin'");
    $cek = mysqli_num_rows($query);
    $nama = mysqli_fetch_assoc($query);
    //echo "proses php";
    //var_dump($cek);
    //die();

    if ($cek) {
      //echo "cek proses php";
      //die();
        $_SESSION['login'] = true;
        $_SESSION['username'] = $nama['username'];
        header("Location: index.php");
        exit;
    } else {
      $_SESSION["gagal"] = "Gagal Login...";
      header("Location: login.php");
      exit;
    }
} else {
    header("Location: login.php");
}
?>
