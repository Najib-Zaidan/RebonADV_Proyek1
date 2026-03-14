<?php 
session_start();
if (!isset($_SESSION["verif"]) || $_SESSION["verif"] !== true) {
    header("Location: login.php");
    exit;
}
require 'konek.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
</head>
<body>
    <h1>Selamat Datang, <?php echo $_SESSION['user']; ?></h1>
    <p>Ini adalah halaman admin Rebon Adventure.</p>
    <a href="home.html">Logout</a>
</body>
</html>
