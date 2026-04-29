<?php
session_start();
require 'konek.php';
$id = $_SESSION['id_akun'];

$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$telepon = $_POST['telepon'];
$tglLahir = $_POST['tglLahir'];
$detail = $_POST['detail'];


$query = "INSERT INTO peserta 
(id_akun, nama, no_hp, tgl_lahir, alamat, riwayat)
VALUES 
($id, '$nama','$telepon','$tglLahir','$alamat','$detail')";

// var_dump($_POST);
// die();


if (mysqli_query($konek, $query)) {
    echo "<script>
            alert('Peserta berhasil disimpan!, Silahkan cek profile dan menu peserta');
            window.location='open_trip.php';
          </script>";
} else {
    echo "Error: " . mysqli_error($konek);
}
?>