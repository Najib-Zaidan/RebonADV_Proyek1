<?php
session_start();
require "konek.php";

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}

// ambil username lama dari session
$username_lama = $_SESSION['username'];

// ambil data dari form
$username_baru     = trim($_POST['username']);
$old_password      = trim($_POST['old_password']);
$new_password      = trim($_POST['new_password']);
$confirm_password  = trim($_POST['confirm_password']);

// ambil data user dari database
$query = "SELECT * FROM akun WHERE username='$username_lama'";
$result = mysqli_query($konek, $query);
$data = mysqli_fetch_assoc($result);

// ================= VALIDASI =================

// cek password lama
if ($old_password != $data['password']) {
    echo "<script>
            alert('Password lama salah!');
            window.location.href='edit_user.php';
          </script>";
    exit;
}

// cek password baru sama konfirmasi
if ($new_password != $confirm_password) {
    echo "<script>
            alert('Konfirmasi password tidak cocok!');
            window.location.href='edit_user.php';
          </script>";
    exit;
}

// kalau username kosong, pakai username lama
if (empty($username_baru)) {
    $username_baru = $username_lama;
}

// ================= UPDATE =================

// kalau password baru kosong → tidak ganti password
if (empty($new_password)) {

    $update = "UPDATE akun SET username='$username_baru' WHERE username='$username_lama'";

} else {

    $update = "UPDATE akun SET 
                username='$username_baru',
                password='$new_password'
               WHERE username='$username_lama'";
}

$exec = mysqli_query($konek, $update);

if ($exec) {

    // update session
    $_SESSION['username'] = $username_baru;

    echo "<script>
            alert('Profil berhasil diupdate!');
            window.location.href='profiluser.php';
          </script>";

} else {

    echo "<script>
            alert('Gagal update profil!');
            window.location.href='edit_user.php';
          </script>";
}
?>