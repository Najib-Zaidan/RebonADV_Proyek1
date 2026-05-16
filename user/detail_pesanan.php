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
// Cek apakah booking ini sudah ada di tabel pembatalan total (batal_open)
$cek_batal_total = kueri("SELECT status FROM batal_open WHERE id_booking = '$id_booking'");
$data_batal_total = ambil($cek_batal_total);
$is_mengajukan_batal = mysqli_num_rows($cek_batal_total) > 0;

// ambil peserta (ditambahkan bd.id_detail untuk keperluan batal per orang)
// ambil peserta serta cek status pembatalannya
$peserta = kueri("
    SELECT 
        bd.id_detail,
        p.nama,
        p.no_hp,
        p.usia,
        p.alamat,
        p.riwayat,
        bp.status_verifikasi -- Tambahkan ini
    FROM detail bd
    JOIN peserta_open p ON bd.id_peserta = p.id_peserta
    LEFT JOIN batal_peserta bp ON bd.id_detail = bp.id_detail -- Tambahkan ini
    WHERE bd.id_booking = '$id_booking'
");

// Inisialisasi variabel untuk menyimpan jumlah peserta agar bisa diakses di tabel bawah
$total_peserta = 0; 

// KODE TAMBAHAN: Ambil data riwayat pembayaran dari tabel payment_open
$pembayaran = kueri("
    SELECT 
        id_payment,
        tgl_bayar,
        nominal,
        bukti_bayar,
        status,
        catatan
    FROM payment_open
    WHERE id_booking = '$id_booking'
    ORDER BY tgl_bayar DESC
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

        /* Style Tambahan untuk Tombol Aksi */
        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            display: inline-block;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-pay { background: #28a745; }
        .btn-cancel-all { background: #dc3545; }
        .btn-cancel-person { background: #ffc107; color: #000; font-size: 12px; font-weight: bold; }
        /* Style baru untuk tombol detail pembayaran */
        .btn-detail { background: #007bff; color: white; font-size: 12px; }
        .btn-detail:hover { background: #0056b3; }
        
        .btn-pay:hover { background: #218838; }
        .btn-cancel-all:hover { background: #c82333; }
        .btn-cancel-person:hover { background: #e0a800; }
        
        .action-group { 
            margin-top: 20px; 
            padding-top: 15px; 
            border-top: 1px solid #eee; 
            display: flex; 
            gap: 10px; 
        }

        .status-label {
            font-size: 12px;
            color: #777;
            font-style: normal;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="profiluser.php" class="back">← Kembali</a>

    <?php 
    // Kita simpan status dalam variabel agar mudah digunakan berulang kali
    if($b = mysqli_fetch_assoc($booking)): 
        $status_sekarang = $b['status'];
        // KODE TAMBAHAN: Simpan jumlah peserta ke variabel agar tidak hilang di luar block IF ini
        $total_peserta = $b['jumlah_peserta']; 
    ?>
    <div class="card">
        <div class="title">Detail Booking</div>

        <div class="info"><b>Tujuan:</b> <?= $b['tujuan']; ?></div>
        <div class="info"><b>Tanggal Berangkat:</b> <?= $b['tgl_berangkat']; ?></div>
        <div class="info"><b>Tanggal Booking:</b> <?= $b['tgl_booking']; ?></div>
        <div class="info"><b>Jumlah Peserta:</b> <?= $b['jumlah_peserta']; ?></div>
        <div class="info"><b>Status:</b> <?= $status_sekarang; ?></div>

        <div class="action-group">
            <?php 
            // Syarat Tombol Bayar: 
            // 1. Belum Lunas/Batal/Refund 
            // 2. TIDAK sedang mengajukan pembatalan total
            if($status_sekarang != 'Lunas' && $status_sekarang != 'Dibatalkan' && $status_sekarang != 'Refund' && !$is_mengajukan_batal): 
            ?>
                <a href="form_pembayaran.php?id_booking=<?= $id_booking; ?>" class="btn btn-pay">Bayar Sekarang</a>
            <?php endif; ?>

            <?php 
            if($status_sekarang != 'Dibatalkan' && $status_sekarang != 'Refund'): 
                if($is_mengajukan_batal):
                    // Jika sudah ada di tabel batal_open
                    if($data_batal_total['status'] == 0): // Jika status FALSE / Belum diverifikasi admin ?>
                        <span class="btn" style="background: #6c757d; cursor: default;">Menunggu Verifikasi Pembatalan</span>
                    <?php else: ?>
                        <span class="status-label">Pembatalan telah disetujui.</span>
                    <?php endif;
                else: 
                    // Jika belum ada di tabel pembatalan sama sekali ?>
                    <a href="batal_pesanan.php?id_booking=<?= $id_booking; ?>" 
                    class="btn btn-cancel-all" 
                    onclick="return confirm('Apakah Anda yakin ingin membatalkan seluruh pesanan ini?')">
                        Batalkan Pesanan (Semua Peserta)
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <span class="status-label">Pesanan ini sudah <?= $status_sekarang; ?>.</span>
            <?php endif; ?>
        </div>
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
                <th>Aksi</th>
            </tr>

            <?php while($p = ambil($peserta)): ?>
            <tr>
                <td><?= $p['nama']; ?></td>
                <td><?= $p['no_hp']; ?></td>
                <td><?= $p['usia']; ?></td>
                <td><?= $p['alamat']; ?></td>
                <td><?= $p['riwayat']; ?></td>
                <td>
                    <?php if($status_sekarang != 'Dibatalkan' && $status_sekarang != 'Refund'): ?>
                        
                        <?php if($p['status_verifikasi'] == 'Menunggu'): ?>
                            <!-- Jika sudah ada di tabel batal_peserta dan statusnya Menunggu -->
                            <span class="btn" style="background: #6c757d; cursor: default;">Menunggu Verifikasi</span>
                        
                        <?php elseif($p['status_verifikasi'] == 'Disetujui'): ?>
                            <!-- Opsi tambahan jika admin sudah setuju tapi data belum terhapus -->
                            <span class="btn" style="background: #28a745; cursor: default;">Dibatalkan</span>

                        <?php else: ?>
                            <!-- KODE TAMBAHAN: Cek jika jumlah peserta hanya 1 atau tinggal 1 -->
                            <?php if($total_peserta <= 1): ?>
                                <span class="status-label" style="font-size: 11px;"> – </span>
                            <?php else: ?>
                                <!-- Jika belum ada pengajuan pembatalan (status_verifikasi kosong/null) -->
                                <a href="batal_peserta.php?id_detail=<?= $p['id_detail']; ?>&id_booking=<?= $id_booking; ?>" 
                                class="btn btn-cancel-person"
                                onclick="return confirm('Ajukan pembatalan untuk peserta ini?')">
                                Batalkan Peserta
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>

        </table>
    </div>

    <!-- KODE TAMBAHAN: Card Baru untuk Riwayat Pembayaran -->
    <div class="card">
        <div class="title">Riwayat Pembayaran</div>

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
                <td>Rp <?= number_format($pay['nominal'], 0, ',', '.'); ?></td>
                <td>
                    <?php if($pay['status'] == 'Diverifikasi'): ?>
                        <span style="color: #28a745; font-weight: bold;"><?= $pay['status']; ?></span>
                    <?php elseif($pay['status'] == 'Ditolak'): ?>
                        <span style="color: #dc3545; font-weight: bold;"><?= $pay['status']; ?></span>
                    <?php else: ?>
                        <span style="color: #6c757d; font-weight: bold;"><?= $pay['status']; ?></span>
                    <?php endif; ?>
                </td>
                <td><?= !empty($pay['catatan']) ? $pay['catatan'] : '-'; ?></td>
                <td>
                    <a href="detail_pembayaran.php?id_payment=<?= $pay['id_payment']; ?>" class="btn btn-detail">Detail</a>
                </td>
            </tr>
            <?php 
                endwhile;
            else: 
            ?>
            <tr>
                <td colspan="5" style="text-align: center; color: #777;">Belum ada riwayat pembayaran untuk pesanan ini.</td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

</div>

</body>
</html>
