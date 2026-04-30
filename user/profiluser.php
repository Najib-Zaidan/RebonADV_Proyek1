<?php
session_start();
require 'fungsi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}

$id_akun = $_SESSION['id_akun'];

$peserta = kueri("SELECT * FROM peserta_open WHERE id_akun = '$id_akun'");
$pesanan = kueri("
    SELECT 
        b.id_booking,
        b.id_trip,
        t.tujuan,
        t.tgl_berangkat,
        b.tgl_booking,
        b.jumlah_peserta,
        b.status
    FROM booking b
    JOIN trip t ON b.id_trip = t.id_trip
    WHERE b.id_akun = '$id_akun'
");
$pembayaran = kueri("
    SELECT 
        py.id_payment,
        py.id_booking,
        py.tgl_bayar,
        py.nominal,
        py.bukti_bayar,
        py.status,
        b.jumlah_peserta,
        t.tujuan
    FROM payment_open py
    JOIN booking b ON py.id_booking = b.id_booking
    JOIN trip t ON b.id_trip = t.id_trip
    WHERE b.id_akun = '$id_akun'
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Profile</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial;}
body{background:#e7e2c8;}

/* NAVBAR & FOOTER (PUNYA KAMU) */
.navbar{display:flex;justify-content:space-between;padding:20px 80px;background:#f4f0e5;}
.logo img{height:50px;}
nav{display:flex;gap:30px;align-items:center;}
nav a{text-decoration:none;color:black;}
.active5{background:#6b3df5;color:white;padding:10px 18px;border-radius:8px;border:none;}

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

/* HERO */
.hero{height:150px;background:linear-gradient(#321180,#5A1EE6);}

/* PROFILE */
.profile-card{
  width: 80%;
  margin: -70px auto 20px;
  background: #e5e1d0;
  padding: 30px;
  border-radius: 15px;

  display: flex;
  justify-content: space-between;
  align-items: center;

   box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

/* bagian kiri */
.profile-left{
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* gambar profil */
.ikon img{
  width: 90px;
  height: 90px;
  object-fit: contain;
  margin-top: 20px;
  margin-bottom: 5px;
  margin-left:5px;
}

/* nama di tengah */
.profile-name{
  flex: 1;
  text-align: center;
}

.profile-name h2{
  font-size: 58px;
  margin: 0;
}

/* tombol kanan */
.action-group{
  display: flex;
  flex-direction: column;
  gap: 10px;
}


/* BUTTON */
.btn{
  padding:8px 15px;border-radius:8px;
  text-decoration:none;color:white;
  font-size:13px;
}

.purple{background:#6b3df5;}
.red{background:#ff416c;}
.orange{background:#ff4b2b;}

.action-group{display:flex;flex-direction:column;gap:10px;}

.btn {
  display: inline-block;
  padding: 8px 14px;
  margin-top: 8px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 14px;
  transition: 0.3s;
}

/* tombol bayar */
.btn.purple {
  background: #6b3df5;
  color: white;
}

.btn.purple:hover {
  background: #5027d6;
}

/* tombol lihat peserta */
.btn:not(.purple) {
  background: #f1f1f1;
  color: #333;
  border: 1px solid #ddd;
}

.btn:not(.purple):hover {
  background: #e4e4e4;
}

/* TAB */
.menu-tabs{
  width:80%;margin:auto;
  display:flex;justify-content:center;
  gap:40px;font-weight:bold;
}
.menu-tabs div{cursor:pointer;padding-bottom:5px;}
.active-tab{border-bottom:3px solid black;}

.tab-content{display:none;}
.tab-content.active{display:block;}

/* DATA */
.data-section{width:80%;margin:auto;}

.data-card{
  background:white;
  padding:20px;
  margin-top:15px;
  border-radius:12px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  box-shadow:0 5px 10px rgba(0,0,0,0.1);
}

.data-left p{margin-bottom:5px;}
.data-right{display:flex;flex-direction:column;gap:8px;}
</style>
</head>

<body>

<!-- NAVBAR -->
<header class="navbar">
  <div class="logo">
    <img src="../gambar/REBON LOGO GRADIENT presisi.png">
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

        <?php else: ?>
            <!-- JIKA BELUM LOGIN -->
            <a href="login_user.php">
              <button class="active5">Masuk</button>
            </a>
        <?php endif; ?>
      </nav>
</header>

<!-- HERO -->
<div class="hero"></div>

<!-- PROFILE -->
<div class="profile-card">

<div class="profil-left">
  <div class="ikon">
    <img src="../gambar/person.png" alt="">
  </div>
  <div>
    <div class="avatar"></div><br>
    <a href="edit_user.php" class="btn purple">Ubah Profil</a>
  </div>
</div>

<div class="profil-name">
  <div>
    <h2><?= $_SESSION['username']; ?></h2>
  </div>
</div>

  <div class="action-group">
    <a href="logout_user.php" class="btn orange" onclick="return confirm('Yakin logout?')">Logout</a>
    <a href="delete_user.php" class="btn red" onclick="return confirm('Yakin hapus akun?')">Hapus</a>
  </div>
</div>

<!-- TAB -->
<div class="menu-tabs">
  <div class="tab active-tab" onclick="tab('peserta',this)">Peserta</div>
  <div class="tab" onclick="tab('pesanan',this)">Pesanan</div>
  <div class="tab" onclick="tab('pembayaran',this)">Pembayaran</div>
</div>

<!-- PESERTA -->
<div id="peserta" class="tab-content active data-section">
<a href="form.php" class="btn purple">+ Tambah Peserta</a>

<?php while($p=ambil($peserta)): ?>
<div class="data-card">
  <div class="data-left">
    <p><b><?= $p['nama']; ?></b></p>
    <p>📞 <?= $p['no_hp']; ?></p>
    <p>📅 <?= $p['usia']; ?></p>
    <p>📍 <?= $p['alamat']; ?></p>
    <p>💀 <?= $p['riwayat']; ?></p>
  </div>

  <div class="data-right">
    <a href="ubah_peserta.php?id=<?= $p['id_peserta']; ?>" class="btn purple">Ubah</a>
    <a href="hapus_peserta.php?id=<?= $p['id_peserta']; ?>" onclick="return confirm('Yakin hapus akun?')" class="btn red">Hapus</a>
  </div>
</div>
<?php endwhile; ?>
</div>

<!-- PESANAN -->
<!-- PESANAN -->
<div id="pesanan" class="tab-content data-section">
<?php while($b=ambil($pesanan)): ?>

<?php
$id_booking = $b['id_booking'];

// cek apakah sudah ajukan batal
$cek_batal_q = kueri("
    SELECT * FROM batal_open 
    WHERE id_booking = '$id_booking'
");
$cek_batal = mysqli_fetch_assoc($cek_batal_q);
?>

<div class="data-card">
  <div class="data-left">
    <p><b><?= $b['tujuan']; ?></b></p>
    <p>👥 Jumlah Peserta: <?= $b['jumlah_peserta']; ?></p>
    <p>📅 Booking: <?= $b['tgl_booking']; ?></p>
    <p>🚍 Berangkat: <?= $b['tgl_berangkat']; ?></p>
    <p>Status: <?= $b['status']; ?></p>
  </div>

  <div class="data-right">

    <?php if (!$cek_batal): ?>
        <a href="form_pembayaran.php?id_booking=<?= $b['id_booking']; ?>" class="btn purple">
          Bayar
        </a>
    <?php endif; ?>

    <a href="detail_peserta.php?id_booking=<?= $b['id_booking']; ?>" class="btn">
      Detail
    </a>

    <?php if ($cek_batal): ?>
        <span style="color:orange;font-weight:bold;">
            ⏳ Menunggu Persetujuan Admin
        </span>
    <?php else: ?>
        <a href="batal_pesanan.php?id_booking=<?= $b['id_booking']; ?>" class="btn">
          Ajukan Pembatalan
        </a>
    <?php endif; ?>

  </div>

</div>

<?php endwhile; ?>
</div>

<!-- PEMBAYARAN -->
<div id="pembayaran" class="tab-content data-section">
<?php while($py=ambil($pembayaran)): ?>
<div class="data-card">
  <div class="data-left">
    <p>👥 Jumlah Peserta: <?= $py['jumlah_peserta']; ?></p>
    <p>📅 <?= $py['tgl_bayar']; ?></p>
    <p>💰 Rp <?= number_format($py['nominal']); ?></p>
    <p>Status: <?= $py['status']; ?></p>
  </div>

  <div class="data-right">
    <a href="../gambar/payment/<?= $py['bukti_bayar']; ?>" target="_blank" class="btn purple">Bukti</a>
  </div>
</div>
<?php endwhile; ?>
</div>

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

<script>
function tab(id,el){
  document.querySelectorAll('.tab-content').forEach(x=>x.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(x=>x.classList.remove('active-tab'));
  document.getElementById(id).classList.add('active');
  el.classList.add('active-tab');
}
</script>

</body>
</html>