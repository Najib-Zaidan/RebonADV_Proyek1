<?php
session_start();
require 'fungsi.php';

$id_akun = $_SESSION['id_akun'];

$query = "SELECT b.id_booking, b.tgl_booking, b.status, p.nama, b.id_trip 
          FROM booking b 
          JOIN peserta p ON b.id_peserta = p.id_peserta 
          WHERE p.id_akun = '$id_akun'";

$result = kueri($query);
?>

<table>
    <thead>
        <tr>
            <th>ID Booking</th>
            <th>Nama Peserta</th>
            <th>Tanggal Booking</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = ambil($result)): ?>
        <tr>
            <td><?php echo $row['id_booking']; ?></td>
            <td><?php echo $row['nama']; ?></td>
            <td><?php echo $row['tgl_booking']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <form action="form_pembayaran.php" method="GET">
                    <input type="hidden" name="id_booking" value="<?php echo $row['id_booking']; ?>">
                    <button type="submit">Bayar</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
