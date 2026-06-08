<?php
session_start();
require 'fungsi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}

$id_akun = $_SESSION['id_akun'];

$peserta = kueri("
    SELECT
        po.*,
        COUNT(DISTINCT d.id_booking) AS jumlah_trip,
        MAX(b.tgl_booking) AS tgl_trip_terakhir,
        (
            SELECT tj.tujuan
            FROM detail d2
            JOIN booking b2 ON d2.id_booking = b2.id_booking
            JOIN trip t2 ON b2.id_trip = t2.id_trip
            JOIN tujuan tj ON t2.id_tujuan = tj.id_tujuan
            WHERE d2.id_peserta = po.id_peserta
            ORDER BY b2.tgl_booking DESC
            LIMIT 1
        ) AS trip_terakhir
    FROM peserta_open po
    LEFT JOIN detail d ON po.id_peserta = d.id_peserta
    LEFT JOIN booking b ON d.id_booking = b.id_booking
    WHERE po.id_akun = '$id_akun'
    GROUP BY po.id_peserta
    ORDER BY po.nama ASC
");
$pesanan = kueri("
    SELECT
        b.*,
        t.*,
        tj.*
    FROM booking b
    JOIN trip t ON b.id_trip = t.id_trip
    JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
    WHERE b.id_akun = '$id_akun'
    ORDER BY b.tgl_booking DESC
");
$pembayaran = kueri("
    SELECT 
        py.*,
        b.jumlah_peserta,
        tj.tujuan
    FROM payment_open py
    JOIN booking b ON py.id_booking = b.id_booking
    JOIN trip t ON b.id_trip = t.id_trip
    JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
    WHERE b.id_akun = '$id_akun'
    ORDER BY py.tgl_bayar DESC
");

$pesanan_private = kueri("
    SELECT * FROM private_trip 
    WHERE id_akun = '$id_akun' 
    ORDER BY tgl_booking DESC
");

$pembayaran_private = kueri("
    SELECT py.*, t.tujuan 
    FROM payment_private py
    JOIN private_trip t ON py.id_private = t.id_private
    WHERE t.id_akun = '$id_akun'
    ORDER BY py.tgl_bayar DESC
");

$notifikasi = kueri("
    SELECT * FROM notif 
    WHERE id_akun = '$id_akun' 
    ORDER BY waktu DESC
");

$cek_belum_dibaca = ambil(kueri("
    SELECT COUNT(*) as total FROM notif 
    WHERE id_akun = '$id_akun' AND dibaca = 0
"));
$jumlah_notif = $cek_belum_dibaca['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Profile</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial;}
body{background:#e7e2c8;}

.navbar{display:flex;justify-content:space-between;padding:20px 80px;background:#f4f0e5;}
.logo img{height:50px;}
nav{display:flex;gap:30px;align-items:center;}
nav a{text-decoration:none;color:black;}
.active5{background:#6b3df5;color:white;padding:10px 18px;border-radius:8px;border:none;}

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
.footer-column h4 { font-size: 16px; margin-bottom: 15px; font-weight: 800; }
.footer-column ul { list-style: none; }
.footer-column ul li { margin-bottom: 8px; font-size: 14px; font-weight: 600; }
.contact-item { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px; font-size: 14px; font-weight: 600; }
.social-section { margin-top: 25px; }
.social-icons { display: flex; gap: 10px; margin-top: 10px; }
.social-icons img { width: 24px; height: 24px; cursor: pointer; }
.footer-logo-img { width: 220px; height: auto; display: block; }
.copyright { text-align: center; font-size: 12px; margin-top: 20px; font-weight: bold; color: #333; }

.hero{height:150px;background:linear-gradient(#321180,#5A1EE6);}

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
.profile-left{ display: flex; flex-direction: column; align-items: center; }
.ikon img{ width: 90px; height: 90px; object-fit: contain; margin-top: 20px; margin-bottom: 5px; margin-left:5px; }
.profile-name{ flex: 1; text-align: center; }
.profile-name h2{ font-size: 58px; margin: 0; }
.action-group{ display: flex; flex-direction: column; gap: 10px; }

.btn{ padding:8px 15px;border-radius:8px; text-decoration:none;color:white; font-size:13px; }
.purple{background:#6b3df5;}
.red{background:#ff416c;}
.orange{background:#ff4b2b;}
.action-group{display:flex;flex-direction:column;gap:10px;}
.btn { display: inline-block; padding: 8px 14px; margin-top: 8px; border-radius: 8px; text-decoration: none; font-size: 14px; transition: 0.3s; }
.btn.purple { background: #6b3df5; color: white; }
.btn.purple:hover { background: #5027d6; }
.btn:not(.purple) { background: #f1f1f1; color: #333; border: 1px solid #ddd; }
.btn:not(.purple):hover { background: #e4e4e4; }

.menu-tabs{ width:80%;margin:auto; display:flex;justify-content:center; gap:40px;font-weight:bold; }
.menu-tabs div{cursor:pointer;padding-bottom:5px;}
.active-tab{border-bottom:3px solid black;}
.tab-content{display:none;}
.tab-content.active{display:block;}

.data-section{width:80%;margin:auto;}
.data-card{ background:white; padding:20px; margin-top:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 5px 10px rgba(0,0,0,0.1); }
.pay-card-horizontal { display: block; width: 100%; margin-bottom: 20px; }
.pay-row { display: grid; grid-template-columns: 1.2fr 1fr 1fr 1fr 0.8fr; gap: 15px; align-items: center; background: #fcfcfc; padding: 15px; border-radius: 10px; border: 1px solid #eee; }
@media (max-width: 992px) { .pay-row { grid-template-columns: 1fr 1fr; } }
.data-left p{margin-bottom:5px;}
.data-right{display:flex;flex-direction:column;gap:8px;}

/* ==============================
   PESERTA CARD - REDESIGN
   ============================== */
.peserta-toolbar {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 18px;
    margin-top: 16px;
    background: #fff;
    padding: 13px 16px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #eee;
}
.peserta-toolbar input[type="text"] {
    flex: 1;
    min-width: 200px;
    padding: 9px 14px 9px 36px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    color: #2d3748;
    background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23a0aec0' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") no-repeat 10px center;
    transition: border 0.2s;
}
.peserta-toolbar input[type="text"]:focus { border-color: #6b3df5; background-color: #fff; }
.peserta-toolbar select {
    padding: 9px 32px 9px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    background: #f8fafc;
    color: #2d3748;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    transition: border 0.2s;
    min-width: 190px;
}
.peserta-toolbar select:focus { border-color: #6b3df5; background-color: #fff; }
.peserta-count { font-size: 12px; color: #718096; font-weight: 600; margin-bottom: 10px; }
.peserta-no-result { display: none; text-align: center; padding: 40px 20px; color: #999; background: white; border-radius: 12px; border: 1px dashed #e2e8f0; font-size: 14px; }

.peserta-card {
    background: white;
    border-radius: 16px;
    margin-bottom: 16px;
    box-shadow: 0 3px 14px rgba(107,61,245,0.08);
    border: 1px solid #ede9fe;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}
.peserta-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(107,61,245,0.13); }
.peserta-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px 14px;
    background: linear-gradient(135deg, #f5f0ff 0%, #ede9fe 100%);
    border-bottom: 1px solid #e9e2fd;
}
.peserta-avatar {
    width: 46px; height: 46px;
    background: linear-gradient(135deg, #6b3df5, #9b59b6);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 19px; font-weight: 900; color: white;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(107,61,245,0.3);
    letter-spacing: 0;
}
.peserta-name-block { flex: 1; margin-left: 14px; }
.peserta-name-block h3 { margin: 0; font-size: 16px; font-weight: 800; color: #321180; }
.peserta-name-block small { font-size: 11px; color: #8b7ec8; font-weight: 600; }
.peserta-actions { display: flex; gap: 8px; align-items: center; }
.peserta-card-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    padding: 0;
}
.peserta-info-col {
    padding: 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    border-right: 1px solid #f0ebff;
}
.peserta-info-col:last-child { border-right: none; }
.peserta-info-item { display: flex; align-items: flex-start; gap: 9px; }
.peserta-info-icon {
    width: 28px; height: 28px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; flex-shrink: 0; margin-top: 1px;
}
.peserta-info-text { display: flex; flex-direction: column; }
.peserta-info-label { font-size: 10px; color: #a0aec0; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }
.peserta-info-value { font-size: 13px; color: #2d3748; font-weight: 600; margin-top: 1px; line-height: 1.3; }
.peserta-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 20px;
    background: #faf8ff;
    border-top: 1px solid #ede9fe;
    gap: 12px;
}
.trip-badge {
    display: flex; align-items: center; gap: 8px;
}
.trip-count-pill {
    background: linear-gradient(135deg, #6b3df5, #8b5cf6);
    color: white; font-size: 11px; font-weight: 800;
    padding: 3px 10px; border-radius: 20px;
    letter-spacing: 0.3px;
}
.trip-last-info { font-size: 12px; color: #718096; }
.trip-last-info span { font-weight: 700; color: #4a5568; }

/* ==============================
   TOOLBAR SEARCH, SORT, FILTER
   ============================== */
.search-sort-bar {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 20px;
    background: #fff;
    padding: 14px 18px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #eee;
}

.search-sort-bar input[type="text"] {
    flex: 1;
    min-width: 180px;
    padding: 9px 14px 9px 36px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    color: #2d3748;
    background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23a0aec0' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") no-repeat 10px center;
    transition: border 0.2s;
}
.search-sort-bar input[type="text"]:focus {
    border-color: #6b3df5;
    background-color: #fff;
}

.search-sort-bar select {
    padding: 9px 32px 9px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    background: #f8fafc;
    color: #2d3748;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    transition: border 0.2s;
    min-width: 160px;
}
.search-sort-bar select:focus {
    border-color: #6b3df5;
    background-color: #fff;
}

.toolbar-label {
    font-size: 11px;
    font-weight: 700;
    color: #a0aec0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.result-count {
    font-size: 12px;
    color: #718096;
    font-weight: 600;
    margin-bottom: 8px;
    padding: 0 2px;
}

.no-result-msg {
    text-align: center;
    padding: 40px 20px;
    color: #999;
    display: none;
    background: white;
    border-radius: 12px;
    border: 1px dashed #e2e8f0;
}

/* PAYMENT TOOLBAR */
.pay-toolbar {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 18px;
    background: #fff;
    padding: 14px 18px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #eee;
}
.pay-toolbar input[type="text"] {
    flex: 1;
    min-width: 180px;
    padding: 9px 14px 9px 36px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    color: #2d3748;
    background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23a0aec0' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") no-repeat 10px center;
    transition: border 0.2s;
}
.pay-toolbar input[type="text"]:focus {
    border-color: #6b3df5;
    background-color: #fff;
}
.pay-toolbar select {
    padding: 9px 32px 9px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    background: #f8fafc;
    color: #2d3748;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    transition: border 0.2s;
    min-width: 165px;
}
.pay-toolbar select:focus {
    border-color: #6b3df5;
    background-color: #fff;
}
.pay-result-count {
    font-size: 12px;
    color: #718096;
    font-weight: 600;
    margin-bottom: 8px;
    padding: 0 2px;
}
.pay-no-result {
    display: none;
    text-align: center;
    padding: 40px 20px;
    color: #999;
    background: white;
    border-radius: 12px;
    border: 1px dashed #e2e8f0;
}

/* NOTIFIKASI */
.tab-notif { position: relative; }
.badge-notif { background: #ff4b2b; color: white; font-size: 11px; padding: 2px 7px; border-radius: 50%; position: absolute; top: -8px; right: -18px; font-weight: bold; box-shadow: 0 2px 5px rgba(255, 75, 43, 0.4); }
.notif-card { background: white; padding: 20px; margin-top: 15px; border-radius: 16px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-left: 6px solid #6b3df5; transition: 0.3s; }
.notif-card.unreads { background: rgba(107, 61, 245, 0.03); border-left: 6px solid #321180; }
.notif-left { flex: 1; padding-right: 20px; }
.notif-msg { font-size: 14px; color: #2d3748; line-height: 1.5; margin-bottom: 6px; }
.notif-time { font-size: 12px; color: #a0aec0; font-weight: 600; }
.notif-right { display: flex; align-items: center; }
.btn-baca { background: #f0f4f8; color: #6b3df5; border: 1px solid #d1d9e6; font-weight: bold; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 12px; transition: 0.2s; }
.btn-baca:hover { background: #6b3df5; color: white; }

/* NOTIF FILTER SWITCH */
.notif-filter-bar {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 20px;
    padding: 14px 18px;
    background: #f4faf6;
    border-radius: 12px;
    border: 1px solid #d4edda;
}
.notif-filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}
.notif-filter-label {
    font-size: 12px;
    font-weight: 700;
    color: #4a5568;
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.switch-group {
    display: flex;
    background: #e8f5e9;
    border-radius: 30px;
    padding: 3px;
    gap: 2px;
    border: 1px solid #c8e6c9;
}
.switch-btn {
    border: none;
    background: transparent;
    border-radius: 25px;
    padding: 6px 16px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    color: #52796f;
    transition: all 0.22s ease;
    white-space: nowrap;
}
.switch-btn.active-sw {
    background: #27ae60;
    color: #fff;
    box-shadow: 0 2px 8px rgba(39,174,96,0.25);
}
.notif-count {
    font-size: 12px;
    color: #718096;
    font-weight: 600;
    margin-bottom: 8px;
}
.notif-no-result {
    display: none;
    text-align: center;
    padding: 40px 20px;
    color: #999;
    background: #f9fffe;
    border-radius: 12px;
    border: 1px dashed #c8e6c9;
    font-size: 14px;
}
</style>
</head>

<body>

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
            <span style="color:blue; margin-right:10px;">
              👤 <?php echo $_SESSION['username']; ?>
            </span>
        </a>
        <?php else: ?>
            <a href="login_user.php">
              <button class="active5">Masuk</button>
            </a>
        <?php endif; ?>
      </nav>
</header>

<div class="hero"></div>

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

<div class="menu-tabs">
  <div class="tab active-tab" onclick="tab('peserta',this)">Peserta</div>
  <div class="tab" onclick="tab('pesanan',this)">Pesanan</div>
  <div class="tab" onclick="tab('pembayaran',this)">Pembayaran</div>
  <div class="tab tab-notif" onclick="tab('notifikasi',this)">
    Notifikasi
    <?php if($jumlah_notif > 0): ?>
      <span class="badge-notif"><?= $jumlah_notif; ?></span>
    <?php endif; ?>
  </div>
</div>

<!-- ======================== TAB PESERTA ======================== -->
<div id="peserta" class="tab-content active data-section">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; margin-top:4px;">
        <div>
            <h2 style="color:#321180; margin:0; font-size:22px; font-weight:800;">Daftar Peserta</h2>
            <p style="color:#718096; font-size:13px; margin-top:4px; margin-bottom:0;">Kelola data peserta untuk trip Anda.</p>
        </div>
        <a href="form.php" class="btn purple" style="margin:0; font-size:13px; font-weight:700; border-radius:10px; padding:10px 20px;">+ Tambah Peserta</a>
    </div>

    <!-- TOOLBAR PESERTA -->
    <div class="peserta-toolbar">
        <input type="text" id="search-peserta" placeholder="Cari nama peserta..." oninput="applyPesertaFilter()">
        <span class="toolbar-label">Urutkan:</span>
        <select id="sort-peserta" onchange="applyPesertaFilter()">
            <option value="nama-asc">Nama A–Z</option>
            <option value="nama-desc">Nama Z–A</option>
            <option value="trip-desc">Trip Terbanyak</option>
            <option value="trip-asc">Trip Tersedikit</option>
        </select>
    </div>
    <div id="peserta-count" class="peserta-count"></div>

    <!-- KARTU PESERTA -->
    <div id="peserta-container">
    <?php while($p = ambil($peserta)):
        $jumlah_trip   = (int)($p['jumlah_trip'] ?? 0);
        $trip_terakhir = $p['trip_terakhir'] ?? null;
        $tgl_terakhir  = $p['tgl_trip_terakhir'] ?? null;
        $initial = mb_strtoupper(mb_substr($p['nama'], 0, 1, 'UTF-8'), 'UTF-8');
        $riwayat = trim($p['riwayat']);
    ?>
    <div class="peserta-card"
         data-nama="<?= strtolower($p['nama']); ?>"
         data-trip="<?= $jumlah_trip; ?>">

        <!-- HEADER -->
        <div class="peserta-card-header">
            <div class="peserta-avatar"><?= $initial; ?></div>
            <div class="peserta-name-block">
                <h3><?= htmlspecialchars($p['nama']); ?></h3>
                <small>ID Peserta #<?= $p['id_peserta']; ?></small>
            </div>
            <div class="peserta-actions">
                <a href="ubah_peserta.php?id=<?= $p['id_peserta']; ?>"
                   style="background:#6b3df5; color:white; padding:7px 16px; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none; transition:0.2s;"
                   onmouseover="this.style.background='#5027d6'" onmouseout="this.style.background='#6b3df5'">Ubah</a>
                <a href="hapus_peserta.php?id=<?= $p['id_peserta']; ?>"
                   onclick="return confirm('Yakin hapus peserta ini?')"
                   style="background:#fff0f0; color:#c53030; border:1px solid #fed7d7; padding:7px 14px; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none; transition:0.2s;"
                   onmouseover="this.style.background='#fed7d7'" onmouseout="this.style.background='#fff0f0'">Hapus</a>
            </div>
        </div>

        <!-- BODY: 2 kolom -->
        <div class="peserta-card-body">
            <div class="peserta-info-col">
                <div class="peserta-info-item">
                    <div class="peserta-info-icon" style="background:#ebf8ff;">📞</div>
                    <div class="peserta-info-text">
                        <span class="peserta-info-label">No. HP</span>
                        <span class="peserta-info-value"><?= htmlspecialchars($p['no_hp']); ?></span>
                    </div>
                </div>
                <div class="peserta-info-item">
                    <div class="peserta-info-icon" style="background:#fef9e7;">🎂</div>
                    <div class="peserta-info-text">
                        <span class="peserta-info-label">Usia</span>
                        <span class="peserta-info-value"><?= $p['usia']; ?> tahun</span>
                    </div>
                </div>
            </div>
            <div class="peserta-info-col">
                <div class="peserta-info-item">
                    <div class="peserta-info-icon" style="background:#f0fff4;">📍</div>
                    <div class="peserta-info-text">
                        <span class="peserta-info-label">Alamat</span>
                        <span class="peserta-info-value"><?= htmlspecialchars($p['alamat']); ?></span>
                    </div>
                </div>
                <div class="peserta-info-item">
                    <div class="peserta-info-icon" style="background:#fff5f5;">🏥</div>
                    <div class="peserta-info-text">
                        <span class="peserta-info-label">Riwayat Penyakit</span>
                        <span class="peserta-info-value"><?= $riwayat ? htmlspecialchars($riwayat) : '<span style="color:#a0aec0;font-style:italic;">Tidak ada</span>'; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER: statistik trip -->
        <div class="peserta-card-footer">
            <div class="trip-badge">
                <span class="trip-count-pill">Telah Mengikuti <?= $jumlah_trip; ?> Trip</span>
                <span class="trip-last-info">
                    <?php if($trip_terakhir): ?>
                        Trip terakhir: <span><?= htmlspecialchars($trip_terakhir); ?></span>
                        <span style="color:#cbd5e0; margin:0 4px;">·</span>
                        <span style="color:#a0aec0;"><?= date('d M Y', strtotime($tgl_terakhir)); ?></span>
                    <?php else: ?>
                        <span style="color:#a0aec0; font-style:italic;">Belum pernah ikut trip</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>

    </div>
    <?php endwhile; ?>
    </div>

    <div id="peserta-no-result" class="peserta-no-result">
        Tidak ada peserta yang sesuai dengan pencarian Anda.
    </div>
</div>

<!-- ======================== TAB PESANAN ======================== -->
<div id="pesanan" class="tab-content data-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
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

    <!-- Filter waktu -->
    <div class="time-switcher" style="display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap;">
        <button onclick="filterTime('all')" class="btn-time active-time" style="border: 1px solid #6b3df5; background: #6b3df5; color: white; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 12px; cursor: pointer; transition: all 0.2s;">Semua</button>
        <button onclick="filterTime('past')" class="btn-time" style="border: 1px solid #cbd5e0; background: white; color: #4a5568; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 12px; cursor: pointer; transition: all 0.2s;">Telah Berlalu</button>
        <button onclick="filterTime('ongoing')" class="btn-time" style="border: 1px solid #cbd5e0; background: white; color: #4a5568; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 12px; cursor: pointer; transition: all 0.2s;">Sedang Berlangsung</button>
        <button onclick="filterTime('upcoming')" class="btn-time" style="border: 1px solid #cbd5e0; background: white; color: #4a5568; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 12px; cursor: pointer; transition: all 0.2s;">Akan Datang</button>
    </div>

    <!-- ======== TOOLBAR OPEN TRIP ======== -->
    <div id="toolbar-open" class="search-sort-bar">
        <span class="toolbar-label">Cari</span>
        <input type="text" id="search-open" placeholder="Cari destinasi open trip..." oninput="applyAllFilters()">
        <span class="toolbar-label">Urutkan:</span>
        <select id="sort-open" onchange="applyAllFilters()">
            <option value="">-- Urutkan --</option>
            <option value="harga-asc">Harga Termurah</option>
            <option value="harga-desc">Harga Termahal</option>
            <option value="peserta-asc">Peserta Paling Sedikit</option>
            <option value="peserta-desc">Peserta Terbanyak</option>
        </select>
        <span class="toolbar-label">Status:</span>
        <select id="filter-status-open" onchange="applyAllFilters()">
            <option value="">Semua Status</option>
            <option value="belum bayar">Belum Bayar</option>
            <option value="bayar non-dp">Bayar non-DP</option>
            <option value="dp">DP</option>
            <option value="lunas">Lunas</option>
            <option value="dibatalkan">Dibatalkan</option>
            <option value="refund">Refund</option>
        </select>
    </div>
    <div id="count-open" class="result-count"></div>

    <!-- ======== TOOLBAR PRIVATE TRIP ======== -->
    <div id="toolbar-private" class="search-sort-bar" style="display:none;">
        <span class="toolbar-label">Cari</span>
        <input type="text" id="search-private" placeholder="Cari destinasi private trip..." oninput="applyAllFilters()">
        <span class="toolbar-label">Urutkan:</span>
        <select id="sort-private" onchange="applyAllFilters()">
            <option value="">-- Urutkan --</option>
            <option value="harga-asc">Harga Termurah</option>
            <option value="harga-desc">Harga Termahal</option>
            <option value="peserta-asc">Peserta Paling Sedikit</option>
            <option value="peserta-desc">Peserta Terbanyak</option>
        </select>
        <span class="toolbar-label">Status Bayar:</span>
        <select id="filter-status-private" onchange="applyAllFilters()">
            <option value="">Semua Status</option>
            <option value="belum bayar">Belum Bayar</option>
            <option value="bayar non-dp">Bayar non-DP</option>
            <option value="dp">DP</option>
            <option value="lunas">Lunas</option>
            <option value="dibatalkan">Dibatalkan</option>
            <option value="refund">Refund</option>
        </select>
    </div>
    <div id="count-private" class="result-count" style="display:none;"></div>

    <!-- ======== KONTEN OPEN TRIP ======== -->
    <div id="trip-content-open">
        <?php
        mysqli_data_seek($pesanan, 0);
        $today = date('Y-m-d');
        $session_id_akun = $_SESSION['id_akun'] ?? null;

        while($b = ambil($pesanan)):
            $id_booking = $b['id_booking'];
            $id_t = $b['id_trip'];
            $t_info = ambil(kueri("SELECT harga, harga_dp, tgl_pulang FROM trip WHERE id_trip = '$id_t'"));
            $total_harga = $t_info['harga'] * $b['jumlah_peserta'];

            $cek_batal_q = kueri("SELECT status FROM batal_open WHERE id_booking = '$id_booking'");
            $cek_batal = mysqli_fetch_assoc($cek_batal_q);
            $status_batal = $cek_batal['status'] ?? null;

            $tgl_berangkat = $b['tgl_berangkat'];
            $tgl_pulang = $t_info['tgl_pulang'] ?? $b['tgl_berangkat'];

            if ($today > $tgl_pulang) {
                $time_category = 'past';
            } elseif ($today >= $tgl_berangkat && $today <= $tgl_pulang) {
                $time_category = 'ongoing';
            } else {
                $time_category = 'upcoming';
            }

            $user_rating = null;
            if ($session_id_akun) {
                $cek_rating_q = kueri("SELECT rating FROM rating WHERE id_trip = '$id_t' AND id_akun = '$session_id_akun' ORDER BY id_rating DESC LIMIT 1");
                $data_rating = mysqli_fetch_assoc($cek_rating_q);
                if ($data_rating) {
                    $user_rating = (int)$data_rating['rating'];
                }
            }

            // Data attributes untuk JS filtering/sorting
            $status_lower = strtolower($b['status']);
            $destinasi_lower = strtolower($b['tujuan']);
        ?>
        <div class="data-card open-card"
             data-time="<?= $time_category; ?>"
             data-destinasi="<?= htmlspecialchars($destinasi_lower); ?>"
             data-harga="<?= $total_harga; ?>"
             data-peserta="<?= (int)$b['jumlah_peserta']; ?>"
             data-status="<?= htmlspecialchars($status_lower); ?>"
             style="display: block; border-left: 6px solid #6b3df5; margin-bottom: 25px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; background: #fff;">

    <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 15px 20px; background: rgba(107, 61, 245, 0.02); border-bottom: 1px solid #f0f0f0;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap;">
                <span style="background:#6b3df5; color:white; font-size:10px; padding:3px 8px; border-radius:5px; font-weight: 900; letter-spacing: 0.5px;">OPEN TRIP</span>
                <h3 style="color: #321180; font-size: 19px; margin: 0; font-weight: 800;"><?= $b['tujuan']; ?></h3>
                <?php if ($time_category === 'past'): ?>
                    <span style="background: #e2e8f0; color: #4a5568; font-size: 10px; padding: 3px 8px; border-radius: 5px; font-weight: 700;">TELAH BERLALU</span>
                <?php elseif ($time_category === 'ongoing'): ?>
                    <span style="background: #feebc8; color: #c05621; font-size: 10px; padding: 3px 8px; border-radius: 5px; font-weight: 700;">SEDANG BERLANGSUNG</span>
                <?php else: ?>
                    <span style="background: #ebf8ff; color: #2b6cb0; font-size: 10px; padding: 3px 8px; border-radius: 5px; font-weight: 700;">AKAN DATANG</span>
                <?php endif; ?>
            </div>
            <p style="font-size: 12px; color: #888; margin: 0;">ID Booking: #BK-O<?= $id_booking; ?> | Pesan: <?= date('d M Y', strtotime($b['tgl_booking'])); ?></p>
        </div>
        <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
            <div>
                <div style="font-size: 10px; color: #aaa; margin-bottom: 4px; font-weight: bold; text-transform: uppercase;">Status Booking</div>
                <span style="padding: 6px 15px; border-radius: 30px; font-size: 11px; font-weight: 800; display: inline-block;
                    <?= $b['status'] == 'Lunas' ? 'background:#e6fffa; color:#2c7a7b; border: 1px solid #b2f5ea;' : ($b['status'] == 'Dibatalkan' ? 'background:#fff5f5; color:#c53030; border: 1px solid #feb2b2;' : 'background:#fffaf0; color:#b7791f; border: 1px solid #fbe3a1;'); ?>">
                    <?= strtoupper($b['status']); ?>
                </span>
            </div>
            <?php if ($time_category === 'past' && $b['status'] !== 'Dibatalkan'): ?>
                <?php if ($user_rating !== null): ?>
                    <div style="background: #fdf4ff; color: #701a75; font-size: 11px; padding: 6px 14px; border-radius: 8px; font-weight: 800; display: inline-flex; flex-direction: column; align-items: flex-end; border: 1px solid #f5d0fe; box-shadow: 0 2px 4px rgba(112, 26, 117, 0.03);">
                        <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 1px;">
                            <span style="color: #eab308; font-size: 14px; letter-spacing: 1px; line-height: 1;">
                                <?php for ($i = 1; $i <= 5; $i++) { echo $i <= $user_rating ? '★' : '☆'; } ?>
                            </span>
                            <span style="font-weight: 900; font-size: 12px; color: #701a75;"><?= $user_rating; ?>/5</span>
                        </div>
                        <span style="font-size: 8.5px; color: #a21caf; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Rating Anda</span>
                    </div>
                <?php else: ?>
                    <a href="form_rating_trip.php?id_booking=<?= $id_booking; ?>"
                       style="background: #fef3c7; color: #92400e; font-size: 11px; padding: 6px 14px; border-radius: 8px; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border: 1px solid #fde68a; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(217, 119, 6, 0.1);"
                       onmouseover="this.style.background='#fde68a'; this.style.borderColor='#f59e0b';"
                       onmouseout="this.style.background='#fef3c7'; this.style.borderColor='#fde68a';">
                        Berikan Penilaian
                    </a>
                <?php endif; ?>
            <?php endif; ?>
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
        <div id="no-result-open" class="no-result-msg">Tidak ada pesanan yang sesuai dengan pencarian / filter Anda.</div>
    </div>

    <!-- ======== KONTEN PRIVATE TRIP ======== -->
    <div id="trip-content-private" style="display:none; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <?php
    mysqli_data_seek($pesanan_private, 0);
    while($pr = ambil($pesanan_private)):
        $id_pr = $pr['id_private'];

        $cek_batal_pr_q = kueri("SELECT status FROM batal_private WHERE id_private = '$id_pr'");
        $cek_batal_pr = mysqli_fetch_assoc($cek_batal_pr_q);
        $status_batal_pr = $cek_batal_pr['status'] ?? null;

        $color_bayar = ($pr['status_bayar'] == 'Lunas') ? '#28a745' : (($pr['status_bayar'] == 'DP') ? '#f39c12' : '#6b3df5');

        $tgl_ber_pr = $pr['tgl_berangkat'];
        $tgl_pul_pr = $pr['tgl_pulang'];

        if ($today > $tgl_pul_pr) {
            $time_category_pr = 'past';
        } elseif ($today >= $tgl_ber_pr && $today <= $tgl_pul_pr) {
            $time_category_pr = 'ongoing';
        } else {
            $time_category_pr = 'upcoming';
        }

        $status_bayar_lower = strtolower($pr['status_bayar']);
        $destinasi_pr_lower = strtolower($pr['tujuan']);
        $harga_pr = (int)($pr['harga'] ?? 0);
    ?>
    <div class="data-card private-card"
         data-time="<?= $time_category_pr; ?>"
         data-destinasi="<?= htmlspecialchars($destinasi_pr_lower); ?>"
         data-harga="<?= $harga_pr; ?>"
         data-peserta="<?= (int)$pr['jumlah_peserta']; ?>"
         data-status="<?= htmlspecialchars($status_bayar_lower); ?>"
         style="display: block; border-left: 6px solid #ff4b2b; margin-bottom: 25px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; background: #fff;">

        <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 15px 20px; background: rgba(255, 75, 43, 0.02); border-bottom: 1px solid #f0f0f0;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap;">
                    <span style="background:#ff4b2b; color:white; font-size:10px; padding:3px 8px; border-radius:5px; font-weight: 900; letter-spacing: 0.5px;">PRIVATE</span>
                    <h3 style="color: #321180; font-size: 19px; margin: 0; font-weight: 800;"><?= $pr['tujuan']; ?></h3>
                    <?php if ($time_category_pr === 'past'): ?>
                        <span style="background: #e2e8f0; color: #4a5568; font-size: 10px; padding: 3px 8px; border-radius: 5px; font-weight: 700;">TELAH BERLALU</span>
                    <?php elseif ($time_category_pr === 'ongoing'): ?>
                        <span style="background: #feebc8; color: #c05621; font-size: 10px; padding: 3px 8px; border-radius: 5px; font-weight: 700;">SEDANG BERLANGSUNG</span>
                    <?php else: ?>
                        <span style="background: #ebf8ff; color: #2b6cb0; font-size: 10px; padding: 3px 8px; border-radius: 5px; font-weight: 700;">AKAN DATANG</span>
                    <?php endif; ?>
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
                <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">HARGA TOTAL</p>
                <p style="font-size: 16px; font-weight: 700; margin:0; color: #321180;"><?= $pr['harga'] ? 'Rp '.number_format($pr['harga']) : '<span style="color:#aaa; font-style:italic;">Menunggu ...</span>'; ?></p>
            </div>
            <div>
                <p style="font-size: 11px; color: #a0aec0; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px;">DP MINIMAL</p>
                <p style="font-size: 16px; font-weight: 700; margin:0; color: #ff4b2b;"><?= $pr['harga_dp'] ? 'Rp '.number_format($pr['harga_dp']) : '–'; ?></p>
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
                <?php endif; ?>
            </div>
        </div>
    </div>
        <?php endwhile; ?>
        <div id="no-result-private" class="no-result-msg">Tidak ada pesanan yang sesuai dengan pencarian / filter Anda.</div>
    </div>
</div>

<!-- ======================== TAB PEMBAYARAN ======================== -->
<div id="pembayaran" class="tab-content data-section">
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

    <!-- ======== TOOLBAR PEMBAYARAN OPEN TRIP ======== -->
    <div id="pay-toolbar-open" class="pay-toolbar">
        <span class="toolbar-label">Cari</span>
        <input type="text" id="pay-search-open" placeholder="Cari destinasi open trip..." oninput="applyPayFilter()">
        <span class="toolbar-label">Waktu:</span>
        <select id="pay-waktu-open" onchange="applyPayFilter()">
            <option value="">-- Semua Waktu --</option>
            <option value="terbaru">Terbaru</option>
            <option value="terlama">Terlama</option>
        </select>
        <span class="toolbar-label">Verifikasi:</span>
        <select id="pay-status-open" onchange="applyPayFilter()">
            <option value="">Semua Status</option>
            <option value="diverifikasi">Diverifikasi</option>
            <option value="belum diverifikasi">Belum Diverifikasi</option>
            <option value="ditolak">Ditolak</option>
        </select>
        <span class="toolbar-label">Urutkan:</span>
        <select id="pay-sort-open" onchange="applyPayFilter()">
            <option value="">-- Urutkan --</option>
            <option value="nominal-desc">Nominal Terbesar</option>
            <option value="nominal-asc">Nominal Terkecil</option>
            <option value="peserta-desc">Peserta Terbanyak</option>
            <option value="peserta-asc">Peserta Paling Sedikit</option>
        </select>
    </div>
    <div id="pay-count-open" class="pay-result-count"></div>

    <div id="pay-content-open">
        <?php
        mysqli_data_seek($pembayaran, 0);
        if(mysqli_num_rows($pembayaran) > 0):
            while($py = ambil($pembayaran)):
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
<div class="data-card pay-card-horizontal pay-open-card"
     data-tujuan="<?= strtolower($py['tujuan']); ?>"
     data-tgl-bayar="<?= strtotime($py['tgl_bayar']); ?>"
     data-status-verif="<?= strtolower($py['status']); ?>"
     data-nominal="<?= (int)$py['nominal']; ?>"
     data-peserta="<?= (int)$py['jumlah_peserta']; ?>"
     style="border-left: 6px solid #6b3df5; border-radius: 16px; display: block; margin-bottom: 25px; transition: transform 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
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
            <a href="detail_bayar_open.php?id_payment=<?= $py['id_payment']; ?>" class="btn" style="margin:0; font-size: 11px; font-weight: bold; padding: 8px; background: #2d3748; color: white; text-align:center; border-radius: 8px; text-decoration:none; transition: 0.2s;">📄 Detail</a>
            <a href="../gambar/payment/<?= $py['bukti_bayar']; ?>" target="_blank" class="btn purple" style="margin:0; font-size: 11px; font-weight: bold; padding: 8px; text-align:center; border-radius: 8px; text-decoration:none; background: #6b3df5;">🔍 Bukti</a>
        </div>
    </div>
    <div style="padding: 12px 20px; background: #fdfdfd; border-top: 1px solid #f0f0f0; display: flex; align-items: flex-start; gap: 10px;">
        <div style="background: #edf2f7; padding: 5px 8px; border-radius: 6px; font-size: 10px; font-weight: bold; color: #4a5568;">CATATAN</div>
        <p style="font-size: 12px; color: #4a5568; margin: 0; line-height: 1.5; font-style: italic;">
            "<?= !empty($py['catatan']) ? $py['catatan'] : 'Tidak ada catatan untuk pembayaran ini.'; ?>"
        </p>
    </div>
</div>
        <?php endwhile;
        else: echo "<div style='text-align:center; padding:40px; color:#999;'><p>Belum ada riwayat pembayaran Open Trip.</p></div>";
        endif; ?>
        <div id="pay-no-result-open" class="pay-no-result">Tidak ada pembayaran yang sesuai dengan pencarian / filter Anda.</div>
    </div>

    <!-- ======== TOOLBAR PEMBAYARAN PRIVATE TRIP ======== -->
    <div id="pay-toolbar-private" class="pay-toolbar" style="display:none;">
        <span class="toolbar-label">Cari</span>
        <input type="text" id="pay-search-private" placeholder="Cari destinasi private trip..." oninput="applyPayFilter()">
        <span class="toolbar-label">Waktu:</span>
        <select id="pay-waktu-private" onchange="applyPayFilter()">
            <option value="">-- Semua Waktu --</option>
            <option value="terbaru">Terbaru</option>
            <option value="terlama">Terlama</option>
        </select>
        <span class="toolbar-label">Verifikasi:</span>
        <select id="pay-status-private" onchange="applyPayFilter()">
            <option value="">Semua Status</option>
            <option value="diverifikasi">Diverifikasi</option>
            <option value="belum diverifikasi">Belum Diverifikasi</option>
            <option value="ditolak">Ditolak</option>
        </select>
        <span class="toolbar-label">Urutkan:</span>
        <select id="pay-sort-private" onchange="applyPayFilter()">
            <option value="">-- Urutkan --</option>
            <option value="nominal-desc">Nominal Terbesar</option>
            <option value="nominal-asc">Nominal Terkecil</option>
            <option value="peserta-desc">Peserta Terbanyak</option>
            <option value="peserta-asc">Peserta Paling Sedikit</option>
        </select>
    </div>
    <div id="pay-count-private" class="pay-result-count" style="display:none;"></div>

    <div id="pay-content-private" style="display: none;">
        <?php
        if(mysqli_num_rows($pembayaran_private) > 0):
            while($pyp = ambil($pembayaran_private)):
                $id_pr = $pyp['id_private'];
                $data_pr = ambil(kueri("SELECT harga, harga_dp, jumlah_peserta, status_bayar, status_trip FROM private_trip WHERE id_private = '$id_pr'"));
                $total_tagihan_pr = $data_pr['harga'] ?? 0;
                $status_bayar_pr = $data_pr['status_bayar'];
        ?>
<div class="data-card pay-card-horizontal pay-private-card"
     data-tujuan="<?= strtolower($pyp['tujuan']); ?>"
     data-tgl-bayar="<?= strtotime($pyp['tgl_bayar']); ?>"
     data-status-verif="<?= strtolower($pyp['status']); ?>"
     data-nominal="<?= (int)$pyp['nominal']; ?>"
     data-peserta="<?= (int)($data_pr['jumlah_peserta'] ?? 0); ?>"
     style="border-left: 6px solid #ff4b2b; border-radius: 16px; display: block; margin-bottom: 25px; transition: transform 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
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
            <a href="detail_bayar_private.php?id_payment=<?= $pyp['id_payment']; ?>" class="btn" style="margin:0; font-size: 11px; font-weight: bold; padding: 8px; background: #2d3748; color: white; text-align:center; border-radius: 8px; text-decoration:none;">📄 Detail</a>
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
        <div id="pay-no-result-private" class="pay-no-result">Tidak ada pembayaran yang sesuai dengan pencarian / filter Anda.</div>
    </div>
</div>

<!-- ======================== TAB NOTIFIKASI ======================== -->
<div id="notifikasi" class="tab-content data-section" style="background: #ffffff; padding: 30px; border-radius: 14px; box-shadow: 0 4px 20px rgba(39, 174, 96, 0.05); border: 1px solid rgba(39, 174, 96, 0.08);">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom: 20px; border-bottom: 2px solid #eafaf1; padding-bottom: 16px;">
        <div>
            <h2 style="color: #27ae60; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">Notifikasi Anda</h2>
            <p style="color: #718096; font-size: 13px; margin-top: 5px; margin-bottom: 0;">Informasi terbaru mengenai pembaruan status dan jadwal trip Anda.</p>
        </div>
    </div>

    <?php if(mysqli_num_rows($notifikasi) > 0): ?>

    <!-- FILTER BAR -->
    <div class="notif-filter-bar">
        <div class="notif-filter-group">
            <span class="notif-filter-label">Urutkan:</span>
            <div class="switch-group">
                <button class="switch-btn active-sw" onclick="setNotifFilter('waktu','semua',this)">Semua</button>
                <button class="switch-btn" onclick="setNotifFilter('waktu','terbaru',this)">Terbaru</button>
                <button class="switch-btn" onclick="setNotifFilter('waktu','terlama',this)">Terlama</button>
            </div>
        </div>
        <div class="notif-filter-group">
            <span class="notif-filter-label">Status:</span>
            <div class="switch-group">
                <button class="switch-btn active-sw" onclick="setNotifFilter('baca','semua',this)">Semua</button>
                <button class="switch-btn" onclick="setNotifFilter('baca','belum',this)">Belum Dibaca</button>
                <button class="switch-btn" onclick="setNotifFilter('baca','sudah',this)">Sudah Dibaca</button>
            </div>
        </div>
    </div>
    <div id="notif-count" class="notif-count"></div>

    <!-- KARTU NOTIFIKASI -->
    <div id="notif-container">
        <?php while($nt = ambil($notifikasi)): ?>
            <?php
                $is_unread = ($nt['dibaca'] == 0);
                $card_bg = $is_unread ? '#eafaf1' : '#fdfbff';
                $card_border = $is_unread ? '1px solid #27ae60' : '1px solid #eae6fa';
                $border_left = $is_unread ? '4px solid #2cc771' : '1px solid #eae6fa';
            ?>
            <div class="notif-item"
                 data-waktu="<?= strtotime($nt['waktu']); ?>"
                 data-dibaca="<?= $is_unread ? '0' : '1'; ?>"
                 style="display:flex; justify-content:space-between; align-items:center; background:<?= $card_bg; ?>; border:<?= $card_border; ?>; border-left:<?= $border_left; ?>; padding:18px 20px; border-radius:10px; margin-bottom:12px; gap:15px;">
                <div style="display:flex; flex-direction:column; gap:6px; flex:1;">
                    <p style="margin:0; font-size:15px; color:#2c3e50; line-height:1.5; font-weight:500; display:flex; align-items:flex-start;">
                        <?php if($is_unread): ?>
                            <span style="color:#2ecc71; margin-right:8px; font-size:12px; align-self:center;">●</span>
                        <?php endif; ?>
                        <?= $nt['pesan']; ?>
                    </p>
                    <p style="margin:0; font-size:12px; color:#7f8c8d; display:flex; align-items:center; gap:5px;">
                        <i class="fa-regular fa-calendar" style="color:#27ae60;"></i> <?= date('d M Y', strtotime($nt['waktu'])); ?>
                        <span style="color:#cbd5e1; margin:0 4px;">|</span>
                        <i class="fa-regular fa-clock" style="color:#27ae60;"></i> <?= date('H:i', strtotime($nt['waktu'])); ?> WIB
                    </p>
                </div>
                <div>
                    <a href="detail_notip.php?id=<?= $nt['id_notif']; ?>" style="display:inline-flex; align-items:center; background:#27ae60; color:white; text-decoration:none; padding:8px 16px; border-radius:6px; font-size:13px; font-weight:600; white-space:nowrap; transition:background 0.2s; box-shadow:0 2px 8px rgba(39,174,96,0.2);" onmouseover="this.style.background='#219653'" onmouseout="this.style.background='#27ae60'">Selengkapnya</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <div id="notif-no-result" class="notif-no-result">Tidak ada notifikasi yang sesuai dengan filter Anda.</div>

    <?php else: ?>
        <div style="text-align: center; padding: 50px 20px; color: #7f8c8d;">
            <i class="fa-regular fa-bell-slash" style="font-size: 40px; color: #eafaf1; margin-bottom: 15px; display: block;"></i>
            <p style="margin: 0; font-size: 14px;">Belum ada pemberitahuan atau aktivitas terbaru.</p>
        </div>
    <?php endif; ?>
</div>

<footer>
  <div class="footer-content">
    <div class="footer-column logo-col">
      <img src="../gambar/logo-rebon.png" alt="Rebon Adventure Logo" class="footer-logo-img"/>
    </div>
    <div class="footer-column">
      <h4>KONTAK KAMI</h4>
      <div class="contact-item"><span class="icon">✉</span><p>rebonadventure@gmail.com</p></div>
      <div class="contact-item"><span class="icon">📞</span><p>+62 812-3456-7890</p></div>
      <div class="contact-item"><span class="icon">📍</span><p>Jl. sukawera No. 15,<br/>Cirebon, Indonesia</p></div>
    </div>
    <div class="footer-column">
      <h4>LAYANAN KAMI</h4>
      <ul><li>OPEN TRIP</li><li>PRIVATE TRIP</li></ul>
    </div>
    <div class="footer-column">
      <h4>INFORMASI</h4>
      <ul><li>TENTANG KAMI</li><li>TRIP TERSEDIA</li><li>FAQ</li></ul>
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

<script>
// ===========================
// TAB & SWITCH FUNCTIONS
// ===========================
function tab(id, el) {
    document.querySelectorAll('.tab-content').forEach(x => x.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(x => x.classList.remove('active-tab'));
    document.getElementById(id).classList.add('active');
    el.classList.add('active-tab');
    if (id === 'pesanan') {
        switchTrip('open');
    }
}

function switchTrip(type) {
    const openCont  = document.getElementById('trip-content-open');
    const privCont  = document.getElementById('trip-content-private');
    const tbOpen    = document.getElementById('toolbar-open');
    const tbPriv    = document.getElementById('toolbar-private');
    const cntOpen   = document.getElementById('count-open');
    const cntPriv   = document.getElementById('count-private');
    const btnOpen   = document.getElementById('btn-open');
    const btnPriv   = document.getElementById('btn-private');

    if (type === 'open') {
        openCont.style.display  = 'block';
        privCont.style.display  = 'none';
        tbOpen.style.display    = 'flex';
        tbPriv.style.display    = 'none';
        cntOpen.style.display   = 'block';
        cntPriv.style.display   = 'none';

        btnOpen.style.background  = '#6b3df5';
        btnOpen.style.color       = '#fff';
        btnOpen.style.boxShadow   = '0 4px 6px rgba(107, 61, 245, 0.2)';
        btnPriv.style.background  = 'transparent';
        btnPriv.style.color       = '#718096';
        btnPriv.style.boxShadow   = 'none';
    } else {
        openCont.style.display  = 'none';
        privCont.style.display  = 'block';
        tbOpen.style.display    = 'none';
        tbPriv.style.display    = 'flex';
        cntOpen.style.display   = 'none';
        cntPriv.style.display   = 'block';

        btnPriv.style.background  = '#ff4b2b';
        btnPriv.style.color       = '#fff';
        btnPriv.style.boxShadow   = '0 4px 6px rgba(255, 75, 43, 0.2)';
        btnOpen.style.background  = 'transparent';
        btnOpen.style.color       = '#718096';
        btnOpen.style.boxShadow   = 'none';
    }
    applyAllFilters();
}

function switchPayment(type) {
    const openCont = document.getElementById('pay-content-open');
    const privCont = document.getElementById('pay-content-private');
    const btnOpen  = document.getElementById('btn-pay-open');
    const btnPriv  = document.getElementById('btn-pay-private');
    const tbOpen   = document.getElementById('pay-toolbar-open');
    const tbPriv   = document.getElementById('pay-toolbar-private');
    const cntOpen  = document.getElementById('pay-count-open');
    const cntPriv  = document.getElementById('pay-count-private');

    if (type === 'open') {
        openCont.style.display = 'block';
        privCont.style.display = 'none';
        tbOpen.style.display   = 'flex';
        tbPriv.style.display   = 'none';
        cntOpen.style.display  = 'block';
        cntPriv.style.display  = 'none';
        btnOpen.style.background  = '#6b3df5';
        btnOpen.style.color       = '#fff';
        btnOpen.style.boxShadow   = '0 4px 6px rgba(107, 61, 245, 0.2)';
        btnPriv.style.background  = 'transparent';
        btnPriv.style.color       = '#718096';
        btnPriv.style.boxShadow   = 'none';
    } else {
        openCont.style.display = 'none';
        privCont.style.display = 'block';
        tbOpen.style.display   = 'none';
        tbPriv.style.display   = 'flex';
        cntOpen.style.display  = 'none';
        cntPriv.style.display  = 'block';
        btnPriv.style.background  = '#ff4b2b';
        btnPriv.style.color       = '#fff';
        btnPriv.style.boxShadow   = '0 4px 6px rgba(255, 75, 43, 0.2)';
        btnOpen.style.background  = 'transparent';
        btnOpen.style.color       = '#718096';
        btnOpen.style.boxShadow   = 'none';
    }
    applyPayFilter();
}

// ===========================
// FILTER WAKTU
// ===========================
let currentTimeFilter = 'all';

function filterTime(type) {
    currentTimeFilter = type;
    const buttons = document.querySelectorAll('.btn-time');
    buttons.forEach(btn => {
        btn.style.background   = 'white';
        btn.style.color        = '#4a5568';
        btn.style.borderColor  = '#cbd5e0';
    });
    event.currentTarget.style.background   = '#6b3df5';
    event.currentTarget.style.color        = 'white';
    event.currentTarget.style.borderColor  = '#6b3df5';
    applyAllFilters();
}

// ===========================
// SEARCH + SORT + FILTER STATUS
// Berlaku untuk tab yang sedang aktif (open / private)
// ===========================
function applyAllFilters() {
    const isOpenActive = document.getElementById('trip-content-open').style.display !== 'none';

    if (isOpenActive) {
        const keyword    = document.getElementById('search-open').value.toLowerCase().trim();
        const sortVal    = document.getElementById('sort-open').value;
        const statusVal  = document.getElementById('filter-status-open').value.toLowerCase();
        const noResult   = document.getElementById('no-result-open');
        const container  = document.getElementById('trip-content-open');
        const countEl    = document.getElementById('count-open');

        filterAndSort('.open-card', keyword, sortVal, statusVal, container, noResult, countEl);
    } else {
        const keyword    = document.getElementById('search-private').value.toLowerCase().trim();
        const sortVal    = document.getElementById('sort-private').value;
        const statusVal  = document.getElementById('filter-status-private').value.toLowerCase();
        const noResult   = document.getElementById('no-result-private');
        const container  = document.getElementById('trip-content-private');
        const countEl    = document.getElementById('count-private');

        filterAndSort('.private-card', keyword, sortVal, statusVal, container, noResult, countEl);
    }
}

function filterAndSort(selector, keyword, sortVal, statusVal, container, noResultEl, countEl) {
    // Ambil hanya kartu di dalam container aktif
    const cards = Array.from(container.querySelectorAll(selector));

    // Filter
    const visibleSet = new Set(cards.filter(card => {
        const timeOk   = (currentTimeFilter === 'all' || card.dataset.time === currentTimeFilter);
        const searchOk = (!keyword || card.dataset.destinasi.includes(keyword));
        const statusOk = (!statusVal || card.dataset.status === statusVal);
        return timeOk && searchOk && statusOk;
    }));

    // Sort kartu yang lolos
    let sorted = Array.from(visibleSet);
    if (sortVal) {
        sorted.sort((a, b) => {
            const hargaA   = parseInt(a.dataset.harga)   || 0;
            const hargaB   = parseInt(b.dataset.harga)   || 0;
            const pesertaA = parseInt(a.dataset.peserta) || 0;
            const pesertaB = parseInt(b.dataset.peserta) || 0;

            if (sortVal === 'harga-asc')    return hargaA   - hargaB;
            if (sortVal === 'harga-desc')   return hargaB   - hargaA;
            if (sortVal === 'peserta-asc')  return pesertaA - pesertaB;
            if (sortVal === 'peserta-desc') return pesertaB - pesertaA;
            return 0;
        });
    }

    // Set display DULU sebelum re-append, agar kartu tersembunyi saat dipindahkan
    cards.forEach(card => {
        card.style.display = visibleSet.has(card) ? 'block' : 'none';
    });

    // Re-append: yang lolos dulu (urut), lalu yang tidak lolos di belakang
    sorted.forEach(card => container.appendChild(card));
    cards.forEach(card => {
        if (!visibleSet.has(card)) container.appendChild(card);
    });

    // Update counter & pesan kosong
    const total = cards.length;
    const shown = sorted.length;
    countEl.textContent = shown < total
        ? `Menampilkan ${shown} dari ${total} pesanan`
        : `Total ${total} pesanan`;

    noResultEl.style.display = shown === 0 ? 'block' : 'none';
}

// ===========================
// FILTER & SORT PEMBAYARAN
// ===========================
function applyPayFilter() {
    const isOpenPay = document.getElementById('pay-content-open').style.display !== 'none';

    if (isOpenPay) {
        const keyword   = document.getElementById('pay-search-open').value.toLowerCase().trim();
        const waktuVal  = document.getElementById('pay-waktu-open').value;
        const statusVal = document.getElementById('pay-status-open').value.toLowerCase();
        const sortVal   = document.getElementById('pay-sort-open').value;
        const container = document.getElementById('pay-content-open');
        const noResult  = document.getElementById('pay-no-result-open');
        const countEl   = document.getElementById('pay-count-open');

        filterAndSortPay('.pay-open-card', keyword, waktuVal, statusVal, sortVal, container, noResult, countEl);
    } else {
        const keyword   = document.getElementById('pay-search-private').value.toLowerCase().trim();
        const waktuVal  = document.getElementById('pay-waktu-private').value;
        const statusVal = document.getElementById('pay-status-private').value.toLowerCase();
        const sortVal   = document.getElementById('pay-sort-private').value;
        const container = document.getElementById('pay-content-private');
        const noResult  = document.getElementById('pay-no-result-private');
        const countEl   = document.getElementById('pay-count-private');

        filterAndSortPay('.pay-private-card', keyword, waktuVal, statusVal, sortVal, container, noResult, countEl);
    }
}

function filterAndSortPay(selector, keyword, waktuVal, statusVal, sortVal, container, noResultEl, countEl) {
    // Ambil HANYA kartu di dalam container yang aktif (bukan semua di halaman)
    const cards = Array.from(container.querySelectorAll(selector));

    // Filter
    const visibleSet = new Set(cards.filter(card => {
        const searchOk = (!keyword || card.dataset.tujuan.includes(keyword));
        const statusOk = (!statusVal || card.dataset.statusVerif === statusVal);
        return searchOk && statusOk;
    }));

    // Tentukan urutan sort untuk kartu yang lolos
    let sorted = Array.from(visibleSet);
    if (sortVal || waktuVal) {
        sorted.sort((a, b) => {
            const nominalA = parseInt(a.dataset.nominal) || 0;
            const nominalB = parseInt(b.dataset.nominal) || 0;
            const pesertaA = parseInt(a.dataset.peserta) || 0;
            const pesertaB = parseInt(b.dataset.peserta) || 0;
            const tglA     = parseInt(a.dataset.tglBayar) || 0;
            const tglB     = parseInt(b.dataset.tglBayar) || 0;

            if (waktuVal === 'terbaru') return tglB - tglA;
            if (waktuVal === 'terlama') return tglA - tglB;
            if (sortVal === 'nominal-desc') return nominalB - nominalA;
            if (sortVal === 'nominal-asc')  return nominalA - nominalB;
            if (sortVal === 'peserta-desc') return pesertaB - pesertaA;
            if (sortVal === 'peserta-asc')  return pesertaA - pesertaB;
            return 0;
        });
    }

    // Set display DULU sebelum re-append, agar kartu tersembunyi saat dipindahkan
    cards.forEach(card => {
        card.style.display = visibleSet.has(card) ? 'block' : 'none';
    });

    // Re-append SEMUA kartu: yang lolos dulu (urut), lalu yang tidak lolos di belakang
    sorted.forEach(card => container.appendChild(card));
    cards.forEach(card => {
        if (!visibleSet.has(card)) container.appendChild(card);
    });

    // Update counter & pesan kosong
    const total = cards.length;
    const shown = sorted.length;
    if (total > 0) {
        countEl.textContent = shown < total
            ? `Menampilkan ${shown} dari ${total} pembayaran`
            : `Total ${total} pembayaran`;
    }
    noResultEl.style.display = shown === 0 ? 'block' : 'none';
}

// ===========================
// FILTER NOTIFIKASI (SWITCH)
// ===========================
const notifFilter = { waktu: 'semua', baca: 'semua' };

function setNotifFilter(type, value, btn) {
    notifFilter[type] = value;

    // Update active class — hanya dalam switch-group yang sama
    const group = btn.closest('.switch-group');
    group.querySelectorAll('.switch-btn').forEach(b => b.classList.remove('active-sw'));
    btn.classList.add('active-sw');

    applyNotifFilter();
}

function applyNotifFilter() {
    const container = document.getElementById('notif-container');
    if (!container) return;

    const cards = Array.from(container.querySelectorAll('.notif-item'));

    // Sembunyikan semua dulu
    cards.forEach(c => c.style.display = 'none');

    // Filter
    const visibleSet = new Set(cards.filter(card => {
        const bacaOk = notifFilter.baca === 'semua'
            || (notifFilter.baca === 'belum' && card.dataset.dibaca === '0')
            || (notifFilter.baca === 'sudah' && card.dataset.dibaca === '1');
        return bacaOk;
    }));

    // Sort waktu
    let sorted = Array.from(visibleSet);
    if (notifFilter.waktu !== 'semua') {
        sorted.sort((a, b) => {
            const tA = parseInt(a.dataset.waktu) || 0;
            const tB = parseInt(b.dataset.waktu) || 0;
            return notifFilter.waktu === 'terbaru' ? tB - tA : tA - tB;
        });
    }

    // Re-append: lolos dulu (urut), tidak lolos di belakang
    sorted.forEach(c => container.appendChild(c));
    cards.forEach(c => { if (!visibleSet.has(c)) container.appendChild(c); });

    // Tampilkan yang lolos
    cards.forEach(c => {
        c.style.display = visibleSet.has(c) ? 'flex' : 'none';
    });

    // Counter & no-result
    const total = cards.length;
    const shown = sorted.length;
    const countEl   = document.getElementById('notif-count');
    const noResult  = document.getElementById('notif-no-result');
    if (countEl) {
        countEl.textContent = shown < total
            ? `Menampilkan ${shown} dari ${total} notifikasi`
            : `Total ${total} notifikasi`;
    }
    if (noResult) noResult.style.display = shown === 0 ? 'block' : 'none';
}

// ===========================
// FILTER & SORT PESERTA
// ===========================
function applyPesertaFilter() {
    const container = document.getElementById('peserta-container');
    if (!container) return;

    const keyword = document.getElementById('search-peserta').value.toLowerCase().trim();
    const sortVal = document.getElementById('sort-peserta').value;
    const cards   = Array.from(container.querySelectorAll('.peserta-card'));

    // Filter
    const visibleSet = new Set(cards.filter(card =>
        !keyword || card.dataset.nama.includes(keyword)
    ));

    // Sort
    let sorted = Array.from(visibleSet);
    sorted.sort((a, b) => {
        const namaA  = a.dataset.nama;
        const namaB  = b.dataset.nama;
        const tripA  = parseInt(a.dataset.trip) || 0;
        const tripB  = parseInt(b.dataset.trip) || 0;
        if (sortVal === 'nama-asc')   return namaA.localeCompare(namaB);
        if (sortVal === 'nama-desc')  return namaB.localeCompare(namaA);
        if (sortVal === 'trip-desc')  return tripB - tripA;
        if (sortVal === 'trip-asc')   return tripA - tripB;
        return 0;
    });

    // Set display dulu, lalu re-append
    cards.forEach(c => { c.style.display = visibleSet.has(c) ? 'block' : 'none'; });
    sorted.forEach(c => container.appendChild(c));
    cards.forEach(c => { if (!visibleSet.has(c)) container.appendChild(c); });

    // Counter & no-result
    const total  = cards.length;
    const shown  = sorted.length;
    const countEl   = document.getElementById('peserta-count');
    const noResult  = document.getElementById('peserta-no-result');
    if (countEl) countEl.textContent = shown < total
        ? `Menampilkan ${shown} dari ${total} peserta`
        : `Total ${total} peserta`;
    if (noResult) noResult.style.display = shown === 0 ? 'block' : 'none';
}

// Jalankan filter awal saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    applyAllFilters();
    applyPayFilter();
    applyNotifFilter();
    applyPesertaFilter();
});
</script>

</body>
</html>
