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

// 2. Validasi parameter ID Notifikasi dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('Akses tidak sah!');
            window.location.href = 'profiluser.php';
          </script>";
    exit;
}

$id_notif = mysqli_real_escape_string($konek, $_GET['id']);
$username = $_SESSION['username'];

// 3. Update otomatis status 'dibaca' menjadi TRUE (1) ketika halaman ini diakses
$query_update = "UPDATE notif n
                 JOIN akun a ON n.id_akun = a.id_akun
                 SET n.dibaca = 1 
                 WHERE n.id_notif = '$id_notif' AND a.username = '$username'";
kueri($query_update);

// 4. Ambil data detail notifikasi untuk ditampilkan
$query_detail = "SELECT n.* FROM notif n
                 JOIN akun a ON n.id_akun = a.id_akun
                 WHERE n.id_notif = '$id_notif' AND a.username = '$username' LIMIT 1";
$result_detail = kueri($query_detail);

if (mysqli_num_rows($result_detail) > 0) {
    $data_notif = mysqli_fetch_assoc($result_detail);
} else {
    echo "<script>
            alert('Notifikasi tidak ditemukan atau Anda tidak memiliki akses!');
            window.location.href = 'profiluser.php';
          </script>";
    exit;
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Detail Notifikasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
</head>
<body style="font-family: 'Poppins', 'Segoe UI', Arial, sans-serif; background-color: #f4fbf7; margin: 0; padding: 40px 15px; color: #2c3e50;">

<div style="max-width: 650px; margin: auto;">

    <a href="profiluser.php" style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; padding: 10px 18px; background: #ffffff; color: #27ae60; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 600; border: 1px solid #eafaf1; box-shadow: 0 2px 10px rgba(39, 174, 96, 0.05); transition: background 0.2s;" onmouseover="this.style.background='#eafaf1'" onmouseout="this.style.background='#ffffff'">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Profil
    </a>

    <div style="background: #ffffff; padding: 35px; border-radius: 14px; box-shadow: 0 4px 20px rgba(39, 174, 96, 0.06); border: 1px solid #eafaf1;">
        
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 25px; border-bottom: 2px solid #eafaf1; padding-bottom: 15px;">
            <div style="background: #eafaf1; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #27ae60; font-size: 20px;">
                <i class="fa-regular fa-envelope-open"></i>
            </div>
            <div>
                <h2 style="color: #27ae60; margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.3px;">Detail Pemberitahuan</h2>
                <p style="margin: 3px 0 0 0; font-size: 12px; color: #7f8c8d; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-regular fa-calendar-check" style="color: #27ae60;"></i> <?= date('d M Y', strtotime($data_notif['waktu'])); ?>
                    <span style="color: #cbd5e1;">|</span>
                    <i class="fa-regular fa-clock" style="color: #27ae60;"></i> <?= date('H:i', strtotime($data_notif['waktu'])); ?> WIB
                </p>
            </div>
        </div>

        <div style="background: #fdfbff; border: 1px solid #eae6fa; border-left: 4px solid #27ae60; padding: 20px; border-radius: 8px; margin-bottom: 25px; min-height: 100px;">
            <p style="margin: 0; font-size: 15px; color: #34495e; line-height: 1.6; white-space: pre-line;">
                <?= htmlspecialchars($data_notif['pesan']); ?>
            </p>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #95a5a6; border-top: 1px dashed #eafaf1; padding-top: 15px;">
            <span>ID Notif: #<?= $data_notif['id_notif']; ?></span>
            <span style="color: #27ae60; background: #eafaf1; padding: 4px 10px; border-radius: 20px; font-weight: 600;">
                <i class="fa-solid fa-check-double"></i> Sudah Dibaca
            </span>
        </div>

    </div>

</div>

</body>
</html>