<?php
session_start();
<<<<<<< HEAD
require 'konek.php';
=======
require "konek.php";

//CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}
>>>>>>> f7fd821cba0d25b169efd9a250dde8dbf9d19ebe

$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$telepon = $_POST['telepon'];
$tglLahir = $_POST['tglLahir'];
$detail = $_POST['detail'];


$query = "INSERT INTO peserta 
(nama, no_hp, tgl_lahir, alamat, riwayat)
VALUES 
('$nama','$telepon','$tglLahir','$alamat','$detail')";

// var_dump($_POST);
// die();


if (mysqli_query($konek, $query)) {
    echo "<script>
            alert('Data berhasil disimpan!');
            window.location='login_user.php';
          </script>";
} else {
    echo "Error: " . mysqli_error($konek);
}
?>