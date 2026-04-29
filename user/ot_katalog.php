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
  aspect-ratio: 3/2;
  object-fit: cover;
}

.thumbs {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.thumbs img {
  width: 120px;
  aspect-ratio: 3 / 2;
    object-fit: cover;
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

.thumbs img {
    cursor: pointer;
    transition: 0.3s;
}
.thumbs img:hover {
    opacity: 0.8;
    border: 2px solid #6b3df5;
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

    <?php

require 'fungsi.php';

$id_trip = $_GET['id'];
$sisa = mysqli_num_rows(kueri("SELECT id_booking FROM booking b JOIN trip t ON b.id_trip = $id_trip WHERE b.status != 'Dibatalkan'"));
$sisa = ambil(kueri("SELECT
            (t.kuota - COUNT(b.id_trip)) sisa
            FROM trip t
            JOIN booking b
            ON t.id_trip = b.id_trip
            WHERE t.id_trip = $id_trip AND status != 'Dibatalkan'"));
$data_trip = kueri("SELECT * FROM trip 
                   INNER JOIN katalog ON trip.id_trip = katalog.id_trip 
                   WHERE trip.id_trip = $id_trip");
$trip = ambil($data_trip);


$data_gambar = kueri("SELECT * FROM gambar WHERE id_trip = $id_trip");

$data_include = kueri("SELECT * FROM fasilitas WHERE id_trip = $id_trip AND jenis = 'Include'");
$data_exclude = kueri("SELECT * FROM fasilitas WHERE id_trip = $id_trip AND jenis = 'Exclude'");

$data_meet = kueri("SELECT * FROM meetpoint WHERE id_trip = $id_trip ORDER BY waktu ASC");

$data_iten = kueri("SELECT * FROM itenerary WHERE id_trip = $id_trip ORDER BY mulai ASC");

function tgl_indo($tanggal) {
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}
?>

<main class="container">
  <div class="top-header">
        <div>
          <h1><?= $trip['tujuan']; ?></h1>
          <p>Via <?= $trip['rute']; ?></p>
        </div>

        <div class="pax-box">
          <strong><?= $sisa['sisa'] ?> / <?= $trip['kuota']; ?> Pax</strong>
          <span>Sisa <?= $sisa['sisa'] ?> pax lagi!</span>
        </div>
      </div>
    <div class="grid">
        <div class="left">
            <?php 
            // Ambil gambar pertama untuk main-img
            $gambar_list = [];
            while($g = ambil($data_gambar)) { $gambar_list[] = $g; }
            $main_img = !empty($gambar_list) ? $gambar_list[0]['nama_file'] : 'gunung.jpg';
            ?>
            <img src="../gambar/upload/<?= $main_img; ?>" id="mainImg" class="main-img" />

            <div class="thumbs">
                <?php foreach($gambar_list as $img) : ?>
                    <img src="../gambar/upload/<?= $img['nama_file']; ?>" / class="thumb-img" onclick="changeImage(this)">
                <?php endforeach; ?>
            </div>

            <div class="price-card">
                <p class="date"><?= tgl_indo($trip['tgl_berangkat']); ?></p>

                <div class="price-box">
                    <p>Harga Per Pax</p>
                    <h2>Rp. <?= number_format($trip['harga'], 0, ',', '.'); ?></h2>
                </div>

                <a href="pilih_peserta.php?id=<?= $trip['id_trip']; ?>"> <button>Pesan sekarang</button></a>
            </div>
        </div>

        <div class="right">
            <div class="deskripsi">
                <?= nl2br($trip['deskripsi']); ?>
            </div>

            <h3>FASILITAS</h3>
            <div class="fasilitas">
                <div class="include">
                    <p class="label">INCLUDE</p>
                    <ul>
                        <?php while($inc = ambil($data_include)) : ?>
                            <li><?= $inc['fasilitas']; ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <div class="exclude">
                    <p class="label">EXCLUDE</p>
                    <ul>
                        <?php while($exc = ambil($data_exclude)) : ?>
                            <li><?= $exc['fasilitas']; ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <h3>MEETING POINT</h3>
            <ul class="meeting-point">
                <?php while($mp = ambil($data_meet)) : ?>
                    <li><?= date('H.i', strtotime($mp['waktu'])); ?> WIB - <?= $mp['kota']; ?> (<?= $mp['daerah']; ?>)</li>
                <?php endwhile; ?>
            </ul>

            <h3>CATATAN TAMBAHAN</h3>
            <div class="catatan">
                <?= nl2br($trip['catatan']); ?>
            </div>
        </div>
    </div>

    <div class="itinerary">
        <h3>ITINERARY</h3>
        <ul class="timeline">
            <?php while($it = ambil($data_iten)) : ?>
                <li>
                    <span><?= date('H.i', strtotime($it['mulai'])); ?> - <?= date('H.i', strtotime($it['selesai'])); ?></span>
                    <p><?= $it['kegiatan']; ?></p>
                </li>
            <?php endwhile; ?>
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
  <script>
function changeImage(element) {
    // Mengambil element gambar utama berdasarkan ID, lalu mengubah sumbernya (src)
    document.getElementById('mainImg').src = element.src;
}
</script>

</html>

