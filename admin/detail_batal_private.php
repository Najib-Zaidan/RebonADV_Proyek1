<?php
include "fungsi.php"; 

$id = $_GET['id'] ?? '';
if (empty($id)) { echo "<script>window.location='index.php?menu=pembatalan&tab=private';</script>"; exit; }

// --- LOGIKA EKSEKUSI ---
if (isset($_POST['update_pembatalan'])) {
    $status_pilihan = $_POST['status_manual'];
    
    // Update status di tabel pembatalan private
    kueri("UPDATE batal_private SET status = 1 WHERE id_batal = '$id'");
    
    // Update status di tabel private_trip utama
    $cari_trip = ambil(kueri("SELECT id_private FROM batal_private WHERE id_batal = '$id'"));
    $id_pri = $cari_trip['id_private'];
    kueri("UPDATE private_trip SET status_bayar = '$status_pilihan' WHERE id_private = '$id_pri'");

    echo "<script>alert('Status Berhasil Diperbarui!'); window.location='index.php?menu=pembatalan&tab=private';</script>";
}

// --- AMBIL DATA DETAIL ---
$query = kueri("SELECT bp.*, pt.tujuan, pt.nama AS penanggung_jawab, a.username, pt.id_private, pt.status_bayar AS status_order, pt.jumlah_peserta, pt.harga_dp
                FROM batal_private bp
                JOIN private_trip pt ON bp.id_private = pt.id_private
                JOIN akun a ON pt.id_akun = a.id_akun
                WHERE bp.id_batal = '$id'");
$data = ambil($query);
$id_private_terkait = $data['id_private'];

// Hitung Pembayaran Terverifikasi
$cek_bayar = ambil(kueri("SELECT SUM(nominal) AS total_masuk FROM payment_private 
                          WHERE id_private = '$id_private_terkait' AND status = 'Diverifikasi'"));
$total_masuk = $cek_bayar['total_masuk'] ?? 0;

// Handle harga_dp jika NULL
$harga_dp = $data['harga_dp'] ?? 0;

// Rekomendasi Status
$rekomendasi = ($data['status_order'] == 'Lunas' || ($total_masuk > 0 && $total_masuk > $harga_dp)) ? "Refund" : "Dibatalkan";
?>

<div style="padding: 20px; font-family: 'Poppins', sans-serif;">
    <div style="margin-bottom: 20px;">
        <a href="index.php?menu=pembatalan&tab=private" style="text-decoration: none; color: #6b3df5; font-weight: bold; display: inline-flex; align-items: center; gap: 8px;">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div style="background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(107, 61, 245, 0.1); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #321180 0%, #6b3df5 100%); padding: 25px; color: white;">
            <h2 style="margin: 0; font-size: 22px;">Kelola Pembatalan Private #<?php echo $data['id_batal']; ?></h2>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">P. Jawab: <?php echo $data['penanggung_jawab']; ?> (@<?php echo $data['username']; ?>) | Tujuan: <?php echo $data['tujuan']; ?></p>
        </div>

        <div style="padding: 30px;">
            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 35px;">
                
                <div>
                    <h4 style="color: #6b3df5; border-bottom: 2px solid #f0f0ff; padding-bottom: 10px;">Detail Pembayaran</h4>
                    <div style="background: #f9f8ff; padding: 20px; border-radius: 15px; border: 1px solid #e0dbff;">
                        <table style="width: 100%; border-spacing: 0 12px;">
                            <tr>
                                <td style="color: #888;">Total Terverifikasi</td>
                                <td style="font-weight: bold; color: #27ae60; text-align: right;">Rp <?php echo number_format($total_masuk, 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Batas Minimal DP</td>
                                <td style="font-weight: bold; color: #333; text-align: right;">Rp <?php echo number_format($harga_dp, 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Status Saat Ini</td>
                                <td style="text-align: right;"><span style="background: #321180; color: white; padding: 3px 10px; border-radius: 5px; font-size: 12px;"><?php echo $data['status_order']; ?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div>
                    <h4 style="color: #6b3df5; border-bottom: 2px solid #f0f0ff; padding-bottom: 10px;">Aksi Pembatalan</h4>
                    <p style="font-size: 14px; color: #666; margin-bottom: 15px;">Alasan: <i>"<?php echo $data['alasan']; ?>"</i></p>
                    
                    <form method="POST">
                        <label style="font-size: 13px; font-weight: bold; color: #321180;">Pilih Status Akhir:</label>
                        <select name="status_manual" style="width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #e0dbff; margin: 10px 0 20px 0; outline: none; font-family: inherit;">
                            <option value="Dibatalkan" <?php echo ($rekomendasi == 'Dibatalkan') ? 'selected' : ''; ?>>Dibatalkan (Tanpa Refund)</option>
                            <option value="Refund" <?php echo ($rekomendasi == 'Refund') ? 'selected' : ''; ?>>Refund (Kembalikan Uang)</option>
                            <option value="DP" <?php echo ($data['status_order'] == 'DP') ? 'selected' : ''; ?>>Tetap DP (Tolak Batal)</option>
                            <option value="Belum Bayar">Belum Bayar</option>
                        </select>

                        <?php if ($data['status'] == 0): ?>
                            <button type="submit" name="update_pembatalan" style="width: 100%; background: #321180; color: white; border: none; padding: 15px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(50, 17, 128, 0.2);">
                                Update & Selesaikan
                            </button>
                        <?php else: ?>
                            <div style="background: #eafff2; color: #27ae60; text-align: center; padding: 15px; border-radius: 10px; font-weight: bold; border: 1px solid #27ae60;">
                                ✔ Pembatalan Selesai Diproses
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
