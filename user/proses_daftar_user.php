<?php
require "konek.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if (empty($username) || empty($password)) {
    echo "Tidak boleh kosong!";
    exit;
}

// HASH PASSWORD
// $password_hash = password_hash($password, PASSWORD_DEFAULT);

$cek = mysqli_query($konek, "SELECT * FROM akun WHERE username='$username'");

if (mysqli_num_rows($cek) > 0) {
    echo "<script>
            alert('Username sudah digunakan!');
            window.history.back();
          </script>";
    exit;
}

// simpan ke tabel akun
$query = "INSERT INTO akun (username, password, role)
VALUES ('$username','$password','user')";

if (mysqli_query($konek, $query)) {
    echo "<script>
            alert('Daftar berhasil!');
            window.location='login_user.php';
          </script>";
} else {
    echo "Gagal: " . mysqli_error($konek);
}
?>