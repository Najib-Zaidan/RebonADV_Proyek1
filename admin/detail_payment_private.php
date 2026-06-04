<?php 
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'konek.php';
require 'fungsi.php';

$id_payment = $_GET['id'];

// Ambil Data Detail Pembayaran & Private Trip di awal (Termasuk id_akun untuk keperluan notifikasi)
$pay = ambil(kueri("SELECT p.*, pt.tgl_booking, pt.tujuan, a.username, pt.jumlah_peserta, pt.harga, pt.id_private, pt.id_akun
                    FROM payment_private p
                    JOIN private_trip pt ON p.id_private = pt.id_private
                    JOIN akun a ON pt.id_akun = a.id_akun
                    WHERE p.id_payment = $id_payment"));

if (!$pay) {
    die("Data pembayaran tidak ditemukan.");
}

$id_private = $pay['id_private'];

// 1. Logika Update Status Pembayaran & Otomatisasi Status Booking Private
if (isset($_POST['update_payment'])) {
    $status_baru = $_POST['status_pembayaran'];
    $status_lama = $pay['status'];
    
    // Update status di payment_private
    kueri("UPDATE payment_private SET status = '$status_baru' WHERE id_payment = $id_payment");

    // --- LOGIKA OTOMATISASI STATUS BOOKING PRIVATE ---
    $info = ambil(kueri("SELECT p.id_private, pt.harga, pt.harga_dp 
                         FROM payment_private p 
                         JOIN private_trip pt ON p.id_private = pt.id_private 
                         WHERE p.id_payment = $id_payment"));
    
    $id_priv = $info['id_private'];
    $total_lunas = $info['harga'];
    $min_dp = $info['harga_dp'];

    // Hitung akumulasi pembayaran yang statusnya 'Diverifikasi'
    $cek_total = ambil(kueri("SELECT SUM(nominal) as total FROM payment_private 
                              WHERE id_private = $id_priv AND status = 'Diverifikasi'"));
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

    // Update ke tabel private_trip (kolom status_bayar)
    kueri("UPDATE private_trip SET status_bayar = '$status_booking' WHERE id_private = $id_priv");

    // --- LOGIKA NOTIFIKASI OTOMATIS BERDASARKAN PERUBAHAN STATUS ---
    if ($status_baru !== $status_lama) {
        $id_akun_user = $pay['id_akun'];
        $tujuan_trip = $pay['tujuan'];
        $nominal_bayar = number_format($pay['nominal']);
        $pesan_notif = "";

        if ($status_baru == 'Diverifikasi') {
            $pesan_notif = "Pembayaran Private Trip Anda sebesar Rp " . $nominal_bayar . " untuk tujuan " . $tujuan_trip . " (Trip ID: #" . $id_private . ") telah BERHASIL DIVERIFIKASI. Status pembayaran Anda: " . $status_booking . ".";
        } elseif ($status_baru == 'Ditolak') {
            $pesan_notif = "Pembayaran Private Trip Anda sebesar Rp " . $nominal_bayar . " untuk tujuan " . $tujuan_trip . " (Trip ID: #" . $id_private . ") telah DITOLAK oleh admin. Silakan periksa kembali bukti transfer Anda atau hubungi admin.";
        } elseif ($status_baru == 'Belum Diverifikasi') {
            $pesan_notif = "Status verifikasi pembayaran Private Trip Anda sebesar Rp " . $nominal_bayar . " ke " . $tujuan_trip . " dikembalikan ke status Menunggu Verifikasi.";
        }

        // Simpan baris notifikasi baru ke database
        if (!empty($pesan_notif)) {
            kueri("INSERT INTO notif (pesan, id_akun) VALUES ('$pesan_notif', $id_akun_user)");
        }
    }

    echo "<script>alert('Status pembayaran dan booking private berhasil diperbarui!'); window.location.href='index.php?menu=payment&type=private';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Pembayaran Private #<?php echo $id_payment; ?></title>
    <style>
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
        .catatan-user { background: #fff9e6; padding: 10px; border-radius: 8px; border-left: 4px solid #ffcc00; font-size: 13px; color: #666; font-style: italic; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php?menu=payment&type=private" class="back-btn">&larr; KEMBALI KE DAFTAR PEMBAYARAN PRIVATE</a>

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
                    <label>ID Private / Tujuan</label>
                    <p>#<?php echo $pay['id_private']; ?> - <?php echo $pay['tujuan']; ?></p>
                </div>

                <div class="info-item">
                    <label>Total Tagihan Private</label>
                    <p>Rp <?php echo number_format($pay['harga']); ?> (<?php echo $pay['jumlah_peserta']; ?> Orang)</p>
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
                <div class="card-header"><h2>Detail Keberangkatan Private</h2></div>
                <div class="info-item">
                    <label>Tanggal Booking / Pengajuan</label>
                    <p><?php echo date('d M Y', strtotime($pay['tgl_booking'])); ?></p>
                </div>
                <div class="info-item">
                    <label>Status Saat Ini</label>
                    <p style="color: #6b3df5;"><?php 
                        $status_pt = ambil(kueri("SELECT status_bayar FROM private_trip WHERE id_private = $id_private"));
                        echo $status_pt['status_bayar']; 
                    ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
