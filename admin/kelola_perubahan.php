<?php
session_start();
// Memastikan admin sudah login
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
    $aksi = $_POST['aksi_perubahan']; // 'Setujui' or 'Tolak'
    $id_private_target = $_POST['id_private'];
    
    // Ambil data perubahan untuk keperluan update jika disetujui
    $q_ambil_baru = kueri("SELECT * FROM ubah_private WHERE id_ubah = $id_ubah LIMIT 1");
    $d_baru = ambil($q_ambil_baru);

    if ($aksi == 'Setujui') {
        // VALIDASI HARGA: Wajib diisi dan tidak boleh 0 atau minus jika disetujui
        $harga_baru    = isset($_POST['harga_baru']) ? intval($_POST['harga_baru']) : 0;
        $harga_dp_baru = isset($_POST['harga_dp_baru']) ? intval($_POST['harga_dp_baru']) : 0;

        if ($harga_baru <= 0 || $harga_dp_baru <= 0) {
            echo "<script>alert('Gagal! Anda memilih untuk MENYETUJUI, maka Harga Trip dan Harga DP baru wajib ditentukan dan tidak boleh 0!'); window.history.back();</script>";
            exit;
        }

        // Ambil komponen data perubahan dari tabel log ubah_private
        $nama_b      = mysqli_real_escape_string($konek, $d_baru['nama']);
        $no_hp_b     = mysqli_real_escape_string($konek, $d_baru['no_hp']);
        $tujuan_b    = mysqli_real_escape_string($konek, $d_baru['tujuan']);
        $tgl_ber_b   = $d_baru['tgl_berangkat'];
        $tgl_pul_b   = $d_baru['tgl_pulang'];
        $jumlah_b    = $d_baru['jumlah_peserta'];
        $catatan_b   = mysqli_real_escape_string($konek, $d_baru['catatan']);

        // Jalankan update ke tabel utama (Disesuaikan dengan kolom: harga, harga_dp, status_trip)
        $update_utama = kueri("UPDATE private_trip SET 
                               nama = '$nama_b',
                               no_hp = '$no_hp_b',
                               tujuan = '$tujuan_b',
                               tgl_berangkat = '$tgl_ber_b',
                               tgl_pulang = '$tgl_pul_b',
                               jumlah_peserta = '$jumlah_b',
                               catatan = '$catatan_b',
                               harga = '$harga_baru',
                               harga_dp = '$harga_dp_baru',
                               status_trip = 'Belum Disetujui' 
                               WHERE id_private = $id_private_target");
        
        // EKSEKUSI PERUBAHAN STATUS PESERTA
        // 1. Ubah yang 'Pengajuan' menjadi 'Aktif'
        kueri("UPDATE peserta_private SET status_peserta = 'Aktif' WHERE id_private = $id_private_target AND status_peserta = 'Pengajuan'");
        // 2. Hapus peserta yang berstatus 'Pending Hapus'
        kueri("DELETE FROM peserta_private WHERE id_private = $id_private_target AND status_peserta = 'Pending Hapus'");

        // Ubah status log perubahan menjadi 1 (Selesai diproses)
        $update_log = kueri("UPDATE ubah_private SET status = 1 WHERE id_ubah = $id_ubah");

        if ($update_utama && $update_log) {
            // [LOGIKA NOTIFIKASI - DISETUJUI]
            $q_pemilik = kueri("SELECT id_akun FROM private_trip WHERE id_private = $id_private_target LIMIT 1");
            if (mysqli_num_rows($q_pemilik) > 0) {
                $d_pemilik = ambil($q_pemilik);
                $id_user_penerima = $d_pemilik['id_akun'];
                $pesan_notif = "Pengajuan perubahan data dan peserta untuk Private Trip Anda ke destinasi $tujuan_b telah DISETUJUI oleh admin. Harga paket trip telah disesuaikan ulang.";
                
                kueri("INSERT INTO notif (id_akun, pesan, dibaca) VALUES ($id_user_penerima, '$pesan_notif', 0)");
            }

            echo "<script>alert('Pengajuan perubahan DISETUJUI. Data master trip, daftar peserta, dan harga baru telah diperbarui!'); window.location.href='detail_private.php?id=$id_private_target';</script>";
            exit;
        }
    } else {
        // [LOGIKA NOTIFIKASI - DITOLAK]
        $q_pemilik = kueri("SELECT pt.id_akun, up.tujuan FROM private_trip pt 
                            JOIN ubah_private up ON pt.id_private = up.id_private 
                            WHERE up.id_ubah = $id_ubah LIMIT 1");
        if (mysqli_num_rows($q_pemilik) > 0) {
            $d_pemilik = ambil($q_pemilik);
            $id_user_penerima = $d_pemilik['id_akun'];
            $tujuan_tujuan = $d_pemilik['tujuan'];
            $pesan_notif = "Mohon maaf, pengajuan perubahan data dan peserta untuk Private Trip Anda ke destinasi $tujuan_tujuan telah DITOLAK oleh admin.";
            
            kueri("INSERT INTO notif (id_akun, pesan, dibaca) VALUES ($id_user_penerima, '$pesan_notif', 0)");
        }

        // KEMBALIKAN STATUS PESERTA KARENA DITOLAK
        // 1. Hapus yang tadinya baru diajukan ('Pengajuan')
        kueri("DELETE FROM peserta_private WHERE id_private = $id_private_target AND status_peserta = 'Pengajuan'");
        // 2. Kembalikan yang tadinya mau dihapus ('Pending Hapus') menjadi 'Aktif' kembali
        kueri("UPDATE peserta_private SET status_peserta = 'Aktif' WHERE id_private = $id_private_target AND status_peserta = 'Pending Hapus'");

        // Hapus baris pengajuan perubahan dari tabel ubah_private
        $hapus_log = kueri("DELETE FROM ubah_private WHERE id_ubah = $id_ubah");
        
        if ($hapus_log) {
            echo "<script>alert('Pengajuan perubahan telah DITOLAK. Data ajuan dihapus dan daftar peserta dikembalikan ke semula.'); window.location.href='index.php?menu=private&sub=pengajuan_perubahan';</script>";
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
                        pt.harga AS harga_asli, pt.harga_dp AS harga_dp_asli,
                        a.username 
                       FROM ubah_private up
                       JOIN private_trip pt ON up.id_private = pt.id_private
                       JOIN akun a ON pt.id_akun = a.id_akun
                       WHERE up.id_ubah = $id_ubah LIMIT 1");

$review = ambil($query_review);

if (!$review) {
    die("Data pengajuan perubahan tidak ditemukan atau sudah diproses.");
}

$id_private_current = $review['id_private'];

// 4. Ambil data peserta berdasarkan status eksisting untuk review admin
$q_peserta_baru  = kueri("SELECT * FROM peserta_private WHERE id_private = $id_private_current AND status_peserta = 'Pengajuan'");
$q_peserta_hapus = kueri("SELECT * FROM peserta_private WHERE id_private = $id_private_current AND status_peserta = 'Pending Hapus'");
$q_peserta_tetap = kueri("SELECT * FROM peserta_private WHERE id_private = $id_private_current AND status_peserta = 'Aktif'");
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
        .table-perbandingan, .table-peserta {
            width: 100%;
            border-collapse: collapse;
        }
        .table-perbandingan th, .table-peserta th {
            background-color: #e1d8f5;
            color: #321180;
            text-align: left;
            padding: 10px;
            font-size: 14px;
        }
        .table-perbandingan td, .table-peserta td {
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
        .input-harga-container {
            background-color: #f9f8ff;
            border: 1px dashed #6b3df5;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
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
        .alert-alasan {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
            padding: 12px;
            border-radius: 4px;
            font-size: 14px;
            margin-top: 5px;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-tambah { background-color: #e8f5e9; color: #2e7d32; }
        .badge-hapus { background-color: #ffebee; color: #c62828; }
        .badge-tetap { background-color: #e3f2fd; color: #1565c0; }
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
                            <td><strong>Jumlah Kuota Peserta</strong></td>
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

            <div class="card">
                <div class="card-title">Manajemen Perubahan Daftar Peserta</div>
                <p style="font-size: 13px; color: #666; margin-bottom: 15px;">
                    Daftar di bawah memuat data rincian rombongan anggota yang baru ditambahkan atau dihapus oleh pemesan.
                </p>

                <table class="table-peserta">
                    <thead>
                        <tr>
                            <th>Nama Peserta</th>
                            <th>Usia</th>
                            <th>Alamat</th>
                            <th>Riwayat Medis</th>
                            <th width="20%">Status Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($q_peserta_baru) > 0): ?>
                            <?php while($p_baru = ambil($q_peserta_baru)): ?>
                                <tr style="background-color: #f4faf4;">
                                    <td><strong><?php echo htmlspecialchars($p_baru['nama']); ?></strong></td>
                                    <td><?php echo $p_baru['usia']; ?> Tahun</td>
                                    <td><?php echo htmlspecialchars($p_baru['alamat']); ?></td>
                                    <td><?php echo htmlspecialchars($p_baru['riwayat']); ?></td>
                                    <td><span class="badge badge-tambah">+ Baru Diajukan</span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <?php if(mysqli_num_rows($q_peserta_hapus) > 0): ?>
                            <?php while($p_hapus = ambil($q_peserta_hapus)): ?>
                                <tr style="background-color: #fff5f5; color: #c62828;">
                                    <td><del><?php echo htmlspecialchars($p_hapus['nama']); ?></del></td>
                                    <td><?php echo $p_hapus['usia']; ?> Tahun</td>
                                    <td><?php echo htmlspecialchars($p_hapus['alamat']); ?></td>
                                    <td><?php echo htmlspecialchars($p_hapus['riwayat']); ?></td>
                                    <td><span class="badge badge-hapus">✕ Akan Dihapus</span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <?php if(mysqli_num_rows($q_peserta_tetap) > 0): ?>
                            <?php while($p_tetap = ambil($q_peserta_tetap)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p_tetap['nama']); ?></td>
                                    <td><?php echo $p_tetap['usia']; ?> Tahun</td>
                                    <td><?php echo htmlspecialchars($p_tetap['alamat']); ?></td>
                                    <td><?php echo htmlspecialchars($p_tetap['riwayat']); ?></td>
                                    <td><span class="badge badge-tetap">Tetap</span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <?php if(mysqli_num_rows($q_peserta_baru) == 0 && mysqli_num_rows($q_peserta_hapus) == 0 && mysqli_num_rows($q_peserta_tetap) == 0): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #888;">Belum ada anggota peserta terdaftar di dalam klan trip ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="right-column">
            <div class="card">
                <div class="card-title">Aksi Validasi Admin</div>
                
                <div style="margin-bottom: 10px; font-size: 14px;">
                    <span>Status Utama Trip Saat Ini:</span><br>
                    <strong style="color: orange;"><?php echo $review['status_trip']; ?></strong>
                </div>
                <div style="margin-bottom: 15px; font-size: 14px;">
                    <span>Harga Trip Lama / DP Lama:</span><br>
                    <strong>Rp <?php echo number_format($review['harga_asli'], 0, ',', '.'); ?></strong> / 
                    <span style="color: #666;">Rp <?php echo number_format($review['harga_dp_asli'], 0, ',', '.'); ?></span>
                </div>

                <form action="" method="POST" onsubmit="return verifikasiKeputusan();">
                    <input type="hidden" name="id_private" value="<?php echo $review['id_private']; ?>">
                    
                    <label style="font-size: 14px; font-weight: bold; display: block; margin-bottom: 8px;">Pilih Keputusan:</label>
                    <select name="aksi_perubahan" id="aksi_perubahan" class="form-control" onchange="togglePersyaratanHarga()" required>
                        <option value="Setujui">Setujui & Perbarui Data Master</option>
                        <option value="Tolak">Tolak Permintaan Perubahan</option>
                    </select>

                    <div class="input-harga-container" id="box_harga_baru">
                        <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 5px; color:#4922c7;">
                            Harga Paket Trip Baru (Rp):
                        </label>
                        <input type="number" name="harga_baru" id="harga_baru" class="form-control" placeholder="Contoh: 3500000" min="0" value="<?php echo $review['harga_asli']; ?>">
                        
                        <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 5px; color:#4922c7;">
                            Harga Down Payment (DP) Baru (Rp):
                        </label>
                        <input type="number" name="harga_dp_baru" id="harga_dp_baru" class="form-control" placeholder="Contoh: 500000" min="0" value="<?php echo $review['harga_dp_asli']; ?>">
                        
                        <span style="font-size: 11px; color:#e67e22; display:block; line-height: 1.3;">
                            *Wajib diisi jika menyetujui perubahan data penyesuaian jumlah kuota/tanggal/peserta.
                        </span>
                    </div>

                    <div style="margin-top: 15px;">
                        <button type="submit" name="proses_perubahan" class="btn-aksi btn-setuju" id="btn_submit_aksi">
                            ✓ Eksekusi Keputusan
                        </button>
                    </div>
                </form>

                <p style="font-size: 11px; color: #888; text-align: center; margin-top: 15px; line-height: 1.4;">
                    *Jika disetujui, sistem otomatis menyalin data ajuan baru, menyinkronkan status peserta baru, serta menghapus data peserta yang dikeluarkan.
                </p>
            </div>
        </div>

    </div>
</div>

<script>
function togglePersyaratanHarga() {
    var aksi = document.getElementById('aksi_perubahan').value;
    var boxHarga = document.getElementById('box_harga_baru');
    var inputTrip = document.getElementById('harga_baru');
    var inputDp = document.getElementById('harga_dp_baru');
    var btn = document.getElementById('btn_submit_aksi');

    if(aksi === 'Setujui') {
        boxHarga.style.display = 'block';
        inputTrip.required = true;
        inputDp.required = true;
        btn.style.backgroundColor = '#2e7d32';
        btn.innerHTML = '✓ Eksekusi Persetujuan';
    } else {
        boxHarga.style.display = 'none';
        inputTrip.required = false;
        inputDp.required = false;
        btn.style.backgroundColor = '#c62828';
        btn.innerHTML = '✕ Eksekusi Penolakan';
    }
}

function verifikasiKeputusan() {
    var aksi = document.getElementById('aksi_perubahan').value;
    if(aksi === 'Setujui') {
        var inputTrip = parseInt(document.getElementById('harga_baru').value) || 0;
        var inputDp = parseInt(document.getElementById('harga_dp_baru').value) || 0;
        
        if(inputTrip <= 0 || inputDp <= 0) {
            alert('Nilai Harga Trip dan DP tidak boleh kosong atau bernilai 0 jika Anda ingin menyetujui pengajuan ini!');
            return false;
        }
        return confirm('Konfirmasi: Menyetujui data trip beserta perubahan struktur status anggotanya?');
    }
    return confirm('Apakah Anda yakin ingin MENOLAK? Data pengajuan baru dan draft peserta pengajuan akan dibersihkan sistem.');
}

// Inisialisasi awal saat halaman dibuka
togglePersyaratanHarga();
</script>

</body>
</html>