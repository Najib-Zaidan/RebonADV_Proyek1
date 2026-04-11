<?php
session_start();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ot katalog</title>

  <style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: sans-serif;
}

body {
  background: #efe8cc;
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

/* CONTAINER */
.container {
  padding: 40px;
}

/* TOP HEADER */
.top-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.top-header h1 {
  color: #5a3fc0;
}

/* PAX BOX */
.pax-box {
  border: 2px solid purple;
  padding: 10px 15px;
  border-radius: 10px;
  text-align: center;
}

.pax-box strong {
  color: purple;
}

/* GRID */
.grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  margin-top: 20px;
}

/* IMAGE */
.main-img {
  width: 100%;
  border-radius: 10px;
}

.thumbs {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.thumbs img {
  width: 120px;
  border-radius: 8px;
}

/* RIGHT */
.right h3 {
  margin-bottom: 10px;
}

.right p {
  margin-bottom: 20px;
}

/* FASILITAS */
.fasilitas {
  display: flex;
  gap: 40px;
}

.include li {
  margin-left: 15px;
  margin-bottom: 10px;
}

.exclude li {
  margin-left: 15px;
  margin-bottom: 10px;
}

/* MEETING POINT */
.meeting-point {
  margin: 10px 0 20px;
  padding-left: 20px;
  margin-top: 15px;
}

.meeting-point li {
  margin-bottom: 8px;
  font-weight: 500;
}

/* CATATAN */
.catatan {
  padding-left: 20px;
}

.catatan li {
  margin-bottom: 6px;
  font-size: 14px;
}

/* PRICE CARD */
.price-card {
  margin-top: 20px;
  background: #dcdcdc;
  padding: 20px;
  border-radius: 12px;
}

.price-box {
  background: linear-gradient(135deg, #7b5cff, #5a3fc0);
  color: white;
  padding: 15px;
  border-radius: 10px;
  margin: 10px 0;
}

.price-card button {
  width: 100%;
  padding: 10px;
  background: #2f2f8f;
  color: white;
  border: none;
  border-radius: 8px;
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

/* LABEL INCLUDE EXCLUDE */
.label {
  font-weight: bold;
  margin-bottom: 8px;
  color: #555;
}

/* ITINERARY */
.itinerary {
  margin-top: 50px;
}

.timeline {
  list-style: none;
  margin-top: 15px;
  padding-left: 20px;
  border-left: 2px solid #aaa;
}

.timeline li {
  margin-bottom: 20px;
  position: relative;
}

.timeline li::before {
  content: "";
  position: absolute;
  left: -7px;
  top: 5px;
  width: 10px;
  height: 10px;
  background: #6b3df5;
  border-radius: 50%;
}

.timeline span {
  font-weight: bold;
  display: block;
  margin-left: 15px;
}

.timeline p {
  margin-left: 15px;
}





</style>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>Gn. Ciremai</title>
    <link rel="stylesheet" href="ot_katalog.css" />
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

    <main class="container">
      <!-- TITLE + PAX -->
      <div class="top-header">
        <div>
          <h1>Gn. Ciremai</h1>
          <p>Via Apuy, Majalengka, Jawa Barat</p>
        </div>

        <div class="pax-box">
          <strong>8 / 15 Pax</strong>
          <span>Sisa 7 pax lagi!</span>
        </div>
      </div>

      <!-- MAIN GRID -->
      <div class="grid">
        <!-- LEFT -->
        <div class="left">
          <img src="../gambar/gunung.jpg" class="main-img" />

          <div class="thumbs">
            <img src="../gambar/gunung.jpg" />
            <img src="../gambar/gunung.jpg" />
          </div>

          <!-- CARD -->
          <div class="price-card">
            <p class="date">Sabtu, 28 Maret 2026</p>

            <div class="price-box">
              <p>Harga Per Pax</p>
              <h2>Rp. 200.000</h2>
            </div>

            <a href="form.php"> <button>Pesan sekarang</button></a>
          </div>
        </div>

        <!-- RIGHT -->
        <div class="right">
          <p>
            Gunung Ciremai (sering kali secara salah kaprah dinamakan Ceremai)
            adalah gunung berapi kerucut yang secara administratif termasuk
            dalam wilayah Kabupaten Kuningan dan Kabupaten Majalengka, Provinsi
            Jawa Barat. Gunung ini memiliki ketinggian 3.078 mdpl dan merupakan
            gunung tertinggi di Jawa Barat.
          </p>

          <p>
            Gunung ini memiliki kawah ganda. Kawah barat berdiameter sekitar 400
            m dan kawah timur sekitar 600 m. Pada ketinggian sekitar 2.900 mdpl
            di lereng selatan terdapat bekas titik letusan yang dinamakan Gowa
            Walet.
          </p>

          <p>
            Saat ini, Gunung Ciremai termasuk dalam kawasan Taman Nasional
            Gunung Ciremai (TNGC) dengan luas total sekitar 15.000 hektar.
          </p>

          <h3>FASILITAS</h3>

          <div class="fasilitas">
            <div class="include">
              <p class="label">INCLUDE</p>
              <ul>
                <li>Transportasi PP</li>
                <li>SIMAKSI</li>
                <li>Tenda</li>
                <li>Guide</li>
              </ul>
            </div>

            <div class="exclude">
              <p class="label">EXCLUDE</p>
              <ul>
                <li>Ojek</li>
                <li>Obat pribadi</li>
              </ul>
            </div>
          </div>

          <h3>MEETING POINT</h3>

          <ul class="meeting-point">
            <li>19.00 WIB - Indramayu (Bundaran Kijang)</li>
            <li>21.00 WIB - Cirebon (Stasiun Kejaksan)</li>
            <li>08.00 WIB - Majalengka (Basecamp Apuy)</li>
          </ul>

          <h3>CATATAN TAMBAHAN</h3>

          <ol class="catatan">
            <li>Pendaftaran ditutup 1 bulan sebelum keberangkatan</li>
            <li>DP minimal Rp. 200.000 sebagai tanda jadi</li>
            <li>Apabila cancel, DP hangus</li>
            <li>Pelunasan H-10 sebelum keberangkatan</li>
            <li>Datang ke meeting point sesuai jadwal</li>
            <li>Persiapkan fisik dan mental dengan baik</li>
            <li>Jaga pola tidur dan istirahat</li>
            <li>Wajib menaati peraturan basecamp & guide</li>
            <li>Info lengkap tersedia di grup WhatsApp</li>
          </ol>
        </div>
      </div>
      <!-- ITINERARY -->
      <div class="itinerary">
        <h3>ITINERARY</h3>

        <ul class="timeline">
          <li>
            <span>00.00 - 01.00</span>
            <p>Meeting point & briefing peserta</p>
          </li>

          <li>
            <span>01.00 - 01.30</span>
            <p>Perjalanan ke basecamp</p>
          </li>

          <li>
            <span>01.30 - 02.00</span>
            <p>Pemanasan & mulai pendakian</p>
          </li>

          <li>
            <span>02.00 - 05.30</span>
            <p>Trek awal hutan & tanjakan stabil</p>
          </li>

          <li>
            <span>05.30 - 07.00</span>
            <p>Menuju puncak & summit attack</p>
          </li>

          <li>
            <span>07.00 - 08.00</span>
            <p>Tiba di puncak & foto</p>
          </li>

          <li>
            <span>08.00 - 13.00</span>
            <p>Turun ke basecamp</p>
          </li>

          <li>
            <span>13.00 - 14.00</span>
            <p>Trip selesai</p>
          </li>
        </ul>
      </div>
    </main>
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

