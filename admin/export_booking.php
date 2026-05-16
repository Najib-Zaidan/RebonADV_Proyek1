<?php
require 'konek.php';
require 'fungsi.php';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_Booking.xls");

$filter = $_GET['filter'] ?? 'hari_ini';
$sort = $_GET['sort'] ?? '';
$destinasi = $_GET['destinasi'] ?? '';
$status = $_GET['status'] ?? '';

$where = [];

if ($filter == 'hari_ini') {
    $where[] = "DATE(b.tgl_booking) = CURDATE()";
} elseif ($filter == 'minggu_ini') {
    $where[] = "YEARWEEK(b.tgl_booking, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter == 'bulan_ini') {
    $where[] = "MONTH(b.tgl_booking) = MONTH(CURDATE()) AND YEAR(b.tgl_booking) = YEAR(CURDATE())";
} elseif ($filter == 'tahun_ini') {
    $where[] = "YEAR(b.tgl_booking) = YEAR(CURDATE())";
}

if ($destinasi != '') $where[] = "tj.tujuan = '$destinasi'";
if ($status != '') $where[] = "b.status = '$status'";

$kondisi = (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";
$order = ($sort != '') ? "ORDER BY total_bayar $sort" : "";

$data = kueri("SELECT b.*, tj.tujuan, t.harga, t.tgl_berangkat, a.username AS nama,
                (SELECT SUM(nominal) FROM payment_open 
                 WHERE id_booking = b.id_booking AND status = 'Diverifikasi') AS total_bayar
                FROM booking b
                JOIN trip t ON b.id_trip = t.id_trip
                JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                JOIN akun a ON b.id_akun = a.id_akun
                $kondisi
                $order");

echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Trip</th>
        <th>Pemesan</th>
        <th>Pax</th>
        <th>Tgl Booking</th>
        <th>Tgl Berangkat</th>
        <th>Pembayaran</th>
        <th>Status</th>
      </tr>";

$no = 1;
while($row = ambil($data)){
    $bayar = $row['total_bayar'] ?? 0;
    $total_tagihan = $row['harga'] * $row['jumlah_peserta'];

    echo "<tr>
            <td>$no</td>
            <td>{$row['tujuan']}</td>
            <td>{$row['nama']}</td>
            <td>{$row['jumlah_peserta']}</td>
            <td>{$row['tgl_booking']}</td>
            <td>{$row['tgl_berangkat']}</td>
            <td>$bayar / $total_tagihan</td>
            <td>{$row['status']}</td>
          </tr>";
    $no++;
}

echo "</table>";
?>
