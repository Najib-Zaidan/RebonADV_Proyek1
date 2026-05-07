<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'fungsi.php';

// Ambil ID Private dari URL
$id_private = $_GET['id'];

// Logika Update Status Trip & Harga
if (isset($_POST['update_status'])) {
    $status_baru = $_POST['status_trip'];
    $harga = $_POST['harga'] ?? 0;
    $harga_dp = $_POST['harga_dp'] ?? 0;

    $update = kueri("UPDATE private_trip SET 
                     status_trip = '$status_baru', 
                     harga = '$harga', 
                     harga_dp = '$harga_dp' 
                     WHERE id_private = $id_private");
    
    if ($update) {
        echo "<script>alert('Status trip berhasil diperbarui!'); window.location.href='detail_private.php?id=$id_private';</script>";
    }
}

// Ambil Data Utama Private Trip
$data = kueri("SELECT pt.*, a.username FROM private_trip pt 
               JOIN akun a ON pt.id_akun = a.id_akun 
               WHERE pt.id_private = $id_private");
$trip = ambil($data);

if (!$trip) {
    die("Data tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Private Trip #<?php echo $id_private; ?></title>
    <style>
        :root {
            --primary: #6b3df5;
            --secondary: #321180;
            --bg: #f4f0ff;
            --white: #ffffff;
            --text: #333;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .back-link {
            text-decoration: none;
            color: var(--primary);
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }

        /* GRID LAYOUT */
        .grid-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .card {
            background: var(--white);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(107, 61, 245, 0.1);
            margin-bottom: 20px;
        }

        h2, h3 {
            color: var(--secondary);
            margin-top: 0;
            border-bottom: 2px solid var(--bg);
            padding-bottom: 10px;
        }

        /* DETAIL LIST */
        .info-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }

        .info-item label {
            display: block;
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
        }

        .info-item p {
            margin: 5px 0;
            font-weight: 600;
        }

        /* STATUS BADGE */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .bg-orange { background: #fff3cd; color: #856404; }
        .bg-green { background: #d4edda; color: #155724; }
        .bg-red { background: #f8d7da; color: #721c24; }

        /* FORM STYLING */
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
        }

        .btn-save {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-save:hover { background: var(--secondary); }

        /* TABLE STYLING */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th { text-align: left; color: #888; font-size: 13px; padding: 10px; border-bottom: 1px solid #eee; }
        td { padding: 12px 10px; font-size: 14px; border-bottom: 1px solid #f9f9f9; }

        .img-bukti {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php?menu=private" class="back-link">← Kembali ke Daftar</a>

    <div class="grid-container">
        
        <div class="main-content">
            
            <div class="card">
                <h2>Detail Pengajuan Trip</h2>
                <div class="info-group">
                    <div class="info-item">
                        <label>Destinasi</label>
                        <p><?php echo $trip['tujuan']; ?></p>
                    </div>
                    <div class="info-item">
                        <label>Status Trip</label>
                        <span class="badge <?php echo ($trip['status_trip'] == 'Belum Disetujui') ? 'bg-orange' : (($trip['status_trip'] == 'Disetujui') ? 'bg-green' : 'bg-red'); ?>">
                            <?php echo $trip['status_trip']; ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Tanggal Berangkat - Pulang</label>
                        <p><?php echo date('d M Y', strtotime($trip['tgl_berangkat'])); ?> - <?php echo date('d M Y', strtotime($trip['tgl_pulang'])); ?></p>
                    </div>
                    <div class="info-item">
                        <label>Jumlah Peserta</label>
                        <p><?php echo $trip['jumlah_peserta']; ?> Orang</p>
                    </div>
                    <div class="info-item">
                        <label>Pemesan</label>
                        <p><?php echo $trip['nama']; ?> (@<?php echo $trip['username']; ?>)</p>
                    </div>
                    <div class="info-item">
                        <label>No. HP / WhatsApp</label>
                        <p><?php echo $trip['no_hp']; ?></p>
                    </div>
                </div>
                <div style="margin-top:20px;">
                    <label style="font-size: 12px; color: #888;">CATATAN KHUSUS:</label>
                    <p style="background: #fdfdfd; padding: 10px; border-left: 4px solid var(--primary); font-style: italic;">
                        "<?php echo $trip['catatan'] ? $trip['catatan'] : 'Tidak ada catatan'; ?>"
                    </p>
                </div>
            </div>

            <div class="card">
                <h3>Daftar Peserta Rombongan</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Usia</th>
                            <th>Alamat</th>
                            <th>Riwayat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $peserta = kueri("SELECT * FROM peserta_private WHERE id_private = $id_private");
                        $no = 1;
                        while($p = ambil($peserta)):
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo $p['nama']; ?></strong></td>
                            <td><?php echo $p['usia']; ?> Thn</td>
                            <td><?php echo $p['alamat']; ?></td>
                            <td><small><?php echo $p['riwayat']; ?></small></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>

        <div class="sidebar-content">
            
            <div class="card">
                <h3>Kelola Status Trip</h3>
                <form action="" method="POST">
                    <label>Ubah Status</label>
                    <select name="status_trip">
                        <option value="Belum Disetujui" <?php if($trip['status_trip'] == 'Belum Disetujui') echo 'selected'; ?>>Belum Disetujui</option>
                        <option value="Disetujui" <?php if($trip['status_trip'] == 'Disetujui') echo 'selected'; ?>>Disetujui</option>
                        <option value="Ditolak" <?php if($trip['status_trip'] == 'Ditolak') echo 'selected'; ?>>Ditolak</option>
                    </select>

                    <label>Harga Total (Rp)</label>
                    <input type="number" name="harga" value="<?php echo $trip['harga']; ?>" placeholder="Tentukan harga trip...">

                    <label>Minimal DP (Rp)</label>
                    <input type="number" name="harga_dp" value="<?php echo $trip['harga_dp']; ?>" placeholder="Tentukan minimal DP...">

                    <button type="submit" name="update_status" class="btn-save">Simpan Perubahan</button>
                </form>
            </div>

            <div class="card">
                <h3>Status Pembayaran</h3>
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="font-size: 24px; font-weight: bold; color: var(--primary);">
                        <?php echo $trip['status_bayar']; ?>
                    </div>
                </div>

                <label style="font-size: 12px; color: #888;">RIWAYAT PEMBAYARAN:</label>
                <table>
                    <?php
                    $payments = kueri("SELECT * FROM payment_private WHERE id_private = $id_private ORDER BY tgl_bayar DESC");
                    if(mysqli_num_rows($payments) > 0):
                        while($pay = ambil($payments)):
                    ?>
                    <tr>
                        <td>
                            <small><?php echo date('d/m/y', strtotime($pay['tgl_bayar'])); ?></small><br>
                            <strong>Rp <?php echo number_format($pay['nominal']); ?></strong>
                        </td>
                        <td>
                            <a href="../gambar/payment/<?php echo $pay['bukti_bayar']; ?>" target="_blank">
                                <img src="../gambar/payment/<?php echo $pay['bukti_bayar']; ?>" class="img-bukti">
                            </a>
                        </td>
                        <td>
                            <span style="font-size: 11px;" class="badge <?php echo ($pay['status'] == 'Diverifikasi') ? 'bg-green' : 'bg-orange'; ?>">
                                <?php echo $pay['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="3" style="text-align:center; color:#ccc;">Belum ada pembayaran</td></tr>
                    <?php endif; ?>
                </table>
            </div>

        </div>
    </div>
</div>

</body>
</html>