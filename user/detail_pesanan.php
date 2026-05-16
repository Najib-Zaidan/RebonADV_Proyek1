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

// KODE DIPERBARUI: Ambil info booking + trip dengan JOIN ke tabel TUJUAN
$booking = kueri("
    SELECT 
        b.id_booking,
        b.tgl_booking,
        b.status,
        b.jumlah_peserta,
        tj.tujuan,
        t.tgl_berangkat,
        t.harga,
        t.harga_dp
    FROM booking b
    JOIN trip t ON b.id_trip = t.id_trip
    JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
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

// KODE TAMBAHAN: Hitung total pembayaran yang sudah DI-VERIFIKASI untuk booking ini
$hitung_bayar = kueri("
    SELECT SUM(nominal) AS total_terbayar 
    FROM payment_open 
    WHERE id_booking = '$id_booking' 
    AND status = 'Diverifikasi'
");
$data_bayar = ambil($hitung_bayar);
$total_terbayar = isset($data_bayar['total_terbayar']) ? $data_bayar['total_terbayar'] : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Peserta</title>
    <!-- KODE TAMBAHAN: Tag Viewport Meta agar halaman responsif di layar handphone -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body{
            font-family: 'Segoe UI', Arial, sans-serif;
            /* KODE TAMBAHAN: Mengubah latar belakang gradasi bertema gelap-ungu lembut */
            background: linear-gradient(135deg, #f1eefc 0%, #e5defa 100%);
            background-attachment: fixed;
            margin: 0;
            padding: 15px;
            color: #4a3b70;
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
            box-shadow: 0 4px 20px rgba(107, 61, 245, 0.08);
            border: 1px solid rgba(107, 61, 245, 0.1);
        }

        /* KODE TAMBAHAN: Styling khusus Card Utama (Detail Booking) bertema ungu asik */
        .card-main {
            background: linear-gradient(135deg, #6b3df5 0%, #4922c7 100%);
            color: white;
            border: none;
            box-shadow: 0 8px 25px rgba(107, 61, 245, 0.25);
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
            background: rgba(255, 255, 255, 0.05);
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

        /* Untuk judul pada card putih biasa */
        .card .title {
            color: #4922c7;
            border-bottom: 2px solid #e5defa;
            padding-bottom: 8px;
        }

        /* Untuk judul pada card utama yang berwarna ungu */
        .card-main .title {
            color: white;
            border-bottom: 2px solid rgba(255, 255, 255, 0.15);
        }

        /* KODE TAMBAHAN: Menyusun info detail booking agar rapi menggunakan grid layout */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 11fr);
            gap: 12px 20px;
            margin-bottom: 20px;
        }

        .info{
            margin-bottom: 0;
            font-size: 15px;
            line-height: 1.5;
        }

        /* KODE TAMBAHAN: Pembungkus tabel agar bisa di-scroll secara horizontal pada layar smartphone */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 15px;
        }

        table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 0; /* Dinetralkan karena sudah ada margin di pembungkus */
            min-width: 600px; /* Memastikan kolom tidak terjepit terlalu ekstrem di hp */
        }

        table th, table td{
            border: none;
            border-bottom: 1px solid #eae6fa;
            padding: 12px 14px;
            text-align: left;
            font-size: 14px;
        }

        table th{
            background: #6b3df5;
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
            background-color: #fcfbfe;
        }

        .back{
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 18px;
            background: #4a3b70;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(74, 59, 112, 0.15);
        }

        .back:hover{
            background: #342852;
            transform: translateY(-1px);
        }

        /* Style Tambahan untuk Tombol Aksi */
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
        
        /* KODE TAMBAHAN: Mengubah saturasi warna tombol agar menyatu dengan nuansa ungu */
        .btn-pay { 
            background: #00cc66; 
            box-shadow: 0 4px 12px rgba(0, 204, 102, 0.2);
        }
        .btn-cancel-all { 
            background: rgba(255, 255, 255, 0.15); 
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }
        .btn-cancel-person { 
            background: #fff0c2; 
            color: #b37400; 
            font-size: 12px; 
            font-weight: bold; 
        }
        /* Style baru untuk tombol detail pembayaran */
        .btn-detail { 
            background: #eae6fa; 
            color: #6b3df5; 
            font-size: 12px; 
            font-weight: bold;
        }
        
        .btn-pay:hover { 
            background: #00b359; 
            transform: translateY(-2px);
        }
        .btn-cancel-all:hover { 
            background: #ff334b; 
            border-color: #ff334b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 51, 75, 0.2);
        }
        .btn-cancel-person:hover { 
            background: #ffe394; 
            transform: translateY(-1px);
        }
        .btn-detail:hover { 
            background: #6b3df5; 
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

        .status-label {
            font-size: 13px;
            color: #e5defa;
            font-style: normal;
            display: inline-flex;
            align-items: center;
        }
        
        .card .status-label {
            color: #777;
        }

        /* KODE TAMBAHAN: Style untuk Progress Bar Pembayaran di dalam Card Utama */
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
            background: linear-gradient(90deg, #00cc66 0%, #00ff80 100%);
            height: 100%;
            text-align: center;
            color: #333;
            font-weight: 700;
            font-size: 11px;
            line-height: 18px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* KODE TAMBAHAN: Media Queries Responsif Spesifik untuk Layar Handphone */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .card {
                padding: 18px;
                border-radius: 12px;
            }
            .info-grid {
                grid-template-columns: 1fr; /* Mengubah susunan grid menjadi 1 kolom saja di HP */
                gap: 10px;
            }
            .progress-wrapper {
                grid-column: span 1;
            }
            .action-group {
                flex-direction: column; /* Tombol aksi berjejer ke bawah penuh di hp */
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
    // Kita simpan status dalam variabel agar mudah digunakan berulang kali
    if($b = mysqli_fetch_assoc($booking)): 
        $status_sekarang = $b['status'];
        // KODE TAMBAHAN: Simpan jumlah peserta ke variabel agar tidak hilang di luar block IF ini
        $total_peserta = $b['jumlah_peserta']; 

        // KODE TAMBAHAN: Hitung total biaya yang harus dibayar (harga trip dikali jumlah peserta)
        $total_tagihan = $b['harga'] * $total_peserta;
        $total_tagihan_dp = $b['harga_dp'] * $total_peserta;

        // KODE TAMBAHAN: Hitung persentase progress pembayaran
        $persentase = 0;
        if($total_tagihan > 0) {
            $persentase = ($total_terbayar / $total_tagihan) * 100;
            // Batasi persentase agar maksimal 100% jika ada overpayment
            if($persentase > 100) $persentase = 100;
        }
    ?>
    <!-- KODE TAMBAHAN: Menambahkan class card-main untuk manipulasi style bertema ungu asik -->
    <div class="card card-main">
        <div class="title">Detail Booking</div>

        <!-- KODE TAMBAHAN: Membungkus elemen dengan struktur class info-grid -->
        <div class="info-grid">
            <div class="info"><b>Tujuan:</b> <?= $b['tujuan']; ?></div>
            <div class="info"><b>Tanggal Berangkat:</b> <?= $b['tgl_berangkat']; ?></div>
            <div class="info"><b>Tanggal Booking:</b> <?= $b['tgl_booking']; ?></div>
            <div class="info"><b>Jumlah Peserta:</b> <?= $b['jumlah_peserta']; ?> Orang</div>
            <div class="info"><b>Harga per Peserta:</b> Rp <?= number_format($b['harga'], 0, ',', '.'); ?> <span style="opacity: 0.8; font-size: 13px;">(DP: Rp <?= number_format($b['harga_dp'], 0, ',', '.'); ?>)</span></div>
            <div class="info"><b>Total Tagihan Trip:</b> Rp <?= number_format($total_tagihan, 0, ',', '.'); ?> <span style="opacity: 0.8; font-size: 13px;">(Min DP: Rp <?= number_format($total_tagihan_dp, 0, ',', '.'); ?>)</span></div>
            <div class="info"><b>Sudah Dibayar:</b> Rp <?= number_format($total_terbayar, 0, ',', '.'); ?></div>
            <div class="info"><b>Status Booking:</b> <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 4px; font-weight: bold;"><?= $status_sekarang; ?></span></div>
            
            <!-- KODE TAMBAHAN: Progress Bar visual yang dibungkus wrapper khusus -->
            <div class="progress-wrapper">
                <div class="info" style="display: flex; justify-content: space-between; font-weight: 600;">
                    <span>Progress Pembayaran</span>
                    <span><?= round($persentase, 1); ?>%</span>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: <?= round($persentase); ?>%;"><?= round($persentase); ?>%</div>
                </div>
            </div>
        </div>

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
                        <span class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); cursor: default;">Menunggu Verifikasi Pembatalan</span>
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

        <!-- KODE TAMBAHAN: Membungkus dengan class table-responsive -->
        <div class="table-responsive">
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
                    <td><b><?= $p['nama']; ?></b></td>
                    <td><?= $p['no_hp']; ?></td>
                    <td><?= $p['usia']; ?> Thn</td>
                    <td><?= $p['alamat']; ?></td>
                    <td><?= !empty($p['riwayat']) ? $p['riwayat'] : '-'; ?></td>
                    <td>
                        <?php if($status_sekarang != 'Dibatalkan' && $status_sekarang != 'Refund'): ?>
                            
                            <?php if($p['status_verifikasi'] == 'Menunggu'): ?>
                                <!-- Jika sudah ada di tabel batal_peserta dan statusnya Menunggu -->
                                <span class="btn" style="background: #6c757d; cursor: default; font-size: 12px; padding: 6px 12px;">Menunggu Verifikasi</span>
                            
                            <?php elseif($p['status_verifikasi'] == 'Disetujui'): ?>
                                <!-- Opsi tambahan jika admin sudah setuju tapi data belum terhapus -->
                                <span class="btn" style="background: #28a745; cursor: default; font-size: 12px; padding: 6px 12px;">Dibatalkan</span>

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
    </div>

    <!-- KODE TAMBAHAN: Card Baru untuk Riwayat Pembayaran -->
    <div class="card">
        <div class="title">Riwayat Pembayaran</div>

        <!-- KODE TAMBAHAN: Membungkus dengan class table-responsive -->
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
                            <span style="color: #00cc66; font-weight: bold; background: #e6fbf1; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?= $pay['status']; ?></span>
                        <?php elseif($pay['status'] == 'Ditolak'): ?>
                            <span style="color: #ff334b; font-weight: bold; background: #ffebeb; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?= $pay['status']; ?></span>
                        <?php else: ?>
                            <span style="color: #6c757d; font-weight: bold; background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?= $pay['status']; ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= !empty($pay['catatan']) ? $pay['catatan'] : '-'; ?></td>
                    <td>
                        <a href="detail_bayar_open.php?id_payment=<?= $pay['id_payment']; ?>" class="btn btn-detail">Detail</a>
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
