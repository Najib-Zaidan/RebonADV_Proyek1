<?php
session_start();
require 'konek.php';
require 'fungsi.php';

$id_akun = $_SESSION['id_akun'];

// ambil id booking dari URL
$id_booking = $_GET['id_booking'];

// validasi sederhana
if(empty($id_booking)){
    echo "<script>alert('ID booking tidak ditemukan');window.location='profiluser.php';</script>";
    exit;
}

// ambil info booking + trip
$booking = kueri("
    SELECT 
        b.id_booking,
        b.tgl_booking,
        b.status,
        b.jumlah_peserta,
        t.tujuan,
        t.tgl_berangkat
    FROM booking b
    JOIN trip t ON b.id_trip = t.id_trip
    WHERE b.id_booking = '$id_booking'
    AND b.id_akun = '$id_akun'
");

// ambil peserta
$peserta = kueri("
    SELECT 
        p.nama,
        p.no_hp,
        p.usia,
        p.alamat,
        p.riwayat
    FROM detail bd
    JOIN peserta_open p ON bd.id_peserta = p.id_peserta
    WHERE bd.id_booking = '$id_booking'
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Peserta</title>

    <style>
        body{
            font-family: Arial;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container{
            max-width: 900px;
            margin: auto;
        }

        .card{
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .title{
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info{
            margin-bottom: 5px;
        }

        table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td{
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th{
            background: #6b3df5;
            color: white;
        }

        .back{
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 12px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .back:hover{
            background: #111;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="profiluser.php" class="back">← Kembali</a>

    <?php if($b = mysqli_fetch_assoc($booking)): ?>
    <div class="card">
        <div class="title">Detail Booking</div>

        <div class="info"><b>Tujuan:</b> <?= $b['tujuan']; ?></div>
        <div class="info"><b>Tanggal Berangkat:</b> <?= $b['tgl_berangkat']; ?></div>
        <div class="info"><b>Tanggal Booking:</b> <?= $b['tgl_booking']; ?></div>
        <div class="info"><b>Jumlah Peserta:</b> <?= $b['jumlah_peserta']; ?></div>
        <div class="info"><b>Status:</b> <?= $b['status']; ?></div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="title">Data Peserta</div>

        <table>
            <tr>
                <th>Nama</th>
                <th>No HP</th>
                <th>Usia</th>
                <th>Alamat</th>
                <th>Riwayat</th>
            </tr>

            <?php while($p = ambil($peserta)): ?>
            <tr>
                <td><?= $p['nama']; ?></td>
                <td><?= $p['no_hp']; ?></td>
                <td><?= $p['usia']; ?></td>
                <td><?= $p['alamat']; ?></td>
                <td><?= $p['riwayat']; ?></td>
            </tr>
            <?php endwhile; ?>

        </table>
    </div>

</div>

</body>
</html>