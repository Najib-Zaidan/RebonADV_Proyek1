<?php
require 'konek.php';

$id = $_GET['id'];

$fotos = mysqli_query($konek,"
SELECT * FROM galeri
WHERE id_album='$id'
");

while($f = mysqli_fetch_assoc($fotos)){

    $file = "../gambar/galeri/".$f['nama_file'];

    if(file_exists($file)){
        unlink($file);
    }
}

mysqli_query($konek,"
DELETE FROM galeri
WHERE id_album='$id'
");

mysqli_query($konek,"
DELETE FROM album
WHERE id_album='$id'
");

header("Location:index.php?menu=galeri");
?>