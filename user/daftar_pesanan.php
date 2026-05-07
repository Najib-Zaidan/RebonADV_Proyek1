<?php
session_start();
require 'fungsi.php';

$id_akun = $_SESSION['id_akun'];

$query = "SELECT b.id_booking, b.tgl_booking, b.status, p.nama, b.id_trip 
          FROM booking b 
          JOIN peserta p ON b.id_peserta = p.id_peserta 
          WHERE p.id_akun = '$id_akun'";

$result = kueri($query);
?>

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

/* NAVBAR (TIDAK DIUBAH) */
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

/* ========================= */
/* ISI (YANG DIPERBAIKI) */
/* ========================= */

.container {
  padding: 40px 80px;
}

.judul {
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 20px;
}

/* TABLE */
table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

thead {
  background: #6b3df5;
  color: white;
}

th {
  padding: 15px;
  text-align: left;
  font-size: 14px;
}

td {
  padding: 15px;
  border-bottom: 1px solid #eee;
  font-size: 14px;
}

tbody tr:hover {
  background: #f9f7ff;
}

/* STATUS */
.status {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
}

.status.pending {
  background: #fff3cd;
  color: #856404;
}

.status.lunas {
  background: #d4edda;
  color: #155724;
}

/* BUTTON */
.btn-bayar {
  background: #6b3df5;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.3s;
}

.btn-bayar:hover {
  background: #4d2bc4;
}

/* FOOTER (TIDAK DIUBAH) */
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
</style>

<body>

<!-- NAVBAR (ASLI) -->
<header class="navbar">
  <div class="logo">
    <img src="../gambar/REBON LOGO GRADIENT presisi.png" alt="Rebon Adventure"/>
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

<!-- ISI -->
<div class="container">
  <div class="judul">Riwayat Booking</div>

  <table>
    <thead>
      <tr>
        <th>ID Booking</th>
        <th>Nama Peserta</th>
        <th>Tanggal Booking</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = ambil($result)): ?>
      <tr>
        <td><?php echo $row['id_booking']; ?></td>
        <td><?php echo $row['nama']; ?></td>
        <td><?php echo $row['tgl_booking']; ?></td>
        <td>
          <span class="status <?php echo strtolower($row['status']); ?>">
            <?php echo $row['status']; ?>
          </span>
        </td>
        <td>
          <form action="form_pembayaran.php" method="GET">
            <input type="hidden" name="id_booking" value="<?php echo $row['id_booking']; ?>">
            <button type="submit" class="btn-bayar">Bayar</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- FOOTER (ASLI) -->
<footer>
  <div class="footer-content">
    <div class="footer-column logo-col">
      <img src="../gambar/logo-rebon.png" class="footer-logo-img"/>
    </div>

    <div class="footer-column">
      <h4>KONTAK KAMI</h4>
      <div class="contact-item">✉ rebonadventure@gmail.com</div>
      <div class="contact-item">📞 +62 812-3456-7890</div>
      <div class="contact-item">📍 Cirebon, Indonesia</div>
    </div>

    <div class="footer-column">
      <h4>LAYANAN</h4>
      <ul>
        <li>OPEN TRIP</li>
        <li>PRIVATE TRIP</li>
      </ul>
    </div>

    <div class="footer-column">
      <h4>INFO</h4>
      <ul>
        <li>TENTANG KAMI</li>
        <li>TRIP</li>
        <li>FAQ</li>
      </ul>
    </div>
  </div>

  <div class="copyright">
    © 2026 REBON ADVENTURE
  </div>
</footer>

</body>