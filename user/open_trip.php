<?php
session_start();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Open Trip</title>

  <style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: sans-serif;
}

body {
  background: linear-gradient(180deg, #5b2bbf, #8e74db);
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

/* GRID TRIP */

.trip-container {
  padding: 60px 80px;
}

.trip-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 40px;
}

/* CARD */

.card {
  background: #f4f0e5;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
}

.card-img {
  position: relative;
}

.card-img img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.badge {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background: #6b3df5;
  color: white;
  padding: 5px 10px;
  border-radius: 6px;
  font-size: 12px;
}

.card-body {
  padding: 15px;
}

.title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.seat {
  font-size: 12px;
}

.via {
  font-size: 13px;
  margin-bottom: 10px;
}

.date {
  font-size: 14px;
  margin-bottom: 10px;
}

.price {
  font-weight: bold;
  font-size: 20px;
}

.price span {
  font-size: 14px;
  font-weight: normal;
}

/* FOOTER */

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
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="home.css" />
    <title>Open Trip</title>
  </head>
  <body>
    <!-- NAVBAR -->
    <header class="navbar">
      <div class="logo">
        <img
          src="../gambar/REBON LOGO GRADIENT presisi.png"
          alt="Rebon Adventure"
        />
      </div>

      <nav>
        <a href="home1.php" class="active1">Home</a>
        <a href="open_trip.php" class="active2">Open</a>
        <a href="private_trip.php" class="active3">Private</a>
        <a href="tentang_kami.php" class="active4">Tentang Kami</a>
        <a href="profile.php"><?php if (isset($_SESSION['username'])): ?>
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

    <!-- OPEN TRIP -->
    <section class="trip-container">
      <div class="trip-grid">
        <!-- CARD -->
        <a href="ot_katalog.php">
          <div class="card">
            <div class="card-img">
              <img src="../gambar/gunung.jpg" />
              <span class="badge">1 Hari</span>
            </div>

            <div class="card-body">
              <div class="title-row">
                <h3>GN. Ciremai</h3>
                <span class="seat">5 / 10 SEAT</span>
              </div>

              <p class="via">Via Apuy</p>

              <p class="date">📅 Tanggal 28 - 29 Maret 2026</p>

              <p class="price">Rp. 200.000 <span>/ Pax</span></p>
            </div>
          </div>
        </a>

        <!-- DUPLIKASI CARD -->
        <div class="card">
          <div class="card-img">
            <img src="../gambar/gunung.jpg" />
            <span class="badge">1 Hari</span>
          </div>

          <div class="card-body">
            <div class="title-row">
              <h3>GN. Ciremai</h3>
              <span class="seat">5 / 10 SEAT</span>
            </div>
            <p class="via">Via Apuy</p>
            <p class="date">📅 Tanggal 28 - 29 Maret 2026</p>
            <p class="price">Rp. 200.000 <span>/ Pax</span></p>
          </div>
        </div>

        <div class="card">
          <div class="card-img">
            <img src="../gambar/gunung.jpg" />
            <span class="badge">1 Hari</span>
          </div>
          <div class="card-body">
            <div class="title-row">
              <h3>GN. Ciremai</h3>
              <span class="seat">5 / 10 SEAT</span>
            </div>
            <p class="via">Via Apuy</p>
            <p class="date">📅 Tanggal 28 - 29 Maret 2026</p>
            <p class="price">Rp. 200.000 <span>/ Pax</span></p>
          </div>
        </div>

        <div class="card">
          <div class="card-img">
            <img src="../gambar/gunung.jpg" />
            <span class="badge">1 Hari</span>
          </div>
          <div class="card-body">
            <div class="title-row">
              <h3>GN. Ciremai</h3>
              <span class="seat">5 / 10 SEAT</span>
            </div>
            <p class="via">Via Apuy</p>
            <p class="date">📅 Tanggal 28 - 29 Maret 2026</p>
            <p class="price">Rp. 200.000 <span>/ Pax</span></p>
          </div>
        </div>

        <div class="card">
          <div class="card-img">
            <img src="../gambar/gunung.jpg" />
            <span class="badge">1 Hari</span>
          </div>
          <div class="card-body">
            <div class="title-row">
              <h3>GN. Ciremai</h3>
              <span class="seat">5 / 10 SEAT</span>
            </div>
            <p class="via">Via Apuy</p>
            <p class="date">📅 Tanggal 28 - 29 Maret 2026</p>
            <p class="price">Rp. 200.000 <span>/ Pax</span></p>
          </div>
        </div>

        <div class="card">
          <div class="card-img">
            <img src="../gambar/gunung.jpg" />
            <span class="badge">1 Hari</span>
          </div>
          <div class="card-body">
            <div class="title-row">
              <h3>GN. Ciremai</h3>
              <span class="seat">5 / 10 SEAT</span>
            </div>
            <p class="via">Via Apuy</p>
            <p class="date">📅 Tanggal 28 - 29 Maret 2026</p>
            <p class="price">Rp. 200.000 <span>/ Pax</span></p>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->

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
