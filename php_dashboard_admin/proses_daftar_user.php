<?php
require "konek.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if (empty($username) || empty($password)) {
    echo "Tidak boleh kosong!";
    exit;
}

// HASH PASSWORD
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// simpan ke tabel akun
$query = "INSERT INTO akun (username, password)
VALUES ('$username','$password_hash')";

if (mysqli_query($konek, $query)) {
    echo "<script>
            alert('Daftar berhasil!');
            window.location='../login_user.html';
          </script>";
} else {
    echo "Gagal: " . mysqli_error($konek);
}
?>