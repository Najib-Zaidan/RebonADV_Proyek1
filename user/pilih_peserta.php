<?php
session_start();

$id = $_SESSION['id_akun'];
$id_trip = $_GET['id'];
if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}
require 'fungsi.php';

$query = "SELECT id_peserta, id_akun, nama, no_hp, usia, alamat, riwayat 
          FROM peserta_open
          WHERE id_akun = '$id'";

$result = kueri($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Pilih Peserta</title>

<style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:Arial, Helvetica, sans-serif;
}

body{
  background:#e7e2c8;
}

/* NAVBAR (TIDAK DIUBAH) */
.navbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:20px 80px;
  background:#f4f0e5;
}

.logo img{height:50px;}

nav{
  display:flex;
  gap:30px;
  align-items:center;
}

nav a{
  text-decoration:none;
  color:black;
}

.active5{
  background:#6b3df5;
  color:white;
  border:none;
  padding:10px 18px;
  border-radius:8px;
}

/* CONTENT */
.container{
  width:80%;
  margin:40px auto;
}

.judul{
  text-align:center;
  margin-bottom:30px;
}

/* CARD */
.card-wrapper{
  display:flex;
  flex-direction:column;
  gap:20px;
}

.peserta-card{
  display:flex;
  align-items:center;
  gap:20px;
  background:white;
  padding:20px;
  border-radius:12px;
  box-shadow:0 5px 10px rgba(0,0,0,0.1);
  transition:0.3s;
}

.peserta-card:hover{
  transform:translateY(-3px);
}

/* DATA */
.data h3{
  margin-bottom:5px;
}

.data p{
  font-size:14px;
  margin-bottom:3px;
}

/* RADIO */
.radio-box{
  cursor:pointer;
}

.radio-box input{
  transform:scale(1.3);
  accent-color:#6b3df5;
}

/* BUTTON */
.btn-submit{
  margin-top:25px;
  width:100%;
  padding:12px;
  background:#6b3df5;
  color:white;
  border:none;
  border-radius:10px;
  font-weight:bold;
  cursor:pointer;
}

.btn-tambah{
  display:inline-block;
  margin-top:10px;
  padding:10px 20px;
  background:#321180;
  color:white;
  border-radius:8px;
  text-decoration:none;
}

/* EMPTY */
.kosong{
  text-align:center;
  margin:50px;
}

/* FOOTER (TIDAK DIUBAH) */
footer{
  background-color:#fdfae6;
  padding:40px 10%;
  margin-top:50px;
}

.footer-content{
  display:flex;
  justify-content:space-between;
  flex-wrap:wrap;
}

.footer-logo-img{width:220px;}

.copyright{
  text-align:center;
  margin-top:20px;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<header class="navbar">
  <div class="logo">
    <img src="../gambar/REBON LOGO GRADIENT presisi.png">
  </div>

  <nav>
    <a href="home1.php">Home</a>
    <a href="open_trip.php">Open</a>
    <a href="private_trip.php">Private</a>
    <a href="tentang_kami.php">Tentang Kami</a>

    <?php if (isset($_SESSION['username'])): ?>
      <span>👤 <?= $_SESSION['username']; ?></span>
    <?php endif; ?>
  </nav>
</header>

<!-- CONTENT -->
<div class="container">

<?php if(mysqli_num_rows($result)): ?>

<h1 class="judul">Pilih Peserta</h1>

<form action="proses_booking.php" method="POST">

<div class="card-wrapper">

<?php while ($row = ambil($result)): ?>
<div class="peserta-card">

  <label class="radio-box">
    <input type="checkbox" name="id_peserta[]" value="<?= $row['id_peserta']; ?>">
  </label>

  <input type="hidden" name="id_trip" value="<?= $id_trip ?>">

  <div class="data">
    <h3><?= $row['nama']; ?></h3>
    <p>📞 <?= $row['no_hp']; ?></p>
    <p>📅 <?= $row['usia']; ?></p>
    <p>📍 <?= $row['alamat']; ?></p>
    <p>🩺 <?= $row['riwayat']; ?></p>
  </div>

</div>
<?php endwhile; ?>

</div>

<button type="submit" class="btn-submit">Submit Pilihan</button>

</form>

<div class="kosong">
  <h3>Atau</h3>
  <a href="form.php" class="btn-tambah">+ Tambah Peserta</a>
</div>

<?php else: ?>

<div class="kosong">
  <h2>Oopss... Kamu belum punya peserta 😢</h2>
  <p>Silakan tambah dulu</p>
  <a href="form.php?loc=pilih_peserta" class="btn-tambah">+ Tambah Peserta</a>
</div>

<?php endif; ?>

</div>

<!-- FOOTER -->
<footer>
  <div class="footer-content">
    <img src="../gambar/logo-rebon.png" class="footer-logo-img">
  </div>

  <div class="copyright">
    © 2026 REBON ADVENTURE
  </div>
</footer>

</body>
</html>