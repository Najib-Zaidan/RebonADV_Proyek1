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
<!-- PESANAN -->
<div id="pesanan" class="tab-content data-section">
    <!-- Tombol Switch Open vs Private -->
    <div style="display: flex; gap: 12px; margin-bottom: 25px; justify-content: center;">
        <button id="btn-open" class="btn purple" onclick="switchTrip('open')" style="padding: 10px 25px; font-weight: bold;">Open Trip</button>
        <button id="btn-private" class="btn" onclick="switchTrip('private')" style="padding: 10px 25px; font-weight: bold; background:#fff; color:#333; border:1px solid #ccc;">Private Trip</button>
    </div>

    <!-- KONTEN OPEN TRIP -->
    <div id="content-open">
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
        <div class="data-card" style="display: block; border-left: 6px solid #6b3df5; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <div>
                    <h3 style="color: #321180; font-size: 18px;"><?= $b['tujuan']; ?></h3>
                    <p style="font-size: 12px; color: #777;">ID Booking: #BK-O<?= $id_booking; ?> | Pesan: <?= date('d M Y', strtotime($b['tgl_booking'])); ?></p>
                </div>
                <div style="text-align: right;">
                    <span style="padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; background: #eee; color: #333;">
                        <?= $b['status']; ?>
                    </span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px; background: #f8f9fa; padding: 15px; border-radius: 10px; border: 1px solid #eee; margin-bottom: 15px;">
                <div>
                    <p style="font-size: 11px; color: #888;">JUMLAH PESERTA</p>
                    <p style="font-weight: bold;"><?= $b['jumlah_peserta']; ?> Orang</p>
                </div>
                <div>
                    <p style="font-size: 11px; color: #888;">HARGA TRIP</p>
                    <p style="font-weight: bold;">Rp <?= number_format($t_info['harga']); ?> <small>/org</small></p>
                </div>
                <div>
                    <p style="font-size: 11px; color: #888;">MINIMAL DP</p>
                    <p style="font-weight: bold; color: #ff4b2b;">Rp <?= number_format($t_info['harga_dp']); ?>/org</p>
                </div>
                <div>
                    <p style="font-size: 11px; color: #888;">TOTAL TAGIHAN</p>
                    <p style="font-weight: bold; color: #6b3df5; font-size: 16px;">Rp <?= number_format($total_harga); ?></p>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <p style="font-size: 13px; color: #555;">📅 Berangkat: <b><?= date('d M Y', strtotime($b['tgl_berangkat'])); ?></b></p>
                <div style="display: flex; gap: 8px;">
                    <a href="detail_peserta.php?id_booking=<?= $id_booking; ?>" class="btn" style="background:#f0f0f0; margin:0;">👥 Detail Peserta</a>
                    <?php if ($status_batal === null): ?>
                        <a href="form_pembayaran.php?id_booking=<?= $id_booking; ?>" class="btn purple" style="margin:0;">💳 Bayar</a>
                        <a href="batal_pesanan.php?id_booking=<?= $id_booking; ?>" class="btn" style="color:red; border:1px solid red; margin:0;">✖ Batal</a>
                    <?php elseif ($status_batal == 0): ?>
                        <span style="color:orange; font-weight:bold; font-size:13px; align-self:center;">⏳ Menunggu Batal</span>
                    <?php else: ?>
                        <span style="color:green; font-weight:bold; font-size:13px; align-self:center;">✅ Dibatalkan</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- KONTEN PRIVATE TRIP -->
<div id="content-private" style="display:none; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
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
    <div class="data-card" style="display: block; border-left: 5px solid #ff4b2b; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-radius: 8px; padding: 15px; background: #fff;">
        
        <!-- Header Card -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
            <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                    <h3 style="color: #333; font-size: 17px; margin: 0; font-weight: 600;"><?= $pr['tujuan']; ?></h3>
                    <span style="background:#ff4b2b; color:white; font-size:9px; padding:2px 6px; border-radius:4px; font-weight: bold;">PRIVATE</span>
                </div>
                <p style="font-size: 12px; color: #777; margin: 0;">ID: #BK-P<?= $id_pr; ?> | Diajukan: <?= date('d M Y', strtotime($pr['tgl_booking'])); ?></p>
            </div>
            <div style="text-align: right;">
                <span style="padding: 5px 10px; border-radius: 5px; font-size: 11px; font-weight: 600;
                    <?= $pr['status_trip'] == 'Disetujui' ? 'background:#e8f5e9; color:#2e7d32;' : ($pr['status_trip'] == 'Ditolak' ? 'background:#ffebee; color:#c62828;' : 'background:#fff8e1; color:#f9a825;'); ?>">
                    <?= $pr['status_trip']; ?>
                </span>
            </div>
        </div>

        <!-- Grid Detail Informasi -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; background: #fdfdfd; padding: 12px; border-radius: 6px; border: 1px solid #eee; margin-bottom: 15px;">
            <div>
                <p style="font-size: 10px; color: #999; margin-bottom: 4px;">PESERTA</p>
                <p style="font-size: 13px; font-weight: 600; color: #333; margin: 0;"><?= $pr['jumlah_peserta']; ?> Orang</p>
            </div>
            <div>
                <p style="font-size: 10px; color: #999; margin-bottom: 4px;">HARGA DEAL</p>
                <p style="font-size: 13px; font-weight: 600; color: #333; margin: 0;"><?= $pr['harga'] ? 'Rp '.number_format($pr['harga']) : '-'; ?></p>
            </div>
            <div>
                <p style="font-size: 10px; color: #999; margin-bottom: 4px;">DP DISEPAKATI</p>
                <p style="font-size: 13px; font-weight: 600; color: #ff4b2b; margin: 0;"><?= $pr['harga_dp'] ? 'Rp '.number_format($pr['harga_dp']) : '-'; ?></p>
            </div>
            <div style="border-left: 1px solid #eee; padding-left: 10px;">
                <p style="font-size: 10px; color: #999; margin-bottom: 4px;">STATUS BAYAR</p>
                <p style="font-size: 13px; font-weight: 600; color: <?= $color_bayar; ?>; margin: 0;"><?= $pr['status_bayar']; ?></p>
            </div>
        </div>

        <!-- Footer & Aksi -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f5f5f5; padding-top: 12px;">
            <div style="display: flex; flex-direction: column;">
                <p style="font-size: 11px; color: #999; margin: 0;">Jadwal:</p>
                <p style="font-size: 12px; color: #555; margin: 0;"><b><?= date('d M Y', strtotime($pr['tgl_berangkat'])); ?></b> - <b><?= date('d M Y', strtotime($pr['tgl_pulang'])); ?></b></p>
            </div>
            
            <div style="display: flex; gap: 6px; align-items: center;">
                <!-- Tombol Detail -->
                <a href="detail_private.php?id=<?= $id_pr; ?>" class="btn" style="background:#f5f5f5; color:#444; margin:0; padding: 6px 12px; border-radius: 4px; font-size: 11px; border: 1px solid #ddd;">👥 Detail</a>

                <?php if ($status_batal_pr === null): ?>
                    <!-- Tombol Bayar -->
                    <?php if($pr['status_trip'] == 'Disetujui' && $pr['status_bayar'] != 'Lunas' && $pr['status_bayar'] != 'Dibatalkan'): ?>
                        <a href="form_bayar_private.php?id=<?= $id_pr; ?>" class="btn purple" style="margin:0; padding: 6px 15px; border-radius: 4px; font-size: 11px; font-weight: bold; background: #6b3df5;">💳 Bayar</a>
                    <?php endif; ?>
                    
                    <!-- Tombol Batal -->
                    <?php if($pr['status_bayar'] != 'Dibatalkan'): ?>
                        <a href="batal_private.php?id=<?= $id_pr; ?>" class="btn" style="color:#d9534f; border:1px solid #d9534f; margin:0; padding: 5px 12px; border-radius: 4px; background: transparent; font-size: 11px;">✖ Batal</a>
                    <?php endif; ?>

                <?php elseif ($status_batal_pr == 0): ?>
                    <span style="color:#f39c12; font-weight:bold; font-size:11px; padding: 6px;">⏳ Menunggu Batal</span>
                <?php else: ?>
                    <span style="color:#28a745; font-weight:bold; font-size:11px; padding: 6px;">✅ Dibatalkan</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
 </div>


<!-- PEMBAYARAN -->
<!-- PEMBAYARAN -->
<!-- PEMBAYARAN -->
<div id="pembayaran" class="tab-content data-section">
    <!-- Header Tab & Switcher -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="color: #321180; margin: 0;">Riwayat Pembayaran</h2>
            <p style="color: #666; font-size: 14px;">Pantau status verifikasi pembayaran Anda di sini.</p>
        </div>
        <div class="trip-switcher" style="display: flex; background: #eee; padding: 5px; border-radius: 30px;">
            <button onclick="switchPayment('open')" id="btn-pay-open" class="btn purple" style="margin:0; border-radius: 25px; padding: 8px 20px;">Open Trip</button>
            <button onclick="switchPayment('private')" id="btn-pay-private" class="btn" style="margin:0; border-radius: 25px; padding: 8px 20px; background:transparent; color:#333;">Private Trip</button>
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
        <div class="data-card" style="border-left: 6px solid #6b3df5; margin-bottom: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <!-- Label & Header -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px dashed #eee; padding-bottom: 15px; margin-bottom: 15px;">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                        <span style="background:rgba(107, 61, 245, 0.1); color:#6b3df5; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:bold; border: 1px solid #6b3df5;">#PYM-O<?= $py['id_payment']; ?></span>
                        <span style="font-size: 12px; color: #888;">📅 <?= date('d M Y, H:i', strtotime($py['tgl_bayar'])); ?></span>
                    </div>
                    <h3 style="margin:0; color:#321180; font-size: 20px;"><?= $py['tujuan']; ?></h3>
                </div>
                <div style="text-align: right;">
                    <span style="display:block; padding: 6px 15px; border-radius: 8px; font-size: 12px; font-weight: bold; 
                        <?= $py['status'] == 'Diverifikasi' ? 'background:#d4edda; color:#155724;' : ($py['status'] == 'Ditolak' ? 'background:#f8d7da; color:#721c24;' : 'background:#fff3cd; color:#856404;'); ?>">
                        <?= $py['status']; ?>
                    </span>
                    <small style="color: #999; font-size: 10px; margin-top: 5px; display: block;">Status Booking: <b><?= $data_trip_open['status_booking']; ?></b></small>
                </div>
            </div>

            <!-- Stats Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px;">
                <div style="background:#f8f9fa; padding:12px; border-radius:10px;">
                    <p style="font-size: 11px; color: #888; margin-bottom:5px;">RINCIAN PESERTA</p>
                    <p style="font-weight: bold; margin:0;"><?= $py['jumlah_peserta']; ?> Orang</p>
                    <small style="color: #666;">@ Rp <?= number_format($harga_per_orang); ?></small>
                </div>
                <div style="background:#f8f9fa; padding:12px; border-radius:10px;">
                    <p style="font-size: 11px; color: #888; margin-bottom:5px;">TOTAL TAGIHAN</p>
                    <p style="font-weight: bold; margin:0; color: #321180;">Rp <?= number_format($total_tagihan); ?></p>
                    <small style="color: #ff4b2b;">Min. DP: Rp <?= number_format($data_trip_open['harga_dp']); ?></small>
                </div>
                <div style="background: #eef2ff; padding: 12px; border-radius: 10px; border: 1px solid #d1d9ff;">
                    <p style="font-size: 11px; color: #6b3df5; font-weight:bold; margin-bottom:5px;">NOMINAL DIBAYAR</p>
                    <p style="font-size: 18px; font-weight: 900; color: #6b3df5; margin:0;">Rp <?= number_format($py['nominal']); ?></p>
                </div>
                <div style="background: #fff; padding: 12px; border-radius: 10px; border: 1px solid #eee;">
                    <p style="font-size: 11px; color: #888; margin-bottom:5px;">SISA PEMBAYARAN</p>
                    <p style="font-weight: bold; margin:0; color: <?= $sisa_tagihan <= 0 ? '#28a745' : '#dc3545'; ?>;">
                        <?= $sisa_tagihan <= 0 ? 'LUNAS' : 'Rp '.number_format($sisa_tagihan); ?>
                    </p>
                </div>
            </div>

            <!-- Footer Card -->
            <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                <p style="font-size: 12px; color: #777; margin:0;"><i>Catatan: <?= $py['catatan'] ?: '-'; ?></i></p>
                <a href="../gambar/payment/<?= $py['bukti_bayar']; ?>" target="_blank" class="btn purple" style="margin:0; font-size: 12px; border-radius: 8px;">🔍 Lihat Bukti Transfer</a>
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
        <div class="data-card" style="border-left: 6px solid #ff4b2b; margin-bottom: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px dashed #eee; padding-bottom: 15px; margin-bottom: 15px;">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                        <span style="background:rgba(255, 75, 43, 0.1); color:#ff4b2b; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:bold; border: 1px solid #ff4b2b;">#PYM-P<?= $pyp['id_payment']; ?></span>
                        <span style="font-size: 12px; color: #888;">📅 <?= date('d M Y, H:i', strtotime($pyp['tgl_bayar'])); ?></span>
                    </div>
                    <h3 style="margin:0; color:#321180; font-size: 20px;"><?= $pyp['tujuan']; ?></h3>
                </div>
                <div style="text-align: right;">
                    <span style="display:block; padding: 6px 15px; border-radius: 8px; font-size: 12px; font-weight: bold; 
                        <?= $pyp['status'] == 'Diverifikasi' ? 'background:#d4edda; color:#155724;' : ($pyp['status'] == 'Ditolak' ? 'background:#f8d7da; color:#721c24;' : 'background:#fff3cd; color:#856404;'); ?>">
                        <?= $pyp['status']; ?>
                    </span>
                    <small style="color: #999; font-size: 10px; margin-top: 5px; display: block;">Status Bayar: <b><?= $data_pr['status_bayar']; ?></b></small>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px;">
                <div style="background:#fffafa; padding:12px; border-radius:10px;">
                    <p style="font-size: 11px; color: #888; margin-bottom:5px;">JUMLAH PESERTA</p>
                    <p style="font-weight: bold; margin:0;"><?= $data_pr['jumlah_peserta']; ?> Orang</p>
                </div>
                <div style="background:#fffafa; padding:12px; border-radius:10px;">
                    <p style="font-size: 11px; color: #888; margin-bottom:5px;">HARGA PAKET</p>
                    <p style="font-weight: bold; margin:0; color: #321180;"><?= $total_tagihan_pr ? 'Rp '.number_format($total_tagihan_pr) : 'Nego Admin'; ?></p>
                </div>
                <div style="background: #ff4b2b; padding: 12px; border-radius: 10px; color: white;">
                    <p style="font-size: 11px; color: #ffcccc; font-weight:bold; margin-bottom:5px;">NOMINAL DIBAYAR</p>
                    <p style="font-size: 18px; font-weight: 900; margin:0;">Rp <?= number_format($pyp['nominal']); ?></p>
                </div>
                <div style="background: #fffafa; padding: 12px; border-radius: 10px; border: 1px solid #ffe5de;">
                    <p style="font-size: 11px; color: #888; margin-bottom:5px;">DP DISEPAKATI</p>
                    <p style="font-weight: bold; margin:0; color: #ff4b2b;"><?= $data_pr['harga_dp'] ? 'Rp '.number_format($data_pr['harga_dp']) : '-'; ?></p>
                </div>
            </div>

            <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                <p style="font-size: 12px; color: #777; margin:0;"><i>Catatan: <?= $pyp['catatan'] ?: '-'; ?></i></p>
                <a href="../gambar/payment_private/<?= $pyp['bukti_bayar']; ?>" target="_blank" class="btn red" style="margin:0; font-size: 12px; border-radius: 8px;">🔍 Lihat Bukti Transfer</a>
            </div>
        </div>
        <?php endwhile; 
        else: echo "<div style='text-align:center; padding:40px; color:#999;'><p>Belum ada riwayat pembayaran Private Trip.</p></div>";
        endif; ?>
    </div>
</div>

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
    const openCont = document.getElementById('content-open');
    const privCont = document.getElementById('content-private');
    const btnOpen = document.getElementById('btn-open');
    const btnPriv = document.getElementById('btn-private');

    if (type === 'open') {
        openCont.style.display = 'block';
        privCont.style.display = 'none';
        btnOpen.className = 'btn purple';
        btnPriv.className = 'btn';
        btnPriv.style.background = '#fff';
    } else {
        openCont.style.display = 'none';
        privCont.style.display = 'block';
        btnPriv.className = 'btn purple';
        btnPriv.style.background = '#6b3df5';
        btnOpen.className = 'btn';
        btnOpen.style.background = '#fff';
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
        btnOpen.className = 'btn purple';
        btnPriv.className = 'btn';
        btnPriv.style.background = 'transparent';
        btnPriv.style.color = '#333';
    } else {
        openCont.style.display = 'none';
        privCont.style.display = 'block';
        btnPriv.className = 'btn red';
        btnPriv.style.background = '#ff4b2b';
        btnPriv.style.color = '#fff';
        btnOpen.className = 'btn';
        btnOpen.style.background = 'transparent';
        btnOpen.style.color = '#333';
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