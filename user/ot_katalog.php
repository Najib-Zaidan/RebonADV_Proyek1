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

/* IMAGE WORKSPACE */
.img-wrapper {
  position: relative;
  width: 100%;
}

.main-img {
  width: 100%;
  border-radius: 10px;
  aspect-ratio: 3/2;
  object-fit: cover;
}

/* BARU: Rating Badge di pojok kanan atas gambar utama */
.rating-badge-main {
  position: absolute;
  top: 15px;
  right: 15px;
  background: rgba(255, 255, 255, 0.95);
  color: #333;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 5px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  z-index: 10;
}

.rating-badge-main .star-main {
  color: #ffca28;
  font-size: 16px;
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
  display: flex;
  justify-content: space-between; 
  align-items: center; 
}

.price-box .harga-kiri p {
  margin: 0 0 5px 0;
  font-size: 14px;
}

.price-box .harga-kiri h2 {
  margin: 0;
}

.price-box .dp {
  margin: 0;
  font-size: 14px;
  font-weight: bold;
  opacity: 0.9; 
}

.price-card .btn-pesan {
  display: block;
  text-align: center;
  text-decoration: none;
  width: 100%;
  box-sizing: border-box;
  padding: 10px;
  background: #2f2f8f;
  color: white;
  border: none;
  border-radius: 8px;
}

.price-card .btn-pesan:hover {
  background: #1e1e64;
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

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 10px;
  font-size: 14px;
  font-weight: 600;
}

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
  width: 220px; 
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

/* BARU: CSS Seksi Ulasan User */
.ulasan-section {
  margin-top: 50px;
  background: #fdfbf3;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.ulasan-section h3 {
  color: #5a3fc0;
  margin-bottom: 20px;
  border-bottom: 2px solid #efe8cc;
  padding-bottom: 10px;
}

.ulasan-container {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.ulasan-card {
  background: white;
  padding: 15px 20px;
  border-radius: 8px;
  border-left: 4px solid #6b3df5;
}

.ulasan-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.ulasan-user {
  font-weight: bold;
  color: #333;
}

.ulasan-stars {
  color: #ffca28;
  font-size: 14px;
}

.ulasan-text {
  font-size: 14px;
  color: #555;
  line-height: 1.5;
  font-style: italic;
}

.ulasan-kosong {
  text-align: center;
  color: #777;
  font-size: 14px;
  padding: 20px 0;
}

/* ===== LOGIN MODAL POPUP ===== */
#loginModal {
  display: none;
  position: fixed;
  inset: 0;
  z-index: 9999;
  align-items: center;
  justify-content: center;
}

#loginModal.active {
  display: flex;
}

.modal-backdrop {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.50);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  animation: fadeIn 0.25s ease;
}

.modal-box {
  position: relative;
  z-index: 1;
  background: #ffffff;
  border-radius: 20px;
  padding: 40px 36px 32px;
  width: 100%;
  max-width: 400px;
  text-align: center;
  box-shadow: 0 20px 60px rgba(75, 0, 255, 0.25);
  animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  font-family: sans-serif;
}

.modal-icon {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background: linear-gradient(135deg, #eee3ff, #d4b8ff);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  font-size: 32px;
}

.modal-box h2 {
  font-size: 22px;
  font-weight: 800;
  color: #111;
  margin: 0 0 10px;
}

.modal-box p {
  font-size: 14px;
  color: #666;
  line-height: 1.6;
  margin: 0 0 28px;
}

.modal-box p span {
  color: #6b3df5;
  font-weight: 700;
}

.modal-btn-group {
  display: flex;
  gap: 12px;
}

.modal-btn-login {
  flex: 1;
  padding: 13px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  font-family: inherit;
  font-size: 15px;
  font-weight: 700;
  background: linear-gradient(180deg, #4b00ff 0%, #7a2cff 100%);
  color: #fff;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.modal-btn-login:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(107, 61, 245, 0.4);
}

.modal-btn-close {
  flex: 1;
  padding: 13px;
  border-radius: 10px;
  border: 2px solid #e0d6ff;
  cursor: pointer;
  font-family: inherit;
  font-size: 15px;
  font-weight: 700;
  background: transparent;
  color: #6b3df5;
  transition: background 0.2s ease, border-color 0.2s ease;
}

.modal-btn-close:hover {
  background: #f3eeff;
  border-color: #6b3df5;
}

.modal-close-x {
  position: absolute;
  top: 14px;
  right: 18px;
  background: none;
  border: none;
  font-size: 20px;
  color: #aaa;
  cursor: pointer;
  line-height: 1;
  padding: 4px;
  transition: color 0.2s;
}

.modal-close-x:hover {
  color: #333;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to   { opacity: 1; }
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px) scale(0.95); }
  to   { opacity: 1; transform: translateY(0)    scale(1);    }
}
/* ===== END MODAL ===== */

/* ===== MODAL BELUM ADA PESERTA ===== */
#pesertaModal {
  display: none;
  position: fixed;
  inset: 0;
  z-index: 9999;
  align-items: center;
  justify-content: center;
}

#pesertaModal.active {
  display: flex;
}
/* ===== END MODAL PESERTA ===== */
  </style>
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
        <a href="profiluser.php"><?php if (isset($_SESSION['username'])): ?>
            <span style="color:blue; margin-right:10px;">
              👤 <?php echo $_SESSION['username']; ?>
            </span>
        </a>
            <a href="logout_user.php">
              <button class="active5" onclick="return confirm('Yakin ingin logout?')">Logout</button>
            </a>

        <?php else: ?>
            <a href="login_user.php">
              <button class="active5">Masuk</button>
            </a>
        <?php endif; ?>
      </nav>
    </header>

    <?php
    require 'fungsi.php';

    $id_trip = intval($_GET['id']);

    // Menghitung sisa kuota yang tersedia
    $sisa = ambil(kueri("SELECT
                (t.kuota - IFNULL(SUM(b.jumlah_peserta), 0)) sisa
                FROM trip t
                LEFT JOIN booking b
                ON t.id_trip = b.id_trip AND b.status != 'Dibatalkan'
                WHERE t.id_trip = $id_trip"));

    // UPDATE QUERY: Menambahkan subquery AVG rating keseluruhan trip
    $data_trip = kueri("SELECT t.*, k.*, tj.tujuan,
                       (SELECT AVG(rating) FROM rating r WHERE r.id_trip = t.id_trip) as rata_rating
                       FROM trip t 
                       INNER JOIN katalog k ON t.id_trip = k.id_trip 
                       INNER JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                       WHERE t.id_trip = $id_trip");
    $trip = ambil($data_trip);

    $data_gambar = kueri("SELECT * FROM gambar WHERE id_trip = $id_trip");

    $data_include = kueri("SELECT * FROM fasilitas WHERE id_trip = $id_trip AND jenis = 'Include'");
    $data_exclude = kueri("SELECT * FROM fasilitas WHERE id_trip = $id_trip AND jenis = 'Exclude'");

    $data_meet = kueri("SELECT * FROM meetpoint WHERE id_trip = $id_trip ORDER BY waktu ASC");

    $data_iten = kueri("SELECT * FROM itenerary WHERE id_trip = $id_trip ORDER BY mulai ASC");

    // NEW QUERY: Menarik data review/ulasan personal dari para user
    $data_ulasan = kueri("SELECT r.*, a.username 
                          FROM rating r 
                          INNER JOIN akun a ON r.id_akun = a.id_akun 
                          WHERE r.id_trip = $id_trip 
                          ORDER BY r.id_rating DESC");

    // CEK jumlah peserta_open milik akun yang sedang login
    $jumlah_peserta_akun = 0;
    if (isset($_SESSION['id_akun'])) {
        $id_akun_login = intval($_SESSION['id_akun']);
        $cek_peserta = kueri("SELECT COUNT(*) as total FROM peserta_open WHERE id_akun = $id_akun_login");
        $row_peserta  = ambil($cek_peserta);
        $jumlah_peserta_akun = (int)$row_peserta['total'];
    }

    function tgl_indo($tanggal) {
        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $pecahkan = explode('-', $tanggal);
        return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
    }
    ?>

<main class="container">
  <div class="top-header">
        <div>
          <h1><?= htmlspecialchars($trip['tujuan']); ?></h1>
          <p>Via <?= htmlspecialchars($trip['rute']); ?></p>
        </div>

        <div class="pax-box">
          <strong>Total Seat <?= $trip['kuota']; ?> Pax</strong>
          <br>
          <span>Sisa <?= $sisa['sisa'] ?> pax lagi!</span>
        </div>
  </div>

    <div class="grid">
        <div class="left">
            <?php 
            $gambar_list = [];
            while($g = ambil($data_gambar)) { $gambar_list[] = $g; }
            $main_img = !empty($gambar_list) ? $gambar_list[0]['nama_file'] : 'default.jpg';
            
            // Format angka rating rata-rata desimal
            $rating_header = $trip['rata_rating'] ? number_format($trip['rata_rating'], 1) : null;
            ?>
            
            <div class="img-wrapper">
                <img src="../gambar/upload/<?= $main_img; ?>" id="mainImg" class="main-img" />
                
                <?php if ($rating_header): ?>
                  <div class="rating-badge-main">
                    <span class="star-main">★</span> <?= $rating_header; ?> / 5.0
                  </div>
                <?php else: ?>
                  <div class="rating-badge-main" style="font-size: 11px; color: #666;">
                    Belum ada ulasan
                  </div>
                <?php endif; ?>
            </div>

            <div class="thumbs">
                <?php foreach($gambar_list as $img) : ?>
                    <img src="../gambar/upload/<?= $img['nama_file']; ?>" class="thumb-img" onclick="changeImage(this)">
                <?php endforeach; ?>
            </div>

            <div class="price-card">
                <p class="date"><?= tgl_indo($trip['tgl_berangkat']); ?></p>

                <div class="price-box">
                    <div class="harga-kiri">
                        <p>Harga Per Pax</p>
                        <h2>Rp <?= number_format($trip['harga'], 0, ',', '.'); ?></h2>
                    </div>
                    <p class="dp">DP Rp <?= number_format($trip['harga_dp'], 0, ',', '.'); ?></p>
                </div>

                <a href="pilih_peserta.php?id=<?= $trip['id_trip']; ?>" class="btn-pesan" id="btnPesan">Pesan sekarang</a>
            </div>
        </div>

        <div class="right">
            <div class="deskripsi">
                <?= nl2br(htmlspecialchars($trip['deskripsi'])); ?>
            </div>

            <h3>FASILITAS</h3>
            <div class="fasilitas">
                <div class="include">
                    <p class="label">INCLUDE</p>
                    <ul>
                        <?php while($inc = ambil($data_include)) : ?>
                            <li><?= htmlspecialchars($inc['fasilitas']); ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <div class="exclude">
                    <p class="label">EXCLUDE</p>
                    <ul>
                        <?php while($exc = ambil($data_exclude)) : ?>
                            <li><?= htmlspecialchars($exc['fasilitas']); ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <h3>MEETING POINT</h3>
            <ul class="meeting-point">
                <?php while($mp = ambil($data_meet)) : ?>
                    <li><?= date('H.i', strtotime($mp['waktu'])); ?> WIB - <?= htmlspecialchars($mp['kota']); ?> (<?= htmlspecialchars($mp['daerah']); ?>)</li>
                <?php endwhile; ?>
            </ul>

            <h3>CATATAN TAMBAHAN</h3>
            <div class="catatan">
                <?= nl2br(htmlspecialchars($trip['catatan'])); ?>
            </div>
        </div>
    </div>

    <div class="itinerary">
        <h3>ITINERARY</h3>
        <ul class="timeline">
            <?php while($it = ambil($data_iten)) : ?>
                <li>
                    <span><?= date('H.i', strtotime($it['mulai'])); ?> - <?= date('H.i', strtotime($it['selesai'])); ?></span>
                    <p><?= htmlspecialchars($it['kegiatan']); ?></p>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <div class="ulasan-section">
        <h3>Ulasan & Rating Pengunjung</h3>
        <div class="ulasan-container">
            <?php 
            $ada_ulasan = false;
            while($ul = ambil($data_ulasan)) : 
                $ada_ulasan = true;
            ?>
                <div class="ulasan-card">
                    <div class="ulasan-header">
                        <span class="ulasan-user">👤 <?= htmlspecialchars($ul['username']); ?></span>
                        <span class="ulasan-stars">
                            <?php 
                            // Membuat looping cetak bintang emas sesuai isi nilai int rating
                            for($bintang = 1; $bintang <= 5; $bintang++) {
                                echo $bintang <= $ul['rating'] ? '★' : '☆';
                            }
                            ?>
                        </span>
                    </div>
                    <p class="ulasan-text">
                        "<?= !empty($ul['ulasan']) ? nl2br(htmlspecialchars($ul['ulasan'])) : 'Pengunjung tidak memberikan deskripsi ulasan.'; ?>"
                    </p>
                </div>
            <?php endwhile; ?>

            <?php if(!$ada_ulasan): ?>
                <div class="ulasan-kosong">
                    <p>Belum ada ulasan untuk paket trip ini. Jadilah yang pertama memberikan review!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

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
              <a href="https://www.instagram.com/rebon_adv?igsh=MW4xcDc1YTJhMzRpMw==" target="_blank" rel="noopener noreferrer">
                <img src="../gambar/ig-icon.png" alt="Instagram" />
              </a>
              <a href="https://www.tiktok.com/@rebon.adventure?is_from_webapp=1&sender_device=pc" target="_blank" rel="noopener noreferrer">
                <img src="../gambar/tt-icon.png" alt="Tiktok" />
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="copyright">© 2026 REBON ADVENTURE. ALL RIGHTS RESERVED.</div>
    </footer>
</body>

<!-- ===== LOGIN MODAL ===== -->
<div id="loginModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-backdrop" onclick="closeLoginModal()"></div>
  <div class="modal-box">
    <button class="modal-close-x" onclick="closeLoginModal()" aria-label="Tutup">&#x2715;</button>
    <div class="modal-icon">🔒</div>
    <h2 id="modalTitle">Login Diperlukan</h2>
    <p>Kamu harus <span>login</span> terlebih dahulu sebelum memesan <span>Open Trip</span>. Yuk masuk ke akunmu!</p>
    <div class="modal-btn-group">
      <a href="login_user.php" class="modal-btn-login">Masuk Sekarang</a>
      <button class="modal-btn-close" onclick="closeLoginModal()">Nanti Saja</button>
    </div>
  </div>
</div>
<!-- ===== END MODAL ===== -->

<!-- ===== MODAL BELUM ADA PESERTA ===== -->
<div id="pesertaModal" role="dialog" aria-modal="true" aria-labelledby="pesertaModalTitle">
  <div class="modal-backdrop" onclick="closePesertaModal()"></div>
  <div class="modal-box">
    <button class="modal-close-x" onclick="closePesertaModal()" aria-label="Tutup">&#x2715;</button>
    <div class="modal-icon">👥</div>
    <h2 id="pesertaModalTitle">Belum Ada Peserta</h2>
    <p>Kamu belum menambahkan <span>data peserta</span> apapun. Tambahkan peserta terlebih dahulu sebelum melanjutkan pemesanan <span>Open Trip</span>.</p>
    <div class="modal-btn-group">
      <a href="form.php" class="modal-btn-login">Tambah Peserta</a>
      <button class="modal-btn-close" onclick="closePesertaModal()">Nanti Saja</button>
    </div>
  </div>
</div>
<!-- ===== END MODAL PESERTA ===== -->

<script>
function changeImage(element) {
    document.getElementById('mainImg').src = element.src;
}

// Status login & jumlah peserta dari PHP session
const isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;
const jumlahPeserta = <?php echo $jumlah_peserta_akun; ?>;

// ===== MODAL LOGIN =====
function showLoginModal() {
  document.getElementById('loginModal').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeLoginModal() {
  document.getElementById('loginModal').classList.remove('active');
  document.body.style.overflow = '';
}

// ===== MODAL PESERTA =====
function showPesertaModal() {
  document.getElementById('pesertaModal').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closePesertaModal() {
  document.getElementById('pesertaModal').classList.remove('active');
  document.body.style.overflow = '';
}

// Tutup semua modal dengan tombol Escape
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeLoginModal();
    closePesertaModal();
  }
});

// Intercept klik tombol "Pesan sekarang" — tiga kondisi:
// 1. Belum login          → modal login
// 2. Login tapi 0 peserta → modal peringatan peserta
// 3. Login & ada peserta  → lanjut ke pilih_peserta.php
document.getElementById('btnPesan').addEventListener('click', function(e) {
  if (!isLoggedIn) {
    e.preventDefault();
    showLoginModal();
  } else if (jumlahPeserta === 0) {
    e.preventDefault();
    showPesertaModal();
  }
  // else: href berjalan normal
});
</script>
</html>