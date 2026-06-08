<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile</title>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
}

/* BACKGROUND */
body {
  background: url('../gambar/bg-profile.jpg') no-repeat center center/cover;
  min-height: 100vh;
}

/* overlay */
body::before {
  content: "";
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.4);
  z-index: -1;
}

/* ================= NAVBAR (TIDAK DIUBAH) ================= */
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

nav .active6 {
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

/* ================= CONTAINER ================= */
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 20px;
  background: linear-gradient(135deg, #667eea, #764ba2);
}

/* FORM BOX */
.form-box {
  position: relative;
  width: 420px;
  padding: 40px 30px;
  background: rgba(255,255,255,0.95);
  border-radius: 20px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.25);
  backdrop-filter: blur(12px);
  text-align: center;
  transition: 0.3s;
}

.form-box:hover {
  transform: translateY(-5px);
}

/* Tombol kembali */
.back-btn {
  position: absolute;
  top: 15px;
  left: 15px;
  width: 35px;
  height: 35px;
  background: #eee;
  border-radius: 50%;
  text-align: center;
  line-height: 35px;
  text-decoration: none;
  color: #333;
  font-weight: bold;
  transition: 0.3s;
}

.back-btn:hover {
  background: #6b3df5;
  color: white;
  transform: scale(1.1);
}

/* TEXT */
.form-box h2 {
  margin-bottom: 10px;
  font-size: 24px;
  font-weight: bold;
  color: #333;
}

.form-box h3 {
  margin-bottom: 20px;
  font-weight: normal;
}

.username {
  color: #6b3df5;
  font-weight: bold;
}

/* INPUT */
.input-group {
  text-align: left;
  margin-bottom: 15px;
}

.input-group label {
  font-size: 13px;
  font-weight: 600;
  color: #444;
}

.input-group input {
  width: 100%;
  padding: 12px;
  margin-top: 5px;
  border-radius: 10px;
  border: 1px solid #ccc;
  outline: none;
  transition: 0.2s;
  background: #f9f9f9;
}

.input-group input:focus {
  border-color: #6b3df5;
  box-shadow: 0 0 6px rgba(107,61,245,0.4);
  background: white;
}

/* BUTTON */
.btn {
  width: 100%;
  margin-top: 15px;
  background: linear-gradient(135deg, #00c851, #00a844);
  color: white;
  padding: 13px;
  border: none;
  border-radius: 12px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}

.btn:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

/* RESPONSIVE */
@media(max-width:600px){
  .form-box {
    width: 100%;
  }
}

footer {
  background-color: #fdfae6;
  padding: 40px 10% 20px 10%;
  color: #333;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 30px;
  border-bottom: 1px solid #ccc;
  padding-bottom: 30px;
}

.footer-column h4 {
  font-size: 16px;
  margin-bottom: 15px;
  font-weight: 800;
}

.footer-column ul {
  list-style: none;
}

.footer-column ul li {
  margin-bottom: 8px;
  font-size: 14px;
  font-weight: 600;
}

/* Styling Kontak dengan Ikon */
.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 10px;
  font-size: 14px;
  font-weight: 600;
}

/* Bagian Media Sosial */
.social-section {
  margin-top: 25px;
}

.social-icons {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.social-icons img {
  width: 24px;
  height: 24px;
  cursor: pointer;
}

.footer-logo-img {
  width: 220px; /* Ukuran logo dikecilkan agar proporsional */
  height: auto;
  display: block;
}

.copyright {
  text-align: center;
  font-size: 12px;
  margin-top: 20px;
  font-weight: bold;
  color: #333;
}

</style>
</head>

<body>

<!-- ================= HEADER ================= -->
<header class="navbar">
  <div class="logo">
    <img src="../gambar/REBON LOGO GRADIENT presisi.png" alt="Rebon Adventure"/>
  </div>

  <nav>
        <a href="home1.php" class="active1">Home</a>
        <a href="open_trip.php" class="active2">Open</a>
        <a href="private_trip.php" class="active3">Private</a>
        <a href="tentang_kami.php" class="active4">Tentang Kami</a>
        <a href="profiluser.php"><?php if (isset($_SESSION['username'])): ?>
            <!-- JIKA SUDAH LOGIN -->
            <span style="color:blue; margin-right:10px;">
              👤 <?php echo $_SESSION['username']; ?>
            </span>
        </a>
            <a href="logout_user.php">
              <button class="active5" onclick="return confirm('Yakin ingin logout?')">Logout</button>
            </a>

        <?php else: ?>
            <!-- JIKA BELUM LOGIN -->
            <a href="login_user.php">
              <button class="active5">Masuk</button>
            </a>
        <?php endif; ?>
      </nav>
</header>

<!-- ================= FORM ================= -->
<div class="container">
  <div class="form-box">

    <!-- Tombol kembali -->
    <a href="profiluser.php" class="back-btn">←</a>

    <h2>EDIT PROFILE</h2>

    <h3>
      Hi,<br>
      <?php if (isset($_SESSION['username'])): ?>
        <span class="username">
          👤 <?php echo $_SESSION['username']; ?>
        </span>
      <?php endif; ?>
    </h3>

    <form method="POST" action="proses_edit_user.php">

      <div class="input-group">
        <label>Username Saat Ini</label>
        <input type="text" value="<?php echo $_SESSION['username']; ?>" readonly>
      </div>

      <div class="input-group">
        <label>Username Baru</label>
        <input type="text" name="username" placeholder="Masukkan username baru">
      </div>

      <div class="input-group">
        <label>Password Lama</label>
        <input type="password" name="old_password" placeholder="Masukkan password lama">
      </div>

      <div class="input-group">
        <label>Password Baru</label>
        <input type="password" name="new_password" placeholder="Password baru">
      </div>

      <div class="input-group">
        <label>Konfirmasi Password Baru</label>
        <input type="password" name="confirm_password" placeholder="Ulangi password baru">
      </div>
<<<<<<< HEAD
      
=======

      <!-- <a href="#"><p class="forgot">Lupa password?</p></a> -->

>>>>>>> f59a1b8c7eaca618524ae61f726f82c695e237b0
      <button type="submit" class="btn">Simpan Perubahan</button>

    </form>
  </div>
</div>
<!-- ================= FOOTER ================= -->
<footer>
      <div class="footer-content">
        <div class="footer-column logo-col">
          <img
            src="../gambar/logo-rebon.png"
            alt="Rebon Adventure Logo"
            class="footer-logo-img"
          />
        </div>

        <div class="footer-column">
          <h4>KONTAK KAMI</h4>
          <div class="contact-item">
            <span class="icon">✉</span>
            <p>rebonadventure@gmail.com</p>
          </div>
          <div class="contact-item">
            <span class="icon">📞</span>
            <p>+62 812-3456-7890</p>
          </div>
          <div class="contact-item">
            <span class="icon">📍</span>
            <p>Jl. sukawera No. 15,<br />Cirebon, Indonesia</p>
          </div>
        </div>

        <div class="footer-column">
          <h4>LAYANAN KAMI</h4>
          <ul>
            <li>OPEN TRIP</li>
            <li>PRIVATE TRIP</li>
          </ul>
        </div>

        <div class="footer-column">
          <h4>INFORMASI</h4>
          <ul>
            <li>TENTANG KAMI</li>
            <li>TRIP TERSEDIA</li>
            <li>FAQ</li>
          </ul>

          <div class="social-section">
            <h4>FOLLOW US ON</h4>
            <div class="social-icons">
              <img src="../gambar/fb-icon.png" alt="FB" />
              <img src="../gambar/ig-icon.png" alt="IG" />
              <img src="../gambar/tt-icon.png" alt="TK" />
            </div>
          </div>
        </div>
      </div>

      <div class="copyright">© 2026 REBON ADVENTURE. ALL RIGHTS RESERVED.</div>
    </footer>
</body>
</html>