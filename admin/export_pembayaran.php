<?php
require 'konek.php';
require 'fungsi.php';

// HEADER DOWNLOAD
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_Pembayaran.xls");

// AMBIL FILTER
$filter = $_GET['filter'] ?? 'hari_ini';
$sort = $_GET['sort'] ?? '';
$destinasi = $_GET['destinasi'] ?? '';
$status = $_GET['status'] ?? '';

// AMANKAN INPUT (ANTI ERROR / MINIMAL SECURITY)
$destinasi = mysqli_real_escape_string($konek, $destinasi);
$status = mysqli_real_escape_string($konek, $status);

$where = [];

// FILTER TANGGAL
if ($filter == 'hari_ini') {
    $where[] = "DATE(pay.tgl_bayar) = CURDATE()";
} elseif ($filter == 'minggu_ini') {
    $where[] = "YEARWEEK(pay.tgl_bayar, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter == 'bulan_ini') {
    $where[] = "MONTH(pay.tgl_bayar) = MONTH(CURDATE()) AND YEAR(pay.tgl_bayar) = YEAR(CURDATE())";
} elseif ($filter == 'tahun_ini') {
    $where[] = "YEAR(pay.tgl_bayar) = YEAR(CURDATE())";
}

// FILTER TAMBAHAN (Mengarahkan ke tabel TUJUAN)
if ($destinasi != '') $where[] = "tj.tujuan = '$destinasi'";
if ($status != '') $where[] = "pay.status = '$status'";

$kondisi = (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";

// SORTING
switch($sort) {
    case 'NOM_ASC': $order = "ORDER BY pay.nominal ASC"; break;
    case 'NOM_DESC': $order = "ORDER BY pay.nominal DESC"; break;
    case 'PES_ASC': $order = "ORDER BY b.jumlah_peserta ASC"; break;
    case 'PES_DESC': $order = "ORDER BY b.jumlah_peserta DESC"; break;
    default: $order = "ORDER BY pay.id_payment DESC"; break;
}

// QUERY (JOIN ke tabel TUJUAN)
$data = kueri("SELECT 
                pay.*, 
                a.username AS nama_pemesan, 
                tj.tujuan, 
                b.tgl_booking, 
                b.jumlah_peserta 
              FROM payment_open pay
              JOIN booking b ON pay.id_booking = b.id_booking
              JOIN trip t ON b.id_trip = t.id_trip
              JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
              JOIN akun a ON b.id_akun = a.id_akun
              $kondisi
              $order");

// OUTPUT TABLE
echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Trip</th>
        <th>Nama Pemesan</th>
        <th>Jumlah Peserta</th>
        <th>Tanggal Bayar</th>
        <th>Tanggal Booking</th>
        <th>Nominal</th>
        <th>Status</th>
      </tr>";

$no = 1;
while($row = ambil($data)){
    echo "<tr>
            <td>$no</td>
            <td>{$row['tujuan']}</td>
            <td>{$row['nama_pemesan']}</td>
            <td>{$row['jumlah_peserta']} Orang</td>
            <td>".date('d/m/Y H:i', strtotime($row['tgl_bayar']))."</td>
            <td>".date('d/m/Y', strtotime($row['tgl_booking']))."</td>
            <td>".number_format($row['nominal'])."</td>
            <td>{$row['status']}</td>
          </tr>";
    $no++;
}

echo "</table>";
?>
