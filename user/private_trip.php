<?php
session_start();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Private Trip</title>

  <style>
    :root {
  --purple-dark: #3d0f6a;
  --purple-mid: #5b1fb2;
  --purple-light: #a67bff;
  --cream: #fff9e6;
  --lavender: #e9d9ff;
  --input-border: rgba(0, 0, 0, 0.08);
  --text-dark: #111;
  --radius: 12px;
  --max-width: 1150px;
  --page-padding: 40px;
  --shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
}

/* reset-ish */
* {
  box-sizing: border-box;
}
html,
body {
  height: 100%;
  margin: 0;
  font-family:
    "Poppins",
    system-ui,
    -apple-system,
    "Segoe UI",
    Roboto,
    "Helvetica Neue",
    Arial;
  color: var(--text-dark);

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

nav .active3 {
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

/* page background gradient (big purple area) */
.hero-area {
  background: linear-gradient(
    180deg,
    var(--purple-dark) 0%,
    var(--purple-mid) 35%,
    var(--purple-light) 100%
  );
  padding: 48px 20px;
}
.hero-inner {
  max-width: var(--max-width);
  margin: 0 auto;
  background: var(--cream);
  border-radius: 12px;
  padding: 28px;
  display: flex;
  gap: 28px;
  box-shadow: var(--shadow);
  align-items: flex-start;
}

/* left image card */
.card-image {
  flex: 0 0 48%;
}
.image-wrap {
  position: relative;
  border-radius: 14px;
  overflow: hidden;
  background: #eee;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}
.image-wrap img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
  aspect-ratio: 1/1;
  min-height: 420px;
}
.hero-title {
  position: absolute;
  left: 20px;
  bottom: 28px;
  margin: 0;
  font-size: 56px;
  line-height: 0.95;
  color: white;
  font-weight: 800;
  text-transform: none;
  text-shadow: 0 6px 18px rgba(0, 0, 0, 0.45);
  font-family: "Montserrat", "Poppins", sans-serif;
}

/* right form */
.card-form {
  flex: 0 0 46%;
}
.trip-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.trip-form input[type="text"],
.trip-form input[type="tel"],
.trip-form input[type="date"],
.trip-form input[type="number"],
.trip-form textarea {
  background: var(--lavender);
  border-radius: 10px;
  border: 1px solid var(--input-border);
  padding: 12px 14px;
  font-size: 15px;
  outline: none;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
}
.trip-form textarea {
  resize: vertical;
}

.row {
  display: flex;
  gap: 12px;
}
.row .half {
  flex: 1;
}

/* CTA button */
.cta-row {
  display: flex;
  justify-content: flex-end;
  margin-top: 6px;

}
.cta-row a {
  text-decoration: none;
  color: #e9d9ff;
}
.btn-whatsapp {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  background: linear-gradient(180deg, #4b00ff 0%, #7a2cff 100%);
  color: white;
  border: none;
  padding: 14px 22px;
  border-radius: 10px;
  font-weight: 700;
  box-shadow: 0 6px 20px rgba(64, 0, 160, 0.25);
  cursor: pointer;
  font-size: 16px;
  text-decoration: none;
  
}
.wa-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.08);
  padding: 6px 8px;
  border-radius: 6px;
  width: 50px;
}
.wa-icon i {
  color: #fff;
  font-size: 18px;
}

/* Footer */
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
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Private Trip</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- CSS -->
  <link rel="stylesheet" href="private.css" />
</head>

<body>

<header class="navbar">
  <div class="logo">
    <img src="../gambar/REBON LOGO GRADIENT presisi.png" alt="Rebon Adventure" />
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

<main class="hero-area">
  <div class="hero-inner">

    <!-- KIRI (GAMBAR) -->
    <div class="card-image">
      <div class="image-wrap">
        <img src="../gambar/123.jpg" alt="Destinasi" />
        <h1 class="hero-title">Rencanakan<br>Trip Anda</h1>
      </div>
    </div>

    <!-- KANAN (FORM) -->
    <aside class="card-form">
      <form action="proses_form_private.php" method="post" class="trip-form">

        <input type="text" name="nama" placeholder="Nama Lengkap" required />
        <input type="text" name="nohp" placeholder="Nomor Telepon" required />
        <input type="text" name="destinasi" placeholder="Lokasi Destinasi" required />

        <div class="row">
          <input type="date" name="tgl_mulai" class="half" required />
          <input type="date" name="tgl_selesai" id="half" required />
        </div>

        <textarea name="catatan" placeholder="Catatan Tambahan" rows="5"></textarea>
        <input type="number" name="jumlah" placeholder="Jumlah Peserta" min="1" required />

        <div class="cta-row">
          <a href="form_private.php">
          <button type="submit" name="submit" class="btn-whatsapp">
            <i class="fab fa-whatsapp"></i>
            <span>Pesan Sekarang</span>
            </a>
          </button>
        </div>

      </form>
    </aside>

  </div>
</main>

<footer>
  <div class="footer-content">

    <div class="footer-column logo-col">
      <img src="../gambar/logo-rebon.png" class="footer-logo-img" />
    </div>

    <div class="footer-column">
      <h4>KONTAK KAMI</h4>
      <div class="contact-item">✉ rebonadventure@gmail.com</div>
      <div class="contact-item">📞 +62 812-3456-7890</div>
      <div class="contact-item">📍 Cirebon, Indonesia</div>
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
    </div>

  </div>

  <div class="copyright">
    © 2026 REBON ADVENTURE
  </div>
</footer>

</body>
</html>
