<?php
require 'konek.php';

// HEADER EXCEL
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keuangan_Trip.xls");

// FILTER
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : 0;
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : 0;

$nama_bulan = [
    1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",
    5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",
    9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
];

$where = "WHERE 1=1";

if ($bulan > 0 && $tahun > 0) {
    $where .= " AND MONTH(b.tgl_booking) = $bulan AND YEAR(b.tgl_booking) = $tahun";
    $judul = $nama_bulan[$bulan] . " " . $tahun;
} elseif ($tahun > 0) {
    $where .= " AND YEAR(b.tgl_booking) = $tahun";
    $judul = "Tahun $tahun";
} else {
    $judul = "Semua Waktu";
}

// QUERY
$query = mysqli_query($konek, "
    SELECT 
        b.id_booking,
        tj.tujuan,
        a.username,
        t.harga,
        b.tgl_booking,

        COUNT(DISTINCT d.id_peserta) AS jumlah_peserta,
        GROUP_CONCAT(DISTINCT ps.nama SEPARATOR ', ') AS nama_peserta,
        IFNULL(SUM(DISTINCT p.nominal),0) AS total_nominal

    FROM booking b
    JOIN trip t ON b.id_trip = t.id_trip
    JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
    JOIN akun a ON b.id_akun = a.id_akun

    LEFT JOIN detail d ON b.id_booking = d.id_booking
    LEFT JOIN peserta_open ps ON d.id_peserta = ps.id_peserta
    LEFT JOIN payment_open p ON b.id_booking = p.id_booking

    $where
    GROUP BY b.id_booking
    ORDER BY a.username ASC, b.tgl_booking ASC
");

// TOTAL
$total_tagihan_all = 0;
$total_bayar_all = 0;
$total_laba_all = 0;

// OUTPUT EXCEL
echo "<h3>Laporan Keuangan Trip - $judul</h3>";

echo "<table border='1'>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Tujuan</th>
    <th>Username</th>
    <th>Peserta</th>
    <th>Harga / Orang</th>
    <th>Jumlah</th>
    <th>Total Biaya</th>
    <th>Total Bayar</th>
    <th>Laba / Rugi</th>
</tr>";

$no = 1;

while($d = mysqli_fetch_assoc($query)){

    $total_biaya = $d['harga'] * $d['jumlah_peserta'];
    $laba = $d['total_nominal'] - $total_biaya;

    $total_tagihan_all += $total_biaya;
    $total_bayar_all += $d['total_nominal'];
    $total_laba_all += $laba;

    echo "<tr>
        <td>$no</td>
        <td>".date('d-m-Y', strtotime($d['tgl_booking']))."</td>
        <td>{$d['tujuan']}</td>
        <td>{$d['username']}</td>
        <td>".($d['nama_peserta'] ?: '-')."</td>
        <td>{$d['harga']}</td>
        <td>{$d['jumlah_peserta']}</td>
        <td>$total_biaya</td>
        <td>{$d['total_nominal']}</td>
        <td>$laba</td>
    </tr>";

    $no++;
}

// TOTAL
echo "<tr>
    <td colspan='7'><b>TOTAL BIAYA</b></td>
    <td><b>$total_tagihan_all</b></td>
    <td colspan='2'></td>
</tr>";

echo "<tr>
    <td colspan='8'><b>TOTAL PEMBAYARAN</b></td>
    <td><b>$total_bayar_all</b></td>
    <td></td>
</tr>";

echo "<tr>
    <td colspan='9'><b>TOTAL LABA / RUGI</b></td>
    <td><b>$total_laba_all</b></td>
</tr>";

echo "</table>";
?>
