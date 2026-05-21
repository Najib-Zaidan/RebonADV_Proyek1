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
            window.location.href = 'history_trip.php'; // Sesuaikan dengan halaman riwayat Anda
          </script>";
    exit;
}

$id_private_target = mysqli_real_escape_string($konek, $_GET['id_private']);

// 3. Tarik data lama dari private_trip untuk ditampilkan di form (Old Value)
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

// 4. Proses submit pengajuan perubahan
if (isset($_POST['submit'])) {
    
    // Tangkap data dari form dan sanitasi
    $nama = mysqli_real_escape_string($konek, $_POST['nama']);
    $no_hp = mysqli_real_escape_string($konek, $_POST['nohp']);
    $tujuan = mysqli_real_escape_string($konek, $_POST['destinasi']);
    $tgl_berangkat = mysqli_real_escape_string($konek, $_POST['tgl_berangkat']);
    $tgl_pulang = mysqli_real_escape_string($konek, $_POST['tgl_pulang']);
    $catatan = mysqli_real_escape_string($konek, $_POST['catatan']);
    $jumlah_peserta = mysqli_real_escape_string($konek, $_POST['jumlah']);
    
    // Atur timezone dan set tanggal pengajuan ke waktu saat ini
    date_default_timezone_set('Asia/Jakarta');
    $tgl_pengajuan = date('Y-m-d H:i:s');

    // Insert ke tabel ubah_private sesuai struktur SQL Anda
    $query_insert_ubah = "INSERT INTO ubah_private (id_private, nama, no_hp, tujuan, tgl_berangkat, tgl_pulang, tgl_pengajuan, catatan, jumlah_peserta, status) 
                          VALUES ('$id_private_target', '$nama', '$no_hp', '$tujuan', '$tgl_berangkat', '$tgl_pulang', '$tgl_pengajuan', '$catatan', '$jumlah_peserta', FALSE)";

    if (kueri($query_insert_ubah)) {
        echo "<script>
                alert('Berhasil! Pengajuan perubahan data Private Trip telah dikirim dan menunggu persetujuan admin.');
                window.location.href = 'home1.php'; 
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
        /* Mengikuti tema background detail_pesanan.php */
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

    /* Card Utama bertema ungu asik dengan aksen lingkaran transparan */
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

    /* Judul pada card putih biasa */
    .card .title {
        color: #4922c7;
        border-bottom: 2px solid #e5defa;
        padding-bottom: 8px;
    }

    /* Judul pada card utama ungu */
    .card-main .title {
        color: white;
        border-bottom: 2px solid rgba(255, 255, 255, 0.15);
    }

    /* Info grid penyusunan kode trip agar rapi */
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

    /* Tombol Kembali / Back Link */
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

    /* Form styling agar seirama dengan inputan modern premium */
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
    .trip-form input[type="number"],
    .trip-form textarea {
        font-family: inherit;
        background: #fdfbff;
        border-radius: 8px;
        border: 1px solid #eae6fa;
        padding: 12px 14px;
        font-size: 14px;
        color: #4a3b70;
        outline: none;
        box-shadow: inset 0 1px 3px rgba(107, 61, 245, 0.03);
        width: 100%;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }

    .trip-form input:focus,
    .trip-form textarea:focus {
        border-color: #6b3df5;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(107, 61, 245, 0.15);
    }

    .trip-form textarea {
        resize: vertical;
    }

    /* Tata letak input tanggal berdampingan */
    .date-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        width: 100%;
    }

    /* Tombol Submit Orange Menyalanya dipertahankan dengan sentuhan modern */
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
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(255, 87, 34, 0.25);
        align-self: flex-end;
    }

    .btn-submit-ubah:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(255, 87, 34, 0.35);
    }

    /* Media Queries Responsif untuk Layar Handphone */
    @media (max-width: 768px) {
        body {
            padding: 10px;
        }
        .card {
            padding: 18px;
            border-radius: 12px;
        }
        .date-group {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .btn-submit-ubah {
            width: 100%;
        }
    }
  </style>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<div class="container">

    <a href="profiluser.php" class="back">← Kembali ke Riwayat</a>

    <div class="card card-main">
        <div class="title">
            <i></i> Detail Private Trip
        </div>
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
        <div class="title">Formulir Perubahan Rencana</div>

        <form action="" method="post" class="trip-form">
            
            <div class="form-group">
                <label><i class="fa-solid fa-user"></i> Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Nama Lengkap Pemesan" autocomplete="off" value="<?php echo $data_old['nama']; ?>" required />
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-phone"></i> Nomor Telepon</label>
                <input type="text" name="nohp" placeholder="Contoh: 0812345xxxxx" autocomplete="off" value="<?php echo $data_old['no_hp']; ?>" required />
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-map-location-dot"></i> Lokasi Destinasi</label>
                <input type="text" name="destinasi" placeholder="Tujuan Destinasi Baru" autocomplete="off" value="<?php echo $data_old['tujuan']; ?>" required />
            </div>

            <div class="date-group">
                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-plus"></i> Tanggal Berangkat Baru</label>
                    <input type="date" name="tgl_berangkat" value="<?php echo $data_old['tgl_berangkat']; ?>" required>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-minus"></i> Tanggal Pulang Baru</label>
                    <input type="date" name="tgl_pulang" value="<?php echo $data_old['tgl_pulang']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-comment-dots"></i> Alasan & Catatan Tambahan Perubahan</label>
                <textarea name="catatan" placeholder="Tuliskan detail atau alasan mengapa rencana trip ingin dirubah..." rows="4"><?php echo $data_old['catatan']; ?></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-users"></i> Jumlah Peserta</label>
                <input type="number" name="jumlah" placeholder="Minimal 1 Peserta" min="1" value="<?php echo $data_old['jumlah_peserta']; ?>" required />
            </div>

            <button type="submit" name="submit" class="btn-submit-ubah">
                <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan Perubahan
            </button>

        </form>
    </div>

</div>

</body>
</html>