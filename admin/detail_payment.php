<?php
require 'fungsi.php';
$id = $_GET['id'];


if (isset($_POST['verifikasi'])) {
    kueri("UPDATE payment SET status = 'Diverifikasi' WHERE id_payment = $id");
    $data = ambil(kueri("SELECT p.nominal, b.*, t.harga FROM payment p
    JOIN booking b ON p.id_booking = b.id_booking 
    JOIN trip t ON b.id_trip = t.id_trip 
    WHERE p.id_payment = $id"));
    $id_book = $data['id_booking'];
    if($data['nominal'] < $data['harga']){
      kueri("UPDATE booking SET status = 'DP' WHERE id_booking = '$id_book'");
    }
    else if($data['nominal'] >= $data['harga']){
      kueri("UPDATE booking SET status = 'Lunas' WHERE id_booking = '$id_book'");
    }
    header("Location: detail_payment.php?id=" . $id);
    exit;
}

if (isset($_POST['batal_verifikasi'])) {
    kueri("UPDATE payment SET status = 'Belum Diverifikasi' WHERE id_payment = $id");
    $data = ambil(kueri("SELECT SUM(p.nominal) total, p.nominal, b.*, t.harga FROM payment p
    JOIN booking b ON p.id_booking = b.id_booking 
    JOIN trip t ON b.id_trip = t.id_trip 
    WHERE p.id_payment = $id"));
    $id_book = $data['id_booking'];
    $bayar = ($data['total'] - $data['nominal']);
    if($bayar == 0){
      kueri("UPDATE booking SET status = 'Belum Bayar' WHERE id_booking = '$id_book'");
    }
    else if(($bayar < $data['harga']) && ($bayar != 0)){
      kueri("UPDATE booking SET status = 'DP' WHERE id_booking = '$id_book'");
    }
    header("Location: detail_payment.php?id=" . $id);
    exit;
}

$sql = "SELECT 
            pay.*, 
            p.nama AS nama_peserta, 
            t.tujuan AS nama_trip 
        FROM payment pay
        JOIN booking b ON pay.id_booking = b.id_booking
        JOIN peserta p ON b.id_peserta = p.id_peserta
        JOIN trip t ON b.id_trip = t.id_trip
        WHERE pay.id_payment = $id";

$eksekusi = kueri($sql);
$data = ambil($eksekusi);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pembayaran</title>
</head>
<body>
    <h2>Detail Pembayaran</h2>
    <a href="index.php?menu=payment">Kembali ke Daftar</a>
    <table border="1" cellpadding="10" cellspacing="0">
        <hr>
        <tr>
            <th>Nama Peserta</th>
            <td><?php echo $data['nama_peserta']; ?></td>
        </tr>
        <tr>
            <th>Nama Trip</th>
            <td><?php echo $data['nama_trip']; ?></td>
        </tr>
        <tr>
            <th>Tanggal Bayar</th>
            <td><?php echo $data['tgl_bayar']; ?></td>
        </tr>
        <tr>
            <th>Nominal</th>
            <td>Rp <?php echo number_format($data['nominal'], 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <th>Bukti Bayar (Nama File)</th>
            <td><?php echo $data['bukti_bayar']; ?></td>
        </tr>
        <tr>
            <th>Status Saat Ini</th>
            <td><strong><?php echo $data['status']; ?></strong></td>
        </tr>
    </table>

    <br>

    <form action="" method="POST">
        <?php if ($data['status'] !== 'Diverifikasi'): ?>
            <button type="submit" name="verifikasi">Verifikasi Pembayaran</button>
        <?php else: ?>
            <p>Pembayaran sudah diverifikasi.</p>
            <button type="submit" name="batal_verifikasi" onclick="return confirm('Batalkan verifikasi pembayaran ini?')">
                Batalkan Verifikasi
            </button>
        <?php endif; ?>
    </form>

</body>
</html>