<?php
session_start();
require 'konek.php';
$id = $_SESSION['id_akun'];

$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$telepon = $_POST['telepon'];
$usia = $_POST['usia'];
$detail = $_POST['detail'];


$query = "INSERT INTO peserta_open 
(id_akun, nama, no_hp, usia, alamat, riwayat)
VALUES 
($id, '$nama','$telepon','$usia','$alamat','$detail')";

// var_dump($_POST);
// die();


if (mysqli_query($konek, $query)) {
    echo "<script>
            alert('Peserta berhasil disimpan!, Silahkan cek profile dan menu peserta');
            window.location='profiluser.php';
          </script>";
} else {
    echo "Error: " . mysqli_error($konek);
}
?>