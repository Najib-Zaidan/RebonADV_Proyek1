<?php
session_start();
require "konek.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM users 
WHERE username='$username' AND password='$password'";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if ($data) {
    $_SESSION['username'] = $data['username'];

    echo "<script>
            alert('Login berhasil!');
            window.location='form.php';
          </script>";
} else {
    echo "<script>
            alert('Login gagal!');
            window.location='login_user.html';
          </script>";
}
?>