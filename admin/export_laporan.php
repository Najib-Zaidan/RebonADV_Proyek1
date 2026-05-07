<?php
require 'konek.php';

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
} elseif ($tahun > 0) {
    $where .= " AND YEAR(b.tgl_booking) = $tahun";
}

header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keuangan_Trip.xls");

$query = mysqli_query($konek, "
    SELECT 
        b.id_booking,
        b.tgl_booking,
        t.tujuan,
        a.username,
        t.harga,
        COUNT(DISTINCT d.id_peserta) AS jumlah_peserta,
        GROUP_CONCAT(DISTINCT ps.nama SEPARATOR ', ') AS nama_peserta,
        IFNULL(SUM(DISTINCT CASE WHEN p.status = 'Diverifikasi' THEN p.nominal END), 0) AS total_nominal
    FROM booking b
    JOIN trip t ON b.id_trip = t.id_trip
    JOIN akun a ON b.id_akun = a.id_akun
    LEFT JOIN detail d ON b.id_booking = d.id_booking
    LEFT JOIN peserta_open ps ON d.id_peserta = ps.id_peserta
    LEFT JOIN payment_open p ON b.id_booking = p.id_booking
    $where
    GROUP BY b.id_booking
    ORDER BY a.username ASC, b.tgl_booking ASC
");

$total_biaya_all = 0;
$total_bayar_all = 0;
$total_laba_all  = 0;

$current_user = "";

echo "
<h2 style='text-align:center;'>LAPORAN KEUANGAN TRIP</h2>
<br>

<table border='1'>
<tr style='background:#6b3df5; color:white;'>
    <th>No</th>
    <th>Tanggal</th>
    <th>Tujuan</th>
    <th>Username</th>
    <th>Peserta</th>
    <th>Biaya / Orang</th>
    <th>Jumlah</th>
    <th>Total Biaya</th>
    <th>Total Bayar</th>
    <th>Laba / Rugi</th>
</tr>
";

$no = 1;

while ($d = mysqli_fetch_assoc($query)) {

    // GROUP USER (biar sama seperti web)
    if ($current_user != $d['username']) {
        echo "
        <tr style='background:#ddd; font-weight:bold;'>
            <td colspan='10'>USER: {$d['username']}</td>
        </tr>
        ";
        $current_user = $d['username'];
    }

    $total_biaya = $d['harga'] * $d['jumlah_peserta'];
    $laba = $d['total_nominal'] - $total_biaya;

    $total_biaya_all += $total_biaya;
    $total_bayar_all += $d['total_nominal'];
    $total_laba_all  += $laba;

    echo "
    <tr>
        <td>{$no}</td>
        <td>".date('d-m-Y', strtotime($d['tgl_booking']))."</td>
        <td>{$d['tujuan']}</td>
        <td>{$d['username']}</td>
        <td>{$d['nama_peserta']}</td>
        <td>{$d['harga']}</td>
        <td>{$d['jumlah_peserta']}</td>
        <td>{$total_biaya}</td>
        <td>{$d['total_nominal']}</td>
        <td>{$laba}</td>
    </tr>
    ";

    $no++;
}

// TOTAL ROW (WAJIB LENGKAP)
echo "
<tr style='background:#d1d5ff; font-weight:bold;'>
    <td colspan='7'>TOTAL BIAYA</td>
    <td>{$total_biaya_all}</td>
    <td></td>
    <td></td>
</tr>

<tr style='background:#d1d5ff; font-weight:bold;'>
    <td colspan='8'>TOTAL PEMBAYARAN</td>
    <td>{$total_bayar_all}</td>
    <td></td>
</tr>

<tr style='background:#d1d5ff; font-weight:bold;'>
    <td colspan='9'>TOTAL LABA / RUGI</td>
    <td>{$total_laba_all}</td>
</tr>
";

echo "</table>";
?>