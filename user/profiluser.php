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
        b.*,
        t.*
    FROM booking b
    JOIN trip t ON b.id_trip = t.id_trip
    WHERE b.id_akun = '$id_akun'
    ORDER BY b.tgl_booking DESC
");
$pembayaran = kueri("
    SELECT 
        py.*,
        b.jumlah_peserta,
        t.tujuan
    FROM payment_open py
    JOIN booking b ON py.id_booking = b.id_booking
    JOIN trip t ON b.id_trip = t.id_trip
    WHERE b.id_akun = '$id_akun'
    ORDER BY py.tgl_bayar DESC
");

// Query untuk mengambil data pengajuan pembatalan Private Trip
$pesanan_private = kueri("
    SELECT * FROM private_trip 
    WHERE id_akun = '$id_akun' 
    ORDER BY tgl_booking DESC
");

// Query untuk menggabungkan riwayat pembayaran Open dan Private (Opsional jika ingin disatu tab)
// Namun di sini saya siapkan data khusus pembayaran private jika ingin dipisah
$pembayaran_private = kueri("
    SELECT py.*, t.tujuan 
    FROM payment_private py
    JOIN private_trip t ON py.id_private = t.id_private
    WHERE t.id_akun = '$id_akun'
    ORDER BY py.tgl_bayar DESC
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

.pay-card-horizontal {
    display: block !important;
    width: 100%;
    margin-bottom: 20px;
}

.pay-row {
    display: grid;
    /* Dibagi menjadi 5 kolom: Info, Tagihan, Bayar, Sisa, Tombol */
    grid-template-columns: 1.2fr 1fr 1fr 1fr 0.8fr; 
    gap: 15px;
    align-items: center;
    background: #fcfcfc;
    padding: 15px;
    border-radius: 10px;
    border: 1px solid #eee;
}

@media (max-width: 992px) {
    .pay-row { grid-template-columns: 1fr 1fr; }
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
<!-- PESANAN -->
<div id="pesanan" class="tab-content data-section">
    <!-- Tombol Switch Open vs Private -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 style="color: #321180; margin: 0; font-size: 24px; font-weight: 800;">Riwayat Pemesanan</h2>
        <p style="color: #718096; font-size: 14px; margin-top: 5px;">Kelola pesanan trip Anda di sini.</p>
    </div>
    
    <div class="trip-switcher" style="display: flex; background: #edf2f7; padding: 4px; border-radius: 50px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; width: fit-content;">
        <button onclick="switchTrip('open')" id="btn-open" 
            style="border: none; border-radius: 50px; padding: 10px 25px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.3s ease; background: #6b3df5; color: #fff; box-shadow: 0 4px 6px rgba(107, 61, 245, 0.2);">
            Open Trip
        </button>
        <button onclick="switchTrip('private')" id="btn-private" 
            style="border: none; border-radius: 50px; padding: 10px 25px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.3s ease; background: transparent; color: #718096;">
            Private Trip
        </button>
    </div>
</div>


    <!-- KONTEN OPEN TRIP -->
    <div id="trip-content-open">
        <?php 
        mysqli_data_seek($pesanan, 0); 
        while($b = ambil($pesanan)): 
            $id_booking = $b['id_booking'];
            
            // Ambil detail harga dari tabel trip
            $id_t = $b['id_trip'];
            $t_info = ambil(kueri("SELECT harga, harga_dp FROM trip WHERE id_trip = '$id_t'"));
            $total_harga = $t_info['harga'] * $b['jumlah_peserta'];

            // Cek status pembatalan
            $cek_batal_q = kueri("SELECT status FROM batal_open WHERE id_booking = '$id_booking'");
            $cek_batal = mysqli_fetch_assoc($cek_batal_q);
            $status_batal = $cek_batal['status'] ?? null;
        ?>
        <div class="data-card" style="display: block; border-left: 6px solid #6b3df5; margin-bottom: 25px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; background: #fff;">
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 15px 20px; background: rgba(107, 61, 245, 0.02); border-bottom: 1px solid #f0f0f0;">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                        <h3 style="color: #321180; font-size: 19px; margin: 0; font-weight: 800;"><?= $b['tujuan']; ?></h3>
                        <span style="background:#6b3df5; color:white; font-size:10px; padding:3px 8px; border-radius:5px; font-weight: 900; letter-spacing: 0.5px;">OPEN TRIP</span>
                    </div>
                    <p style="font-size: 12px; color: #888; margin: 0;">ID Booking: #BK-O<?= $id_booking; ?> | Pesan: <?= date('d M Y', strtotime($b['tgl_booking'])); ?></p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 10px; color: #aaa; margin-bottom: 4px; font-weight: bold; text-transform: uppercase;">Status Booking</div>
                    <span style="padding: 6px 15px; border-radius: 30px; font-size: 11px; font-weight: 800; 
                        <?= $b['status'] == 'Lunas' ? 'background:#e6fffa; color:#2c7a7b; border: 1px solid #b2f5ea;' : ($b['status'] == 'Dibatalkan' ? 'background:#fff5f5; color:#c53030; border: 1px solid #feb2b2;' : 'background:#fffaf0; color:#b7791f; border: 1px solid #fbe3a1;'); ?>">
                        <?= strtoupper($b['status']); ?>
                    </span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 15px; background: #f8f9fa; padding: 20px; border-radius: 12px; border: 1px solid #eee; margin: 15px 20px;">
                <div>
                    <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">PESERTA</p>
                    <p style="font-size: 16px; font-weight: 700; margin:0; color: #2d3748;"><?= $b['jumlah_peserta']; ?> Orang</p>
                </div>
                <div>
                    <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">HARGA TRIP</p>
                    <p style="font-size: 16px; font-weight: 700; margin:0; color: #321180;">Rp <?= number_format($t_info['harga']); ?> <small style="font-size: 10px; font-weight: normal;">/org</small></p>
                </div>
                <div>
                    <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">MINIMAL DP</p>
                    <p style="font-size: 16px; font-weight: 700; margin:0; color: #ff4b2b;">Rp <?= number_format($t_info['harga_dp']); ?> <small style="font-size: 10px; font-weight: normal;">/org</small></p>
                </div>
                <div style="border-left: 2px solid #e2e8f0; padding-left: 15px;">
                    <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">TOTAL TAGIHAN</p>
                    <p style="font-size: 16px; font-weight: 800; color: #6b3df5; margin: 0;">Rp <?= number_format($total_harga); ?></p>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 20px 20px 20px;">
                <div style="display: flex; flex-direction: column;">
                    <p style="font-size: 11px; color: #a0aec0; margin: 0; font-weight: bold;">JADWAL BERANGKAT:</p>
                    <p style="font-size: 13px; color: #4a5568; margin: 0;">📅 <b><?= date('d M Y', strtotime($b['tgl_berangkat'])); ?></b></p>
                </div>
                
                <div style="display: flex; gap: 8px; align-items: center;">
                    <a href="detail_pesanan.php?id_booking=<?= $id_booking; ?>" class="btn" style="background:#f0f4f8; color:#4a5568; margin:0; font-weight: bold; font-size: 12px; border-radius: 8px; border: 1px solid #d1d9e6; text-decoration:none;">Detail Pesanan</a>

                    <?php if ($status_batal === null && $b['status'] != 'Lunas'): ?>
                        <a href="form_pembayaran.php?id_booking=<?= $id_booking; ?>" class="btn purple" style="margin:0; font-weight: 900; font-size: 12px; border-radius: 8px; background: #6b3df5; color:white; text-decoration:none;">Bayar</a>
                        
                    
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php endwhile; ?>
    </div>

    <!-- KONTEN PRIVATE TRIP -->
<div id="trip-content-private" style="display:none; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <?php 
    mysqli_data_seek($pesanan_private, 0);
    while($pr = ambil($pesanan_private)): 
        $id_pr = $pr['id_private'];
        
        // Cek status pembatalan di database
        $cek_batal_pr_q = kueri("SELECT status FROM batal_private WHERE id_private = '$id_pr'");
        $cek_batal_pr = mysqli_fetch_assoc($cek_batal_pr_q);
        $status_batal_pr = $cek_batal_pr['status'] ?? null;
        
        // Logika warna status bayar yang lebih soft
        $color_bayar = ($pr['status_bayar'] == 'Lunas') ? '#28a745' : (($pr['status_bayar'] == 'DP') ? '#f39c12' : '#6b3df5');
    ?>
<div class="data-card" style="display: block; border-left: 6px solid #ff4b2b; margin-bottom: 25px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; background: #fff;">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 15px 20px; background: rgba(255, 75, 43, 0.02); border-bottom: 1px solid #f0f0f0;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <h3 style="color: #321180; font-size: 19px; margin: 0; font-weight: 800;"><?= $pr['tujuan']; ?></h3>
                <span style="background:#ff4b2b; color:white; font-size:10px; padding:3px 8px; border-radius:5px; font-weight: 900; letter-spacing: 0.5px;">PRIVATE</span>
            </div>
            <p style="font-size: 12px; color: #888; margin: 0;">ID Booking: #BK-P<?= $id_pr; ?> | Diajukan: <?= date('d M Y', strtotime($pr['tgl_booking'])); ?></p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 10px; color: #aaa; margin-bottom: 4px; font-weight: bold; text-transform: uppercase;">Status Pengajuan</div>
            <span style="padding: 6px 15px; border-radius: 30px; font-size: 11px; font-weight: 800; 
                <?= $pr['status_trip'] == 'Disetujui' ? 'background:#e6fffa; color:#2c7a7b; border: 1px solid #b2f5ea;' : ($pr['status_trip'] == 'Ditolak' ? 'background:#fff5f5; color:#c53030; border: 1px solid #feb2b2;' : 'background:#fffaf0; color:#b7791f; border: 1px solid #fbe3a1;'); ?>">
                <?= strtoupper($pr['status_trip']); ?>
            </span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 15px; background: #f8f9fa; padding: 20px; border-radius: 12px; border: 1px solid #eee; margin: 15px 20px;">
        <div>
            <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">PESERTA</p>
            <p style="font-size: 16px; font-weight: 700; margin:0; color: #2d3748;"><?= $pr['jumlah_peserta']; ?> Orang</p>
        </div>
        <div>
            <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">HARGA DEAL</p>
            <p style="font-size: 16px; font-weight: 700; margin:0; color: #321180;"><?= $pr['harga'] ? 'Rp '.number_format($pr['harga']) : '<span style="color:#aaa; font-style:italic;">Nego</span>'; ?></p>
        </div>
        <div>
            <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">DP DISEPAKATI</p>
            <p style="font-size: 16px; font-weight: 700; margin:0; color: #ff4b2b;"><?= $pr['harga_dp'] ? 'Rp '.number_format($pr['harga_dp']) : '-'; ?></p>
        </div>
        <div style="border-left: 2px solid #e2e8f0; padding-left: 15px;">
            <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">STATUS BAYAR</p>
            <p style="font-size: 15px; font-weight: 800; color: <?= $color_bayar; ?>; margin: 0;"><?= strtoupper($pr['status_bayar']); ?></p>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 20px 20px 20px;">
        <div style="display: flex; flex-direction: column;">
            <p style="font-size: 11px; color: #a0aec0; margin: 0; font-weight: bold;">JADWAL TRIP:</p>
            <p style="font-size: 13px; color: #4a5568; margin: 0;">📅 <b><?= date('d M Y', strtotime($pr['tgl_berangkat'])); ?></b> s/d <b><?= date('d M Y', strtotime($pr['tgl_pulang'])); ?></b></p>
        </div>
        
        <div style="display: flex; gap: 8px; align-items: center;">
            <a href="detail_private.php?id=<?= $id_pr; ?>" class="btn" style="background:#f0f4f8; color:#4a5568; margin:0; font-weight: bold; font-size: 12px; border-radius: 8px; border: 1px solid #d1d9e6; text-decoration:none;">Detail Trip</a>

            <?php if ($status_batal_pr === null): ?>
                <?php if($pr['status_trip'] == 'Disetujui' && $pr['status_bayar'] != 'Lunas' && $pr['status_bayar'] != 'Dibatalkan'): ?>
                    <a href="form_pembayaran_private.php?id=<?= $id_pr; ?>" class="btn purple" style="margin:0; font-weight: 900; font-size: 12px; border-radius: 8px; background: #6b3df5; color:white; text-decoration:none;">Bayar</a>
                <?php endif; ?>
                
                <?php if($pr['status_bayar'] != 'Dibatalkan' && $pr['status_trip'] == 'Disetujui'): ?>
                    <a href="batal_private.php?id=<?= $id_pr; ?>" class="btn" style="color:#e53e3e; border:1px solid #e53e3e; margin:0; font-weight: bold; font-size: 12px; border-radius: 8px; background: transparent; text-decoration:none;">Batal</a>
                <?php endif; ?>

            <?php elseif ($status_batal_pr == 0): ?>
                <div style="background: #fffaf0; padding: 6px 12px; border-radius: 8px; border: 1px solid #fbd38d;">
                    <span style="color:#dd6b20; font-weight:800; font-size:12px;">⏳ Menunggu Batal</span>
                </div>
            <?php else: ?>
                <div style="background: #f0fff4; padding: 6px 12px; border-radius: 8px; border: 1px solid #9ae6b4;">
                    <span style="color:#38a169; font-weight:800; font-size:12px;">✅ Dibatalkan</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

    <?php endwhile; ?>
 </div>
</div>

<!-- PEMBAYARAN -->
<!-- PEMBAYARAN -->
<!-- PEMBAYARAN -->
<div id="pembayaran" class="tab-content data-section">
    <!-- Header Tab & Switcher -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 style="color: #321180; margin: 0; font-size: 24px; font-weight: 800;">Riwayat Pembayaran</h2>
        <p style="color: #718096; font-size: 14px; margin-top: 5px;">Pantau status verifikasi pembayaran Anda di sini.</p>
    </div>
    
    <div class="trip-switcher" style="display: flex; background: #edf2f7; padding: 4px; border-radius: 50px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        <button onclick="switchPayment('open')" id="btn-pay-open" 
            style="border: none; border-radius: 50px; padding: 10px 25px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.3s ease; background: #6b3df5; color: #fff; box-shadow: 0 4px 6px rgba(107, 61, 245, 0.2);">
            Open Trip
        </button>
        <button onclick="switchPayment('private')" id="btn-pay-private" 
            style="border: none; border-radius: 50px; padding: 10px 25px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.3s ease; background: transparent; color: #718096;">
            Private Trip
        </button>
    </div>
</div>


    <!-- SECTION OPEN TRIP -->
    <div id="pay-content-open">
        <?php 
        mysqli_data_seek($pembayaran, 0); 
        if(mysqli_num_rows($pembayaran) > 0):
            while($py = ambil($pembayaran)): 
                $id_b_open = $py['id_booking'];
                $data_trip_open = ambil(kueri("SELECT t.harga, t.harga_dp, b.status as status_booking FROM booking b JOIN trip t ON b.id_trip = t.id_trip WHERE b.id_booking = '$id_b_open'"));
                
                $harga_per_orang = $data_trip_open['harga'];
                $total_tagihan = $harga_per_orang * $py['jumlah_peserta'];
                $sudah_bayar = $py['nominal'];
                $sisa_tagihan = $total_tagihan - $sudah_bayar;
        ?>
<?php 
    $id_b_open = $py['id_booking'];
    $res_trip = kueri("SELECT t.harga, t.harga_dp, b.status as status_booking 
                       FROM booking b 
                       JOIN trip t ON b.id_trip = t.id_trip 
                       WHERE b.id_booking = '$id_b_open'");
    $data_trip_open = ambil($res_trip);
    
    $harga_per_orang = $data_trip_open['harga'] ?? 0;
    $total_tagihan = $harga_per_orang * $py['jumlah_peserta'];
    $min_dp_kolektif = ($data_trip_open['harga_dp'] ?? 0) * $py['jumlah_peserta'];
    $status_dari_db = $data_trip_open['status_booking']; 
?>
<div class="data-card pay-card-horizontal" style="border-left: 6px solid #6b3df5; border-radius: 16px; display: block !important; margin-bottom: 25px; transition: transform 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(107, 61, 245, 0.02); padding: 15px 20px; border-bottom: 1px solid #f0f0f0;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="text-align: center; background: #6b3df5; color: white; padding: 5px 12px; border-radius: 8px;">
                <span style="font-size: 10px; display: block; opacity: 0.8; font-weight: bold;">PAYMENT ID</span>
                <span style="font-size: 14px; font-weight: 900;">#<?= $py['id_payment']; ?></span>
            </div>
            <div>
                <h3 style="margin:0; color:#321180; font-size: 19px; font-weight: 800;"><?= $py['tujuan']; ?></h3>
                <div style="display: flex; gap: 10px; margin-top: 3px;">
                    <span style="font-size: 11px; color: #888;">📅 <?= date('d M Y', strtotime($py['tgl_bayar'])); ?></span>
                    <span style="font-size: 11px; color: #888;">⏰ <?= date('H:i', strtotime($py['tgl_bayar'])); ?> WIB</span>
                </div>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 10px; color: #aaa; margin-bottom: 4px; font-weight: bold; text-transform: uppercase;">Status Verifikasi</div>
            <span style="padding: 6px 15px; border-radius: 30px; font-size: 11px; font-weight: 800; border: 1px solid;
                <?= $py['status'] == 'Diverifikasi' ? 'background:#e6fffa; color:#2c7a7b; border-color:#b2f5ea;' : ($py['status'] == 'Ditolak' ? 'background:#fff5f5; color:#c53030; border-color:#feb2b2;' : 'background:#fffaf0; color:#b7791f; border-color:#fbe3a1;'); ?>">
                <?= strtoupper($py['status']); ?>
            </span>
        </div>
    </div>

    <div class="pay-row" style="padding: 20px; display: grid; grid-template-columns: 1fr 1fr 1.2fr 1fr 0.8fr; gap: 20px; align-items: center; background: white;">
        <div>
            <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">PESERTA</p>
            <p style="font-size: 16px; font-weight: 700; margin:0; color: #2d3748;"><?= $py['jumlah_peserta']; ?> Orang</p>
            <small style="color:#718096; font-size:11px;">@ Rp <?= number_format($harga_per_orang); ?></small>
        </div>
        
        <div>
            <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">TAGIHAN</p>
            <p style="font-size: 16px; font-weight: 700; margin:0; color:#321180;">Rp <?= number_format($total_tagihan); ?></p>
            <small style="color:#e53e3e; font-size:11px; font-weight:bold;">Min DP: <?= number_format($min_dp_kolektif); ?></small>
        </div>

        <div style="background: #f0f5ff; padding: 12px; border-radius: 12px; text-align: center; border: 2px dashed #cbd5e0;">
            <p style="font-size: 11px; color: #5a67d8; font-weight:bold; margin-bottom: 4px;">NOMINAL DIBAYAR</p>
            <p style="font-size: 20px; font-weight: 900; color: #434190; margin:0;">Rp <?= number_format($py['nominal']); ?></p>
        </div>

        <div style="text-align: center;">
            <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">PROSES BOOKING</p>
            <div style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 900; color: white; background: <?= 
                ($status_dari_db == 'Lunas') ? '#48bb78' : 
                (($status_dari_db == 'DP' || $status_dari_db == 'Bayar non-DP') ? '#5a67d8' : '#ed64a1'); 
            ?>;">
                <?= strtoupper($status_dari_db); ?>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <a href="detail_pembayaran.php?id=<?= $py['id_payment']; ?>" class="btn" style="margin:0; font-size: 11px; font-weight: bold; padding: 8px; background: #2d3748; color: white; text-align:center; border-radius: 8px; text-decoration:none; transition: 0.2s;">📄 Detail</a>
            <a href="../gambar/payment/<?= $py['bukti_bayar']; ?>" target="_blank" class="btn purple" style="margin:0; font-size: 11px; font-weight: bold; padding: 8px; text-align:center; border-radius: 8px; text-decoration:none; background: #6b3df5;">🔍 Bukti</a>
        </div>
    </div>

    <div style="padding: 12px 20px; background: #fdfdfd; border-top: 1px solid #f0f0f0; display: flex; align-items: flex-start; gap: 10px;">
        <div style="background: #edf2f7; padding: 5px 8px; border-radius: 6px; font-size: 10px; font-weight: bold; color: #4a5568;">CATATAN</div>
        <p style="font-size: 12px; color: #4a5568; margin: 0; line-height: 1.5; font-style: italic;">
            "<?= !empty($py['catatan']) ? $py['catatan'] : 'Tidak ada catatan dari penyewa.'; ?>"
        </p>
    </div>
</div>

        <?php endwhile; 
        else: echo "<div style='text-align:center; padding:40px; color:#999;'><p>Belum ada riwayat pembayaran Open Trip.</p></div>";
        endif; ?>
    </div>

    <!-- SECTION PRIVATE TRIP -->
    <div id="pay-content-private" style="display: none;">
        <?php 
        if(mysqli_num_rows($pembayaran_private) > 0):
            while($pyp = ambil($pembayaran_private)): 
                $id_pr = $pyp['id_private'];
                $data_pr = ambil(kueri("SELECT harga, harga_dp, jumlah_peserta, status_bayar FROM private_trip WHERE id_private = '$id_pr'"));
                $total_tagihan_pr = $data_pr['harga'] ?? 0;
        ?>
<?php 
    $id_pr = $pyp['id_private'];
    $data_pr = ambil(kueri("SELECT harga, harga_dp, jumlah_peserta, status_bayar, status_trip FROM private_trip WHERE id_private = '$id_pr'"));
    
    $total_tagihan_pr = $data_pr['harga'] ?? 0;
    $status_bayar_pr = $data_pr['status_bayar'];
?>
<div class="data-card pay-card-horizontal" style="border-left: 6px solid #ff4b2b; border-radius: 16px; display: block !important; margin-bottom: 25px; transition: transform 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255, 75, 43, 0.02); padding: 15px 20px; border-bottom: 1px solid #f0f0f0;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="text-align: center; background: #ff4b2b; color: white; padding: 5px 12px; border-radius: 8px;">
                <span style="font-size: 10px; display: block; opacity: 0.8; font-weight: bold;">PAYMENT ID</span>
                <span style="font-size: 14px; font-weight: 900;">#P-<?= $pyp['id_payment']; ?></span>
            </div>
            <div>
                <h3 style="margin:0; color:#321180; font-size: 19px; font-weight: 800;"><?= $pyp['tujuan']; ?></h3>
                <div style="display: flex; gap: 10px; margin-top: 3px;">
                    <span style="font-size: 11px; color: #888;">📅 <?= date('d M Y', strtotime($pyp['tgl_bayar'])); ?></span>
                    <span style="font-size: 11px; color: #888;">⏰ <?= date('H:i', strtotime($pyp['tgl_bayar'])); ?> WIB</span>
                </div>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 10px; color: #aaa; margin-bottom: 4px; font-weight: bold; text-transform: uppercase;">Status Verifikasi</div>
            <span style="padding: 6px 15px; border-radius: 30px; font-size: 11px; font-weight: 800; border: 1px solid;
                <?= $pyp['status'] == 'Diverifikasi' ? 'background:#e6fffa; color:#2c7a7b; border-color:#b2f5ea;' : ($pyp['status'] == 'Ditolak' ? 'background:#fff5f5; color:#c53030; border-color:#feb2b2;' : 'background:#fffaf0; color:#b7791f; border-color:#fbe3a1;'); ?>">
                <?= strtoupper($pyp['status']); ?>
            </span>
        </div>
    </div>

    <div class="pay-row" style="padding: 20px; display: grid; grid-template-columns: 1fr 1fr 1.2fr 1fr 0.8fr; gap: 20px; align-items: center; background: white;">
        <div>
            <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">TOTAL PESERTA</p>
            <p style="font-size: 16px; font-weight: 700; margin:0; color: #2d3748;"><?= $data_pr['jumlah_peserta']; ?> Orang</p>
            <small style="color:#718096; font-size:11px;">Status: <?= $data_pr['status_trip']; ?></small>
        </div>
        
        <div>
            <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">HARGA PAKET</p>
            <p style="font-size: 16px; font-weight: 700; margin:0; color:#321180;"><?= $total_tagihan_pr ? 'Rp '.number_format($total_tagihan_pr) : 'Nego Admin'; ?></p>
            <small style="color:#ff4b2b; font-size:11px; font-weight:bold;">DP: <?= $data_pr['harga_dp'] ? 'Rp '.number_format($data_pr['harga_dp']) : '-'; ?></small>
        </div>

        <div style="background: #fff5f2; padding: 12px; border-radius: 12px; text-align: center; border: 2px dashed #feb2b2;">
            <p style="font-size: 11px; color: #ff4b2b; font-weight:bold; margin-bottom: 4px;">NOMINAL DIBAYAR</p>
            <p style="font-size: 20px; font-weight: 900; color: #c53030; margin:0;">Rp <?= number_format($pyp['nominal']); ?></p>
        </div>

        <div style="text-align: center;">
            <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">STATUS BAYAR</p>
            <div style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 900; color: white; background: <?= 
                ($status_bayar_pr == 'Lunas') ? '#48bb78' : 
                ($status_bayar_pr == 'DP' ? '#ff4b2b' : '#ed64a1'); 
            ?>;">
                <?= strtoupper($status_bayar_pr); ?>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <a href="detail_pembayaran_private.php?id=<?= $pyp['id_payment']; ?>" class="btn" style="margin:0; font-size: 11px; font-weight: bold; padding: 8px; background: #2d3748; color: white; text-align:center; border-radius: 8px; text-decoration:none;">📄 Detail</a>
            <a href="../gambar/payment_private/<?= $pyp['bukti_bayar']; ?>" target="_blank" class="btn red" style="margin:0; font-size: 11px; font-weight: bold; padding: 8px; text-align:center; border-radius: 8px; text-decoration:none; background: #ff4b2b; color: white;">🔍 Bukti</a>
        </div>
    </div>

    <div style="padding: 12px 20px; background: #fdfdfd; border-top: 1px solid #f0f0f0; display: flex; align-items: flex-start; gap: 10px;">
        <div style="background: #fff5f2; padding: 5px 8px; border-radius: 6px; font-size: 10px; font-weight: bold; color: #ff4b2b;">CATATAN ANDA</div>
        <p style="font-size: 12px; color: #4a5568; margin: 0; line-height: 1.5; font-style: italic;">
            "<?= !empty($pyp['catatan']) ? $pyp['catatan'] : 'Tidak ada catatan untuk pembayaran ini.'; ?>"
        </p>
    </div>
</div>

        <?php endwhile; 
        else: echo "<div style='text-align:center; padding:40px; color:#999;'><p>Belum ada riwayat pembayaran Private Trip.</p></div>";
        endif; ?>
    </div>
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
function switchTrip(type) {
    const openCont = document.getElementById('trip-content-open'); // Sesuaikan ID kontenmu
    const privCont = document.getElementById('trip-content-private'); // Sesuaikan ID kontenmu
    const btnOpen = document.getElementById('btn-open');
    const btnPriv = document.getElementById('btn-private');

    if (type === 'open') {
        openCont.style.display = 'block';
        privCont.style.display = 'none';
        
        // Style Active Open
        btnOpen.style.background = '#6b3df5';
        btnOpen.style.color = '#fff';
        btnOpen.style.boxShadow = '0 4px 6px rgba(107, 61, 245, 0.2)';
        
        // Style Inactive Private
        btnPriv.style.background = 'transparent';
        btnPriv.style.color = '#718096';
        btnPriv.style.boxShadow = 'none';
    } else {
        openCont.style.display = 'none';
        privCont.style.display = 'block';
        
        // Style Active Private
        btnPriv.style.background = '#ff4b2b';
        btnPriv.style.color = '#fff';
        btnPriv.style.boxShadow = '0 4px 6px rgba(255, 75, 43, 0.2)';
        
        // Style Inactive Open
        btnOpen.style.background = 'transparent';
        btnOpen.style.color = '#718096';
        btnOpen.style.boxShadow = 'none';
    }
}


function switchPayment(type) {
    const openCont = document.getElementById('pay-content-open');
    const privCont = document.getElementById('pay-content-private');
    const btnOpen = document.getElementById('btn-pay-open');
    const btnPriv = document.getElementById('btn-pay-private');

    if (type === 'open') {
        openCont.style.display = 'block';
        privCont.style.display = 'none';
        
        // Styling Button Open
        btnOpen.style.background = '#6b3df5';
        btnOpen.style.color = '#fff';
        btnOpen.style.boxShadow = '0 4px 6px rgba(107, 61, 245, 0.2)';
        
        // Styling Button Private
        btnPriv.style.background = 'transparent';
        btnPriv.style.color = '#718096';
        btnPriv.style.boxShadow = 'none';
    } else {
        openCont.style.display = 'none';
        privCont.style.display = 'block';
        
        // Styling Button Private
        btnPriv.style.background = '#ff4b2b';
        btnPriv.style.color = '#fff';
        btnPriv.style.boxShadow = '0 4px 6px rgba(255, 75, 43, 0.2)';
        
        // Styling Button Open
        btnOpen.style.background = 'transparent';
        btnOpen.style.color = '#718096';
        btnOpen.style.boxShadow = 'none';
    }
}

// Update fungsi tab agar selalu reset ke 'open' saat klik tab pesanan
function tab(id, el) {
    document.querySelectorAll('.tab-content').forEach(x => x.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(x => x.classList.remove('active-tab'));
    document.getElementById(id).classList.add('active');
    el.classList.add('active-tab');

    if (id === 'pesanan') {
        switchTrip('open'); // Reset ke open trip setiap kali tab pesanan ditekan
    }
}
</script>

</body>
</html>