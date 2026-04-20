<?php
session_start();
require 'fungsi.php';

$id_akun = $_SESSION['id_akun'];

$query = "SELECT py.id_payment, py.tgl_bayar, py.nominal, py.bukti_bayar, py.status, ps.nama 
          FROM payment py
          JOIN booking bk ON py.id_booking = bk.id_booking
          JOIN peserta ps ON bk.id_peserta = ps.id_peserta
          WHERE ps.id_akun = '$id_akun'";

$result = kueri($query);
?>

<table>
    <thead>
        <tr>
            <th>ID Pembayaran</th>
            <th>Nama Peserta</th>
            <th>Tanggal Bayar</th>
            <th>Nominal</th>
            <th>Bukti</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = ambil($result)): ?>
        <tr>
            <td><?php echo $row['id_payment']; ?></td>
            <td><?php echo $row['nama']; ?></td>
            <td><?php echo $row['tgl_bayar']; ?></td>
            <td><?php echo number_format($row['nominal'], 0, ',', '.'); ?></td>
            <td><a href="../gambar/payment/<?php echo $row['bukti_bayar']; ?>" target="_blank">Lihat Bukti</a></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
