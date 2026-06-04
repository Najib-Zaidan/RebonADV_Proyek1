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
  overflow-x: auto; 
  box-sizing: border-box;
  width: 100%;
}

.trip-container::-webkit-scrollbar {
  height: 8px;
}
.trip-container::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 10px;
}
.trip-container::-webkit-scrollbar-track {
  background: transparent;
}

.trip-container a {
  text-decoration: none;
  flex-shrink: 0; 
  display: flex;
}

.trip-card {
  background: white;
  width: 250px;
  border-radius: 10px;
  overflow: hidden;
  padding-bottom: 15px;
  box-sizing: border-box; 
  display: flex;
  flex-direction: column;
}

/* Pembungkus gambar dibuat relatif untuk meletakkan badge rating */
.card-img-wrapper {
  position: relative;
  width: 100%;
  height: 150px;
  overflow: hidden;
}

.card-img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* BARU: Style Badge Rating Pojok Kanan Atas Gambar */
.rating-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(255, 255, 255, 0.9);
  color: #333;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 4px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  z-index: 2;
}

.rating-badge .star {
  color: #ffca28; /* Warna kuning emas */
  font-size: 13px;
}

/* Area konten teks */
.card-content {
  padding: 10px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  width: 100%;
  box-sizing: border-box; 
  overflow: hidden; 
}

.trip-card h3 {
  margin: 0 0 8px 0;
  font-size: 16px;
  color: #333; 
  word-wrap: break-word; 
}

.trip-card p.desc {
  margin: 0 0 10px 0;
  font-size: 14px;
  color: #555;
  display: -webkit-box;
  -webkit-line-clamp: 3; 
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  word-break: break-word; 
}

.trip-card .date {
  margin-top: auto; 
  margin-bottom: 5px;
  font-size: 13px;
  color: #777;
}

.price {
  display: block;
  font-weight: bold;
  color: #6b3df5;
  font-size: 15px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
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

  .private-trip {
    margin: 30px 20px;
    padding: 20px;
  }

  .galeri {
    padding: 30px 20px;
  }

  .galeri-container {
    grid-template-columns: 1fr;
  }

  .footer-content {
    flex-direction: column;
    text-align: center;
    align-items: center;
  }
}
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

        <a href="tentang_kami.php">Tentang Kami</a>
      </div>
    </section>

    <section class="trip">
      <div class="trip-header">
        <h2>Trip Tersedia</h2>
        <a href="open_trip.php">Lihat Selengkapnya →</a>
      </div>

      <div class="trip-container">
        <?php
        require 'fungsi.php';

        // UPDATE QUERY: Ditambahkan subquery rating (AVG(r.rating) as rata_rating)
        $data_trip = kueri("SELECT t.*, tj.tujuan, k.*,
                           (SELECT nama_file FROM gambar g WHERE g.id_trip = t.id_trip LIMIT 1) AS foto,
                           (SELECT AVG(rating) FROM rating r WHERE r.id_trip = t.id_trip) as rata_rating
                           FROM trip t 
                           JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan 
                           JOIN katalog k ON k.id_trip = t.id_trip
                           LIMIT 5");

        while ($row = ambil($data_trip)) :
            $tgl_berangkat = date('d', strtotime($row['tgl_berangkat']));
            $tgl_pulang = date('d F Y', strtotime($row['tgl_pulang']));
            $display_tgl = "Tanggal $tgl_berangkat - $tgl_pulang";
            
            // Format tampilan rating ke 1 desimal jika data tersedia
            $rating_tampil = $row['rata_rating'] ? number_format($row['rata_rating'], 1) : null;
        ?>

            <a href="ot_katalog.php?id=<?= $row['id_trip']; ?>">
                  <div class="trip-card">
                      <div class="card-img-wrapper">
                          <img src="../gambar/upload/<?= $row['foto'] ? $row['foto'] : 'default.jpg'; ?>" />
                          
                          <?php if ($rating_tampil): ?>
                            <div class="rating-badge">
                              <span class="star">★</span> <?= $rating_tampil; ?> / 5
                            </div>
                          <?php else: ?>
                            <div class="rating-badge" style="font-size: 9px; color: #777;">
                              Baru
                            </div>
                          <?php endif; ?>
                      </div>
                      
                      <div class="card-content">
                          <h3><?= htmlspecialchars($row['tujuan']); ?></h3>
                          
                          <p class="desc"><?= htmlspecialchars($row['deskripsi']); ?></p>
                          
                          <p class="date"><?= $display_tgl; ?></p>
                          <span class="price">Rp. <?= number_format($row['harga'], 0, ',', '.'); ?> / Pax</span>
                      </div>
                  </div>
            </a>

        <?php endwhile; ?>
      </div>
    </section>

    <section class="private-trip">
      <div class="private-text">
        <h2>Trip Sesuka Hati</h2>

        <p>Mau naik gunung tanpa ribet dan tanpa orang asing?</p>

        <p>Yuk ambil paket Private Trip!</p>

        <p class="p_bawah">
          Bisa atur tanggal sendiri, rombongan sendiri, bahkan konsep trip
          sesuai request kamu. Liburan jadi lebih intimate dan bebas drama.
        </p>

        <a href="private_trip.php">RENCANAKAN TRIP SEKARANG</a>
      </div>

      <div class="private-img">
        <img src="../gambar/ciremai.jpeg" />
      </div>
    </section>

    <section class="galeri">
    <style>
    .galeri{
        padding:40px 6%;
    }

    .galeri h2{
        text-align:center;
        font-size:30px;
        margin-bottom:25px;
        color:#333;
    }

    .galeri-container{
        display:flex;
        flex-wrap:wrap;
        gap:15px;
        justify-content:center;
    }

    .card-galeri{
        width:180px;
        background:#fff;
        border-radius:12px;
        overflow:hidden;
        box-shadow:0 2px 8px rgba(0,0,0,0.08);
        transition:0.3s;
    }

    .card-galeri:hover{
        transform:translateY(-3px);
    }

    .grid-foto{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:2px;
        padding:2px;
    }

    .grid-foto img{
        width:100%;
        height:85px;
        object-fit:cover;
        display:block;
    }

    .kosong{
        width:100%;
        height:85px;
        background:#ddd;
    }

    .judul-album{
        padding:10px;
        text-align:center;
        font-size:14px;
        font-weight:bold;
        color:#333;
    }
    </style>

    <h2>GALERI KAMI</h2>

    <div class="galeri-container">
    <?php
    $data = mysqli_query($konek,"SELECT * FROM album ORDER BY id_album DESC");

    while($g = mysqli_fetch_assoc($data)):
        $fotos = mysqli_query($konek,"SELECT nama_file FROM galeri WHERE id_album='$g[id_album]' LIMIT 4");
        $gambar = [];

        while($f = mysqli_fetch_assoc($fotos)){
            $gambar[] = $f['nama_file'];
        }
    ?>

    <a href="detail_galeri_user.php?id=<?php echo $g['id_album']; ?>" style="text-decoration:none;">
      <div class="card-galeri">
        <div class="grid-foto">
          <?php for($i=0; $i<4; $i++): ?>
              <?php if(isset($gambar[$i])): ?>
                  <img src="../gambar/galeri/<?php echo $gambar[$i]; ?>">
              <?php else: ?>
                  <div class="kosong"></div>
              <?php endif; ?>
          <?php endfor; ?>
        </div>

        <div class="judul-album">
            <?php echo $g['nama']; ?>
        </div>
      </div>
    </a>

    <?php endwhile; ?>
    </div>
    </section>

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