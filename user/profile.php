<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        /* Global Styles */
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

        /* Hero & Content Section */
        .hero {
    background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.card-container {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    width: 100%;
    max-width: 500px;
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    animation: fadeIn 0.5s ease;
}

/* Animasi */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Text */
.profile-view h2 {
    text-transform: uppercase;
    font-size: 1rem;
    color: #555;
}

.profile-view h1 {
    font-size: 2.2rem;
    margin: 15px 0 25px;
}

.username {
    color: #007bff;
    font-size: 1.5rem;
}

/* Button group */
.btn-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Button umum */
.btn {
    padding: 12px;
    border-radius: 10px;
    color: white;
    font-weight: bold;
    text-decoration: none;
    transition: 0.3s;
}

/* Hover effect */
.btn:hover {
    transform: translateY(-2px);
    opacity: 0.9;
}

/* Variasi tombol */
.btn-edit {
    background: linear-gradient(to right, #4facfe, #00f2fe);
}

.btn-logout {
    background: #ff4b2b;
}

.btn-delete {
    background: #ff416c;
}

.btn-home {
    background: #6c757d;
}

/* Responsive */
@media (max-width: 500px) {
    .profile-view h1 {
        font-size: 1.8rem;
    }
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
        <a href="profile.php" class="active6" ><?php if (isset($_SESSION['username'])): ?>
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
            <a href="../login_user.html">
              <button class="active5">Masuk</button>
            </a>
        <?php endif; ?>
      </nav>
    </header>

    <main class="hero">
    <div class="card-container">
        
        <div class="profile-view">
            <h2>Profile</h2>
            <h1>Welcome, <br>
            
            <?php if (isset($_SESSION['username'])): ?>
                <span class="username">
                    👤 <?php echo $_SESSION['username']; ?>
                </span>
            <?php endif; ?>

            <div class="btn-group">
                <a href="edit_user.php" class="btn btn-edit">Ubah Profil</a>
                <a href="logout_user.php" class="btn btn-logout" onclick="return confirm('Yakin ingin logout?')">Log Out</a>
                <a href="delete_user.php" class="btn btn-delete" onclick="return confirm('Yakin hapus akun?')">Hapus Akun</a>
                <a href="home1.php" class="btn btn-home">🏠 Kembali ke Home</a>
                <a href="daftar_pesanan.php" class="btn btn-home">Daftar Pesanan</a>
                <a href="daftar_pembayaran.php" class="btn btn-home">Riwayat Pembayaran</a>
            </div>
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
</html>