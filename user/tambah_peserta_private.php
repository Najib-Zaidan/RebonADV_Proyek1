<?php
session_start();
require "fungsi.php";

// 1. Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>
            alert('Silakan login terlebih dahulu!');
            window.location.href = 'login_user.php';
          </script>";
    exit;
}

$username = $_SESSION['username'];

// 2. Ambil id_private dari URL target
if (!isset($_GET['id_private']) || empty($_GET['id_private'])) {
    echo "<script>
            alert('ID Private Trip tidak valid!');
            window.location.href = 'history_trip.php';
          </script>";
    exit;
}

$id_private_target = mysqli_real_escape_string($konek, $_GET['id_private']);

// 3. Validasi kepemilikan trip (mencegah user jahil menembak id_private milik orang lain via URL)
$query_cek = "SELECT pt.id_private FROM private_trip pt 
              JOIN akun a ON pt.id_akun = a.id_akun 
              WHERE pt.id_private = '$id_private_target' AND a.username = '$username' LIMIT 1";
$result_cek = kueri($query_cek);

if (mysqli_num_rows($result_cek) == 0) {
    echo "<script>
            alert('Anda tidak memiliki akses ke data trip ini!');
            window.location.href = 'home1.php';
          </script>";
    exit;
}

// 4. Proses submit penambahan peserta
if (isset($_POST['submit_peserta'])) {
    $nama_peserta  = mysqli_real_escape_string($konek, $_POST['nama_peserta']);
    $usia_peserta  = mysqli_real_escape_string($konek, $_POST['usia_peserta']);
    $alamat        = mysqli_real_escape_string($konek, $_POST['alamat_peserta']);
    $riwayat_sakit = mysqli_real_escape_string($konek, $_POST['riwayat_sakit']);

    // Set default riwayat strip (-) jika kosong
    if (empty($riwayat_sakit)) {
        $riwayat_sakit = '-';
    }

    // Insert dengan status_peserta 'Pengajuan' sesuai struktur ENUM baru
    $query_add = "INSERT INTO peserta_private (id_private, nama, usia, alamat, riwayat, status_peserta) 
                  VALUES ('$id_private_target', '$nama_peserta', '$usia_peserta', '$alamat', '$riwayat_sakit', 'Pengajuan')";

    if (kueri($query_add)) {
        echo "<script>
                alert('Peserta baru berhasil diajukan! Data tersimpan dengan status Pengajuan.');
                window.location.href = 'ubah_private.php?id_private=" . $id_private_target . "';
              </script>";
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan peserta: " . mysqli_error($konek) . "');</script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tambah Peserta Private Trip</title>

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
        max-width: 600px;
        margin: 40px auto;
    }

    .card {
        background: white;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(107, 61, 245, 0.08);
        border: 1px solid rgba(107, 61, 245, 0.1);
        box-sizing: border-box;
    }

    .title {
        font-size: 22px;
        font-weight: 700;
        color: #4922c7;
        border-bottom: 2px solid #e5defa;
        padding-bottom: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
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

    .btn-group {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 10px;
    }

    .btn-submit {
        background: #27ae60;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.2s, transform 0.2s;
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.2);
    }

    .btn-submit:hover {
        background: #219653;
        transform: translateY(-1px);
    }

    .btn-cancel {
        background: #e74c3c;
        color: white;
        text-decoration: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-align: center;
        transition: background 0.2s;
    }
    
    .btn-cancel:hover {
        background: #c0392b;
    }
  </style>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<div class="container">

    <a href="ubah_private.php?id_private=<?php echo $id_private_target; ?>" class="back">← Kembali</a>

    <div class="card">
        <div class="title">
            <i class="fa-solid fa-user-plus"></i> Form Pengajuan Peserta Baru
        </div>

        <form action="" method="post" class="trip-form">
            
            <div class="form-group">
                <label><i class="fa-solid fa-id-card"></i> Nama Lengkap Peserta</label>
                <input type="text" name="nama_peserta" placeholder="Masukkan nama sesuai KPT/Kartu Pelajar" autocomplete="off" required />
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-cake-candles"></i> Usia Peserta (Tahun)</label>
                <input type="number" name="usia_peserta" placeholder="Contoh: 21" min="1" max="100" required />
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-house-chimney"></i> Alamat Domisili</label>
                <textarea name="alamat_peserta" placeholder="Tuliskan alamat lengkap asal peserta..." rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-heart-pulse"></i> Riwayat Penyakit (Opsional)</label>
                <input type="text" name="riwayat_sakit" placeholder="Contoh: Asma, Alergi Dingin (Kosongkan jika tidak ada)" autocomplete="off" />
            </div>

            <div class="btn-group">
                <a href="ubah_private.php?id_private=<?php echo $id_private_target; ?>" class="btn-cancel">Batal</a>
                <button type="submit" name="submit_peserta" class="btn-submit">
                    <i class="fa-solid fa-check"></i> Ajukan Peserta
                </button>
            </div>

        </form>
    </div>

</div>

</body>
</html>