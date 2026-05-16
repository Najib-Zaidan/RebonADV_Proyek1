<?php
include "fungsi.php"; 

$id = $_GET['id'] ?? '';
if (empty($id)) { echo "<script>window.location='index.php?menu=pembatalan&tab=peserta';</script>"; exit; }

// --- LOGIKA EKSEKUSI ---
if (isset($_POST['update_pembatalan'])) {
    $status_pilihan = $_POST['status_verifikasi']; // Disetujui atau Ditolak
    $waktu_sekarang = date('Y-m-d H:i:s');
    
    // 1. Update status dan tgl_verifikasi di tabel batal_peserta
    kueri("UPDATE batal_peserta SET 
            status_verifikasi = '$status_pilihan', 
            tgl_verifikasi = '$waktu_sekarang' 
           WHERE id_pembatalan = '$id'");
    
    // 2. Jika Disetujui, maka eksekusi pembersihan data
    if ($status_pilihan == 'Disetujui') {
        $data_bantu = ambil(kueri("SELECT id_detail FROM batal_peserta WHERE id_pembatalan = '$id'"));
        $id_det = $data_bantu['id_detail'];

        // Ambil ID Booking dan ID Trip terkait sebelum dihapus dengan JOIN ke tabel TUJUAN
        $cari_book = ambil(kueri("SELECT b.id_booking, b.id_trip, t.harga_dp 
                                 FROM detail d 
                                 JOIN booking b ON d.id_booking = b.id_booking 
                                 JOIN trip t ON b.id_trip = t.id_trip
                                 JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                                 WHERE d.id_detail = '$id_det'"));
        $id_book = $cari_book['id_booking'];
        $harga_dp_trip = $cari_book['harga_dp'];

        // Hapus data dari detail (peserta resmi keluar)
        kueri("DELETE FROM detail WHERE id_detail = '$id_det'");

        // Kurangi jumlah peserta di tabel booking
        kueri("UPDATE booking SET jumlah_peserta = jumlah_peserta - 1 WHERE id_booking = '$id_book'");

        // --- CEK APAKAH PESERTA SUDAH HABIS (0) ---
        $cek_sisa = ambil(kueri("SELECT jumlah_peserta FROM booking WHERE id_booking = '$id_book'"));
        
        if ($cek_sisa['jumlah_peserta'] <= 0) {
            // Hitung total uang yang sudah masuk dan diverifikasi
            $cek_uang = ambil(kueri("SELECT SUM(nominal) AS total FROM payment_open 
                                     WHERE id_booking = '$id_book' AND status = 'Diverifikasi'"));
            $total_bayar = $cek_uang['total'] ?? 0;

            // Tentukan status booking (Refund jika bayar > DP, selain itu Dibatalkan)
            $status_akhir_booking = ($total_bayar > $harga_dp_trip) ? "Refund" : "Dibatalkan";

            // Update status booking utama karena peserta sudah kosong
            kueri("UPDATE booking SET status = '$status_akhir_booking' WHERE id_booking = '$id_book'");
        }
    }

    echo "<script>alert('Pembatalan Berhasil Diproses & Status Booking Diperbarui!'); window.location='index.php?menu=pembatalan&tab=peserta';</script>";
}

// --- AMBIL DATA DETAIL UNTUK TAMPILAN (JOIN ke tabel TUJUAN) ---
$query = kueri("SELECT bp.*, p.nama AS nama_peserta, tj.tujuan, a.username, b.id_booking, b.status AS status_order, t.harga_dp
                FROM batal_peserta bp
                JOIN detail d ON bp.id_detail = d.id_detail
                JOIN peserta_open p ON d.id_peserta = p.id_peserta
                JOIN booking b ON d.id_booking = b.id_booking
                JOIN trip t ON b.id_trip = t.id_trip
                JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                JOIN akun a ON b.id_akun = a.id_akun
                WHERE bp.id_pembatalan = '$id'");
$data = ambil($query);

if (!$data) { echo "<script>alert('Data tidak ditemukan'); window.location='index.php?menu=pembatalan&tab=peserta';</script>"; exit; }
?>
<div style="padding: 20px; font-family: 'Poppins', sans-serif;">
    <div style="margin-bottom: 20px;">
        <a href="index.php?menu=pembatalan&tab=peserta" style="text-decoration: none; color: #6b3df5; font-weight: bold; display: inline-flex; align-items: center; gap: 8px;">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Kembali ke Daftar Peserta
        </a>
    </div>

    <div style="background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(107, 61, 245, 0.1); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #321180 0%, #6b3df5 100%); padding: 25px; color: white;">
            <h2 style="margin: 0; font-size: 22px;">Verifikasi Pembatalan Peserta #<?php echo $id; ?></h2>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">Peserta: <b><?php echo $data['nama_peserta']; ?></b> | Akun Pemesan: @<?php echo $data['username']; ?></p>
        </div>

        <div style="padding: 30px;">
            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 35px;">
                
                <div>
                    <h4 style="color: #6b3df5; border-bottom: 2px solid #f0f0ff; padding-bottom: 10px;">Informasi Trip</h4>
                    <div style="background: #f9f8ff; padding: 20px; border-radius: 15px; border: 1px solid #e0dbff;">
                        <table style="width: 100%; border-spacing: 0 12px;">
                            <tr>
                                <td style="color: #888;">Tujuan Trip</td>
                                <td style="font-weight: bold; color: #333; text-align: right;"><?php echo $data['tujuan']; ?></td>
                            </tr>
                            <tr>
                                <td style="color: #888;">ID Booking Grup</td>
                                <td style="font-weight: bold; color: #333; text-align: right;">#<?php echo $data['id_booking']; ?></td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Status Pembayaran Grup</td>
                                <td style="text-align: right;"><span style="background: #321180; color: white; padding: 3px 10px; border-radius: 5px; font-size: 12px;"><?php echo $data['status_order']; ?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div>
                    <h4 style="color: #6b3df5; border-bottom: 2px solid #f0f0ff; padding-bottom: 10px;">Aksi Verifikasi</h4>
                    <p style="font-size: 14px; color: #666; margin-bottom: 15px;">Alasan Pembatalan:<br> 
                       <i style="color: #333;">"<?php echo $data['alasan_batal']; ?>"</i>
                    </p>
                    
                    <form method="POST">
                        <label style="font-size: 13px; font-weight: bold; color: #321180;">Tindakan Admin:</label>
                        <select name="status_verifikasi" style="width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #e0dbff; margin: 10px 0 20px 0; outline: none; font-family: inherit;">
                            <option value="Menunggu" <?php echo ($data['status_verifikasi'] == 'Menunggu') ? 'selected' : ''; ?>>Biarkan Menunggu</option>
                            <option value="Disetujui">Setujui (Hapus Peserta & Kurangi Kuota)</option>
                            <option value="Ditolak">Tolak Pembatalan</option>
                        </select>

                        <?php if ($data['status_verifikasi'] == 'Menunggu'): ?>
                            <button type="submit" name="update_pembatalan" style="width: 100%; background: #321180; color: white; border: none; padding: 15px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(50, 17, 128, 0.2);">
                                Proses Sekarang
                            </button>
                        <?php else: ?>
                            <div style="background: #eafff2; color: #27ae60; text-align: center; padding: 15px; border-radius: 10px; font-weight: bold; border: 1px solid #27ae60;">
                                ✔ Status: <?php echo $data['status_verifikasi']; ?>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
