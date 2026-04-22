<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Profile</title>

<style>

/* RESET */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* NAVBAR */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 80px;
  background: #f4f0e5;
}

.logo img {
  height: 50px;
}

nav {
  display: flex;
  gap: 30px;
  align-items: center;
}

nav a {
  text-decoration: none;
  color: black;
  font-weight: 500;
}

nav .active2 {
  color: #6b3df5;
}

.active5 {
  background: #6b3df5;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
}

/* HERO */
.hero {
  height: 150px;
  background: linear-gradient(90deg, #321180, #5A1EE6);
}

/* PROFILE */
.profile-card {
  width: 80%;
  margin: -70px auto 30px;
  background: #e5e1d0;
  padding: 30px;
  border-radius: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 5px 10px rgba(0,0,0,0.2);
}

.avatar {
  width: 80px;
  height: 80px;
  background: #5A1EE6;
  border-radius: 50%;
}

.profile-right {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* BUTTON */
.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  color: white;
}

.purple {
  background: linear-gradient(#321180, #5A1EE6);
}

.red {
  background: #e50914;
}

/* MENU TAB */
.menu-tabs {
  width: 80%;
  margin: 20px auto;
  display: flex;
  justify-content: center;
  gap: 50px;
  font-weight: bold;
}

.menu-tabs div {
  cursor: pointer;
  padding-bottom: 5px;
}

.active-tab {
  border-bottom: 3px solid black;
}

/* DATA */
.data-section {
  width: 80%;
  margin: auto;
}

.data-card {
  margin-top: 20px;
  background: #cfc6e3;
  padding: 20px;
  border-radius: 15px;
  display: flex;
  justify-content: space-between;
}

.data-left p {
  margin-bottom: 10px;
}

.data-right {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* TAB CONTENT */
.tab-content {
  display: none;
}

.tab-content.active {
  display: block;
}

/* FOOTER */
footer {
  background-color: #fdfae6;
  padding: 40px 10% 20px 10%;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 30px;
  border-bottom: 1px solid #ccc;
  padding-bottom: 30px;
}

.footer-logo-img {
  width: 220px;
}

.copyright {
  text-align: center;
  font-size: 12px;
  margin-top: 20px;
  font-weight: bold;
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
    <a href="open_trip.php" class="active2">Open</a>
    <a href="private_trip.php">Private</a>
    <a href="tentang_kami.php">Tentang Kami</a>

    <?php if (isset($_SESSION['username'])): ?>
      <span>👤 <?= $_SESSION['username']; ?></span>
      <a href="logout_user.php">
        <button class="active5">Logout</button>
      </a>
    <?php else: ?>
      <a href="login_user.php">
        <button class="active5">Masuk</button>
      </a>
    <?php endif; ?>
  </nav>
</header>

<!-- HERO -->
<section class="hero"></section>

<!-- PROFILE -->
<section class="profile-card">
  <div>
    <div class="avatar"></div>
    <button class="btn purple">Ubah Profil</button>
  </div>

  <div>
    <p>PROFILE</p>
    <h2>najip jerug</h2>
  </div>

  <div class="profile-right">
    <button class="btn red">Hapus Akun</button>
    <button class="btn red">Log Out</button>
  </div>
</section>

<!-- MENU TAB -->
<div class="menu-tabs">
  <div class="tab active-tab" onclick="showTab('peserta', this)">DATA PESERTA</div>
  <div class="tab" onclick="showTab('pesanan', this)">DATA PESANAN</div>
  <div class="tab" onclick="showTab('pembayaran', this)">DATA PEMBAYARAN</div>
</div>

<!-- TAB 1 -->
<section id="peserta" class="data-section tab-content active">
  <button class="btn purple">+ Tambah</button>

  <div class="data-card">
    <div class="data-left">
      <p>👤 NAMA</p>
      <p>📞 0888888888</p>
      <p>📅 12-02-2006</p>
      <p>📍 Jl. Lohbener</p>
    </div>

    <div class="data-right">
      <button class="btn purple">Ubah</button>
      <button class="btn red">Hapus</button>
    </div>
  </div>
</section>

<!-- TAB 2 -->
<section id="pesanan" class="data-section tab-content">
  <h3>Belum ada data pesanan</h3>
</section>

<!-- TAB 3 -->
<section id="pembayaran" class="data-section tab-content">
  <h3>Belum ada data pembayaran</h3>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-content">
    <div>
      <img src="../gambar/logo-rebon.png" class="footer-logo-img">
    </div>

    <div>
      <h4>KONTAK KAMI</h4>
      <p>rebonadventure@gmail.com</p>
      <p>+62 812-3456-7890</p>
    </div>

    <div>
      <h4>LAYANAN</h4>
      <p>OPEN TRIP</p>
      <p>PRIVATE TRIP</p>
    </div>

    <div>
      <h4>INFORMASI</h4>
      <p>TENTANG KAMI</p>
      <p>FAQ</p>
    </div>
  </div>

  <div class="copyright">
    © 2026 REBON ADVENTURE
  </div>
</footer>

<!-- JS -->
<script>
function showTab(id, el) {
  document.querySelectorAll('.tab-content').forEach(e => e.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(e => e.classList.remove('active-tab'));

  document.getElementById(id).classList.add('active');
  el.classList.add('active-tab');
}
</script>

</body>
</html>