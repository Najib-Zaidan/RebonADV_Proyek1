<?php
session_start();
require 'fungsi.php';

$id_akun = $_SESSION['id_akun'];

$query = "SELECT py.id_payment, py.tgl_bayar, py.nominal, py.bukti_bayar, py.status, ps.nama 
          FROM payment py
          JOIN booking bk ON py.id_booking = bk.id_booking
          JOIN peserta ps ON bk.id_peserta = ps.id_peserta
          WHERE ps.id_akun = '$id_akun'";

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
/* ISI (DIPERBAGUS) */
/* ========================= */

.container {
  padding: 40px 80px;
}

.judul {
  font-size: 26px;
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
  padding: 14px;
  text-align: left;
  font-size: 14px;
}

td {
  padding: 14px;
  border-bottom: 1px solid #eee;
  font-size: 14px;
}

tbody tr:hover {
  background: #f9f7ff;
}

/* STATUS BADGE */
.status {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
}

.status.diverifikasi {
  background: #d4edda;
  color: #155724;
}

.status.pending {
  background: #fff3cd;
  color: #856404;
}

.status.ditolak {
  background: #f8d7da;
  color: #721c24;
}

/* LINK BUKTI */
.link-bukti {
  color: #6b3df5;
  font-weight: bold;
}

.link-bukti:hover {
  text-decoration: underline;
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

<header class="navbar">
  <div class="logo">
    <img src="../gambar/REBON LOGO GRADIENT presisi.png" alt="Rebon Adventure"/>
  </div>

  <nav>
    <a href="home1.php" class="active1">Home</a>
    <a href="open_trip.php">Open</a>
    <a href="private_trip.php">Private</a>
    <a href="tentang_kami.php">Tentang Kami</a>

    <a href="profile.php">
      <?php if (isset($_SESSION['username'])): ?>
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

<!-- ISI -->
<div class="container">
  <div class="judul">Riwayat Pembayaran</div>

  <table>
    <thead>
      <tr>
        <th>ID Pembayaran</th>
        <th>Nama Peserta</th>
        <th>Tanggal Bayar</th>
        <th>Nominal</th>
        <th>Bukti</th>
        <th>Status</th>
      </tr>
    </thead>

    <tbody>
      <?php while ($row = ambil($result)): ?>
      <tr>
        <td><?php echo $row['id_payment']; ?></td>
        <td><?php echo $row['nama']; ?></td>
        <td><?php echo $row['tgl_bayar']; ?></td>
        <td>Rp <?php echo number_format($row['nominal'], 0, ',', '.'); ?></td>

        <td>
          <a href="../gambar/payment/<?php echo $row['bukti_bayar']; ?>" target="_blank" class="link-bukti">
            Lihat Bukti
          </a>
        </td>

        <td>
          <span class="status <?php echo strtolower($row['status']); ?>">
            <?php echo $row['status']; ?>
          </span>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

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