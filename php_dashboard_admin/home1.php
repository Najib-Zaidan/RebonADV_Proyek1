<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Home</title>

  <style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
}

body {
  background: #e7e2c8;
}

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

nav .active1 {
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
  height: 600px;
  background: url("../gambar/prau.jpg") center/cover no-repeat;
  display: flex;
  align-items: center;
  padding-left: 80px;
  color: white;
}

.hero h1 {
  font-size: 42px;
  line-height: 1.4;
  margin-bottom: 20px;
}

.hero span {
  color: #c8b6ff;
}

.hero b {
  color: #ffc107;
}

.hero-logo {
  width: 200px;
  margin-bottom: 20px;
}

.hero-content a {
  margin-top: 20px;
  padding: 12px 25px;
  border: none;
  border-radius: 25px;
  background: white;
  color: #333;
  cursor: pointer;
  text-decoration: none;
}

/* TRIP TERSEDIA */

.trip {
  padding: 60px 80px;
}

.trip-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.trip-header h2 {
  color: #5a3ec8;
}

.trip-header a {
  text-decoration: none;
  color: #5a3ec8;
}

.trip-container {
  background: linear-gradient(135deg, #7f5af0, #3b1fa5);
  padding: 30px;
  border-radius: 15px;

  display: flex;
  gap: 25px;
}

.trip-container a {
  text-decoration: none;
}

.trip-card {
  background: white;
  width: 250px;
  border-radius: 10px;
  overflow: hidden;
  padding-bottom: 15px;
}

.trip-card img {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.trip-card h3 {
  margin: 10px;
}

.trip-card p {
  margin: 0 10px;
  font-size: 14px;
  color: #555;
}

.trip-card .date {
  margin-top: 5px;
}

.price {
  display: block;
  margin: 10px;
  font-weight: bold;
}

/* PRIVATE TRIP */

.private-trip {
  margin: 60px 80px;
  background: #f1edf8;
  padding: 40px;
  border-radius: 15px;

  display: flex;
  align-items: center;
  gap: 40px;
}

.private-text {
  flex: 1;
}

.private-text h2 {
  color: #5a3ec8;
  margin-bottom: 15px;
}

.private-text p {
  margin-bottom: 10px;
}

.private-text .p_bawah {
  margin-bottom: 50px;
}

.private-text a {
  margin-top: 20px;
  padding: 10px 20px;
  border: none;
  background: #4f46e5;
  color: white;
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
}

.private-img img {
  width: 450px;
  border-radius: 12px;
}

/* GALERI */

.galeri {
  padding: 60px 80px;
  text-align: center;
}

.galeri h2 {
  color: #5a3ec8;
  margin-bottom: 40px;
}

.galeri-container {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 25px;
}

.galeri-container img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  border-radius: 10px;
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

@media (max-width: 1024px) {
  .navbar {
    padding: 20px 40px;
  }

  .hero {
    padding-left: 40px;
    height: 500px;
  }

  .trip {
    padding: 40px;
  }

  .trip-container {
    flex-wrap: wrap;
    justify-content: center;
  }

  .private-trip {
    flex-direction: column;
    text-align: center;
  }

  .private-img img {
    width: 100%;
    max-width: 400px;
  }

  .galeri-container {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* MOBILE */
@media (max-width: 768px) {
  /* NAVBAR */
  .navbar {
    flex-direction: column;
    padding: 20px;
    gap: 15px;
  }

  nav {
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
  }

  /* HERO */
  .hero {
    height: auto;
    padding: 40px 20px;
    text-align: center;
    justify-content: center;
  }

  .hero h1 {
    font-size: 28px;
  }

  .hero-logo {
    width: 150px;
  }

  /* TRIP */
  .trip {
    padding: 30px 20px;
  }

  .trip-container {
    flex-direction: column;
    align-items: center;
  }

  .trip-card {
    width: 100%;
    max-width: 300px;
  }

  /* PRIVATE */
  .private-trip {
    margin: 30px 20px;
    padding: 20px;
  }

  /* GALERI */
  .galeri {
    padding: 30px 20px;
  }

  .galeri-container {
    grid-template-columns: 1fr;
  }

  /* FOOTER */
  .footer-content {
    flex-direction: column;
    text-align: center;
    align-items: center;
  }
}
    </style>
    
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="home.css" />
    <title>Home</title>
  </head>
  <body>
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
              <button class="active5">Logout</button>
            </a>

        <?php else: ?>
            <!-- JIKA BELUM LOGIN -->
            <a href="login_user.php">
              <button class="active5">Masuk</button>
            </a>
        <?php endif; ?>
      </nav>
    </header>

    <!-- HERO -->
    <section class="hero">
      <div class="hero-content">
        <img
          src="../gambar/REBON LOGO GRADIENT presisi.png"
          class="hero-logo"
        />

        <h1>
          Jelajahi Sudut <br />
          <span>Tersembunyi Indonesia</span><br />
          Bersama <b>Teman Baru</b>
        </h1>

        <a href="tentangkami.html">Tentang Kami</a>
      </div>
    </section>

    <!-- TRIP TERSEDIA -->
    <section class="trip">
      <div class="trip-header">
        <h2>Trip Tersedia</h2>
        <a href="open_trip.php">Lihat Selengkapnya →</a>
      </div>

      <div class="trip-container">
        <a href="ot_katalog.php">
          <div class="trip-card">
            <img src="../gambar/ciremai.jpeg" />
            <h3>GN. Ciremai</h3>
            <p>Via Apuy</p>
            <p class="date">Tanggal 28 - 29 Maret 2026</p>
            <span class="price">Rp. 325.000 / Pax</span>
          </div>
        </a>
        <div class="trip-card">
          <a href="ot_katalog.php">
            <img src="../gambar/prau.jpg" />
            <h3>GN. Prau</h3>
            <p>Via Patak Banteng</p>
            <p class="date">Tanggal 28 - 29 Maret 2026</p>
            <span class="price">Rp. 350.000 / Pax</span>
          </a>
        </div>

        <div class="trip-card">
          <a href="ot_katalog.php">
            <img src="../gambar/sumbing.jpg" />
            <h3>GN. Sumbing</h3>
            <p>Via Garung</p>
            <p class="date">Tanggal 28 - 29 Maret 2026</p>
            <span class="price">Rp. 350.000 / Pax</span>
          </a>
        </div>
      </div>
    </section>

    <!-- TRIP SESUKA HATI -->
    <section class="private-trip">
      <div class="private-text">
        <h2>Trip Sesuka Hati</h2>

        <p>Mau naik gunung tanpa ribet dan tanpa orang asing?</p>

        <p>Yuk ambil paket Private Trip!</p>

        <p class="p_bawah">
          Bisa atur tanggal sendiri, rombongan sendiri, bahkan konsep trip
          sesuai request kamu. Liburan jadi lebih intimate dan bebas drama.
        </p>

        <a href="private.html">RENCANAKAN TRIP SEKARANG</a>
      </div>

      <div class="private-img">
        <img src="../gambar/ciremai.jpeg" />
      </div>
    </section>

    <!-- GALERI -->
    <section class="galeri">
      <h2>GALERI KAMI</h2>

      <div class="galeri-container">
        <img src="../gambar/profil1.jpeg" />
        <img src="../gambar/profil2.jpeg" />
        <img src="../gambar/profil3.jpeg" />
        <img src="../gambar/gunung.jpg" />
        <img src="../gambar/gunung.jpg" />
        <img src="../gambar/gunung.jpg" />
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