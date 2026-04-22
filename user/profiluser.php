<?php
session_start();
require 'fungsi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}

$id_akun = $_SESSION['id_akun'];

$peserta = kueri("SELECT * FROM peserta WHERE id_akun = '$id_akun'");
$pesanan = kueri("SELECT b.*, t.tujuan, t.tgl_berangkat
FROM booking b
JOIN trip t ON b.id_trip = t.id_trip
JOIN peserta p ON b.id_peserta = p.id_peserta
WHERE p.id_akun = '$id_akun'");
$pembayaran = kueri("SELECT py.*, ps.nama
FROM payment py
JOIN booking bk ON py.id_booking = bk.id_booking
JOIN peserta ps ON bk.id_peserta = ps.id_peserta
WHERE ps.id_akun = '$id_akun'");
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

footer{background:#fdfae6;padding:40px 10%;}
.footer-content{display:flex;justify-content:space-between;flex-wrap:wrap;}
.footer-logo-img{width:220px;}
.copyright{text-align:center;margin-top:20px;}

/* HERO */
.hero{height:150px;background:linear-gradient(#321180,#5A1EE6);}

/* PROFILE */
.profile-card{
  width:80%;margin:-70px auto 20px;
  background:#e5e1d0;padding:30px;
  border-radius:15px;
  display:flex;justify-content:space-between;align-items:center;
}

.avatar{width:80px;height:80px;background:#5A1EE6;border-radius:50%;}

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
  <div>
    <div class="avatar"></div><br>
    <a href="edit_user.php" class="btn purple">Ubah Profil</a>
  </div>

  <div>
    <h2><?= $_SESSION['username']; ?></h2>
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
    <p>📅 <?= $p['tgl_lahir']; ?></p>
    <p>📍 <?= $p['alamat']; ?></p>
    <p>💀 <?= $p['riwayat']; ?></p>
  </div>

  <div class="data-right">
    <a href="edit_peserta.php?id=<?= $p['id_peserta']; ?>" class="btn purple">Ubah</a>
    <a href="hapus_peserta.php?id=<?= $p['id_peserta']; ?>" class="btn red">Hapus</a>
  </div>
</div>
<?php endwhile; ?>
</div>

<!-- PESANAN -->
<div id="pesanan" class="tab-content data-section">
<?php while($b=ambil($pesanan)): ?>
<div class="data-card">
  <div class="data-left">
    <p><b><?= $b['tujuan']; ?></b></p>
    <p>📅 Booking: <?= $b['tgl_booking']; ?></p>
    <p>🚍 Berangkat: <?= $b['tgl_berangkat']; ?></p>
    <p>Status: <?= $b['status']; ?></p>
  </div>

  <div class="data-right">
    <a href="form_pembayaran.php?id_booking=<?= $b['id_booking']; ?>" class="btn purple">Bayar</a>
  </div>
</div>
<?php endwhile; ?>
</div>

<!-- PEMBAYARAN -->
<div id="pembayaran" class="tab-content data-section">
<?php while($py=ambil($pembayaran)): ?>
<div class="data-card">
  <div class="data-left">
    <p><b><?= $py['nama']; ?></b></p>
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
    <img src="../gambar/logo-rebon.png" class="footer-logo-img">
  </div>
  <div class="copyright">© 2026 REBON ADVENTURE</div>
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