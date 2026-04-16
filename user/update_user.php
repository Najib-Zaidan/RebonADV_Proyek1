<?php
session_start();
require "konek.php";

if (!isset($_SESSION['username'])) {
    header("Location: ../login_user.html");
    exit;
}

$old_username = $_SESSION['username'];
$new_username = trim($_POST['username']);
$password = trim($_POST['password']);

// kalau password diisi → hash
if (!empty($password)) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE akun SET 
                username='$new_username', 
                password='$password_hash' 
              WHERE username='$old_username'";
} else {
    // kalau password kosong → hanya update username
    $query = "UPDATE akun SET 
                username='$new_username' 
              WHERE username='$old_username'";
}

if (mysqli_query($konek, $query)) {

    // update session
    $_SESSION['username'] = $new_username;

    echo "<script>
            alert('Profile berhasil diupdate!');
            window.location='profile.php';
          </script>";

} else {
    echo "Gagal update: " . mysqli_error($konek);
}
?>