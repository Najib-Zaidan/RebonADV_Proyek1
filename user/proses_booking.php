<?php
session_start();
require 'konek.php';
require 'fungsi.php';

$id_trip = $_POST['id_trip'];
$id_peserta = $_POST['id_peserta']; // array
$id_akun = $_SESSION['id_akun'];
$tgl_booking = date('Y-m-d H:i:s');
$status = "Belum Bayar";

// VALIDASI
if(empty($id_peserta)){
    echo "<script>alert('Pilih minimal 1 peserta');history.back();</script>";
    exit;
}

// hitung jumlah peserta
$jumlah_peserta = count($id_peserta);

// INSERT booking (1x saja)
kueri("
    INSERT INTO booking (id_trip, id_akun, jumlah_peserta, tgl_booking, status)
    VALUES ('$id_trip', '$id_akun', '$jumlah_peserta', '$tgl_booking', '$status')
");

// ambil id booking terakhir
$id_booking = mysqli_insert_id($konek);

// simpan peserta ke detail
foreach($id_peserta as $p){
    kueri("
        INSERT INTO detail (id_booking, id_peserta)
        VALUES ('$id_booking', '$p')
    ");
}

echo "<script>
alert('Booking berhasil!');
window.location='profiluser.php';
</script>";
?>