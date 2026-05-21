<?php
require 'fungsi.php';

// cek id trip
if (!isset($_GET['id']) || empty($_GET['id'])) {

    echo "<script>
            alert('ID Trip tidak ditemukan!');
            window.location.href='index.php';
          </script>";

    exit;
}

$id_trip = mysqli_real_escape_string(
    $konek,
    $_GET['id']
);

// ambil data trip
$query_trip = kueri("
SELECT 
    t.*,
    tj.tujuan,
    tj.kota,
    tj.provinsi
FROM trip t
JOIN tujuan tj 
ON t.id_tujuan = tj.id_tujuan
WHERE t.id_trip = '$id_trip'
");

if (mysqli_num_rows($query_trip) == 0) {

    echo "<script>
            alert('Data trip tidak ditemukan!');
            window.location.href='index.php';
          </script>";

    exit;
}

$trip = ambil($query_trip);

// data peserta
$peserta = kueri("
SELECT 
    po.*,
    b.tgl_booking,
    b.status AS status_bayar
FROM booking b

JOIN detail d
ON b.id_booking = d.id_booking

JOIN peserta_open po
ON d.id_peserta = po.id_peserta

WHERE b.id_trip = '$id_trip'

ORDER BY po.nama ASC
");

// hitung total peserta
$total_peserta = mysqli_num_rows($peserta);

// hitung total pemasukan
$total_lunas = 0;

$data_pembayaran = kueri("
SELECT 
    SUM(nominal) AS total
FROM payment_open

WHERE id_booking IN (

    SELECT id_booking 
    FROM booking 
    WHERE id_trip = '$id_trip'

)

AND status = 'Diverifikasi'
");

$bayar = ambil($data_pembayaran);

if ($bayar['total'] != null) {

    $total_lunas = $bayar['total'];

}
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <title>
        Laporan Per Trip
    </title>

    <style>

        body{
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #333;
        }

        h1{
            text-align: center;
            margin-bottom: 5px;
        }

        .sub{
            text-align: center;
            margin-bottom: 30px;
            color: #666;
        }

        .info-box{
            margin-bottom: 25px;
        }

        .info-box table{
            width: 100%;
        }

        .info-box td{
            padding: 6px 0;
            vertical-align: top;
        }

        .table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td{
            border: 1px solid #ccc;
            padding: 10px;
            font-size: 14px;
        }

        .table th{
            background: #f3f3f3;
        }

        .text-center{
            text-align: center;
        }

        .status{
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }

        .lunas{
            background: #d4edda;
            color: #155724;
        }

        .dp{
            background: #fff3cd;
            color: #856404;
        }

        .belum{
            background: #f8d7da;
            color: #721c24;
        }

        .footer{
            margin-top: 30px;
        }

        .btn-print{
            display: inline-block;
            padding: 10px 20px;
            background: #6f42c1;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .btn-kembali{
            display: inline-block;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 20px;
            margin-left: 10px;
        }

        .btn-kembali:hover{
            background: #5a6268;
        }

        @media print{

            .btn-print,
            .btn-kembali{
                display: none;
            }

            body{
                margin: 0;
            }

        }

    </style>

</head>
<body>

<!-- tombol -->

<a href="#"
   onclick="window.print()"
   class="btn-print">

    Cetak Laporan

</a>

<a href="detail_trip.php?id=<?= $id_trip ?>"
   class="btn-kembali">

    Kembali ke Detail Trip

</a>

<!-- header -->

<h1>
    LAPORAN PER TRIP
</h1>

<p class="sub">
    Rebon Adventure
</p>

<!-- info trip -->

<div class="info-box">

    <table>

        <tr>

            <td width="220">
                <strong>Nama Trip</strong>
            </td>

            <td>
                : <?= htmlspecialchars($trip['tujuan']) ?>
            </td>

        </tr>

        <tr>

            <td>
                <strong>Lokasi</strong>
            </td>

            <td>
                : <?= htmlspecialchars($trip['kota']) ?>,
                <?= htmlspecialchars($trip['provinsi']) ?>
            </td>

        </tr>

        <tr>

            <td>
                <strong>Tanggal Berangkat</strong>
            </td>

            <td>
                : <?= date('d F Y', strtotime($trip['tgl_berangkat'])) ?>
            </td>

        </tr>

        <tr>

            <td>
                <strong>Tanggal Pulang</strong>
            </td>

            <td>
                : <?= date('d F Y', strtotime($trip['tgl_pulang'])) ?>
            </td>

        </tr>

        <tr>

            <td>
                <strong>Harga Trip</strong>
            </td>

            <td>
                : Rp <?= number_format($trip['harga'],0,',','.') ?>
            </td>

        </tr>

        <tr>

            <td>
                <strong>Total Peserta</strong>
            </td>

            <td>
                : <?= $total_peserta ?> Orang
            </td>

        </tr>

        <tr>

            <td>
                <strong>Total Pemasukan</strong>
            </td>

            <td>
                : Rp <?= number_format($total_lunas,0,',','.') ?>
            </td>

        </tr>

        <tr>

            <td>
                <strong>Rute Pendakian</strong>
            </td>

            <td>
                : <?= htmlspecialchars($trip['rute']) ?>
            </td>

        </tr>

    </table>

</div>

<!-- data peserta -->

<h3>
    Data Peserta
</h3>

<table class="table">

    <thead>

        <tr>

            <th width="50">
                No
            </th>

            <th>
                Nama
            </th>

            <th width="70">
                Usia
            </th>

            <th>
                No HP
            </th>

            <th>
                Alamat
            </th>

            <th>
                Riwayat
            </th>

            <th width="130">
                Status Bayar
            </th>

        </tr>

    </thead>

    <tbody>

    <?php if(mysqli_num_rows($peserta) > 0): ?>

        <?php $no = 1; ?>

        <?php while($p = ambil($peserta)): ?>

            <?php

                $status = $p['status_bayar'];

                if($status == 'Lunas'){

                    $class = 'lunas';

                }elseif($status == 'DP'){

                    $class = 'dp';

                }else{

                    $class = 'belum';

                }

            ?>

            <tr>

                <td class="text-center">
                    <?= $no++ ?>
                </td>

                <td>
                    <?= htmlspecialchars($p['nama']) ?>
                </td>

                <td class="text-center">
                    <?= htmlspecialchars($p['usia']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($p['no_hp']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($p['alamat']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($p['riwayat']) ?>
                </td>

                <td class="text-center">

                    <span class="status <?= $class ?>">

                        <?= htmlspecialchars($status) ?>

                    </span>

                </td>

            </tr>

        <?php endwhile; ?>

    <?php else: ?>

        <tr>

            <td colspan="7" class="text-center">

                Belum ada peserta

            </td>

        </tr>

    <?php endif; ?>

    </tbody>

</table>

<!-- footer -->

<div class="footer">

    <br><br>

    <table width="100%">

        <tr>

            <td width="70%"></td>

            <td align="center">

                <p>
                    <?= date('d F Y') ?>
                </p>

                <br><br><br>

                <p>
                    _____________________
                </p>

                <p>
                    Admin Rebon Adventure
                </p>

            </td>

        </tr>

    </table>

</div>

</body>
</html>