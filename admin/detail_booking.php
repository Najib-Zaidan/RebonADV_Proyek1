<?php
include 'fungsi.php';

$id_booking = $_GET['id'];

if (isset($_POST['update_status'])) {
    $status_baru = $_POST['status_manual'];
    kueri("UPDATE booking SET status = '$status_baru' WHERE id_booking = $id_booking");
}

$query = "SELECT b.*, t.tujuan, t.harga, p.nama
          FROM booking b
          JOIN trip t ON b.id_trip = t.id_trip
          JOIN peserta p ON b.id_peserta = p.id_peserta 
          WHERE b.id_booking = $id_booking";
$data = ambil(kueri($query));
$payment = kueri("SELECT * FROM payment WHERE id_booking = $id_booking");

$diverifikasi = "SELECT SUM(nominal) total FROM payment 
WHERE id_booking = $id_booking AND status = 'Diverifikasi'";

$total_bayar = ambil(kueri($diverifikasi));

if(empty($total_bayar['total'])){
  $total_bayar['total'] = 0;
}

if (!isset($_POST['update_status'])) {
    $status_otomatis = "Belum Bayar";
    if ($total_bayar['total'] >= $data['harga']) {
        $status_otomatis = "Lunas";
    } elseif ($total_bayar['total'] > 0) {
        $status_otomatis = "DP";
    }
    /*
    if ($data['status'] != $status_otomatis) {
        kueri("UPDATE booking SET status = '$status_otomatis' WHERE id_booking = $id_booking");
        $data['status'] = $status_otomatis;
    } */
}

$persentase = ($total_bayar['total'] / $data['harga']) * 100;
?>

<h1>Detail Booking</h1>

<a href="index.php?menu=booking">Kembali ke Menu</a>

<table>
    <tr>
        <td>Tujuan Trip</td>
        <td>: <?php echo $data['tujuan']; ?></td>
    </tr>
    <tr>
        <td>Nama Peserta</td>
        <td>: <?php echo $data['nama']; ?></td>
    </tr>
    <tr>
        <td>Tanggal Booking</td>
        <td>: <?php echo $data['tgl_booking']; ?></td>
    </tr>
    <tr>
        <td>Harga Trip</td>
        <td>: <?php echo number_format($data['harga']); ?></td>
    </tr>
</table>

<h3>Progress Pembayaran</h3>
<p>Total Terverifikasi: <?php echo number_format($total_bayar['total']); ?> / <?php echo number_format($data['harga']); ?> (<?php echo round($persentase, 2); ?>%)</p>

<h3>Status Pesanan</h3>
<p>Status Saat Ini: <strong><?php echo $data['status']; ?></strong></p>
<!-- <form method="post">
    <select name="status_manual">
        <option value="Belum Bayar" <?php if($data['status'] == 'Belum Bayar') echo 'selected'; ?>>Belum Bayar</option>
        <option value="DP" <?php if($data['status'] == 'DP') echo 'selected'; ?>>DP</option>
        <option value="Lunas" <?php if($data['status'] == 'Lunas') echo 'selected'; ?>>Lunas</option>
        <option value="Dibatalkan" <?php if($data['status'] == 'Batal') echo 'selected'; ?>>Batal</option>
    </select>
    <button type="submit" name="update_status">Update Status Manual</button>
</form> -->

<h3>Riwayat Pembayaran</h3>
<table border="1">
    <thead>
        <tr>
            <th>Tanggal Bayar</th>
            <th>Nominal</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while($p = ambil($payment)) : ?>
        <tr>
            <td><?php echo $p['tgl_bayar']; ?></td>
            <td><?php echo number_format($p['nominal']); ?></td>
            <td><?php echo $p['status']; ?></td>
            <td><a href="detail_payment.php?id=<?php echo $p['id_payment']; ?>">Detail</a></td>

        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
