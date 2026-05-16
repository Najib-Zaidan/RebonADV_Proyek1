<?php
include "fungsi.php"; 

$id = $_GET['id'] ?? '';
if (empty($id)) { echo "<script>window.location='index.php?menu=pembatalan';</script>"; exit; }

// --- LOGIKA EKSEKUSI ---
if (isset($_POST['update_pembatalan'])) {
    $status_pilihan = $_POST['status_manual'];
    
    // Ambil ID Booking terlebih dahulu sebelum ada kemungkinan baris dihapus
    $cari_booking = ambil(kueri("SELECT id_booking FROM batal_open WHERE id_batal = '$id'"));
    $id_book = $cari_booking['id_booking'];

    // LOGIKA TAMBAHAN: Jika admin memilih status "DP" (Tolak Batal)
    if ($status_pilihan == 'DP') {
        // Hapus baris pengajuan di tabel batal_open karena ditolak
        kueri("DELETE FROM batal_open WHERE id_batal = '$id'");
        
        // Kembalikan status di tabel booking utama menjadi DP
        kueri("UPDATE booking SET status = 'DP' WHERE id_booking = '$id_book'");
        
        echo "<script>alert('Pembatalan Ditolak! Data pengajuan dihapus.'); window.location='index.php?menu=pembatalan&tab=open';</script>";
        exit;
    }

    // --- LOGIKA ASLI (JIKA DISETUJUI/REFUND/LAINNYA) ---
    // Update status di tabel pembatalan menjadi selesai (status = 1)
    kueri("UPDATE batal_open SET status = 1 WHERE id_batal = '$id'");
    
    // Update status di tabel booking utama sesuai pilihan (Dibatalkan/Refund/Belum Bayar)
    kueri("UPDATE booking SET status = '$status_pilihan' WHERE id_booking = '$id_book'");

    echo "<script>alert('Status Berhasil Diperbarui!'); window.location='index.php?menu=pembatalan&tab=open';</script>";
}

// --- AMBIL DATA DETAIL (JOIN ke tabel TUJUAN) ---
$query = kueri("SELECT bo.*, tj.tujuan, a.username, b.id_booking, b.status AS status_order, b.jumlah_peserta, t.harga_dp
                FROM batal_open bo
                JOIN booking b ON bo.id_booking = b.id_booking
                JOIN trip t ON b.id_trip = t.id_trip
                JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                JOIN akun a ON b.id_akun = a.id_akun
                WHERE bo.id_batal = '$id'");
$data = ambil($query);
$id_booking_terkait = $data['id_booking'];

// Hitung Pembayaran Terverifikasi
$cek_bayar = ambil(kueri("SELECT SUM(nominal) AS total_masuk FROM payment_open 
                          WHERE id_booking = '$id_booking_terkait' AND status = 'Diverifikasi'"));
$total_masuk = $cek_bayar['total_masuk'] ?? 0;

// Rekomendasi Status
$rekomendasi = ($data['status_order'] == 'Lunas' || $total_masuk > $data['harga_dp']) ? "Refund" : "Dibatalkan";
?>

<div style="padding: 20px; font-family: 'Poppins', sans-serif;">
    <!-- Tombol Kembali yang Hilang -->
    <div style="margin-bottom: 20px;">
        <a href="index.php?menu=pembatalan&tab=open" style="text-decoration: none; color: #6b3df5; font-weight: bold; display: inline-flex; align-items: center; gap: 8px;">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div style="background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(107, 61, 245, 0.1); overflow: hidden;">
        <!-- Header Gradasi Ungu Asik -->
        <div style="background: linear-gradient(135deg, #321180 0%, #6b3df5 100%); padding: 25px; color: white;">
            <h2 style="margin: 0; font-size: 22px;">Kelola Pembatalan #<?php echo $data['id_batal']; ?></h2>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">Pemesan: @<?php echo $data['username']; ?> | Trip: <?php echo $data['tujuan']; ?></p>
        </div>

        <div style="padding: 30px;">
            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 35px;">
                
                <!-- Info Trip & Keuangan -->
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
                                <td style="font-weight: bold; color: #333; text-align: right;">Rp <?php echo number_format($data['harga_dp'], 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Status Saat Ini</td>
                                <td style="text-align: right;"><span style="background: #321180; color: white; padding: 3px 10px; border-radius: 5px; font-size: 12px;"><?php echo $data['status_order']; ?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Form Aksi Admin -->
                <div>
                    <h4 style="color: #6b3df5; border-bottom: 2px solid #f0f0ff; padding-bottom: 10px;">Verifikasi Pembatalan</h4>
                    <p style="font-size: 14px; color: #666; margin-bottom: 15px;">Alasan: <i>"<?php echo $data['alasan']; ?>"</i></p>
                    
                    <form method="POST">
                        <label style="font-size: 13px; font-weight: bold; color: #321180;">Tindakan Admin:</label>
                        <select name="status_manual" style="width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #e0dbff; margin: 10px 0 20px 0; outline: none; font-family: inherit;">
                            <option value="<?php echo $rekomendasi; ?>">
                                Terima Verifikasi (Status: <?php echo $rekomendasi; ?>)
                            </option>
                            
                            <option value="DP">
                                Tolak Verifikasi (Hapus Pengajuan)
                            </option>
                        </select>

                        <?php if ($data['status'] == 0): ?>
                            <button type="submit" name="update_pembatalan" style="width: 100%; background: #321180; color: white; border: none; padding: 15px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(50, 17, 128, 0.2);">
                                Proses Verifikasi
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
