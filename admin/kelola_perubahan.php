<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'fungsi.php';

// 1. Ambil ID Log Perubahan (id_ubah) dari URL
if (!isset($_GET['id_ubah']) || empty($_GET['id_ubah'])) {
    echo "<script>alert('ID pengajuan perubahan tidak valid!'); window.location.href='index.php?menu=private&sub=pengajuan_perubahan';</script>";
    exit;
}

$id_ubah = mysqli_real_escape_string($konek, $_GET['id_ubah']);

// 2. Logika Proses Persetujuan (Aksi Admin)
if (isset($_POST['proses_perubahan'])) {
    $aksi = $_POST['aksi_perubahan']; // 'Setujui' atau 'Tolak'
    $id_private_target = $_POST['id_private'];
    
    // Ambil data perubahan untuk keperluan update jika disetujui
    $q_ambil_baru = kueri("SELECT * FROM ubah_private WHERE id_ubah = $id_ubah LIMIT 1");
    $d_baru = ambil($q_ambil_baru);

    if ($aksi == 'Setujui') {
        // Update data utama di tabel private_trip dengan data baru dari ubah_private
        $nama_b      = mysqli_real_escape_string($konek, $d_baru['nama']);
        $no_hp_b     = mysqli_real_escape_string($konek, $d_baru['no_hp']);
        $tujuan_b    = mysqli_real_escape_string($konek, $d_baru['tujuan']);
        $tgl_ber_b   = $d_baru['tgl_berangkat'];
        $tgl_pul_b   = $d_baru['tgl_pulang'];
        $jumlah_b    = $d_baru['jumlah_peserta'];
        $catatan_b   = mysqli_real_escape_string($konek, $d_baru['catatan']);

        // Jalankan update ke tabel utama, kembalikan status_trip ke 'Belum Disetujui' agar admin bisa meninjau ulang harganya jika diperlukan
        $update_utama = kueri("UPDATE private_trip SET 
                               nama = '$nama_b',
                               no_hp = '$no_hp_b',
                               tujuan = '$tujuan_b',
                               tgl_berangkat = '$tgl_ber_b',
                               tgl_pulang = '$tgl_pul_b',
                               jumlah_peserta = '$jumlah_b',
                               catatan = '$catatan_b',
                               status_trip = 'Belum Disetujui' 
                               WHERE id_private = $id_private_target");
        
        // Ubah status log perubahan menjadi 1 (Selesai diproses)
        $update_log = kueri("UPDATE ubah_private SET status = 1 WHERE id_ubah = $id_ubah");

        if ($update_utama && $update_log) {
            // [LOGIKA NOTIFIKASI - DISETUJUI]
            // Mengambil id_akun pemilik trip berdasarkan target tabel private_trip
            $q_pemilik = kueri("SELECT id_akun FROM private_trip WHERE id_private = $id_private_target LIMIT 1");
            if (mysqli_num_rows($q_pemilik) > 0) {
                $d_pemilik = ambil($q_pemilik);
                $id_user_penerima = $d_pemilik['id_akun'];
                $pesan_notif = "Pengajuan perubahan data untuk Private Trip Anda ke destinasi $tujuan_b telah DISETUJUI oleh admin.";
                
                // Menyesuaikan struktur tabel notif (id_akun, pesan, dibaca = 0)
                kueri("INSERT INTO notif (id_akun, pesan, dibaca) VALUES ($id_user_penerima, '$pesan_notif', 0)");
            }

            echo "<script>alert('Pengajuan perubahan DISETUJUI. Data master trip telah diperbarui!'); window.location.href='detail_private.php?id=$id_private_target';</script>";
            exit;
        }
    } else {
        // [LOGIKA NOTIFIKASI - DITOLAK]
        // Mengambil id_akun pemilik trip dan data tujuan sebelum record ubah_private dihapus dari database
        $q_pemilik = kueri("SELECT pt.id_akun, up.tujuan FROM private_trip pt 
                            JOIN ubah_private up ON pt.id_private = up.id_private 
                            WHERE up.id_ubah = $id_ubah LIMIT 1");
        if (mysqli_num_rows($q_pemilik) > 0) {
            $d_pemilik = ambil($q_pemilik);
            $id_user_penerima = $d_pemilik['id_akun'];
            $tujuan_tujuan = $d_pemilik['tujuan'];
            $pesan_notif = "Mohon maaf, pengajuan perubahan data untuk Private Trip Anda ke destinasi $tujuan_tujuan telah DITOLAK oleh admin.";
            
            // Menyesuaikan struktur tabel notif (id_akun, pesan, dibaca = 0)
            kueri("INSERT INTO notif (id_akun, pesan, dibaca) VALUES ($id_user_penerima, '$pesan_notif', 0)");
        }

        // REVISI: Jika ditolak, hapus baris pengajuan perubahan dari tabel ubah_private agar tidak tabrakan di sisi user
        $hapus_log = kueri("DELETE FROM ubah_private WHERE id_ubah = $id_ubah");
        
        if ($hapus_log) {
            echo "<script>alert('Pengajuan perubahan telah DITOLAK dan data pengajuan berhasil dihapus.'); window.location.href='index.php?menu=private&sub=pengajuan_perubahan';</script>";
            exit;
        }
    }
}

// 3. Ambil Data Log Perubahan dan Join ke Data Lama (Master) serta Akun
$query_review = kueri("SELECT 
                        up.id_ubah, up.id_private, up.nama AS nama_baru, up.no_hp AS no_hp_baru, 
                        up.tujuan AS tujuan_baru, up.tgl_berangkat AS tgl_berangkat_baru, 
                        up.tgl_pulang AS tgl_pulang_baru, up.jumlah_peserta AS jumlah_baru, 
                        up.catatan AS catatan_baru, up.tgl_pengajuan, up.status AS status_log,
                        pt.nama AS nama_asli, pt.no_hp AS no_hp_asli, pt.tujuan AS tujuan_asli, 
                        pt.tgl_berangkat AS tgl_berangkat_asli, pt.tgl_pulang AS tgl_pulang_asli, 
                        pt.jumlah_peserta AS jumlah_asli, pt.status_trip, pt.status_bayar,
                        a.username 
                       FROM ubah_private up
                       JOIN private_trip pt ON up.id_private = pt.id_private
                       JOIN akun a ON pt.id_akun = a.id_akun
                       WHERE up.id_ubah = $id_ubah LIMIT 1");

$review = ambil($query_review);

if (!$review) {
    die("Data pengajuan perubahan tidak ditemukan atau sudah diproses.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Perubahan Private Trip</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f2f7;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
        }
        .btn-kembali {
            text-decoration: none;
            background: #6c757d;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }
        .grid-detail {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        @media (max-width: 850px) {
            .grid-detail {
                grid-template-columns: 1fr;
            }
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 18px;
            color: #321180;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 8px;
            margin-top: 0;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .table-perbandingan {
            width: 100%;
            border-collapse: collapse;
        }
        .table-perbandingan th {
            background-color: #e1d8f5;
            color: #321180;
            text-align: left;
            padding: 10px;
            font-size: 14px;
        }
        .table-perbandingan td {
            padding: 12px 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            vertical-align: top;
        }
        .txt-asli {
            color: #777;
            text-decoration: line-through;
            font-size: 12px;
            display: block;
        }
        .txt-baru-berubah {
            color: #2e7d32;
            font-weight: bold;
            background-color: #e8f5e9;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }
        .txt-tetap {
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn-aksi {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .btn-setuju {
            background-color: #2e7d32;
        }
        .btn-setuju:hover {
            background-color: #235e26;
        }
        .btn-tolak {
            background-color: #c62828;
        }
        .btn-tolak:hover {
            background-color: #9a1f1f;
        }
        .alert-alasan {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
            padding: 12px;
            border-radius: 4px;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    
    <a href="index.php?menu=private&sub=pengajuan_perubahan" class="btn-kembali">
        ← Kembali ke Daftar Perubahan
    </a>

    <div style="margin-bottom: 20px;">
        <h2 style="color: #321180; margin: 0;">Review Pengajuan Perubahan Private Trip</h2>
        <p style="font-size: 14px; color: #666; margin: 5px 0 0 0;">
            Diajukan oleh: <strong>@<?php echo htmlspecialchars($review['username']); ?></strong> pada <?php echo date('d/m/Y H:i', strtotime($review['tgl_pengajuan'])); ?> WIB
        </p>
    </div>

    <div class="grid-detail">
        
        <div class="left-column">
            <div class="card">
                <div class="card-title">Perbandingan Data Trip (Lama vs Ajuan Baru)</div>
                <p style="font-size: 13px; color: #666; margin-bottom: 15px;">
                    * Bagian yang berwarna <span style="color: #2e7d32; font-weight: bold; background: #e8f5e9; padding: 2px 4px; border-radius:3px;">hijau</span> menandakan adanya perubahan data dari user.
                </p>

                <table class="table-perbandingan">
                    <thead>
                        <tr>
                            <th width="30%">Komponen Data</th>
                            <th width="70%">Keterangan Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Nama Kontak</strong></td>
                            <td>
                                <?php if($review['nama_baru'] !== $review['nama_asli']): ?>
                                    <span class="txt-asli">Asli: <?php echo htmlspecialchars($review['nama_asli']); ?></span>
                                    <span class="txt-baru-berubah"><?php echo htmlspecialchars($review['nama_baru']); ?></span>
                                <?php else: ?>
                                    <span class="txt-tetap"><?php echo htmlspecialchars($review['nama_asli']); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Nomor HP / WhatsApp</strong></td>
                            <td>
                                <?php if($review['no_hp_baru'] !== $review['no_hp_asli']): ?>
                                    <span class="txt-asli">Asli: <?php echo htmlspecialchars($review['no_hp_asli']); ?></span>
                                    <span class="txt-baru-berubah"><?php echo htmlspecialchars($review['no_hp_baru']); ?></span>
                                <?php else: ?>
                                    <span class="txt-tetap"><?php echo htmlspecialchars($review['no_hp_asli']); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Destinasi / Tujuan</strong></td>
                            <td>
                                <?php if($review['tujuan_baru'] !== $review['tujuan_asli']): ?>
                                    <span class="txt-asli">Asli: <?php echo htmlspecialchars($review['tujuan_asli']); ?></span>
                                    <span class="txt-baru-berubah"><?php echo htmlspecialchars($review['tujuan_baru']); ?></span>
                                <?php else: ?>
                                    <span class="txt-tetap"><?php echo htmlspecialchars($review['tujuan_asli']); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Jumlah Peserta</strong></td>
                            <td>
                                <?php if($review['jumlah_baru'] != $review['jumlah_asli']): ?>
                                    <span class="txt-asli">Asli: <?php echo $review['jumlah_asli']; ?> Orang</span>
                                    <span class="txt-baru-berubah"><?php echo $review['jumlah_baru']; ?> Orang</span>
                                <?php else: ?>
                                    <span class="txt-tetap"><?php echo $review['jumlah_asli']; ?> Orang</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Rencana Tanggal (PP)</strong></td>
                            <td>
                                <?php 
                                $tgl_asli_fmt = date('d F Y', strtotime($review['tgl_berangkat_asli'])) . " s/d " . date('d F Y', strtotime($review['tgl_pulang_asli']));
                                $tgl_baru_fmt = date('d F Y', strtotime($review['tgl_berangkat_baru'])) . " s/d " . date('d F Y', strtotime($review['tgl_pulang_baru']));
                                
                                $tgl_berubah = ($review['tgl_berangkat_baru'] !== $review['tgl_berangkat_asli'] || $review['tgl_pulang_baru'] !== $review['tgl_pulang_asli']);
                                ?>
                                <?php if($tgl_berubah): ?>
                                    <span class="txt-asli">Asli: <?php echo $tgl_asli_fmt; ?></span>
                                    <span class="txt-baru-berubah"><?php echo $tgl_baru_fmt; ?></span>
                                <?php else: ?>
                                    <span class="txt-tetap"><?php echo $tgl_asli_fmt; ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Alasan Perubahan</strong></td>
                            <td>
                                <div class="alert-alasan">
                                    <strong>Catatan User:</strong><br>
                                    <?php echo !empty($review['catatan_baru']) ? nl2br(htmlspecialchars($review['catatan_baru'])) : '- Tidak ada catatan tambahan -'; ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="right-column">
            <div class="card">
                <div class="card-title">Aksi Validasi Admin</div>
                
                <div style="margin-bottom: 15px; font-size: 14px;">
                    <span style="display:block; margin-bottom: 5px;">Status Utama Trip Saat Ini:</span>
                    <strong style="color: orange;"><?php echo $review['status_trip']; ?></strong>
                </div>
                <div style="margin-bottom: 20px; font-size: 14px;">
                    <span style="display:block; margin-bottom: 5px;">Status Pembayaran:</span>
                    <strong style="color: #321180; background: #f0f0f0; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                        <?php echo $review['status_bayar']; ?>
                    </strong>
                </div>

                <form action="" method="POST" onsubmit="return confirm('Apakah Anda yakin dengan keputusan pengujian ini?');">
                    <input type="hidden" name="id_private" value="<?php echo $review['id_private']; ?>">
                    
                    <label style="font-size: 14px; font-weight: bold; display: block; margin-bottom: 8px;">Pilih Keputusan:</label>
                    <select name="aksi_perubahan" class="form-control" required>
                        <option value="Setujui">Setujui & Perbarui Data Master</option>
                        <option value="Tolak">Tolak Permintaan Perubahan</option>
                    </select>

                    <div style="margin-top: 15px;">
                        <button type="submit" name="proses_perubahan" class="btn-aksi btn-setuju">
                            ✓ Eksekusi Keputusan
                        </button>
                    </div>
                </form>

                <p style="font-size: 11px; color: #888; text-align: center; margin-top: 15px; line-height: 1.4;">
                    *Jika disetujui, sistem akan menyalin data ajuan baru ke data inti perjalanan pengguna.
                </p>
            </div>
        </div>

    </div>
</div>

</body>
</html>