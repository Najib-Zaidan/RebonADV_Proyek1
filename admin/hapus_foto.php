<?php
require 'konek.php';

$id = $_GET['id'];
$album = $_GET['album'];

$data = mysqli_fetch_assoc(mysqli_query($konek,"
SELECT * FROM galeri
WHERE id_galeri='$id'
"));

$file = "../gambar/galeri/".$data['nama_file'];

if(file_exists($file)){
    unlink($file);
}

mysqli_query($konek,"
DELETE FROM galeri
WHERE id_galeri='$id'
");

header("Location:detail_album.php?id=$album");
?>