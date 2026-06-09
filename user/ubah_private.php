<?php
session_start();
require "fungsi.php";

// 1. Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>
            alert('Silakan login terlebih dahulu untuk mengakses halaman ini!');
            window.location.href = 'login_user.php';
          </script>";
    exit;
}

$username = $_SESSION['username'];

// 2. Ambil id_private dari URL
if (!isset($_GET['id_private']) || empty($_GET['id_private'])) {
    echo "<script>
            alert('Pilih data trip yang ingin diubah terlebih dahulu!');
            window.location.href = 'history_trip.php';
          </script>";
    exit;
}

$id_private_target = mysqli_real_escape_string($konek, $_GET['id_private']);

// PROSES AKSI: Ajukan Hapus Peserta Individual
if (isset($_GET['action']) && $_GET['action'] == 'minta_hapus_peserta' && isset($_GET['id_peserta'])) {
    $id_peserta_batal = mysqli_real_escape_string($konek, $_GET['id_peserta']);
    
    $query_pending_peserta = "UPDATE peserta_private SET status_peserta = 'Pending Hapus' WHERE id_peserta = '$id_peserta_batal' AND id_private = '$id_private_target'";
    
    if (kueri($query_pending_peserta)) {
        echo "<script>
                alert('Permintaan hapus peserta berhasil dicatat! Status diubah menjadi Pending Hapus.');
                window.location.href = 'ubah_private.php?id_private=" . $id_private_target . "';
              </script>";
        exit;
    }
}

// PROSES AKSI: Batalkan Request Hapus atau Batalkan Pengajuan Peserta Baru
if (isset($_GET['action']) && ($_GET['action'] == 'batal_minta_hapus' || $_GET['action'] == 'batal_pengajuan_baru') && isset($_GET['id_peserta'])) {
    $id_peserta_batal = mysqli_real_escape_string($konek, $_GET['id_peserta']);
    $action = $_GET['action'];

    if ($action == 'batal_minta_hapus') {
        $query_restore = "UPDATE peserta_private SET status_peserta = 'Aktif' WHERE id_peserta = '$id_peserta_batal' AND id_private = '$id_private_target'";
    } else {
        $query_restore = "DELETE FROM peserta_private WHERE id_peserta = '$id_peserta_batal' AND id_private = '$id_private_target' AND status_peserta = 'Pengajuan'";
    }
    
    if (kueri($query_restore)) {
        echo "<script>
                window.location.href = 'ubah_private.php?id_private=" . $id_private_target . "';
              </script>";
        exit;
    }
}

// 3. Tarik data lama dari private_trip
$query_old = "SELECT pt.* FROM private_trip pt 
              JOIN akun a ON pt.id_akun = a.id_akun 
              WHERE pt.id_private = '$id_private_target' AND a.username = '$username' LIMIT 1";
$result_old = kueri($query_old);

if (mysqli_num_rows($result_old) > 0) {
    $data_old = mysqli_fetch_assoc($result_old);
} else {
    echo "<script>
            alert('Data trip tidak ditemukan atau Anda tidak memiliki akses!');
            window.location.href = 'home1.php';
          </script>";
    exit;
}

$query_peserta = "SELECT * FROM peserta_private WHERE id_private = '$id_private_target'";
$result_peserta = kueri($query_peserta);

// Cek apakah ada modifikasi pada data struktur peserta (apakah ada yang 'Pending Hapus' atau 'Pengajuan')
$query_cek_modif_peserta = "SELECT COUNT(*) as jml FROM peserta_private WHERE id_private = '$id_private_target' AND status_peserta IN ('Pending Hapus', 'Pengajuan')";
$res_cek_peserta = kueri($query_cek_modif_peserta);
$data_cek_peserta = mysqli_fetch_assoc($res_cek_peserta);
$ada_perubahan_peserta = ($data_cek_peserta['jml'] > 0);

// 4. Proses submit pengajuan perubahan utama
if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($konek, $_POST['nama']);
    $no_hp = mysqli_real_escape_string($konek, $_POST['nohp']);
    $tujuan = mysqli_real_escape_string($konek, $_POST['destinasi']);
    $tgl_berangkat = mysqli_real_escape_string($konek, $_POST['tgl_berangkat']);
    $tgl_pulang = mysqli_real_escape_string($konek, $_POST['tgl_pulang']);
    $catatan = mysqli_real_escape_string($konek, $_POST['catatan']);
    
    // Validasi Sisi Backend: Cek perubahan data form utama
    $apakah_form_berubah = (
        $nama !== $data_old['nama'] ||
        $no_hp !== $data_old['no_hp'] ||
        $tujuan !== $data_old['tujuan'] ||
        $tgl_berangkat !== $data_old['tgl_berangkat'] ||
        $tgl_pulang !== $data_old['tgl_pulang'] ||
        $catatan !== $data_old['catatan']
    );

    // Jika form detail tidak berubah DAN struktur tabel peserta juga tidak disentuh sama sekali
    if (!$apakah_form_berubah && !$ada_perubahan_peserta) {
        echo "<script>
                alert('Gagal Mengirim! Anda belum melakukan perubahan apa pun pada data trip maupun susunan peserta.');
                window.location.href = 'ubah_private.php?id_private=" . $id_private_target . "';
              </script>";
        exit;
    }

    $query_hitung = "SELECT COUNT(*) as total FROM peserta_private WHERE id_private = '$id_private_target' AND status_peserta != 'Pending Hapus'";
    $res_hitung = kueri($query_hitung);
    $data_hitung = mysqli_fetch_assoc($res_hitung);
    $jumlah_peserta_final = $data_hitung['total'];

    date_default_timezone_set('Asia/Jakarta');
    $tgl_pengajuan = date('Y-m-d H:i:s');

    $query_insert_ubah = "INSERT INTO ubah_private (id_private, nama, no_hp, tujuan, tgl_berangkat, tgl_pulang, tgl_pengajuan, catatan, jumlah_peserta, status) 
                          VALUES ('$id_private_target', '$nama', '$no_hp', '$tujuan', '$tgl_berangkat', '$tgl_pulang', '$tgl_pengajuan', '$catatan', '$jumlah_peserta_final', FALSE)";

    if (kueri($query_insert_ubah)) {
        echo "<script>
                alert('Berhasil! Pengajuan perubahan data Private Trip beserta modifikasi struktur peserta telah dikirim dan menunggu verifikasi admin.');
                window.location.href = 'profiluser.php'; 
              </script>";
    } else {
        echo "<script>alert('Gagal mengirim pengajuan: " . mysqli_error($konek) . "');</script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pengajuan Perubahan Private Trip</title>

  <style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: linear-gradient(135deg, #f1eefc 0%, #e5defa 100%);
        background-attachment: fixed;
        margin: 0;
        padding: 15px;
        color: #4a3b70;
    }

    .container {
        max-width: 900px;
        margin: auto;
    }

    .card {
        background: white;
        padding: 25px;
        border-radius: 14px;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(107, 61, 245, 0.08);
        border: 1px solid rgba(107, 61, 245, 0.1);
        box-sizing: border-box;
    }

    .card-main {
        background: linear-gradient(135deg, #6b3df5 0%, #4922c7 100%);
        color: white;
        border: none;
        box-shadow: 0 8px 25px rgba(107, 61, 245, 0.25);
        position: relative;
        overflow: hidden;
    }

    .card-main::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        pointer-events: none;
    }

    .title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 20px;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card .title {
        color: #4922c7;
        border-bottom: 2px solid #e5defa;
        padding-bottom: 8px;
    }

    .card-main .title {
        color: white;
        border-bottom: 2px solid rgba(255, 255, 255, 0.15);
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .info {
        margin-bottom: 0;
        font-size: 15px;
        line-height: 1.5;
    }

    .back {
        display: inline-block;
        margin-bottom: 20px;
        padding: 10px 18px;
        background: #4a3b70;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(74, 59, 112, 0.15);
    }

    .back:hover {
        background: #342852;
        transform: translateY(-1px);
    }

    /* Form & Input Utility */
    .trip-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #6b3df5;
    }

    .trip-form input[type="text"],
    .trip-form input[type="date"],
    .trip-form textarea {
        font-family: inherit;
        background: #fdfbff;
        border-radius: 8px;
        border: 1px solid #eae6fa;
        padding: 12px 14px;
        font-size: 14px;
        color: #4a3b70;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }

    .trip-form input:focus,
    .trip-form textarea:focus {
        border-color: #6b3df5;
        box-shadow: 0 0 0 3px rgba(107, 61, 245, 0.15);
    }

    .date-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        width: 100%;
    }

    .btn-submit-ubah {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: linear-gradient(135deg, #ff5722 0%, #ff9800 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12 rgba(255, 87, 34, 0.25);
        align-self: flex-end;
    }

    .btn-submit-ubah:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(255, 87, 34, 0.35);
    }

    /* Header Tabel Manajemen Peserta */
    .panel-peserta-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #e5defa;
        padding-bottom: 8px;
        margin-bottom: 15px;
    }

    .panel-peserta-header .title-text {
        font-size: 22px;
        font-weight: 700;
        color: #4922c7;
    }

    .btn-add-peserta {
        background: #27ae60;
        color: white;
        padding: 8px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s, transform 0.2s;
    }

    .btn-add-peserta:hover {
        background: #219653;
        transform: translateY(-1px);
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        border-radius: 8px;
        border: 1px solid #e5defa;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 750px;
    }

    table th, table td {
        padding: 12px 14px;
        text-align: left;
        font-size: 14px;
        border-bottom: 1px solid #f5f2fe;
        vertical-align: middle;
    }

    table th {
        background: #6b3df5;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
    }

    .btn-action-del {
        display: inline-block;
        background: #e74c3c;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        transition: background 0.2s;
    }

    .btn-action-del:hover { background: #c0392b; }

    .btn-action-cancel {
        display: inline-block;
        background: #7f8c8d;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        transition: background 0.2s;
    }
    
    .btn-action-cancel:hover { background: #6c7a7a; }

    table th:nth-child(5), table td:nth-child(5) {
        width: 110px;
    }

    table th:nth-child(6), table td:nth-child(6) {
        width: 120px;
        text-align: center;
    }

    .status-badge {
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        display: inline-block;
        white-space: nowrap;
    }

    .badge-pending { color: #e67e22; background: #fdf5e6; }
    .badge-pengajuan { color: #2980b9; background: #eaf2f8; }
    .badge-aktif { color: #27ae60; background: #eafaf1; }

    @media (max-width: 768px) {
        body { padding: 10px; }
        .card { padding: 18px; }
        .date-group { grid-template-columns: 1fr; }
        .btn-submit-ubah { width: 100%; }
        .panel-peserta-header { flex-direction: column; align-items: flex-start; gap: 10px; }
        .btn-add-peserta { width: 100%; justify-content: center; }
    }
  </style>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<div class="container">

    <a href="profiluser.php" class="back">← Kembali ke Riwayat</a>

    <div class="card card-main">
        <div class="title">Detail Private Trip</div>
        <div class="info-grid">
            <div class="info">
                <b>Kode Trip Saat Ini:</b> 
                <span style="background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 4px; font-weight: bold; font-family: monospace;">
                    #PT-<?php echo $data_old['id_private']; ?>
                </span>
            </div>
            <div class="info" style="opacity: 0.9; font-size: 13px; margin-top: 5px;">
                *Silakan isi formulir di bawah ini untuk mengajukan perubahan data rencana perjalanan Anda kepada admin.
            </div>
        </div>
    </div>

    <div class="card">
        <div class="panel-peserta-header">
            <div class="title-text">Manajemen Anggota Peserta</div>
            <a href="tambah_peserta_private.php?id_private=<?php echo $id_private_target; ?>" class="btn-add-peserta">
                <i class="fa-solid fa-user-plus"></i> Tambah Peserta Baru
            </a>
        </div>
        <div class="info" style="font-size: 13px; margin-bottom: 15px; color: #7f8c8d;">
            * Peserta dengan status <b>Pengajuan</b> atau <b>Pending Hapus</b> akan dievaluasi dan dikonfirmasi langsung oleh admin bersama dengan form perubahan rencana di bawah.
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nama Peserta</th>
                        <th>Usia</th>
                        <th>Alamat</th>
                        <th>Riwayat Penyakit</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result_peserta) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($result_peserta)): ?>
                    <tr>
                        <td><b><?php echo $p['nama']; ?></b></td>
                        <td><?php echo $p['usia']; ?> Thn</td>
                        <td><?php echo $p['alamat']; ?></td>
                        <td><?php echo !empty($p['riwayat']) ? $p['riwayat'] : '-'; ?></td>
                        <td>
                            <?php if ($p['status_peserta'] == 'Pending Hapus'): ?>
                                <span class="status-badge badge-pending">Pending Hapus</span>
                            <?php elseif ($p['status_peserta'] == 'Pengajuan'): ?>
                                <span class="status-badge badge-pengajuan">Pengajuan</span>
                            <?php else: ?>
                                <span class="status-badge badge-aktif">Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($p['status_peserta'] == 'Pending Hapus'): ?>
                                <a href="ubah_private.php?id_private=<?php echo $id_private_target; ?>&action=batal_minta_hapus&id_peserta=<?php echo $p['id_peserta']; ?>" class="btn-action-cancel" onclick="return confirm('Batalkan permintaan hapus?')">Pulihkan</a>
                            <?php elseif ($p['status_peserta'] == 'Pengajuan'): ?>
                                <a href="ubah_private.php?id_private=<?php echo $id_private_target; ?>&action=batal_pengajuan_baru&id_peserta=<?php echo $p['id_peserta']; ?>" class="btn-action-del" onclick="return confirm('Batalkan pengajuan peserta baru ini?')">Batal Tambah</a>
                            <?php else: ?>
                                <a href="ubah_private.php?id_private=<?php echo $id_private_target; ?>&action=minta_hapus_peserta&id_peserta=<?php echo $p['id_peserta']; ?>" class="btn-action-del" onclick="return confirm('Ajukan penghapusan peserta ini dari trip?')">Ajukan Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #777; padding: 20px;">Belum ada data peserta. Silakan klik tombol tambah di atas.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="title">Formulir Perubahan Rencana</div>

        <form action="" method="post" class="trip-form" id="formUbahTrip">
            
            <div class="form-group">
                <label><i class="fa-solid fa-user"></i> Nama Lengkap</label>
                <input type="text" id="inputNama" name="nama" placeholder="Nama Lengkap Pemesan" autocomplete="off" value="<?php echo $data_old['nama']; ?>" required />
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-phone"></i> Nomor Telepon</label>
                <input type="text" id="inputNoHp" name="nohp" placeholder="Contoh: 0812345xxxxx" autocomplete="off" value="<?php echo $data_old['no_hp']; ?>" required />
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-map-location-dot"></i> Lokasi Destinasi</label>
                <input type="text" id="inputTujuan" name="destinasi" placeholder="Tujuan Destinasi Baru" autocomplete="off" value="<?php echo $data_old['tujuan']; ?>" required />
            </div>

            <div class="date-group">
                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-plus"></i> Tanggal Berangkat Baru</label>
                    <input type="date" id="inputTglBerangkat" name="tgl_berangkat" value="<?php echo $data_old['tgl_berangkat']; ?>" required>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-minus"></i> Tanggal Pulang Baru</label>
                    <input type="date" id="inputTglPulang" name="tgl_pulang" value="<?php echo $data_old['tgl_pulang']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-comment-dots"></i> Alasan & Catatan Tambahan Perubahan</label>
                <textarea id="inputCatatan" name="catatan" placeholder="Tuliskan detail atau alasan mengapa rencana trip ingin dirubah..." rows="4"><?php echo $data_old['catatan']; ?></textarea>
            </div>

            <button type="submit" name="submit" class="btn-submit-ubah">
                <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan Perubahan
            </button>

        </form>
    </div>

</div>

<script type="text/javascript">
    document.getElementById('formUbahTrip').addEventListener('submit', function(e) {
        // Ambil value lama dari PHP via inject string javascript
        const oldNama = "<?php echo addslashes($data_old['nama']); ?>";
        const oldNoHp = "<?php echo addslashes($data_old['no_hp']); ?>";
        const oldTujuan = "<?php echo addslashes($data_old['tujuan']); ?>";
        const oldTglBerangkat = "<?php echo $data_old['tgl_berangkat']; ?>";
        const oldTglPulang = "<?php echo $data_old['tgl_pulang']; ?>";
        const oldCatatan = "<?php echo addslashes($data_old['catatan']); ?>";
        
        // Status deteksi modifikasi peserta dari backend
        const adaPerubahanPeserta = <?php echo $ada_perubahan_peserta ? 'true' : 'false'; ?>;

        // Ambil value saat ini dari input form
        const currentNama = document.getElementById('inputNama').value.trim();
        const currentNoHp = document.getElementById('inputNoHp').value.trim();
        const currentTujuan = document.getElementById('inputTujuan').value.trim();
        const currentTglBerangkat = document.getElementById('inputTglBerangkat').value;
        const currentTglPulang = document.getElementById('inputTglPulang').value;
        const currentCatatan = document.getElementById('inputCatatan').value.trim();

        // Bandingkan data input form sekarang dengan data original
        const isFormChanged = (
            currentNama !== oldNama ||
            currentNoHp !== oldNoHp ||
            currentTujuan !== oldTujuan ||
            currentTglBerangkat !== oldTglBerangkat ||
            currentTglPulang !== oldTglPulang ||
            currentCatatan !== oldCatatan
        );

        // Jika form isian kosong/tidak berubah DAN data list peserta juga tidak dimodifikasi
        if (!isFormChanged && !adaPerubahanPeserta) {
            e.preventDefault(); // Menghentikan form submit
            alert('Gagal Mengirim! Anda belum melakukan perubahan apa pun pada data isian formulir maupun pada data list peserta.');
        }
    });
</script>

</body>
</html>