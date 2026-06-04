<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'fungsi.php'; 

$id = $_GET['id'] ?? '';
if (empty($id)) { echo "<script>window.location='index.php?menu=pembatalan&tab=peserta';</script>"; exit; }

// --- AMBIL DATA DETAIL UNTUK TAMPILAN & NOTIFIKASI (JOIN ke tabel TUJUAN dan AKUN) ---
$query = kueri("SELECT bp.*, p.nama AS nama_peserta, tj.tujuan, a.username, b.id_booking, b.status AS status_order, t.harga_dp, b.id_akun
                FROM batal_peserta bp
                JOIN detail d ON bp.id_detail = d.id_detail
                JOIN peserta_open p ON d.id_peserta = p.id_peserta
                JOIN booking b ON d.id_booking = b.id_booking
                JOIN trip t ON b.id_trip = t.id_trip
                JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                JOIN akun a ON b.id_akun = a.id_akun
                WHERE bp.id_pembatalan = '$id'");
$data = ambil($query);

// Cek jika data tidak ditemukan (bisa terjadi jika data sudah dihapus atau ID salah)
if (!$data) { 
    echo "<script>alert('Data tidak ditemukan atau sudah diproses sebelumnya.'); window.location='index.php?menu=pembatalan&tab=peserta';</script>"; 
    exit; 
}

$id_booking_terkait = $data['id_booking'];
$id_akun_user = $data['id_akun'];
$nama_peserta_batal = $data['nama_peserta'];
$tujuan_trip = $data['tujuan'];

// --- LOGIKA EKSEKUSI ---
if (isset($_POST['update_pembatalan'])) {
    $status_pilihan = $_POST['status_verifikasi']; // Disetujui atau Ditolak
    $waktu_sekarang = date('Y-m-d H:i:s');
    $pesan_notif = "";
    
    // Jika admin memilih "Biarkan Menunggu" tidak perlu memproses database dan notifikasi
    if ($status_pilihan == 'Menunggu') {
        echo "<script>window.location='index.php?menu=pembatalan&tab=peserta';</script>";
        exit;
    }
    
    // **SKENARIO 1: Pengajuan Pembatalan Anggota DITOLAK Admin**
    if ($status_pilihan == 'Ditolak') {
        // Logika kembali ke awal: Hapus baris pengajuan di tabel batal_peserta karena ditolak
        kueri("DELETE FROM batal_peserta WHERE id_pembatalan = '$id'");
        
        // Buat pesan notifikasi penolakan untuk user
        $pesan_notif = "Pengajuan pembatalan untuk peserta bernama " . $nama_peserta_batal . " pada trip " . $tujuan_trip . " (Booking ID: #" . $id_booking_terkait . ") telah DITOLAK oleh admin. Anggota tersebut tetap terdaftar sebagai peserta aktif.";
        
        kueri("INSERT INTO notif (pesan, id_akun) VALUES ('$pesan_notif', $id_akun_user)");
        
        echo "<script>alert('Pembatalan Ditolak! Data pengajuan dihapus & user telah dinotifikasi.'); window.location='index.php?menu=pembatalan&tab=peserta';</script>";
        exit;
    }
    
    // **SKENARIO 2 & 3: Pengajuan Pembatalan Anggota DISETUJUI Admin**
    if ($status_pilihan == 'Disetujui') {
        // 1. Update status dan tgl_verifikasi di tabel batal_peserta
        kueri("UPDATE batal_peserta SET 
                status_verifikasi = 'Disetujui', 
                tgl_verifikasi = '$waktu_sekarang' 
               WHERE id_pembatalan = '$id'");

        $id_det = $data['id_detail'];
        $harga_dp_trip = $data['harga_dp'];

        // Hapus data dari detail (peserta resmi keluar)
        kueri("DELETE FROM detail WHERE id_detail = '$id_det'");

        // Kurangi jumlah peserta di tabel booking
        kueri("UPDATE booking SET jumlah_peserta = jumlah_peserta - 1 WHERE id_booking = '$id_booking_terkait'");

        // Ambil sisa kuota rombongan setelah dikurangi
        $cek_sisa = ambil(kueri("SELECT jumlah_peserta FROM booking WHERE id_booking = '$id_booking_terkait'"));
        $sisa_peserta = $cek_sisa['jumlah_peserta'] ?? 0;
        
        if ($sisa_peserta <= 0) {
            // **SKENARIO 3: Anggota habis (0), otomatis membatalkan seluruh invoice booking grup**
            $cek_uang = ambil(kueri("SELECT SUM(nominal) AS total FROM payment_open 
                                     WHERE id_booking = '$id_booking_terkait' AND status = 'Diverifikasi'"));
            $total_bayar = $cek_uang['total'] ?? 0;

            // Tentukan status akhir booking utama grup (Refund jika bayar > DP, selain itu Dibatalkan)
            $status_akhir_booking = ($total_bayar > $harga_dp_trip) ? "Refund" : "Dibatalkan";
            kueri("UPDATE booking SET status = '$status_akhir_booking' WHERE id_booking = '$id_booking_terkait'");

            $pesan_notif = "Pembatalan peserta " . $nama_peserta_batal . " disetujui. Karena tidak ada anggota lain yang tersisa, seluruh pesanan Anda untuk trip " . $tujuan_trip . " (Booking ID: #" . $id_booking_terkait . ") otomatis diubah menjadi " . strtoupper($status_akhir_booking) . ".";
        } else {
            // **SKENARIO 2: Masih ada sisa peserta lain di dalam rombongan grup**
            $pesan_notif = "Pengajuan pembatalan untuk peserta bernama " . $nama_peserta_batal . " pada trip " . $tujuan_trip . " (Booking ID: #" . $id_booking_terkait . ") telah DISETUJUI. Sisa anggota rombongan Anda kini menjadi " . $sisa_peserta . " orang.";
        }

        // Masukkan notifikasi ke database
        kueri("INSERT INTO notif (pesan, id_akun) VALUES ('$pesan_notif', $id_akun_user)");
        
        echo "<script>alert('Pembatalan Berhasil Disetujui, Status Booking Diperbarui, & user telah dinotifikasi!'); window.location='index.php?menu=pembatalan&tab=peserta';</script>";
        exit;
    }
}
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
                            <option value="Disetujui">Setujui (Hapus Peserta & Sesuaikan Grup)</option>
                            <option value="Ditolak">Tolak Pembatalan (Hapus Pengajuan)</option>
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
