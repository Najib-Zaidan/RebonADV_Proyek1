<?php
require 'fungsi.php';

$id_trip = $_POST['id_trip'];
$id_peserta = $_POST['id_peserta'];
$tgl_booking = date('Y-m-d H:i:s');
$status = "Belum Bayar";

$query = "INSERT INTO booking (id_trip, id_peserta, tgl_booking, status) VALUES ('$id_trip', '$id_peserta', '$tgl_booking', '$status')";

if (kueri($query)) {
    header("Location: home1.php");
} else {
    echo "Error: " . mysqli_error($konek);
}
?>
