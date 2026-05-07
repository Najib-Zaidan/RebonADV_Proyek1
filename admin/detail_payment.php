<?php 
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'konek.php';
require 'fungsi.php';

$id_payment = $_GET['id'];

// 1. Logika Update Status Pembayaran & Otomatisasi Status Booking
if (isset($_POST['update_payment'])) {
    $status_baru = $_POST['status_pembayaran'];
    
    // Update status di payment_open
    kueri("UPDATE payment_open SET status = '$status_baru' WHERE id_payment = $id_payment");

    // --- LOGIKA OTOMATISASI STATUS BOOKING ---
    $info = ambil(kueri("SELECT b.id_booking, t.harga, t.harga_dp, b.jumlah_peserta 
                         FROM payment_open p 
                         JOIN booking b ON p.id_booking = b.id_booking 
                         JOIN trip t ON b.id_trip = t.id_trip 
                         WHERE p.id_payment = $id_payment"));
    
    $id_book = $info['id_booking'];
    $total_lunas = $info['harga'] * $info['jumlah_peserta'];
    $min_dp = $info['harga_dp'];

    // Hitung akumulasi pembayaran yang statusnya 'Diverifikasi'
    $cek_total = ambil(kueri("SELECT SUM(nominal) as total FROM payment_open 
                              WHERE id_booking = $id_book AND status = 'Diverifikasi'"));
    $total_masuk = $cek_total['total'] ?? 0;

    // Tentukan Status Booking Baru
    if ($total_masuk >= $total_lunas) {
        $status_booking = "Lunas";
    } elseif ($total_masuk >= $min_dp) {
        $status_booking = "DP";
    } elseif ($total_masuk > 0) {
        $status_booking = "Bayar non-DP";
    } else {
        $status_booking = "Belum Bayar";
    }

    // Update ke tabel booking
    kueri("UPDATE booking SET status = '$status_booking' WHERE id_booking = $id_book");

    echo "<script>alert('Status pembayaran dan booking berhasil diperbarui!'); window.location.href='index.php?menu=payment';</script>";
}

// 2. Ambil Data Detail Pembayaran & Booking (Ditarik ulang agar variabel $pay tersedia)
$pay = ambil(kueri("SELECT p.*, b.tgl_booking, t.tujuan, a.username, b.jumlah_peserta, t.harga, b.id_booking
                    FROM payment_open p
                    JOIN booking b ON p.id_booking = b.id_booking
                    JOIN trip t ON b.id_trip = t.id_trip
                    JOIN akun a ON b.id_akun = a.id_akun
                    WHERE p.id_payment = $id_payment"));

$id_booking = $pay['id_booking'];
$total_tagihan = $pay['harga'] * $pay['jumlah_peserta'];

// 3. Ambil Daftar Peserta
$daftar_peserta = kueri("SELECT p.* FROM peserta_open p 
                         JOIN detail d ON p.id_peserta = d.id_peserta 
                         WHERE d.id_booking = $id_booking");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Pembayaran #<?php echo $id_payment; ?></title>
    <style>
        /* Style tetap sama sesuai keinginanmu */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #d9d9d9; color: #333; padding: 30px; }
        .container { max-width: 1300px; margin: auto; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: #5a1ee6; text-decoration: none; font-weight: bold; }
        .layout-grid { display: grid; grid-template-columns: 350px 1fr; gap: 25px; align-items: start; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .card-header { border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .card-header h2 { color: #321180; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; }
        .bukti-container { text-align: center; background: #f0f0f0; padding: 10px; border-radius: 10px; border: 2px dashed #ccc; }
        .bukti-img { width: 100%; max-height: 800px; object-fit: contain; border-radius: 8px; }
        .info-item { margin-bottom: 15px; }
        .info-item label { display: block; font-size: 11px; color: #888; text-transform: uppercase; margin-bottom: 3px; }
        .info-item p { font-size: 15px; font-weight: 600; color: #321180; }
        .verif-box { background: #f4f0ff; padding: 20px; border-radius: 12px; border: 1px solid #dcd0ff; }
        select { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 15px; outline: none; }
        .btn-verif { width: 100%; padding: 12px; background: #321180; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-verif:hover { background: #6b3df5; }
        .tagihan-info { background: #321180; color: white; padding: 15px; border-radius: 10px; margin-bottom: 15px; text-align: center;}
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #6b3df5; color: white; padding: 12px; text-align: left; font-size: 12px; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 13px; }
        .catatan-user { background: #fff9e6; padding: 10px; border-radius: 8px; border-left: 4px solid #ffcc00; font-size: 13px; color: #666; font-style: italic; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php?menu=payment" class="back-btn">&larr; KEMBALI KE DAFTAR PEMBAYARAN</a>

    <div class="layout-grid">
        <div class="side-panel">
            <div class="card">
                <div class="tagihan-info">
                    <small>Nominal Bayar:</small>
                    <h1 style="font-size: 24px;">Rp <?php echo number_format($pay['nominal']); ?></h1>
                </div>

                <div class="info-item">
                    <label>Nama Pemesan (Akun)</label>
                    <p><?php echo $pay['username']; ?></p>
                </div>
                
                <div class="info-item">
                    <label>Tanggal Bayar</label>
                    <p><?php echo date('d M Y, H:i', strtotime($pay['tgl_bayar'])); ?></p>
                </div>

                <div class="info-item">
                    <label>ID Booking / Trip</label>
                    <p>#<?php echo $pay['id_booking']; ?> - <?php echo $pay['tujuan']; ?></p>
                </div>

                <div class="info-item">
                    <label>Total Tagihan Booking</label>
                    <p>Rp <?php echo number_format($total_tagihan); ?> (<?php echo $pay['jumlah_peserta']; ?> Orang)</p>
                </div>

                <div class="info-item">
                    <label>Catatan dari User:</label>
                    <div class="catatan-user">
                        "<?php echo $pay['catatan'] ?: 'Tidak ada catatan dari user.'; ?>"
                    </div>
                </div>
            </div>

            <div class="card verif-box">
                <div class="card-header"><h2>Verifikasi Admin</h2></div>
                <form action="" method="post">
                    <select name="status_pembayaran" required>
                        <option value="Belum Diverifikasi" <?php if($pay['status'] == 'Belum Diverifikasi') echo 'selected'; ?>>Tunda Verifikasi</option>
                        <option value="Diverifikasi" <?php if($pay['status'] == 'Diverifikasi') echo 'selected'; ?>>Terima / Sah</option>
                        <option value="Ditolak" <?php if($pay['status'] == 'Ditolak') echo 'selected'; ?>>Tolak / Palsu</option>
                    </select>
                    <button type="submit" name="update_payment" class="btn-verif">SIMPAN PERUBAHAN</button>
                </form>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-header"><h2>Foto Bukti Transfer</h2></div>
                <div class="bukti-container">
                    <img src="../gambar/payment/<?php echo $pay['bukti_bayar']; ?>" class="bukti-img">
                </div>
            </div>

            <div class="card" style="border-top: 5px solid #6b3df5;">
                <div class="card-header"><h2>Daftar Peserta (<?php echo $pay['jumlah_peserta']; ?> Orang)</h2></div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>No. HP</th>
                            <th>Usia</th>
                            <th>Alamat</th>
                            <th>Riwayat Kesehatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($p = ambil($daftar_peserta)): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td style="font-weight:bold;"><?php echo $p['nama']; ?></td>
                            <td><?php echo $p['no_hp']; ?></td>
                            <td><?php echo $p['usia']; ?> Thn</td>
                            <td><?php echo $p['alamat']; ?></td>
                            <td><?php echo $p['riwayat'] ?: '-'; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>