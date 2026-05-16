<?php
session_start();
require 'konek.php';
require 'fungsi.php';

$id_akun = $_SESSION['id_akun'];

// Ambil id_payment dari URL
$id_payment = $_GET['id_payment'];

// Validasi sederhana
if(empty($id_payment)){
    echo "<script>alert('ID pembayaran tidak ditemukan');window.location='profiluser.php';</script>";
    exit;
}

// Ambil detail data payment serta gabungkan dengan info private trip terkait untuk verifikasi kepemilikan akun
$payment = kueri("
    SELECT 
        p.id_payment,
        p.id_private,
        p.tgl_bayar,
        p.nominal,
        p.bukti_bayar,
        p.status AS status_bayar,
        p.catatan AS catatan_user, -- Diambil dari inputan user saat kirim bayar (kolom catatan di payment_private)
        pt.nama AS nama_pemesan,
        pt.tujuan,
        pt.tgl_berangkat
    FROM payment_private p
    JOIN private_trip pt ON p.id_private = pt.id_private
    WHERE p.id_payment = '$id_payment'
    AND pt.id_akun = '$id_akun'
");

$p = ambil($payment);

// Jika data tidak ditemukan atau bukan milik akun yang sedang login
if(!$p){
    echo "<script>alert('Data pembayaran tidak ditemukan atau Anda tidak memiliki akses');window.location='profiluser.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pembayaran Private Trip</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body{
            font-family: 'Segoe UI', Arial, sans-serif;
            /* Tema Oren Asik: Latar belakang gradasi oren soft hangat */
            background: linear-gradient(135deg, #fff5ec 0%, #ffe3cc 100%);
            background-attachment: fixed;
            margin: 0;
            padding: 15px;
            color: #5c3a21;
        }

        .container{
            max-width: 700px;
            margin: auto;
        }

        .card{
            background: white;
            padding: 25px;
            border-radius: 14px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(230, 126, 34, 0.08);
            border: 1px solid rgba(230, 126, 34, 0.1);
        }

        /* Card Utama bertema Oren Asik untuk Status Pembayaran */
        .card-main {
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            color: white;
            border: none;
            box-shadow: 0 8px 25px rgba(211, 84, 0, 0.25);
            position: relative;
            overflow: hidden;
            text-align: center;
            padding: 30px 20px;
        }

        .card-main::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            pointer-events: none;
        }

        .main-nominal {
            font-size: 32px;
            font-weight: 800;
            margin: 10px 0;
            letter-spacing: 0.5px;
        }

        .main-status {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 5px;
        }

        /* Warna label status dinamis di dalam card utama */
        .status-diverifikasi { background: #2ecc71; color: white; }
        .status-ditolak { background: #e74c3c; color: white; }
        .status-proses { background: rgba(255, 255, 255, 0.25); color: white; }

        .title{
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #d35400;
            border-bottom: 2px solid #ffe3cc;
            padding-bottom: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px 20px;
        }

        .info{
            font-size: 15px;
            line-height: 1.5;
        }

        .info-full {
            grid-column: span 2;
        }

        .label-text {
            font-size: 13px;
            color: #a8846c;
            margin-bottom: 2px;
            font-weight: 500;
        }

        .value-text {
            font-weight: 600;
            color: #422917;
        }

        /* Frame khusus pratinjau bukti transfer */
        .bukti-container {
            margin-top: 10px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px dashed #ffe3cc;
            background: #fffdfb;
            text-align: center;
            padding: 10px;
        }

        .bukti-img {
            max-width: 100%;
            height: auto;
            max-height: 450px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .back{
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 18px;
            background: #5c3a21;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(92, 58, 33, 0.15);
        }

        .back:hover{
            background: #422917;
            transform: translateY(-1px);
        }

        /* Tombol Cetak PDF Berwarna Biru Informasi agar Kontras dan Menarik */
        .btn-print {
            display: block;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            padding: 12px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.2);
            margin-bottom: 20px;
        }

        .btn-print:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
        }

        /* Media Queries Responsif Layar Handphone */
        @media (max-width: 576px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .info-full {
                grid-column: span 1;
            }
            .main-nominal {
                font-size: 26px;
            }
            .card {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Tombol kembali umum ke menu utama -->
    <a href="profiluser.php" class="back">← Kembali</a>

    <!-- Card 1: Rangkuman Pembayaran -->
    <div class="card card-main">
        <div style="opacity: 0.85; font-size: 14px; font-weight: 500;">Nominal Transaksi Private Trip</div>
        <div class="main-nominal">Rp <?= number_format($p['nominal'], 0, ',', '.'); ?></div>
        
        <div class="main-status 
            <?php 
                if($p['status_bayar'] == 'Diverifikasi') echo 'status-diverifikasi';
                elseif($p['status_bayar'] == 'Ditolak') echo 'status-ditolak';
                else echo 'status-proses';
            ?>">
            <?= $p['status_bayar']; ?>
        </div>
    </div>

    <!-- Tombol Cetak Dokumen (File terpisah cetak_pembayaran_private.php) -->
    <a href="cetak_bayar_private.php?id_payment=<?= $p['id_payment']; ?>" target="_blank" class="btn-print">
        ️ Cetak Bukti Pembayaran Private Trip (PDF)
    </a>

    <!-- Card 2: Detail Rincian Informasi -->
    <div class="card">
        <div class="title">Informasi Transaksi</div>
        
        <div class="info-grid">
            <div class="info">
                <div class="label-text">ID Pembayaran</div>
                <div class="value-text">#PAY-PT-<?= $p['id_payment']; ?></div>
            </div>
            <div class="info">
                <div class="label-text">Tanggal Bayar</div>
                <div class="value-text"><?= $p['tgl_bayar']; ?></div>
            </div>
            <div class="info">
                <div class="label-text">Nama Pemesan / Rombongan</div>
                <div class="value-text"><?= $p['nama_pemesan']; ?></div>
            </div>
            <div class="info">
                <div class="label-text">Tujuan Wisata Private</div>
                <div class="value-text"><?= $p['tujuan']; ?></div>
            </div>
            <div class="info">
                <div class="label-text">Tanggal Keberangkatan</div>
                <div class="value-text"><?= $p['tgl_berangkat']; ?></div>
            </div>
            <div class="info-full">
                <div class="label-text">Catatan Pembayaran dari Anda</div>
                <div class="value-text" style="font-weight: normal; color: #5c3a21; background: #fffcf9; padding: 10px; border-radius: 6px; border-left: 3px solid #e67e22;">
                    <?= !empty($p['catatan_user']) ? $p['catatan_user'] : 'Tidak ada catatan yang dilampirkan.'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Pratinjau Lampiran Bukti Transfer -->
    <div class="card">
        <div class="title">Lampiran Bukti Bayar</div>
        
        <div class="bukti-container">
            <!-- Menampilkan gambar bukti transfer yang diunggah user -->
            <img src="img/bukti/<?= $p['bukti_bayar']; ?>" alt="Bukti Pembayaran" class="bukti-img" onerror="this.onerror=null; this.src='img/no-image.png';">
        </div>
    </div>

</div>

</body>
</html>
