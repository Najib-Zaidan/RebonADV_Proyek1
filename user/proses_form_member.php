<?php
session_start();
require 'konek.php';

$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$tglLahir = $_POST['tglLahir'];
$detail = $_POST['detail'];


$query = "INSERT INTO member 
(nama, tgl_lahir, alamat, riwayat)
VALUES 
('$nama', '$tglLahir','$alamat','$detail')";

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