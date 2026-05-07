<?php
require 'fungsi.php';

$id_booking = $_POST['id_private'];
$nominal = $_POST['nominal'];
$catatan = $_POST['catatan'];
$status = "Belum Diverifikasi";


$nama_asli = $_FILES['bukti_bayar']['name'];
$ekstensi = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
$nama_file = time() . "_bb_" . $id_booking . "." . $ekstensi;
$tmp_file = $_FILES['bukti_bayar']['tmp_name'];
$path = "../gambar/payment/" . $nama_file;

if (move_uploaded_file($tmp_file, $path)) {
    $query = "INSERT INTO payment_private (id_private, nominal, bukti_bayar, status, catatan) 
              VALUES ('$id_booking', '$nominal', '$nama_file', '$status', '$catatan')";
    
    if (kueri($query)) {
        header("Location: profiluser.php");
    } else {
        echo "Error: " . mysqli_error($konek);
    }
} else {
    echo "Gagal mengupload gambar.";
}
?>
