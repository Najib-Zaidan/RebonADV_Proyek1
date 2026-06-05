<?php
session_start();
require 'konek.php';
require 'fungsi.php';

$id_akun = $_SESSION['id_akun'];

// ambil id private trip dari URL
$id_private = $_GET['id'];

// validasi sederhana
if(empty($id_private)){
    echo "<script>alert('ID private trip tidak ditemukan');window.location='profiluser.php';</script>";
    exit;
}

// Ambil info private trip berdasarkan id_private dan id_akun
$booking = kueri("
    SELECT 
        id_private,
        nama,
        no_hp,
        tujuan,
        tgl_berangkat,
        tgl_pulang,
        tgl_booking,
        catatan,
        jumlah_peserta,
        harga,
        harga_dp,
        status_trip,
        status_bayar
    FROM private_trip
    WHERE id_private = '$id_private'
    AND id_akun = '$id_akun'
");

// Cek apakah booking ini sudah ada di tabel pembatalan (batal_private)
$cek_batal = kueri("SELECT status FROM batal_private WHERE id_private = '$id_private'");
$data_batal = ambil($cek_batal);
$is_mengajukan_batal = mysqli_num_rows($cek_batal) > 0;

// Cek apakah booking ini sedang mengajukan perubahan di tabel ubah_private (status FALSE/0)
$cek_ubah = kueri("SELECT status FROM ubah_private WHERE id_private = '$id_private' AND status = FALSE");
$is_mengajukan_ubah = mysqli_num_rows($cek_ubah) > 0;

// Ambil data peserta private trip
$peserta = kueri("
    SELECT 
        id_peserta,
        nama,
        usia,
        alamat,
        riwayat
    FROM peserta_private
    WHERE id_private = '$id_private'
");

// Ambil data riwayat pembayaran dari tabel payment_private
$pembayaran = kueri("
    SELECT 
        id_payment,
        tgl_bayar,
        nominal,
        bukti_bayar,
        status,
        catatan
    FROM payment_private
    WHERE id_private = '$id_private'
    ORDER BY tgl_bayar DESC
");

// Hitung total pembayaran yang sudah DI-VERIFIKASI untuk private trip ini
$hitung_bayar = kueri("
    SELECT SUM(nominal) AS total_terbayar 
    FROM payment_private 
    WHERE id_private = '$id_private' 
    AND status = 'Diverifikasi'
");
$data_bayar = ambil($hitung_bayar);
$total_terbayar = isset($data_bayar['total_terbayar']) ? $data_bayar['total_terbayar'] : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Private Trip</title>
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
            max-width: 900px;
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

        /* Styling Card Utama (Detail Booking) bertema Oren-Oren Asik */
        .card-main {
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            color: white;
            border: none;
            box-shadow: 0 8px 25px rgba(211, 84, 0, 0.25);
            position: relative;
            overflow: hidden;
        }

        .card-main::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            pointer-events: none;
        }

        .title{
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card .title {
            color: #d35400;
            border-bottom: 2px solid #ffe3cc;
            padding-bottom: 8px;
        }

        .card-main .title {
            color: white;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px 20px;
            margin-bottom: 20px;
        }

        .info{
            margin-bottom: 0;
            font-size: 15px;
            line-height: 1.5;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px;
            border: 1px solid #ffe3cc;
            margin-top: 15px;
        }

        table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            min-width: 600px;
        }

        table th, table td{
            border: none;
            border-bottom: 1px solid #fff5ec;
            padding: 12px 14px;
            text-align: left;
            font-size: 14px;
        }

        table th{
            background: #e67e22;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:hover td {
            background-color: #fffdfb;
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

        /* Tombol Aksi */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .btn-pay { 
            background: #2ecc71; 
            box-shadow: 0 4px 12px rgba(46, 204, 113, 0.2);
        }
        /* Tombol tambahan: Ajukan Perubahan */
        .btn-edit-trip {
            background: #3498db;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
        }
        .btn-cancel-all { 
            background: rgba(255, 255, 255, 0.15); 
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }
        /* Aksi Ubah Peserta */
        .btn-edit-person { 
            background: #ffe3cc; 
            color: #d35400; 
            font-size: 12px; 
            font-weight: bold; 
        }
        .btn-detail { 
            background: #ffe3cc; 
            color: #e67e22; 
            font-size: 12px; 
            font-weight: bold;
        }
        
        .btn-pay:hover { 
            background: #27ae60; 
            transform: translateY(-2px);
        }
        .btn-edit-trip:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        .btn-cancel-all:hover { 
            background: #e74c3c; 
            border-color: #e74c3c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.2);
        }
        .btn-edit-person:hover { 
            background: #ffd1b3; 
            transform: translateY(-1px);
        }
        .btn-detail:hover { 
            background: #e67e22; 
            color: white;
            transform: translateY(-1px);
        }
        
        .action-group { 
            margin-top: 25px; 
            padding-top: 20px; 
            border-top: 1px solid rgba(255, 255, 255, 0.15); 
            display: flex; 
            flex-wrap: wrap;
            gap: 12px; 
        }

        /* Label Khusus Info Tulisan Status (Misal: Pesanan Sudah Dibatalkan) */
        .status-label {
            font-size: 14px;
            color: #ffffff;
            font-style: normal;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            background: rgba(0, 0, 0, 0.25);
            padding: 10px 20px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        .card .status-label {
            color: #777;
        }

        /* Progress Bar Pembayaran bertema Hijau Neon Kontras */
        .progress-wrapper {
            grid-column: span 2;
            background: rgba(0, 0, 0, 0.12);
            padding: 15px;
            border-radius: 10px;
            margin-top: 5px;
        }
        
        .progress-container {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            margin-top: 8px;
            width: 100%;
            height: 18px;
            overflow: hidden;
            display: block;
        }
        
        .progress-bar {
            background: linear-gradient(90deg, #2ecc71 0%, #27ae60 100%);
            height: 100%;
            text-align: center;
            color: white;
            font-weight: 700;
            font-size: 11px;
            line-height: 18px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .card {
                padding: 18px;
                border-radius: 12px;
            }
            .info-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .progress-wrapper {
                grid-column: span 1;
            }
            .action-group {
                flex-direction: column;
                width: 100%;
            }
            .btn {
                width: 100%;
                box-sizing: border-box;
            }
            .status-label {
                text-align: center;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <a href="profiluser.php" class="back">← Kembali</a>

    <?php 
    if($b = mysqli_fetch_assoc($booking)): 
        $status_sekarang = $b['status_bayar'];
        $status_trip = $b['status_trip'];
        $total_peserta = $b['jumlah_peserta']; 

        // Hitung total tagihan dari harga total yang diberikan admin (jika sudah ditentukan)
        $total_tagihan = isset($b['harga']) ? $b['harga'] : 0;
        $total_tagihan_dp = isset($b['harga_dp']) ? $b['harga_dp'] : 0;

        // Progress Pembayaran
        $persentase = 0;
        if($total_tagihan > 0) {
            $persentase = ($total_terbayar / $total_tagihan) * 100;
            if($persentase > 100) $persentase = 100;
        }
    ?>
    <div class="card card-main">
        <div class="title">Detail Private Trip ke <?= $b['tujuan']; ?></div>

        <div class="info-grid">
            <div class="info"><b>Tujuan Wisata:</b> <?= $b['tujuan']; ?></div>
            <div class="info"><b>Penanggung Jawab:</b> <?= $b['nama']; ?></div>
            <div class="info"><b>No. HP Penanggung Jawab:</b> <?= $b['no_hp']; ?></div>
            <div class="info"><b>Tanggal Berangkat:</b> <?= $b['tgl_berangkat']; ?></div>
            <div class="info"><b>Tanggal Pulang:</b> <?= $b['tgl_pulang']; ?></div>
            <div class="info"><b>Tanggal Booking:</b> <?= $b['tgl_booking']; ?></div>
            <div class="info"><b>Jumlah Peserta:</b> <?= $b['jumlah_peserta']; ?> Orang</div>
            
            <div class="info"><b>Total Harga Trip:</b> <?= $total_tagihan > 0 ? 'Rp ' . number_format($total_tagihan, 0, ',', '.') : '<span style="opacity: 0.8;">Menunggu Konfirmasi Admin</span>'; ?></div>
            <div class="info"><b>Minimal Bayar DP:</b> <?= $total_tagihan_dp > 0 ? 'Rp ' . number_format($total_tagihan_dp, 0, ',', '.') : '<span style="opacity: 0.8;">Menunggu Konfirmasi Admin</span>'; ?></div>
            <div class="info"><b>Sudah Dibayar:</b> Rp <?= number_format($total_terbayar, 0, ',', '.'); ?></div>
            <div class="info"><b>Status Persetujuan:</b> <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 4px; font-weight: bold;"><?= $status_trip; ?></span></div>
            <div class="info"><b>Status Pembayaran:</b> <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 4px; font-weight: bold;"><?= $status_sekarang; ?></span></div>
            <div class="info"><b>Catatan Permintaan:</b> <?= !empty($b['catatan']) ? $b['catatan'] : '-'; ?></div>
            
            <?php if($total_tagihan > 0): ?>
            <div class="progress-wrapper">
                <div class="info" style="display: flex; justify-content: space-between; font-weight: 600;">
                    <span>Progress Pembayaran</span>
                    <span><?= round($persentase, 1); ?>%</span>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: <?= round($persentase); ?>%;"><?= round($persentase); ?>%</div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="action-group">
            <?php 
            // KUNCI UTAMA: Semua tombol aksi hanya muncul jika trip berstatus 'Disetujui'
            if($status_trip == 'Disetujui'): 
            ?>
                
                <?php 
                // 1. TOMBOL BAYAR SEKARANG
                // Aktif jika belum lunas, belum batal, belum refund, dan tidak sedang mengajukan pembatalan total
                if($status_sekarang != 'Lunas' && $status_sekarang != 'Dibatalkan' && $status_sekarang != 'Refund' && !$is_mengajukan_batal): 
                ?>
                    <a href="form_pembayaran_private.php?id_private=<?= $id_private; ?>" class="btn btn-pay">Bayar Sekarang</a>
                <?php endif; ?>

                <?php 
                // 2. TOMBOL AJUKAN PERUBAHAN
                // Aktif jika status pembayaran bukan batal/refund, dan tidak sedang mengajukan pembatalan total
                if($status_sekarang != 'Dibatalkan' && $status_sekarang != 'Refund' && !$is_mengajukan_batal):
                    if($is_mengajukan_ubah): ?>
                        <span class="btn" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); cursor: default;">Perubahan sedang diproses...</span>
                    <?php else: ?>
                        <a href="ubah_private.php?id_private=<?= $id_private; ?>" class="btn btn-edit-trip">Ajukan Perubahan</a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php 
                // 3. TOMBOL BATALKAN PESANAN ATAU LABEL STATUS PEMBATALAN
                if($status_sekarang != 'Dibatalkan' && $status_sekarang != 'Refund'): 
                    if($is_mengajukan_batal):
                        if($data_batal['status'] == 0): ?>
                            <span class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); cursor: default;">Menunggu Verifikasi Pembatalan</span>
                        <?php else: ?>
                            <span class="status-label">Pembatalan telah disetujui.</span>
                        <?php endif;
                    else: ?>
                        <a href="batal_private.php?id_private=<?= $id_private; ?>" 
                        class="btn btn-cancel-all" 
                        onclick="return confirm('Apakah Anda yakin ingin membatalkan seluruh pengajuan private trip ini?')">
                            Batalkan Pesanan
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="status-label">Pesanan ini sudah <?= $status_sekarang; ?>.</span>
                <?php endif; ?>

            <?php 
            // Jika status_trip bernilai 'Ditolak' atau status lainnya selain 'Disetujui'
            else: 
            ?>
                <span class="status-label">
                    <?php 
                    if($status_trip == 'Ditolak') {
                        echo "❌ Pengajuan trip ini ditolak oleh Admin.";
                    } else {
                        echo "⏳ Menunggu persetujuan pengajuan oleh Admin.";
                    }
                    ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="title">Data Peserta</div>

        <div class="table-responsive">
            <table>
                <tr>
                    <th>Nama</th>
                    <th>Usia</th>
                    <th>Alamat</th>
                    <th>Riwayat Penyakit</th>
                </tr>

                <?php if(mysqli_num_rows($peserta) > 0): ?>
                    <?php while($p = ambil($peserta)): ?>
                    <tr>
                        <td><b><?= $p['nama']; ?></b></td>
                        <td><?= $p['usia']; ?> Thn</td>
                        <td><?= $p['alamat']; ?></td>
                        <td><?= !empty($p['riwayat']) ? $p['riwayat'] : '-'; ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #777; padding: 20px;">Belum ada data peserta yang dimasukkan.</td>
                    </tr>
                <?php endif; ?>

            </table>
        </div>
    </div>

    <div class="card">
        <div class="title">Riwayat Pembayaran</div>

        <div class="table-responsive">
            <table>
                <tr>
                    <th>Tanggal Bayar</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>

                <?php 
                if(mysqli_num_rows($pembayaran) > 0):
                    while($pay = ambil($pembayaran)): 
                ?>
                <tr>
                    <td><?= $pay['tgl_bayar']; ?></td>
                    <td><b>Rp <?= number_format($pay['nominal'], 0, ',', '.'); ?></b></td>
                    <td>
                        <?php if($pay['status'] == 'Diverifikasi'): ?>
                            <span style="color: #27ae60; font-weight: bold; background: #e8f8f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?= $pay['status']; ?></span>
                        <?php elseif($pay['status'] == 'Ditolak'): ?>
                            <span style="color: #e74c3c; font-weight: bold; background: #fdedec; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?= $pay['status']; ?></span>
                        <?php else: ?>
                            <span style="color: #7f8c8d; font-weight: bold; background: #f2f4f4; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?= $pay['status']; ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= !empty($pay['catatan']) ? $pay['catatan'] : '-'; ?></td>
                    <td>
                        <a href="detail_bayar_private.php?id_payment=<?= $pay['id_payment']; ?>" class="btn btn-detail">Detail</a>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else: 
                ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #777; padding: 20px;">Belum ada riwayat pembayaran untuk pesanan ini.</td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>