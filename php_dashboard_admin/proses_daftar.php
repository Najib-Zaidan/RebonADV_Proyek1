<?php
session_start();
require 'konek.php';

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
            window.location='home1.php';
          </script>";
} else {
    echo "Error: " . mysqli_error($konek);
}
?>