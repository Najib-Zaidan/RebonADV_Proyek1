<?php
session_start();
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Open Trip</title>
<!-- <audio id="backsound" loop>
  <source src="../gambar/upload/dan.mp3" type="audio/mpeg">
</audio>

<div id="overlay-transparan"></div>

<style>#overlay-transparan {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 99999; /* Sangat tinggi agar tidak tertutup elemen lain */
  background-color: transparent; /* Benar-benar bening */
  cursor: default; /* Cursor tetap standar agar user tidak curiga ada tombol */
}

</style>
<script>
  const overlay = document.getElementById('overlay-transparan');
const musik = document.getElementById('backsound');

overlay.addEventListener('click', function() {
    // Putar musik
    musik.play().catch(e => console.log("Gagal putar:", e));

    // Hapus total elemen pelapis agar user bisa klik menu/tombol asli di web
    overlay.remove();
    
    console.log("Overlay dihapus, musik dimulai.");
}, { once: true });

</script> -->
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

/* ================= ISI (DI BAGUSIN) ================= */

/* FILTER BAR (SEARCH + SORT) */
.filter-bar{
  padding: 0 80px;
  margin-top: 30px;
}

.filter-form{
  display:flex;
  justify-content: space-between;
  align-items:center;
  gap:20px;
  flex-wrap:wrap;
}

/* SEARCH */
.search-box{
  display:flex;
  align-items:center;
  background:white;
  border-radius:12px;
  overflow:hidden;
  box-shadow:0 5px 15px rgba(0,0,0,0.15);
}

.search-box input{
  padding:12px 15px;
  border:none;
  outline:none;
  width:350px;
  font-size:14px;
}

.search-box button{
  padding:12px 15px;
  border:none;
  background:#6b3df5;
  color:white;
  cursor:pointer;
  transition:0.3s;
}

.search-box button:hover{
  background:#4b25c7;
}

/* SORT */
.sort-box select{
  padding:12px 16px;
  border-radius:12px;
  border:none;
  background:white;
  font-weight:500;
  cursor:pointer;
  box-shadow:0 5px 15px rgba(0,0,0,0.15);
  transition:0.3s;
}

.sort-box select:hover{
  transform:translateY(-2px);
}

/* RESPONSIVE */
@media(max-width:768px){
  .filter-form{
    flex-direction:column;
    align-items:stretch;
  }

  .search-box input{
    width:100%;
  }
}

.sort-container select {
  padding: 12px 16px;
  border-radius: 12px;
  border: none;
  background: white;
  font-weight: 500;
  cursor: pointer;
  box-shadow: 0 5px 15px rgba(0,0,0,0.15);
  transition: 0.3s;
}

.sort-container select:hover {
  transform: translateY(-2px);
}

/* grid */
.trip-container {
  padding: 50px 80px;
}

.trip-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 30px;
}

/* card modern */
.card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  transition: 0.3s;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.card-img img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  transition: 0.3s;
}

.card:hover .card-img img {
  transform: scale(1.05);
}

.card-img {
  position: relative;
}

.badge {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background: #6b3df5;
  color: white;
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 12px;
}

.card-body {
  padding: 18px;
}

.title-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
}

.title-row h3 {
  font-size: 16px;
  font-weight: 600;
}

.seat {
  font-size: 12px;
  color: #777;
}

.via {
  font-size: 13px;
  color: #555;
  margin-bottom: 8px;
}

.date {
  font-size: 13px;
  color: #666;
  margin-bottom: 10px;
}

.price {
  font-size: 20px;
  font-weight: bold;
  color: #6b3df5;
}

/* ================= FOOTER (TETAP) ================= */

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
a {
  text-decoration: none;
}
    </style>
</head>

<body>

<!-- ================= HEADER ================= -->
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

$sort = $_GET['sort'] ?? 'default';
$keyword = $_GET['keyword'] ?? '';

$order_by = "t.id_trip DESC";

if ($sort == 'harga_terendah') $order_by = "t.harga ASC";
elseif ($sort == 'harga_tertinggi') $order_by = "t.harga DESC";
elseif ($sort == 'nama') $order_by = "t.tujuan ASC";
elseif ($sort == 'keberangkatan') $order_by = "t.tgl_berangkat ASC";

/* QUERY UTAMA */
$sql = "SELECT t.*, k.*,
        (SELECT nama_file FROM gambar g WHERE g.id_trip = t.id_trip LIMIT 1) as gambar,
        (SELECT SUM(jumlah_peserta) FROM booking b 
         WHERE b.id_trip = t.id_trip AND b.status != 'Dibatalkan') as terisi
        FROM trip t
        JOIN katalog k ON t.id_trip = k.id_trip
        WHERE 1";

/* SEARCH */
if($keyword != ''){
    $keyword = mysqli_real_escape_string($konek, $keyword);

    $sql .= " AND (
        t.tujuan LIKE '%$keyword%' OR
        t.rute LIKE '%$keyword%' OR 
        t.catatan LIKE '%$keyword%' OR
        EXISTS (
            SELECT 1 FROM fasilitas f
            WHERE f.id_trip = t.id_trip
            AND f.fasilitas LIKE '%$keyword%'
        )
    )";
}

/* SORT (tetap) */
$sql .= " ORDER BY $order_by";

/* EKSEKUSI */
$result = kueri($sql);
?>

<!-- SEARCH + SORT (DIGABUNG) -->
<div class="filter-bar">
  <form method="GET" class="filter-form">
    
    <div class="search-box">
      <input 
        type="text" 
        name="keyword" 
        placeholder="Cari tujuan / fasilitas / jalur pendakian..."
        value=""
        autofocus
      >
      <button type="submit">CARI</button>
    </div>

    <div class="sort-box">
      <select name="sort" onchange="this.form.submit()">
        <option value="default" <?= $sort=='default'?'selected':'' ?>>Urutkan</option>
        <option value="harga_terendah" <?= $sort=='harga_terendah'?'selected':'' ?>>Harga Termurah</option>
        <option value="harga_tertinggi" <?= $sort=='harga_tertinggi'?'selected':'' ?>>Harga Termahal</option>
        <option value="nama" <?= $sort=='nama'?'selected':'' ?>>Nama A-Z</option>
        <option value="keberangkatan" <?= $sort=='keberangkatan'?'selected':'' ?>>Keberangkatan Terdekat</option>
      </select>
    </div>

  </form>
</div>

<!-- KATALOG -->
<section class="trip-container">
<div class="trip-grid">

<?php
if (mysqli_num_rows($result) > 0) {
  while($row = ambil($result)) {

    $tgl_berangkat = new DateTime($row['tgl_berangkat']);
    $tgl_pulang = new DateTime($row['tgl_pulang']);
    $durasi = $tgl_berangkat->diff($tgl_pulang)->days + 1;
    $sisa_kuota = $row['kuota'] - $row['terisi'];
    if ($sisa_kuota == 0 || $row['publik'] == 0) continue;
    $tgl_tampil = date('d', strtotime($row['tgl_berangkat'])) . " - " . date('d F Y', strtotime($row['tgl_pulang']));
?>

<a href="ot_katalog.php?id=<?php echo $row['id_trip']; ?>">
  <div class="card">
    <div class="card-img">
      <img src="../gambar/upload/<?php echo $row['gambar'] ? $row['gambar'] : 'default.jpg'; ?>" />
      <span class="badge"><?php echo $durasi; ?> Hari</span>
    </div>

    <div class="card-body">
      <div class="title-row">
        <h3><?php echo htmlspecialchars($row['tujuan']); ?></h3>
        <span class="seat"><?php echo $sisa_kuota; ?> / <?php echo $row['kuota']; ?> SEAT</span>
      </div>

      <p class="via"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
      <p class="date">📅 <?php echo $tgl_tampil; ?></p>
      <p class="price">Rp. <?php echo number_format($row['harga'],0,',','.'); ?></p>
    </div>
  </div>
</a>

<?php }} ?>

</div>
</section>

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