<?php 
session_start();
//echo "index php";
if (!isset($_SESSION["verif"]) || $_SESSION["verif"] != true) {
  echo "isset index php";
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
    <h1>Selamat Datang, <?php echo $_SESSION['nama']; ?></h1>
    <p>Ini adalah halaman admin Rebon Adventure.</p>
    <a href="home.html">Logout</a>
</body>
</html>
