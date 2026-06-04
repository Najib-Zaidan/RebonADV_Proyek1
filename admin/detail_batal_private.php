<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'fungsi.php'; 

$id = $_GET['id'] ?? '';
if (empty($id)) { echo "<script>window.location='index.php?menu=pembatalan&tab=private';</script>"; exit; }

// --- AMBIL DATA DETAIL DI AWAL SEBELUM EKSEKUSI (JOIN ke tabel PRIVATE_TRIP & AKUN) ---
$query = kueri("SELECT bp.*, pt.tujuan, pt.nama AS penanggung_jawab, a.username, pt.id_private, pt.status_bayar AS status_order, pt.jumlah_peserta, pt.harga, pt.harga_dp, pt.id_akun
                FROM batal_private bp
                JOIN private_trip pt ON bp.id_private = pt.id_private
                JOIN akun a ON pt.id_akun = a.id_akun
                WHERE bp.id_batal = '$id'");
$data = ambil($query);

if (!$data) {
    die("Data pengajuan pembatalan private trip tidak ditemukan.");
}

$id_private_terkait = $data['id_private'];
$id_akun_user = $data['id_akun'];
$tujuan_trip = $data['tujuan'];
$jumlah_peserta = $data['jumlah_peserta'];

// Hitung Pembayaran Terverifikasi dari database payment_private
$cek_bayar = ambil(kueri("SELECT SUM(nominal) AS total_masuk FROM payment_private 
                          WHERE id_private = '$id_private_terkait' AND status = 'Diverifikasi'"));
$total_masuk = $cek_bayar['total_masuk'] ?? 0;

// Kalkulasi akumulasi biaya berdasarkan data di tabel private_trip
$total_biaya_lunas = $data['harga'] ?? 0;
$total_biaya_dp = $data['harga_dp'] ?? 0;

// Rekomendasi Status jika pembatalan DISETUJUI (Refund jika sudah mencapai batas minimal DP)
$rekomendasi = ($total_masuk > $total_biaya_dp && $total_masuk > 0) ? "Refund" : "Dibatalkan";

// --- LOGIKA EKSEKUSI ---
if (isset($_POST['update_pembatalan'])) {
    $status_pilihan = $_POST['status_manual'];
    $pesan_notif = "";

    // **SKENARIO 1: Admin memilih status "TOLAK" (Tolak Pembatalan)**
    if ($status_pilihan == 'TOLAK') {
        // Hapus baris pengajuan di tabel batal_private karena ditolak
        kueri("DELETE FROM batal_private WHERE id_batal = '$id'");
        
        // --- LOGIKA PENENTUAN STATUS ASLI SECARA PRESISI (ENUM MATCHING) ---
        if ($total_masuk >= $total_biaya_lunas && $total_biaya_lunas > 0) {
            $status_sebenarnya = 'Lunas';
        } elseif ($total_masuk >= $total_biaya_dp && $total_biaya_dp > 0) {
            $status_sebenarnya = 'DP';
        } elseif ($total_masuk > 0) {
            // Jika ada uang masuk diverifikasi tetapi belum memenuhi standar minimal DP
            $status_sebenarnya = 'Bayar non-DP';
        } else {
            // Jika sama sekali tidak ada uang masuk terverifikasi
            $status_sebenarnya = 'Belum Bayar';
        }
        
        // Kembalikan status bayar di tabel private_trip utama secara objektif
        kueri("UPDATE private_trip SET status_bayar = '$status_sebenarnya' WHERE id_private = '$id_private_terkait'");
        
        // Buat notifikasi penolakan ke tabel notif
        $pesan_notif = "Pengajuan pembatalan Private Trip Anda ke " . $tujuan_trip . " (Booking ID: #" . $id_private_terkait . ") telah DITOLAK oleh admin. Status pesanan Anda kembali aktif berstatus: " . strtoupper($status_sebenarnya) . ".";
        kueri("INSERT INTO notif (pesan, id_akun) VALUES ('$pesan_notif', $id_akun_user)");

        echo "<script>alert('Pembatalan Ditolak! Data pengajuan dihapus dan status pesanan disesuaikan menjadi $status_sebenarnya.'); window.location='index.php?menu=pembatalan&tab=private';</script>";
        exit;
    }

    // **SKENARIO 2 & 3: Terima Pembatalan (Refund / Dibatalkan Biasa)**
    // Update status di tabel pembatalan private menjadi selesai (status = 1)
    kueri("UPDATE batal_private SET status = 1 WHERE id_batal = '$id'");
    
    // Update status_bayar di tabel private_trip utama sesuai pilihan admin (Refund / Dibatalkan)
    kueri("UPDATE private_trip SET status_bayar = '$status_pilihan' WHERE id_private = '$id_private_terkait'");

    if ($status_pilihan == 'Refund') {
        $pesan_notif = "Pengajuan pembatalan Private Trip ke " . $tujuan_trip . " (Booking ID: #" . $id_private_terkait . ") telah DISETUJUI. Status pesanan diubah menjadi REFUND. Proses pengembalian uang akan segera diproses oleh admin.";
    } else {
        $pesan_notif = "Pengajuan pembatalan Private Trip ke " . $tujuan_trip . " (Booking ID: #" . $id_private_terkait . ") telah DISETUJUI. Status pesanan Anda saat ini resmi DIBATALKAN.";
    }

    // Masukkan notifikasi ke database
    kueri("INSERT INTO notif (pesan, id_akun) VALUES ('$pesan_notif', $id_akun_user)");

    echo "<script>alert('Status Berhasil Diperbarui dan user telah dinotifikasi!'); window.location='index.php?menu=pembatalan&tab=private';</script>";
    exit;
}
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
                    <h4 style="color: #6b3df5; border-bottom: 2px solid #f0f0ff; padding-bottom: 10px;">Detail Pembayaran Rombongan</h4>
                    <div style="background: #f9f8ff; padding: 20px; border-radius: 15px; border: 1px solid #e0dbff;">
                        <table style="width: 100%; border-spacing: 0 12px;">
                            <tr>
                                <td style="color: #888;">Jumlah Peserta</td>
                                <td style="font-weight: bold; color: #333; text-align: right;"><?php echo $jumlah_peserta; ?> Orang</td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Total Uang Terverifikasi</td>
                                <td style="font-weight: bold; color: #27ae60; text-align: right;">Rp <?php echo number_format($total_masuk, 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Total Biaya Lunas</td>
                                <td style="font-weight: bold; color: #333; text-align: right;">Rp <?php echo number_format($total_biaya_lunas, 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Batas Minimal DP</td>
                                <td style="font-weight: bold; color: #333; text-align: right;">Rp <?php echo number_format($total_biaya_dp, 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Status Saat Ini</td>
                                <td style="text-align: right;"><span style="background: #321180; color: white; padding: 3px 10px; border-radius: 5px; font-size: 12px;"><?php echo $data['status_order']; ?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div>
                    <h4 style="color: #6b3df5; border-bottom: 2px solid #f0f0ff; padding-bottom: 10px;">Verifikasi Pembatalan</h4>
                    <p style="font-size: 14px; color: #666; margin-bottom: 15px;">Alasan User: <i>"<?php echo $data['alasan']; ?>"</i></p>
                    
                    <form method="POST">
                        <label style="font-size: 13px; font-weight: bold; color: #321180;">Tindakan Admin:</label>
                        <select name="status_manual" style="width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #e0dbff; margin: 10px 0 20px 0; outline: none; font-family: inherit;">
                            <option value="<?php echo $rekomendasi; ?>">
                                Terima Verifikasi (Status: <?php echo $rekomendasi; ?>)
                            </option>
                            
                            <option value="TOLAK">
                                Tolak Verifikasi (Hapus Pengajuan & Hitung Uang Status Asli)
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
