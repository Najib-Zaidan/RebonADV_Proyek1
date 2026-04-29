<?php
session_start();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tentang Kami</title>

  <style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

/* Header */
/* HEADER BARU */
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

nav .active4 {
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

/* HEADER LAMA */
/* Container Header */

/*

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 8%;
    background-color: #fdfae6; /* Warna krem sesuai foto 
    position: sticky;
    top: 0;
    z-index: 1000;
}

.main-logo {
    height: 50px; /* Sesuaikan ukuran logo 
}

Navigasi Desktop 
nav ul {
    display: flex;
    list-style: none;
    gap: 30px;
}

nav ul li a {
    text-decoration: none;
    color: #333;
    font-weight: 700;
    font-size: 15px;
    transition: 0.3s;
}

/* Link Aktif (Tentang Kami) 
nav ul li a.active {
    color: #7d5fff; /* Warna ungu sesuai foto 
}

/* Tombol Masuk 
.btn-masuk {
    background-color: #3f408c;
    color: white;
    padding: 10px 30px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
}

/* Hidden Elements 
#menu-auth, .hamburger, .mobile-only {
    display: none;
} 
*/

/* ========================================== */
/* RESPONSIVE (VERSI HP)                      */
/* ========================================== */
@media (max-width: 768px) {
    .hamburger {
        display: block;
        font-size: 28px;
        cursor: pointer;
        order: 3;
    }

    .logo-container {
        order: 1;
    }

    .auth-buttons {
        display: none; /* Sembunyikan tombol desktop */
    }

    /* Menu Navigasi Jadi Dropdown/Overlay */
    nav {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background-color: #fdfae6;
        display: none; /* Sembunyikan menu */
        border-top: 1px solid #ddd;
    }

    nav ul {
        flex-direction: column;
        padding: 20px;
        gap: 20px;
        text-align: center;
    }

    .mobile-only {
        display: block;
    }

    .btn-masuk-mobile {
        background-color: #3f408c;
        color: white;
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
    }

    /* Fungsi Klik Hamburger (Checkbox Hack) */
    #menu-auth:checked ~ nav {
        display: block;
    }
}


/* Hero Section */
.hero {
    background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('background-gunung.jpg');
    background-size: cover;
    height: 500px;
    text-align: center;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-content h3 {
    color: #7d5fff;
    font-size: 24px;
    margin-bottom: 20px;
}

.hero-content p {
    max-width: 800px;
    font-size: 18px;
    line-height: 1.6;
}

/* Gallery Section */
.gallery-container {
    background: linear-gradient(to bottom, #7d5fff, #a29bfe);
    padding: 50px;
    text-align: center;
}

.card-wrapper {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap; /* Penting: Agar card turun ke bawah jika layar HP terlalu sempit */
}


.card {
    background: #fdfae6;
    padding: 15px;
    border-radius: 15px; /* Lebih bulat sesuai gambar */
    width: 300px; /* Ukuran pas untuk 3 kolom di layar desktop */
    box-shadow: 0 4px 15px rgba(0,0,0,0.1); /* Efek bayangan halus */
}
.card:hover{
    background-color: #5758bb;
    transform: translateY(-5px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}

.btn-cek {
    background: #5758bb;
    color: white;
    padding: 12px 40px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    border: 1px solid rgba(255,255,255,0.3);
}


.img-main { width: 100%; border-radius: 5px; }

.img-grid {
    display: flex;
    gap: 5px;
    margin-top: 10px;
}

.img-grid img { width: 32%; border-radius: 3px; }

.btn-detail {
    background: #5758bb;
    color: white;
    width: 100%;
    border: none;
    padding: 10px;
    margin-top: 10px;
    border-radius: 5px;
}
.btn-detail:hover{
    background-color: #5758bb;
    transform: translateY(-5px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}

.detail {
    background: #5758bb;
    color: white;
    width: 300px;
    border: none;
    padding: 10px;
    margin-top: 10px;
    border-radius: 5px
}
.detail:hover{
    background-color: #5758bb;
    transform: translateY(-5px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}


/* Footer */
footer {
  text-align: center; /* biar isi tengah */
}

/* Button FAQ */
.faq-bar {
  display: inline-block;
  background-color: #6661b3;
  color: white;
  padding: 12px 30px;
  width: 250px;
  text-align: center;
  font-weight: bold;
  border-radius: 8px;
  letter-spacing: 2px;
  text-decoration: none; /* hilangkan garis link */
  transition: 0.3s;
}

/* Hover biar lebih hidup */
.faq-bar:hover {
  background-color: #5752a3;
  transform: translateY(-5px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.2)
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

/* Responsif untuk HP */
@media (max-width: 768px) {
    .footer-content {
        flex-direction: column;
        text-align: left;
    }
    
    .faq-bar {
        width: 100%;
    }
}




</style>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami</title>
    <link rel="stylesheet" href="tentangkami.css">
</head>
<body>
    <!-- HEADER BARU -->
    <header class="navbar">
      <div class="logo">
        <img src="../gambar/REBON LOGO GRADIENT presisi.png" alt="Rebon Adventure" />
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
    
    <!-- HEADER LAMA -->
    
    <!-- 
    
    <header>
    <div class="logo-container">
        <img src="logo-rebon.png" alt="REBON ADVENTURE" class="main-logo">
    </div>

    <input type="checkbox" id="menu-auth">
    <label for="menu-auth" class="hamburger">
        &#9776;
    </label>

    <nav>
        <ul>
            <li><a href="home.html">Home</a></li>
            <li><a href="Open_Trip.html">Open</a></li>
            <li><a href="private.html">Private</a></li>
            <li><a href="tentangkami.html">Tentang Kami</a></li>
            <li class="mobile-only"><button class="btn-masuk-mobile">Masuk</button></li>
        </ul>
    </nav>

    <div class="auth-buttons">
        <button class="btn-masuk">Masuk</button>
    </div>
</header>

-->

    <section class="hero">
        <div class="hero-content">
            <h3>SIAPA KAMI?</h3>
            <p>Rebon Adventure adalah penyedia layanan Open Trip dan Private Trip yang berfokus pada perjalanan pendakian dan eksplorasi alam. Kami berkomitmen menghadirkan pengalaman perjalanan yang aman, terencana, dan berkesan bagi setiap peserta.</p>
        </div>
    </section>

    <section class="gallery-container">
    <div class="card-wrapper">
        <a href="open_trip.php">
        <div class="card">
            <img src="../gambar/gunung.jpg" class="img-main" alt="Gunung">
            <div class="img-grid">
                <img src="../gambar/thumb.jpg" alt="Sub 1">
                <img src="../gambar/thumb.jpg" alt="Sub 2">
                <img src="../gambar/thumb.jpg" alt="Sub 3">
            </div>
            <button class="btn-detail">Lihat selengkapnya ></button>
            </a>
        </div>

        <div class="card">
            <a href="open_trip.php">
            <img src="../gambar/gunung.jpg" class="img-main" alt="Gunung">
            <div class="img-grid">
                <img src="../gambar/thumb.jpg" alt="Sub 1">
                <img src="../gambar/thumb.jpg" alt="Sub 2">
                <img src="../gambar/thumb.jpg" alt="Sub 3">
            </div>
            <button class="btn-detail">Lihat selengkapnya ></button>
            </a>
        </div>

        <div class="card">
            <a href="open_trip.php">
            <img src="../gambar/gunung.jpg" class="img-main" alt="Gunung">
            <div class="img-grid">
                <img src="../gambar/thumb.jpg" alt="Sub 1">
                <img src="../gambar/thumb.jpg" alt="Sub 2">
                <img src="../gambar/thumb.jpg" alt="Sub 3">
            </div>
            <button class="btn-detail">Lihat selengkapnya ></button>
            </a>
        </div>

    </div>

        <div>
   <a href="open_trip.php">
            <button class="detail">Lihat selengkapnya ></button>
    </a>
    </div>
</section>
<br>
<br>
<footer>
  <a href="faq.php" class="faq-bar">FAQ</a>
</footer>
<br>
<br>
<br>
<br>
    </section>

    

    <div class="footer-content">
        <div class="footer-column logo-col">
            <img src="../gambar/logo-rebon.png" alt="Rebon Adventure Logo" class="footer-logo-img">
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
                <p>Jl. sukawera No. 15,<br>Cirebon, Indonesia</p>
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
                    <img src="../gambar/fb-icon.png" alt="FB">
                    <img src="../gambar/ig-icon.png" alt="IG">
                    <img src="../gambar/tt-icon.png" alt="TK">
                </div>
            </div>
        </div>
    </div>

    <div class="copyright">
        © 2026 REBON ADVENTURE. ALL RIGHTS RESERVED.
    </div>
</footer>

  </body>
</html>
