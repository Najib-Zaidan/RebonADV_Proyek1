<?php
session_start();
require "konek.php";

// cek login
if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}

$username = $_SESSION['username'];

// hapus akun dari database
$query = "DELETE FROM akun WHERE username='$username'";

if (mysqli_query($konek, $query)) {

    // hancurkan session
    session_destroy();

    echo "<script>
            alert('Akun berhasil dihapus!');
            window.location='home1.php';
          </script>";

} else {
    echo "Gagal hapus akun: " . mysqli_error($konek);
}
?>