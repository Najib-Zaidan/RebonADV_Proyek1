<?php
require 'konek.php';

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
    $judul_filter = $nama_bulan[$bulan] . " " . $tahun;
} elseif ($tahun > 0) {
    $where .= " AND YEAR(b.tgl_booking) = $tahun";
    $judul_filter = "Tahun $tahun";
} else {
    $judul_filter = "Semua Waktu";
}

// QUERY
$query = mysqli_query($konek, "
    SELECT 
    b.id_booking,
    t.tujuan,
    a.username,
    t.harga,
    b.tgl_booking,
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
$total_laba_all = 0;

$current_user = "";
?>

<!DOCTYPE html>
<html>
<head>
<title>Laporan Keuangan Trip</title>

<style>
body { font-family: Arial; background:#eef1f7; padding:30px; }

.card {
    max-width:1300px;
    margin:auto;
    background:white;
    padding:25px;
    border-radius:18px;
}

h2 { text-align:center; }

.subtitle {
    text-align:center;
    margin-bottom:20px;
    font-weight:bold;
}

.filter-box {
    display:flex;
    justify-content:center;
    gap:10px;
    margin-bottom:15px;
}

select, button {
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
}

.btn-print {
    background: linear-gradient(135deg,#6b3df5,#8b5cf6);
    color:white;
    border:none;
    padding:10px 18px;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
}

table {
    width:100%;
    border-collapse:collapse;
}

th {
    background:#6b3df5;
    color:white;
    padding:10px;
}

td {
    padding:10px;
    text-align:center;
}

tr:nth-child(even){ background:#f9f9f9; }

.user-group {
    background:#ddd;
    font-weight:bold;
    text-align:left;
}

.total {
    background:#d1d5ff;
    font-weight:bold;
}

/* PRINT */
@media print {
    body { background:white; padding:0; }

    .filter-box,
    .btn-print,
    .btn-export,
    .aksi-print,
    .btn-kembali {
        display:none;
    }

    .card {
        box-shadow:none;
        padding:0;
    }

    table, th, td {
        border:1px solid black;
    }

    th {
        background:none !important;
        color:black !important;
    }
}
</style>
</head>

<body>

<div class="card">

<h2>Laporan Keuangan Trip</h2>
<div class="subtitle">Periode: <?= $judul_filter; ?></div>

<!-- FILTER -->
<form method="GET" class="filter-box">
    <select name="bulan">
        <option value="0">Semua Bulan</option>
        <?php foreach($nama_bulan as $i => $nama): ?>
            <option value="<?= $i ?>" <?= ($bulan == $i ? 'selected' : '') ?>>
                <?= $nama ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="tahun">
        <option value="0">Semua Tahun</option>
        <?php for($t=date('Y'); $t>=2020; $t--): ?>
            <option value="<?= $t ?>" <?= ($tahun == $t ? 'selected' : '') ?>>
                <?= $t ?>
            </option>
        <?php endfor; ?>
    </select>

    <button type="submit">Filter</button>
</form>

<!-- PRINT -->
<div class="aksi-print" style="text-align:center; margin-bottom:15px;">
    <button onclick="window.print()" class="btn-print">
        Cetak Laporan
    </button>

    <a class="btn-export" href="export_laporan.php"
       style="display:inline-block; margin-top:10px; padding:10px 15px; background:green; color:white; border-radius:8px; text-decoration:none;">
       Export Excel
    </a>
</div>

<a class="btn-kembali" href="index.php?menu=laporan">Kembali</a>

<table>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Tujuan</th>
    <th>Username</th>
    <th>Peserta</th>
    <th>Biaya / Orang</th>
    <th>Jumlah</th>
    <th>Total Bayar</th>
    <th>Sudah Dibayar</th>
    <th>Tagihan</th>
</tr>

<?php $no=1; ?>
<?php while($d = mysqli_fetch_assoc($query)): ?>

<?php
if ($current_user != $d['username']) {
    echo "<tr class='user-group'><td colspan='10'>User: {$d['username']}</td></tr>";
    $current_user = $d['username'];
}

$total_biaya = $d['harga'] * $d['jumlah_peserta'];
$laba = $d['total_nominal'] - $total_biaya;

$total_biaya_all += $total_biaya;
$total_bayar_all += $d['total_nominal'];
$total_laba_all += $laba;
?>

<tr>
    <td><?= $no++; ?></td>
    <td><?= date('d-m-Y', strtotime($d['tgl_booking'])); ?></td>
    <td><?= $d['tujuan']; ?></td>
    <td><?= $d['username']; ?></td>
    <td><?= $d['nama_peserta'] ?: '-'; ?></td>
    <td>Rp <?= number_format($d['harga']); ?></td>
    <td><?= $d['jumlah_peserta']; ?></td>
    <td>Rp <?= number_format($total_biaya); ?></td>
    <td>Rp <?= number_format($d['total_nominal']); ?></td>
    <td>
        <?php
        if ($laba >= 0) {
            echo "<span style='color:green;'>Rp ".number_format($laba)."</span>";
        } else {
            echo "<span style='color:red;'>Rp ".number_format($laba)."</span>";
        }
        ?>
    </td>
</tr>

<?php endwhile; ?>

<tr class="total">
    <td colspan="7">TOTAL BIAYA</td>
    <td>Rp <?= number_format($total_biaya_all); ?></td>
    <td colspan="2"></td>
</tr>

<tr class="total">
    <td colspan="8">TOTAL PEMBAYARAN</td>
    <td>Rp <?= number_format($total_bayar_all); ?></td>
    <td></td>
</tr>

<tr class="total">
    <td colspan="9">TOTAL LABA / RUGI</td>
    <td>
        <?php
        if ($total_laba_all >= 0) {
            echo "<span style='color:green;'>Rp ".number_format($total_laba_all)."</span>";
        } else {
            echo "<span style='color:red;'>Rp ".number_format($total_laba_all)."</span>";
        }
        ?>
    </td>
</tr>

</table>

</div>

</body>
</html>