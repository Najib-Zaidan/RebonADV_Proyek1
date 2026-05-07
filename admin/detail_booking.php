<?php 
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'konek.php';
require 'fungsi.php';

$id_booking = $_GET['id'];

// 1. Logika Update Status Booking (Enum: Belum Bayar, Bayar non-DP, DP, Lunas, Dibatalkan, Refund)
if (isset($_POST['update_status'])) {
    $status_baru = $_POST['status_booking'];
    kueri("UPDATE booking SET status = '$status_baru' WHERE id_booking = $id_booking");
    echo "<script>alert('Status booking berhasil diperbarui!'); window.location.href='detail_booking.php?id=$id_booking';</script>";
}

// 2. Ambil Data Utama Booking & Trip
$data_booking = ambil(kueri("SELECT b.*, t.tujuan, t.tgl_berangkat, t.harga, a.username 
                             FROM booking b 
                             JOIN trip t ON b.id_trip = t.id_trip 
                             JOIN akun a ON b.id_akun = a.id_akun 
                             WHERE b.id_booking = $id_booking"));

$total_tagihan = $data_booking['harga'] * $data_booking['jumlah_peserta'];

// 3. Ambil Data Peserta (Relasi Many-to-Many via tabel detail)
$daftar_peserta = kueri("SELECT p.* FROM peserta_open p 
                         JOIN detail d ON p.id_peserta = d.id_peserta 
                         WHERE d.id_booking = $id_booking");

// 4. Ambil Riwayat Pembayaran (Hanya yang Diverifikasi untuk total masuk)
$riwayat_pembayaran = kueri("SELECT * FROM payment_open WHERE id_booking = $id_booking ORDER BY tgl_bayar DESC");
$total_masuk = ambil(kueri("SELECT SUM(nominal) as total FROM payment_open WHERE id_booking = $id_booking AND status = 'Diverifikasi'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Booking #<?php echo $id_booking; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #d9d9d9; color: #333; padding: 30px; }
        .container { max-width: 1100px; margin: auto; }
        
        .back-btn { display: inline-block; margin-bottom: 20px; color: #5a1ee6; text-decoration: none; font-weight: bold; font-size: 14px; }
        
        /* Dashboard Card Style */
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 25px; border-left: 5px solid #321180; }
        .card-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .card-header h2 { color: #321180; font-size: 22px; }

        /* Info Grid */
        .grid-info { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .info-group label { display: block; font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .info-group p { font-size: 16px; font-weight: 600; color: #321180; margin-bottom: 15px; }

        /* Payment Summary Box */
        .summary-box { background: linear-gradient(135deg, #5a1ee6, #321180); color: white; padding: 20px; border-radius: 12px; text-align: center; }
        .summary-box h3 { font-size: 24px; margin: 10px 0; }
        .summary-box span { font-size: 13px; opacity: 0.8; }

        /* Table Style */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; border-radius: 10px; overflow: hidden; }
        th { background: #6b3df5; color: white; padding: 12px; text-align: left; font-size: 13px; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        tr:hover { background: #f9f7ff; }

        /* Status & Form */
        .status-select { padding: 8px; border-radius: 8px; border: 1px solid #ddd; outline: none; }
        .btn-save { background: #321180; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-save:hover { background: #c70039; } /* Crimson touch on hover */
        
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; }
        .bg-green { background: #d4edda; color: #155724; }
        .bg-red { background: #f8d7da; color: #721c24; }
        .bg-yellow { background: #fff3cd; color: #856404; }

        .bukti-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php?menu=booking" class="back-btn">&larr; KEMBALI KE DAFTAR BOOKING</a>

    <div class="card">
        <div class="card-header">
            <h2>Informasi Booking #<?php echo $id_booking; ?></h2>
            <form action="" method="post" style="display: flex; gap: 10px;">
                <select name="status_booking" class="status-select">
                    <?php 
                    $status_list = ['Belum Bayar', 'Bayar non-DP', 'DP', 'Lunas', 'Dibatalkan', 'Refund'];
                    foreach($status_list as $st):
                        $selected = ($data_booking['status'] == $st) ? 'selected' : '';
                        echo "<option value='$st' $selected>$st</option>";
                    endforeach;
                    ?>
                </select>
                <button type="submit" name="update_status" class="btn-save">SIMPAN STATUS</button>
            </form>
        </div>

        <div class="grid-info">
            <div class="info-details">
                <div class="info-group">
                    <label>Tujuan Trip</label>
                    <p><?php echo $data_booking['tujuan']; ?></p>
                </div>
                <div class="info-group">
                    <label>Nama Pemesan (Akun)</label>
                    <p><?php echo $data_booking['username']; ?></p>
                </div>
                <div class="info-group">
                    <label>Tanggal Berangkat</label>
                    <p><?php echo date('d F Y', strtotime($data_booking['tgl_berangkat'])); ?></p>
                </div>
                <div class="info-group">
                    <label>Waktu Booking</label>
                    <p><?php echo $data_booking['tgl_booking']; ?></p>
                </div>
            </div>

            <div class="info-payment">
                <div class="summary-box">
                    <span>Total Tagihan (<?php echo $data_booking['jumlah_peserta']; ?> Orang)</span>
                    <h3>Rp <?php echo number_format($total_tagihan); ?></h3>
                    <div style="height: 1px; background: rgba(255,255,255,0.2); margin: 15px 0;"></div>
                    <span>Total Dibayar (Tervalidasi)</span>
                    <p style="font-size: 20px; font-weight: bold;">Rp <?php echo number_format($total_masuk); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="border-left: 5px solid #c70039;"> <div class="card-header">
            <h2>Daftar Peserta (Pax)</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>No. HP</th>
                    <th>Usia</th>
                    <th>Riwayat Kesehatan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; while($p = ambil($daftar_peserta)): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td style="font-weight: bold;"><?php echo $p['nama']; ?></td>
                    <td><?php echo $p['no_hp']; ?></td>
                    <td><?php echo $p['usia']; ?> Tahun</td>
                    <td><?php echo $p['riwayat'] ?: '-'; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Log Pembayaran (Open Trip)</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nominal</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Catatan Admin</th>
                </tr>
            </thead>
            <tbody>
                <?php while($pay = ambil($riwayat_pembayaran)): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($pay['tgl_bayar'])); ?></td>
                    <td style="color: #321180; font-weight: bold;">Rp <?php echo number_format($pay['nominal']); ?></td>
                    <td>
                        <a href="img/bukti/<?php echo $pay['bukti_bayar']; ?>" target="_blank">
                            <img src="img/bukti/<?php echo $pay['bukti_bayar']; ?>" class="bukti-img" alt="Bukti">
                        </a>
                    </td>
                    <td>
                        <?php 
                        $cls = ($pay['status'] == 'Diverifikasi') ? 'bg-green' : (($pay['status'] == 'Ditolak') ? 'bg-red' : 'bg-yellow');
                        ?>
                        <span class="badge <?php echo $cls; ?>"><?php echo $pay['status']; ?></span>
                    </td>
                    <td style="font-style: italic; font-size: 12px;"><?php echo $pay['catatan'] ?: '-'; ?></td>
                </tr>
                <?php endwhile; 
                if(mysqli_num_rows($riwayat_pembayaran) == 0) echo "<tr><td colspan='5' align='center'>Belum ada data pembayaran masuk.</td></tr>";
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
