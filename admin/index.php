<?php 
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'konek.php';
require 'fungsi.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard Admin</title>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial;
}

body {
  background: #d9d9d9;
}

/* LAYOUT */
.container {
  display: flex;
  min-height: 100vh;
}

/* SIDEBAR */
.full-sidebar {
   position: fixed;
}

.sidebar {
  width: 250px;
  background: linear-gradient(180deg, #5a1ee6, #321180);
  color: white;
  padding: 20px;
}

.sidebar h2 {
  text-align: center;
  margin-bottom: 30px;
}

.menu {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.menu a {
  text-decoration: none;
  color: white;
  padding: 12px;
  border-radius: 8px;
  transition: 0.3s;
}

.menu a:hover {
  background: rgba(255,255,255,0.2);
}

/* LOGOUT */
.logout {
  margin-top: 30px;
  margin-bottom: 15px;
}

.logout button {
  width: 100%;
  padding: 10px;
  border: none;
  background: white;
  color: #5a1ee6;
  border-radius: 8px;
  cursor: pointer;
}

/* CONTENT */
.content {
  flex: 1;
  background: #f4f0ff;
  padding: 30px;
  margin-left: 250px;
}

/* HEADER */
.header {
  margin-bottom: 20px;
}

.header h1 {
  color: #321180;
}

/* CARD */
.card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* TABLE */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

thead {
  background: #6b3df5;
  color: white;
}

th, td {
  padding: 12px;
  font-size: 14px;
}

tbody tr {
  border-bottom: 1px solid #eee;
}

tbody tr:hover {
  background: #f9f7ff;
}

/* BUTTON */
.btn-tambah {
  display: inline-block;
  margin-top: 10px;
  padding: 10px 15px;
  background: #6b3df5;
  color: white;
  border-radius: 8px;
  text-decoration: none;
}

/* LINK */
a {
  color: #6b3df5;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}

/* IMAGE */
td img {
  width: 60px;
  border-radius: 6px;
}
</style>

</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<div class="full-sidebar">
  <div class="sidebar">
    <h2>Admin Panel</h2>

    <div class="menu">
      <a href="index.php?menu=trip">Open Trip</a>
      <a href="index.php?menu=destinasi">Destinasi</a>
      <a href="index.php?menu=private">Private Trip</a>
      <a href="index.php?menu=booking">Pesanan</a>
      <a href="index.php?menu=payment">Pembayaran</a>
      <a href="index.php?menu=peserta">Peserta</a>
      <a href="index.php?menu=pembatalan">Pembatalan</a>
      <a href="index.php?menu=laporan">Laporan</a>
      <a href="index.php?menu=galeri">Galeri</a>
    </div>

    <div class="logout">
      <a href="logout.php" onclick="return confirm('Yakin Ingin Keluar?')">
        <button>Logout</button>
      </a>
    </div>
  </div>
</div>

<!-- CONTENT -->
<div class="content">

<div class="header">
  <h1>Selamat Datang, <?php echo $_SESSION['nama']; ?></h1>
  <p>Dashboard Admin Rebon Adventure</p>
</div>

<div class="card">

<?php
if(!isset($_GET['menu'])){
  $_GET['menu'] = "trip";
}

$menu = $_GET['menu'];

/* ================= TRIP ================= */
if($menu == "trip"):

$hasil = kueri("SELECT t.*, tj.tujuan FROM trip t JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan");
?>

<a href="tambah_trip_v2.php" class="btn-tambah">+ Tambah Trip</a>

<table>
<tr>
<th>No</th>
<th>Destinasi</th>
<th>Tanggal</th>
<th>Durasi</th>
<th>Meeting Point</th>
<th>Harga</th>
<th>Kuota</th>
<th>Aksi</th>
</tr>

<?php
$no = 1;
while($row = ambil($hasil)){
$id = $row['id_trip'];

// Menghitung total peserta yang sudah booking untuk trip ini
$data_terisi = kueri("SELECT SUM(jumlah_peserta) AS total_terisi 
                      FROM booking 
                      WHERE id_trip = $id AND status != 'Dibatalkan'");
$terisi = ambil($data_terisi)['total_terisi'] ?? 0;

$sisa = $row['kuota'] - $terisi;
$data = kueri("SELECT (DATEDIFF(tgl_pulang, tgl_berangkat)+1) durasi FROM trip WHERE id_trip=$id");
$durasi = ambil($data)['durasi'];

$data = kueri("SELECT m.kota FROM meetpoint m WHERE id_trip=$id");

echo "<tr>";
echo "<td>$no</td>";
echo "<td>{$row['tujuan']}</td>";
echo "<td>{$row['tgl_berangkat']}</td>";
echo "<td>$durasi Hari</td>";
echo "<td>";
while($mp = ambil($data)){
  echo $mp['kota']."<br>";
}
echo "</td>";
echo "<td>{$row['harga']}</td>";
echo "<td>$sisa / {$row['kuota']}</td>";
echo "<td>
<a href='detail_trip.php?id=$id'>Detail</a> |
<a href='ubah_tripv2.php?id=$id'>Ubah</a> |
<a href='hapus_trip.php?id=$id' onclick=\"return confirm('Yakin hapus?')\">Hapus</a>
</td>";
echo "</tr>";

$no++;
}
?>
</table>





<?php elseif($menu == "booking"): ?>

<style>
/* =============================================
   BOOKING ADMIN - LIVE FILTER & PAGING STYLES
   ============================================= */

/* FILTER BAR ATAS: tombol periode */
.booking-period-bar {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  margin-bottom: 12px;
}
.booking-period-bar a {
  padding: 8px 13px;
  border-radius: 8px;
  text-decoration: none;
  color: white;
  font-size: 13px;
  font-weight: 700;
  transition: 0.2s;
  white-space: nowrap;
  letter-spacing: 0.2px;
}
.booking-period-bar a:hover { opacity: 0.85; text-decoration: none; }

/* TOOLBAR LIVE FILTER */
.booking-toolbar {
  display: flex;
  gap: 10px;
  align-items: center;
  flex-wrap: wrap;
  background: white;
  padding: 13px 16px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.07);
  border: 1px solid #eee;
  margin-bottom: 14px;
}

/* SEARCH INPUT */
.booking-search-wrap {
  position: relative;
  flex: 1;
  min-width: 200px;
}
.booking-search-wrap svg {
  position: absolute;
  left: 11px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}
.booking-toolbar input[type="text"] {
  width: 100%;
  padding: 9px 14px 9px 36px;
  border: 1.5px solid #e2e8f0;
  border-radius: 8px;
  font-size: 13px;
  outline: none;
  color: #2d3748;
  background: #f8fafc;
  transition: border 0.2s;
}
.booking-toolbar input[type="text"]:focus {
  border-color: #6b3df5;
  background: #fff;
}

/* SELECT STYLING */
.booking-toolbar select {
  padding: 9px 32px 9px 11px;
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
  min-width: 155px;
}
.booking-toolbar select:focus { border-color: #6b3df5; background-color: #fff; }

/* DIVIDER */
.toolbar-divider { width: 1px; height: 30px; background: #e2e8f0; flex-shrink: 0; }

/* LABEL KECIL */
.toolbar-lbl {
  font-size: 11px;
  font-weight: 700;
  color: #a0aec0;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  white-space: nowrap;
}

/* INFO BARIS & PAGING */
.booking-info-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 10px;
  padding: 0 2px;
}
.booking-result-count {
  font-size: 12px;
  color: #718096;
  font-weight: 600;
}

/* SELECT ENTRIES */
.entries-select-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #718096;
  font-weight: 600;
}
.entries-select-wrap select {
  padding: 5px 28px 5px 10px;
  border: 1.5px solid #e2e8f0;
  border-radius: 7px;
  font-size: 12px;
  font-weight: 700;
  outline: none;
  background: #f8fafc;
  color: #4a5568;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 8px center;
}

/* =============================================
   TABLE — BEAUTIFUL REDESIGN
   ============================================= */
.bk-table-wrap {
  overflow-x: auto;
  border-radius: 14px;
  box-shadow: 0 4px 24px rgba(107,61,245,0.10), 0 1.5px 6px rgba(0,0,0,0.05);
  border: 1.5px solid #ede9fe;
  background: white;
}
.bk-table-wrap table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 0;
  font-size: 13.5px;
}
.bk-table-wrap table thead {
  background: linear-gradient(135deg, #6b3df5 0%, #321180 100%);
}
.bk-table-wrap table thead th {
  color: white;
  padding: 13px 14px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
  border: none;
  position: relative;
}
.bk-table-wrap table thead th:not(:last-child)::after {
  content: '';
  position: absolute;
  right: 0; top: 25%; height: 50%;
  width: 1px;
  background: rgba(255,255,255,0.18);
}
.bk-table-wrap table tbody tr {
  border-bottom: 1px solid #f0ebff;
  transition: background 0.15s, transform 0.12s;
}
.bk-table-wrap table tbody tr:last-child {
  border-bottom: none;
}
.bk-table-wrap table tbody tr:hover {
  background: #f8f5ff;
  transform: scale(1.001);
  box-shadow: 0 2px 8px rgba(107,61,245,0.07);
}
.bk-table-wrap table tbody tr:nth-child(even) {
  background: #fcfaff;
}
.bk-table-wrap table tbody tr:nth-child(even):hover {
  background: #f3eeff;
}
.bk-table-wrap table td {
  padding: 12px 14px;
  color: #2d3748;
  border: none;
  vertical-align: middle;
}
/* No col */
.bk-table-wrap table td:first-child {
  font-weight: 800;
  color: #a0aec0;
  font-size: 12px;
  text-align: center;
  width: 40px;
}
/* Trip col */
.bk-table-wrap table td:nth-child(2) {
  font-weight: 700;
  color: #321180;
}
/* Pemesan col */
.bk-table-wrap table td:nth-child(3) {
  color: #4a5568;
  font-weight: 600;
}
/* Pax col */
.bk-table-wrap table td:nth-child(4) {
  text-align: center;
  font-weight: 800;
  color: #5a1ee6;
}
/* Tgl Booking */
.bk-table-wrap table td:nth-child(5) {
  font-size: 12.5px;
  color: #718096;
  white-space: nowrap;
}
/* Tgl Berangkat */
.bk-table-wrap table td:nth-child(6) {
  font-size: 12.5px;
  color: #718096;
  white-space: nowrap;
}
/* Pembayaran */
.bk-table-wrap table td:nth-child(7) {
  font-size: 12.5px;
  font-weight: 700;
  white-space: nowrap;
  color: #2d3748;
}
/* Status + Aksi */
.bk-table-wrap table td:nth-child(8),
.bk-table-wrap table td:nth-child(9) {
  text-align: center;
  white-space: nowrap;
}
.bk-detail-btn {
  display: inline-block;
  padding: 5px 14px;
  background: linear-gradient(135deg, #6b3df5, #321180);
  color: white !important;
  border-radius: 7px;
  font-size: 12px;
  font-weight: 700;
  text-decoration: none !important;
  transition: opacity 0.18s, box-shadow 0.18s;
  box-shadow: 0 2px 7px rgba(107,61,245,0.25);
  letter-spacing: 0.2px;
}
.bk-detail-btn:hover {
  opacity: 0.88;
  box-shadow: 0 4px 12px rgba(107,61,245,0.35);
  text-decoration: none !important;
}
/* Pax badge */
.pax-badge {
  display: inline-block;
  background: linear-gradient(135deg,#ede9fe,#ddd6fe);
  color: #5a1ee6;
  border-radius: 20px;
  padding: 3px 11px;
  font-size: 12.5px;
  font-weight: 800;
  border: 1px solid #c4b5fd;
  letter-spacing: 0.2px;
}

/* NO RESULT */
.booking-no-result {
  display: none;
  text-align: center;
  padding: 45px 20px;
  color: #a0aec0;
  background: white;
  border-radius: 12px;
  border: 1px dashed #e2e8f0;
  font-size: 14px;
  margin-top: 10px;
}

/* PAGINATION */
.booking-pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 5px;
  margin-top: 18px;
  flex-wrap: wrap;
}
.pg-btn {
  min-width: 34px;
  height: 34px;
  padding: 0 10px;
  border: 1.5px solid #e2e8f0;
  background: white;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  color: #4a5568;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}
.pg-btn:hover { background: #f0ebff; border-color: #6b3df5; color: #6b3df5; }
.pg-btn.active  { background: #6b3df5; color: white; border-color: #6b3df5; box-shadow: 0 3px 8px rgba(107,61,245,0.3); }
.pg-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.pg-btn.pg-ellipsis { cursor: default; border-color: transparent; background: transparent; color: #a0aec0; }
</style>

<?php
  /* =============================================
     QUERY: ambil SEMUA data booking yang lolos
     filter PERIODE dari server. Filter lain
     (search, destinasi, status, sort) dikerjakan
     di sisi klien secara live oleh JavaScript.
     ============================================= */
  $filter = $_GET['filter'] ?? 'semua';

  $where_server = [];
  if ($filter == 'hari_ini') {
      $where_server[] = "DATE(b.tgl_booking) = CURDATE()";
  } elseif ($filter == 'minggu_ini') {
      $where_server[] = "YEARWEEK(b.tgl_booking, 1) = YEARWEEK(CURDATE(), 1)";
  } elseif ($filter == 'bulan_ini') {
      $where_server[] = "MONTH(b.tgl_booking) = MONTH(CURDATE()) AND YEAR(b.tgl_booking) = YEAR(CURDATE())";
  } elseif ($filter == 'tahun_ini') {
      $where_server[] = "YEAR(b.tgl_booking) = YEAR(CURDATE())";
  }

  $kondisi_server = (count($where_server) > 0) ? "WHERE " . implode(" AND ", $where_server) : "";

  /* Ambil semua row — urutan awal terbaru */
  $data_booking = kueri("SELECT b.*, tj.tujuan, t.harga, t.tgl_berangkat, a.username AS nama,
                  (SELECT SUM(nominal) FROM payment_open
                   WHERE id_booking = b.id_booking AND status = 'Diverifikasi') AS total_bayar
                  FROM booking b
                  JOIN trip t ON b.id_trip = t.id_trip
                  JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                  JOIN akun a ON b.id_akun = a.id_akun
                  $kondisi_server
                  ORDER BY b.tgl_booking DESC");

  /* Tampung semua row ke array PHP agar bisa di-embed ke JS */
  $all_rows = [];
  while ($row = ambil($data_booking)) {
      $bayar          = (int)($row['total_bayar'] ?? 0);
      $total_tagihan  = (int)$row['harga'] * (int)$row['jumlah_peserta'];
      $all_rows[] = [
          'id_booking'      => (int)$row['id_booking'],
          'tujuan'          => $row['tujuan'],
          'nama'            => $row['nama'],
          'jumlah_peserta'  => (int)$row['jumlah_peserta'],
          'tgl_booking'     => $row['tgl_booking'],
          'tgl_berangkat'   => $row['tgl_berangkat'],
          'bayar'           => $bayar,
          'total_tagihan'   => $total_tagihan,
          'status'          => $row['status'],
      ];
  }
?>

<!-- ===== BARIS PERIODE ===== -->
<div class="booking-period-bar">
<?php
$periods = [
  'hari_ini'   => 'Hari Ini',
  'minggu_ini' => 'Minggu Ini',
  'bulan_ini'  => 'Bulan Ini',
  'tahun_ini'  => 'Tahun Ini',
  'semua'      => 'Semua Data',
];
foreach ($periods as $key => $label) {
    $bg = ($filter == $key) ? '#321180' : '#6b3df5';
    echo "<a href=\"index.php?menu=booking&filter=$key\" style=\"background:$bg;\">$label</a>";
}
?>
</div>

<!-- ===== TOOLBAR LIVE FILTER ===== -->
<div class="booking-toolbar">
  <!-- SEARCH -->
  <div class="booking-search-wrap">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#a0aec0" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="bk-search"
           placeholder="Cari destinasi atau nama pemesan..."
           oninput="bookingFilter()">
  </div>

  <div class="toolbar-divider"></div>

  <!-- SORT PEMBAYARAN -->
  <span class="toolbar-lbl">Urutkan:</span>
  <select id="bk-sort" onchange="bookingFilter()">
    <option value="">Urutkan Pembayaran</option>
    <option value="bayar-asc">Pembayaran Terkecil</option>
    <option value="bayar-desc">Pembayaran Terbesar</option>
  </select>

  <!-- FILTER DESTINASI -->
  <select id="bk-destinasi" onchange="bookingFilter()">
    <option value="">Semua Destinasi</option>
    <?php
    $list_dest = kueri("SELECT DISTINCT tujuan FROM tujuan ORDER BY tujuan ASC");
    while ($td = ambil($list_dest)) {
        echo "<option value=\"" . htmlspecialchars(strtolower($td['tujuan'])) . "\">" . htmlspecialchars($td['tujuan']) . "</option>";
    }
    ?>
  </select>

  <!-- FILTER STATUS -->
  <select id="bk-status" onchange="bookingFilter()">
    <option value="">Semua Status</option>
    <?php
    $list_st = kueri("SELECT DISTINCT status FROM booking ORDER BY status ASC");
    while ($sv = ambil($list_st)) {
        echo "<option value=\"" . htmlspecialchars(strtolower($sv['status'])) . "\">" . htmlspecialchars($sv['status']) . "</option>";
    }
    ?>
  </select>

  <!-- FILTER PAX -->
  <select id="bk-pax" onchange="bookingFilter()">
    <option value="">Semua Pax</option>
    <option value="pax-asc">Pax Paling Sedikit</option>
    <option value="pax-desc">Pax Terbanyak</option>
  </select>
</div>

<!-- ===== INFO BAR: jumlah entri + hasil pencarian ===== -->
<div class="booking-info-bar">
  <div id="bk-result-count" class="booking-result-count"></div>
  <div class="entries-select-wrap">
    <span>Tampilkan</span>
    <select id="bk-per-page" onchange="bookingFilter()">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
    <span>entri per halaman</span>
  </div>
</div>

<!-- ===== DOWNLOAD EXCEL ===== -->
<a href="export_booking.php?filter=<?php echo $filter; ?>"
   style="display:inline-block; margin-bottom:12px; padding:8px 15px; background:green; color:white; border-radius:8px; text-decoration:none; font-size:13px; font-weight:bold;">
   Download Excel
</a>

<!-- ===== TABEL BOOKING ===== -->
<div class="bk-table-wrap">
<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Trip</th>
      <th>Pemesan</th>
      <th>Pax</th>
      <th>Tgl Booking</th>
      <th>Tgl Berangkat</th>
      <th>Pembayaran (Lunas / Tagihan)</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody id="booking-table-body">
    <!-- diisi oleh JavaScript -->
  </tbody>
</table>
</div>

<!-- NO RESULT -->
<div id="bk-no-result" class="booking-no-result">
  🔍 Tidak ada pesanan yang sesuai dengan pencarian / filter Anda.
</div>

<!-- PAGINATION -->
<div id="bk-pagination" class="booking-pagination"></div>

<!-- ===== EMBED DATA PHP → JS ===== -->
<script>
/* Data semua booking dari PHP */
const BK_DATA = <?php echo json_encode($all_rows, JSON_UNESCAPED_UNICODE); ?>;

/* State paging */
let bkCurrentPage  = 1;
let bkFiltered     = [];

/* -----------------------------------------------
   FORMAT ANGKA (Rupiah tanpa simbol)
   ----------------------------------------------- */
function fmtRp(n) {
  return n.toLocaleString('id-ID');
}

/* -----------------------------------------------
   WARNA STATUS
   ----------------------------------------------- */
function statusStyle(s) {
  const lc = s.toLowerCase();
  if (lc === 'lunas')      return 'color:#2c7a7b; background:#e6fffa; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:800; border:1px solid #b2f5ea; white-space:nowrap;';
  if (lc === 'dibatalkan') return 'color:#c53030; background:#fff5f5; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:800; border:1px solid #feb2b2; white-space:nowrap;';
  if (lc === 'dp')         return 'color:#b7791f; background:#fffaf0; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:800; border:1px solid #fbe3a1; white-space:nowrap;';
  if (lc === 'refund')     return 'color:#6b46c1; background:#faf5ff; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:800; border:1px solid #d6bcfa; white-space:nowrap;';
  return 'color:#6b46c1; background:#f0ebff; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:800; border:1px solid #d6bcfa; white-space:nowrap;';
}

/* -----------------------------------------------
   UTAMA: filter + sort + render
   ----------------------------------------------- */
function bookingFilter(resetPage) {
  if (resetPage === undefined) resetPage = true;
  if (resetPage) bkCurrentPage = 1;

  const keyword    = document.getElementById('bk-search').value.toLowerCase().trim();
  const sortVal    = document.getElementById('bk-sort').value;
  const destVal    = document.getElementById('bk-destinasi').value.toLowerCase();
  const statusVal  = document.getElementById('bk-status').value.toLowerCase();
  const paxVal     = document.getElementById('bk-pax').value;
  const perPage    = parseInt(document.getElementById('bk-per-page').value) || 10;

  /* ---- FILTER ---- */
  bkFiltered = BK_DATA.filter(row => {
    const matchSearch  = !keyword ||
                         row.tujuan.toLowerCase().includes(keyword) ||
                         row.nama.toLowerCase().includes(keyword);
    const matchDest    = !destVal  || row.tujuan.toLowerCase() === destVal;
    const matchStatus  = !statusVal || row.status.toLowerCase() === statusVal;
    return matchSearch && matchDest && matchStatus;
  });

  /* ---- SORT: Pembayaran diprioritaskan, lalu Pax jika pembayaran sama ---- */
  if (sortVal === 'bayar-asc')       bkFiltered.sort((a,b) => a.bayar - b.bayar);
  else if (sortVal === 'bayar-desc') bkFiltered.sort((a,b) => b.bayar - a.bayar);
  else if (paxVal === 'pax-asc')     bkFiltered.sort((a,b) => a.jumlah_peserta - b.jumlah_peserta);
  else if (paxVal === 'pax-desc')    bkFiltered.sort((a,b) => b.jumlah_peserta - a.jumlah_peserta);

  /* ---- PAGING ---- */
  const total = bkFiltered.length;
  const totalPages = Math.max(1, Math.ceil(total / perPage));
  if (bkCurrentPage > totalPages) bkCurrentPage = totalPages;

  const start = (bkCurrentPage - 1) * perPage;
  const pageData = bkFiltered.slice(start, start + perPage);

  /* ---- RENDER TABEL ---- */
  const tbody = document.getElementById('booking-table-body');
  tbody.innerHTML = '';

  pageData.forEach((row, idx) => {
    const no  = start + idx + 1;
    const tr  = document.createElement('tr');
    tr.innerHTML =
      `<td>${no}</td>` +
      `<td>${row.tujuan}</td>` +
      `<td>${row.nama}</td>` +
      `<td><span class="pax-badge">${row.jumlah_peserta} pax</span></td>` +
      `<td>${row.tgl_booking}</td>` +
      `<td>${row.tgl_berangkat}</td>` +
      `<td>Rp ${fmtRp(row.bayar)} / ${fmtRp(row.total_tagihan)}</td>` +
      `<td><span style="${statusStyle(row.status)}">${row.status}</span></td>` +
      `<td><a class="bk-detail-btn" href="detail_booking.php?id=${row.id_booking}">Detail</a></td>`;
    tbody.appendChild(tr);
  });

  /* ---- COUNTER ---- */
  const countEl = document.getElementById('bk-result-count');
  if (total === BK_DATA.length) {
    countEl.textContent = `Total ${total} pesanan`;
  } else {
    countEl.textContent = `Menampilkan ${total} dari ${BK_DATA.length} pesanan`;
  }

  /* ---- NO RESULT ---- */
  const noResult = document.getElementById('bk-no-result');
  noResult.style.display = (total === 0) ? 'block' : 'none';

  /* ---- RENDER PAGINATION ---- */
  renderPagination(totalPages, perPage);
}

/* -----------------------------------------------
   RENDER PAGINATION
   ----------------------------------------------- */
function renderPagination(totalPages, perPage) {
  const pg = document.getElementById('bk-pagination');
  pg.innerHTML = '';
  if (totalPages <= 1) return;

  const make = (label, page, disabled, active, ellipsis) => {
    const btn = document.createElement('button');
    btn.className = 'pg-btn' + (active ? ' active' : '') + (ellipsis ? ' pg-ellipsis' : '');
    btn.innerHTML = label;
    btn.disabled  = disabled;
    if (!disabled && !ellipsis) {
      btn.onclick = () => {
        bkCurrentPage = page;
        bookingFilter(false);
      };
    }
    return btn;
  };

  /* Prev */
  pg.appendChild(make('&laquo;', bkCurrentPage - 1, bkCurrentPage === 1, false, false));

  /* Nomor halaman dengan ellipsis */
  const range = [];
  if (totalPages <= 7) {
    for (let i = 1; i <= totalPages; i++) range.push(i);
  } else {
    range.push(1);
    if (bkCurrentPage > 4) range.push('...');
    for (let i = Math.max(2, bkCurrentPage-1); i <= Math.min(totalPages-1, bkCurrentPage+1); i++) range.push(i);
    if (bkCurrentPage < totalPages - 3) range.push('...');
    range.push(totalPages);
  }

  range.forEach(r => {
    if (r === '...') {
      pg.appendChild(make('...', 0, true, false, true));
    } else {
      pg.appendChild(make(r, r, false, r === bkCurrentPage, false));
    }
  });

  /* Next */
  pg.appendChild(make('&raquo;', bkCurrentPage + 1, bkCurrentPage === totalPages, false, false));
}

/* ---- Inisialisasi saat halaman dimuat ---- */
document.addEventListener('DOMContentLoaded', function() {
  bookingFilter();
});
</script>

<?php elseif($menu == "destinasi"): ?>

<div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 style="color: #321180;">Manajemen Destinasi</h2>
        <p style="font-size: 14px; color: #666;">Kelola master data tujuan, wilayah, dan harga default trip.</p>
    </div>
    <!-- Tombol Tambah Destinasi -->
    <a href="tambah_destinasi.php" class="btn-tambah" style="margin-top: 0; font-weight: bold; padding: 12px 20px;">
        + Tambah Destinasi
    </a>
</div>

<?php
// Ambil data dari tabel tujuan yang baru sesuai struktur SQL Anda
$data_destinasi = kueri("SELECT * FROM tujuan ORDER BY id_tujuan DESC");
?>

<!-- GRID LAYOUT CARD -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px;">
    
    <?php 
    if(mysqli_num_rows($data_destinasi) > 0){
        while($row = ambil($data_destinasi)){ 
    ?>
        <!-- Card Item -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 20px; display: flex; flex-direction: column; justify-content: space-between; border-left: 5px solid #6b3df5; transition: 0.3s;">
            
            <div>
                <!-- Nama Destinasi -->
                <h3 style="color: #321180; margin-bottom: 5px; font-size: 18px;"><?php echo $row['tujuan']; ?></h3>
                
                <!-- Lokasi Kota & Provinsi -->
                <p style="font-size: 13px; color: #888; margin-bottom: 15px; display: flex; align-items: center; gap: 5px;">
                    📍 <?php echo $row['kota']; ?>, <?php echo $row['provinsi']; ?>
                </p>
                
                <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 15px;">
                
                <!-- Detail Harga & Rute -->
                <div style="font-size: 13px; color: #444; display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px;">
                    <div>
                        <span style="color: #666;">Harga Default:</span> <br>
                        <strong style="color: #6b3df5; font-size: 14px;">
                            <?php echo $row['harga_def'] ? "Rp " . number_format($row['harga_def']) : "-"; ?>
                        </strong>
                    </div>
                    <div>
                        <span style="color: #666;">DP Default:</span> <br>
                        <strong style="color: #ff9800;">
                            <?php echo $row['harga_dp_def'] ? "Rp " . number_format($row['harga_dp_def']) : "-"; ?>
                        </strong>
                    </div>
                    <div>
                        <span style="color: #666;">Rute Default:</span> <br>
                        <span style="font-style: italic; color: #555; font-size: 12px;">
                            <?php echo $row['rute_def'] ?? "Belum diatur"; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi (Ubah & Detail) -->
            <div style="display: flex; gap: 10px; margin-top: auto;">
                <a href="detail_destinasi.php?id=<?php echo $row['id_tujuan']; ?>" 
                   style="flex: 1; text-align: center; background: #f4f0ff; color: #321180; padding: 8px; border-radius: 6px; font-size: 13px; font-weight: bold; border: 1px solid #321180; transition: 0.3s; text-decoration: none;">
                   Detail
                </a>
                <a href="ubah_destinasi.php?id=<?php echo $row['id_tujuan']; ?>" 
                   style="flex: 1; text-align: center; background: #6b3df5; color: white; padding: 8px; border-radius: 6px; font-size: 13px; font-weight: bold; transition: 0.3s; text-decoration: none;">
                   Ubah
                </a>
            </div>

        </div>
    <?php 
        } 
    } else {
        echo "<div style='grid-column: 1/-1; background: white; padding: 30px; text-align: center; border-radius: 12px; color: #666;'>Belum ada data destinasi. Klik tombol di atas untuk menambah.</div>";
    }
    ?>

</div>






<?php elseif($menu == "private"): ?>

<?php
/* =============================================================
   PRIVATE TRIP — ambil SEMUA data, filter/sort via JS (client)
   Struktur mengacu pada tabel private_trip di database rebon.
   ============================================================= */
$sub_tab = $_GET['sub'] ?? 'pengajuan_baru';

if ($sub_tab == 'pengajuan_baru') {

    /* ---- Ambil semua private trip sekaligus ---- */
    $res_pt = kueri("SELECT pt.id_private, pt.nama, pt.tujuan, pt.jumlah_peserta,
                            pt.tgl_berangkat, pt.tgl_booking,
                            pt.status_trip, pt.status_bayar,
                            a.username
                     FROM private_trip pt
                     JOIN akun a ON pt.id_akun = a.id_akun
                     ORDER BY pt.tgl_booking DESC");

    $pt_rows = [];
    while ($r = ambil($res_pt)) {
        $pt_rows[] = [
            'id_private'      => (int)$r['id_private'],
            'nama'            => $r['nama'],
            'username'        => $r['username'],
            'tujuan'          => $r['tujuan'],
            'jumlah_peserta'  => (int)$r['jumlah_peserta'],
            'tgl_berangkat'   => $r['tgl_berangkat'],
            'tgl_booking'     => $r['tgl_booking'],
            'status_trip'     => $r['status_trip'],
            'status_bayar'    => $r['status_bayar'],
        ];
    }

    /* ---- Daftar tujuan unik untuk dropdown filter ---- */
    $res_tujuan_pt = kueri("SELECT DISTINCT tujuan FROM private_trip ORDER BY tujuan ASC");
    $list_tujuan_pt = [];
    while ($tt = ambil($res_tujuan_pt)) $list_tujuan_pt[] = $tt['tujuan'];
}
?>

<!-- ============================================================
     CSS — senada dengan tab Pesanan & Pembayaran
     ============================================================ -->
<style>
/* ---- TAB SWITCH (Pengajuan Baru / Pengajuan Perubahan) ---- */
.pvt-tab-bar {
  display: flex;
  gap: 0;
  border-bottom: 2.5px solid #e1d8f5;
  margin-bottom: 20px;
}
.pvt-tab-bar a {
  text-decoration: none !important;
  padding: 10px 22px;
  font-weight: 800;
  font-size: 14px;
  color: #a0aec0;
  border-bottom: 3px solid transparent;
  margin-bottom: -2.5px;
  transition: color 0.2s, border-color 0.2s;
  letter-spacing: 0.2px;
}
.pvt-tab-bar a:hover { color: #6b3df5; text-decoration: none !important; }
.pvt-tab-bar a.pvt-tab-active {
  color: #321180;
  border-bottom-color: #321180;
}

/* ---- TOOLBAR (identik .booking-toolbar & .pay-toolbar) ---- */
.pvt-toolbar {
  display: flex;
  gap: 10px;
  align-items: center;
  flex-wrap: wrap;
  background: white;
  padding: 13px 16px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.07);
  border: 1px solid #eee;
  margin-bottom: 14px;
}

/* SEARCH */
.pvt-search-wrap {
  position: relative;
  flex: 1;
  min-width: 220px;
}
.pvt-search-wrap svg {
  position: absolute;
  left: 11px; top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}
.pvt-toolbar input[type="text"] {
  width: 100%;
  padding: 9px 14px 9px 36px;
  border: 1.5px solid #e2e8f0;
  border-radius: 8px;
  font-size: 13px;
  outline: none;
  color: #2d3748;
  background: #f8fafc;
  transition: border 0.2s;
}
.pvt-toolbar input[type="text"]:focus {
  border-color: #6b3df5;
  background: #fff;
}

/* SELECT */
.pvt-toolbar select {
  padding: 9px 32px 9px 11px;
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
.pvt-toolbar select:focus { border-color: #6b3df5; background-color: #fff; }

/* DIVIDER & LABEL */
.pvt-toolbar .toolbar-divider {
  width: 1px; height: 30px;
  background: #e2e8f0; flex-shrink: 0;
}
.pvt-toolbar .toolbar-lbl {
  font-size: 11px; font-weight: 700;
  color: #a0aec0; text-transform: uppercase;
  letter-spacing: 0.4px; white-space: nowrap;
}

/* ---- INFO BAR ---- */
.pvt-info-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 10px;
  padding: 0 2px;
}
.pvt-result-count { font-size: 12px; color: #718096; font-weight: 600; }
.pvt-entries-wrap {
  display: flex; align-items: center; gap: 8px;
  font-size: 12px; color: #718096; font-weight: 600;
}
.pvt-entries-wrap select {
  padding: 5px 28px 5px 10px;
  border: 1.5px solid #e2e8f0; border-radius: 7px;
  font-size: 12px; font-weight: 700; outline: none;
  background: #f8fafc; color: #4a5568; cursor: pointer;
  appearance: none; -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 8px center;
}

/* ---- NO RESULT ---- */
.pvt-no-result {
  display: none;
  text-align: center;
  padding: 45px 20px;
  color: #a0aec0;
  background: white;
  border-radius: 12px;
  border: 1px dashed #e2e8f0;
  font-size: 14px;
  margin-top: 10px;
}

/* ---- TABLE WRAP (identik .bk-table-wrap & .pay-table-wrap) ---- */
.pvt-table-wrap {
  overflow-x: auto;
  max-width: 100%;
  width: 100%;
  border-radius: 14px;
  box-shadow: 0 4px 24px rgba(107,61,245,0.10), 0 1.5px 6px rgba(0,0,0,0.05);
  border: 1.5px solid #ede9fe;
  background: white;
}
.pvt-table-wrap table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 0;
  font-size: 13.5px;
}
.pvt-table-wrap table thead {
  background: linear-gradient(135deg, #6b3df5 0%, #321180 100%);
}
.pvt-table-wrap table thead th {
  color: white;
  padding: 13px 14px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
  border: none;
  position: relative;
}
.pvt-table-wrap table thead th:not(:last-child)::after {
  content: '';
  position: absolute;
  right: 0; top: 25%; height: 50%;
  width: 1px;
  background: rgba(255,255,255,0.18);
}
.pvt-table-wrap table tbody tr {
  border-bottom: 1px solid #f0ebff;
  transition: background 0.15s, transform 0.12s;
}
.pvt-table-wrap table tbody tr:last-child { border-bottom: none; }
.pvt-table-wrap table tbody tr:hover {
  background: #f8f5ff;
  transform: scale(1.001);
  box-shadow: 0 2px 8px rgba(107,61,245,0.07);
}
.pvt-table-wrap table tbody tr:nth-child(even) { background: #fcfaff; }
.pvt-table-wrap table tbody tr:nth-child(even):hover { background: #f3eeff; }
.pvt-table-wrap table td {
  padding: 12px 14px;
  color: #2d3748;
  border: none;
  vertical-align: middle;
}
/* No */
.pvt-table-wrap table td:first-child {
  font-weight: 800; color: #a0aec0;
  font-size: 12px; text-align: center; width: 40px;
}
/* Pemesan */
.pvt-table-wrap table td:nth-child(2) { font-weight: 700; color: #321180; }
/* Tujuan */
.pvt-table-wrap table td:nth-child(3) { color: #4a5568; font-weight: 600; }
/* Peserta */
.pvt-table-wrap table td:nth-child(4) { text-align: center; }
/* Tgl Berangkat */
.pvt-table-wrap table td:nth-child(5) { font-size: 12.5px; color: #718096; white-space: nowrap; }
/* Tgl Booking */
.pvt-table-wrap table td:nth-child(6) { font-size: 12.5px; color: #718096; white-space: nowrap; }
/* Status Trip, Status Bayar, Aksi */
.pvt-table-wrap table td:nth-child(7),
.pvt-table-wrap table td:nth-child(8),
.pvt-table-wrap table td:nth-child(9) { text-align: center; white-space: nowrap; }

/* ---- KOLOM CATATAN ALASAN — selalu wrap, tidak overflow ---- */
.pvt-table-wrap table td.col-catatan {
  white-space: normal !important;
  word-break: break-word;
  min-width: 180px;
  max-width: 280px;
  font-size: 12.5px;
  line-height: 1.5;
  vertical-align: top;
}

/* ---- PAX BADGE (identik dengan tab Pesanan/Pembayaran) ---- */
.pax-badge {
  display: inline-block;
  background: linear-gradient(135deg,#ede9fe,#ddd6fe);
  color: #5a1ee6;
  border-radius: 20px;
  padding: 3px 11px;
  font-size: 12.5px;
  font-weight: 800;
  border: 1px solid #c4b5fd;
  letter-spacing: 0.2px;
}

/* ---- DETAIL BUTTON (identik .bk-detail-btn) ---- */
.pvt-detail-btn {
  display: inline-block;
  padding: 5px 14px;
  background: linear-gradient(135deg, #6b3df5, #321180);
  color: white !important;
  border-radius: 7px;
  font-size: 12px;
  font-weight: 700;
  text-decoration: none !important;
  transition: opacity 0.18s, box-shadow 0.18s;
  box-shadow: 0 2px 7px rgba(107,61,245,0.25);
  letter-spacing: 0.2px;
}
.pvt-detail-btn:hover {
  opacity: 0.88;
  box-shadow: 0 4px 12px rgba(107,61,245,0.35);
  text-decoration: none !important;
}

/* ---- PAGINATION (identik .pay-pagination & .booking-pagination) ---- */
.pvt-pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 5px;
  margin-top: 18px;
  flex-wrap: wrap;
}
.pvt-pagination .pg-btn {
  min-width: 34px; height: 34px;
  padding: 0 10px;
  border: 1.5px solid #e2e8f0;
  background: white;
  border-radius: 8px;
  font-size: 13px; font-weight: 700;
  color: #4a5568; cursor: pointer;
  transition: all 0.2s;
  display: flex; align-items: center; justify-content: center;
  line-height: 1;
}
.pvt-pagination .pg-btn:hover {
  background: #f0ebff; border-color: #6b3df5; color: #6b3df5;
}
.pvt-pagination .pg-btn.active {
  background: #6b3df5; color: white;
  border-color: #6b3df5;
  box-shadow: 0 3px 8px rgba(107,61,245,0.3);
}
.pvt-pagination .pg-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.pvt-pagination .pg-btn.pg-ellipsis {
  cursor: default; border-color: transparent;
  background: transparent; color: #a0aec0;
}
</style>

<!-- ============================================================
     HEADER JUDUL
     ============================================================ -->
<div style="margin-bottom: 18px;">
  <h2 style="color: #321180; margin-bottom: 4px;">Daftar Pengajuan Private Trip</h2>
  <p style="font-size: 14px; color: #666;">Kelola permintaan private trip dan log pengajuan perubahan data dari pengguna di sini.</p>
</div>

<!-- ============================================================
     TAB SWITCH (Pengajuan Baru / Pengajuan Perubahan)
     ============================================================ -->
<div class="pvt-tab-bar">
  <a href="index.php?menu=private&sub=pengajuan_baru"
     class="<?php echo ($sub_tab == 'pengajuan_baru') ? 'pvt-tab-active' : ''; ?>">
     Pengajuan Baru
  </a>
  <a href="index.php?menu=private&sub=pengajuan_perubahan"
     class="<?php echo ($sub_tab == 'pengajuan_perubahan') ? 'pvt-tab-active' : ''; ?>">
     Pengajuan Perubahan
  </a>
</div>

<!-- ============================================================
     SUB-TAB: PENGAJUAN BARU
     ============================================================ -->
<?php if ($sub_tab == 'pengajuan_baru'): ?>

<!-- TOOLBAR LIVE FILTER -->
<div class="pvt-toolbar">

  <!-- SEARCH -->
  <div class="pvt-search-wrap">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#a0aec0" stroke-width="2">
      <circle cx="11" cy="11" r="8"/>
      <line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="pvt-search"
           placeholder="Cari nama pemesan, akun, atau tujuan..."
           oninput="pvtFilter()">
  </div>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Urutkan:</span>

  <!-- SORT TANGGAL -->
  <select id="pvt-sort-date" onchange="pvtFilter()">
    <option value="">Urutkan Tanggal</option>
    <option value="booking-desc">Booking Terbaru</option>
    <option value="booking-asc">Booking Terlama</option>
    <option value="berangkat-desc">Berangkat Terbaru</option>
    <option value="berangkat-asc">Berangkat Terlama</option>
  </select>

  <!-- SORT PESERTA -->
  <select id="pvt-sort-pax" onchange="pvtFilter()">
    <option value="">Urutkan Peserta</option>
    <option value="pax-desc">Peserta Terbanyak</option>
    <option value="pax-asc">Peserta Paling Sedikit</option>
  </select>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Filter:</span>

  <!-- FILTER STATUS TRIP -->
  <select id="pvt-filter-trip" onchange="pvtFilter()">
    <option value="">Semua Status Trip</option>
    <option value="belum disetujui">Belum Disetujui</option>
    <option value="disetujui">Disetujui</option>
    <option value="ditolak">Ditolak</option>
  </select>

  <!-- FILTER STATUS BAYAR -->
  <select id="pvt-filter-bayar" onchange="pvtFilter()">
    <option value="">Semua Status Bayar</option>
    <option value="belum bayar">Belum Bayar</option>
    <option value="bayar non-dp">Bayar non-DP</option>
    <option value="dp">DP</option>
    <option value="lunas">Lunas</option>
    <option value="dibatalkan">Dibatalkan</option>
    <option value="refund">Refund</option>
  </select>

</div>

<!-- INFO BAR: jumlah entri + pagination per-page -->
<div class="pvt-info-bar">
  <div id="pvt-result-count" class="pvt-result-count"></div>
  <div class="pvt-entries-wrap">
    <span>Tampilkan</span>
    <select id="pvt-per-page" onchange="pvtFilter()">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
    <span>entri per halaman</span>
  </div>
</div>

<!-- TABEL -->
<div class="pvt-table-wrap">
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Pemesan</th>
        <th>Tujuan</th>
        <th>Peserta</th>
        <th>Tgl Berangkat</th>
        <th>Tgl Booking</th>
        <th>Status Trip</th>
        <th>Status Bayar</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody id="pvt-table-body">
      <!-- diisi JS -->
    </tbody>
  </table>
</div>

<!-- NO RESULT -->
<div id="pvt-no-result" class="pvt-no-result">
  🔍 Tidak ada pengajuan private trip yang sesuai dengan pencarian / filter Anda.
</div>

<!-- PAGINATION -->
<div id="pvt-pagination" class="pvt-pagination"></div>

<!-- ============================================================
     EMBED DATA PHP → JAVASCRIPT
     ============================================================ -->
<script>
/* Semua data private trip dari PHP */
const PVT_DATA = <?php echo json_encode($pt_rows, JSON_UNESCAPED_UNICODE); ?>;

/* State paging */
let pvtCurrentPage = 1;
let pvtFiltered    = [];

/* ---- Badge Status Trip ---- */
function pvtTripBadge(s) {
  const lc = s.toLowerCase();
  if (lc === 'disetujui')
    return `<span style="color:#276749;background:#f0fff4;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #9ae6b4;white-space:nowrap;">${s}</span>`;
  if (lc === 'ditolak')
    return `<span style="color:#c53030;background:#fff5f5;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #feb2b2;white-space:nowrap;">${s}</span>`;
  /* Belum Disetujui */
  return `<span style="color:#b7791f;background:#fffaf0;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #fbe3a1;white-space:nowrap;">${s}</span>`;
}

/* ---- Badge Status Bayar ---- */
function pvtBayarBadge(s) {
  const lc = s.toLowerCase();
  if (lc === 'lunas')
    return `<span style="color:#2c7a7b;background:#e6fffa;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #b2f5ea;white-space:nowrap;">${s}</span>`;
  if (lc === 'dibatalkan')
    return `<span style="color:#c53030;background:#fff5f5;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #feb2b2;white-space:nowrap;">${s}</span>`;
  if (lc === 'dp')
    return `<span style="color:#b7791f;background:#fffaf0;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #fbe3a1;white-space:nowrap;">${s}</span>`;
  if (lc === 'refund')
    return `<span style="color:#6b46c1;background:#faf5ff;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #d6bcfa;white-space:nowrap;">${s}</span>`;
  if (lc === 'bayar non-dp')
    return `<span style="color:#2b6cb0;background:#ebf8ff;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #bee3f8;white-space:nowrap;">${s}</span>`;
  /* Belum Bayar */
  return `<span style="color:#c53030;background:#fff5f5;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #feb2b2;white-space:nowrap;">${s}</span>`;
}

/* ---- Format Tanggal dd/mm/yyyy ---- */
function pvtFmtDate(str) {
  if (!str) return '-';
  const d = new Date(str);
  const dd = String(d.getDate()).padStart(2,'0');
  const mm = String(d.getMonth()+1).padStart(2,'0');
  return dd+'/'+mm+'/'+d.getFullYear();
}

/* ---- UTAMA: filter + sort + render ---- */
function pvtFilter(resetPage) {
  if (resetPage === undefined) resetPage = true;
  if (resetPage) pvtCurrentPage = 1;

  const keyword    = document.getElementById('pvt-search').value.toLowerCase().trim();
  const sortDate   = document.getElementById('pvt-sort-date').value;
  const sortPax    = document.getElementById('pvt-sort-pax').value;
  const filterTrip = document.getElementById('pvt-filter-trip').value.toLowerCase();
  const filterBayar= document.getElementById('pvt-filter-bayar').value.toLowerCase();
  const perPage    = parseInt(document.getElementById('pvt-per-page').value) || 10;

  /* ---- FILTER ---- */
  pvtFiltered = PVT_DATA.filter(row => {
    /* Search: nama pemesan, username akun, atau tujuan */
    const matchSearch = !keyword ||
      row.nama.toLowerCase().includes(keyword) ||
      row.username.toLowerCase().includes(keyword) ||
      ('@' + row.username).toLowerCase().includes(keyword) ||
      row.tujuan.toLowerCase().includes(keyword);

    const matchTrip  = !filterTrip  || row.status_trip.toLowerCase()  === filterTrip;
    const matchBayar = !filterBayar || row.status_bayar.toLowerCase() === filterBayar;

    return matchSearch && matchTrip && matchBayar;
  });

  /* ---- SORT ---- */
  /* Tanggal diprioritaskan; jika tidak ada sort tanggal, coba sort pax */
  if (sortDate === 'booking-desc')
    pvtFiltered.sort((a,b) => new Date(b.tgl_booking)  - new Date(a.tgl_booking));
  else if (sortDate === 'booking-asc')
    pvtFiltered.sort((a,b) => new Date(a.tgl_booking)  - new Date(b.tgl_booking));
  else if (sortDate === 'berangkat-desc')
    pvtFiltered.sort((a,b) => new Date(b.tgl_berangkat) - new Date(a.tgl_berangkat));
  else if (sortDate === 'berangkat-asc')
    pvtFiltered.sort((a,b) => new Date(a.tgl_berangkat) - new Date(b.tgl_berangkat));
  else if (sortPax === 'pax-desc')
    pvtFiltered.sort((a,b) => b.jumlah_peserta - a.jumlah_peserta);
  else if (sortPax === 'pax-asc')
    pvtFiltered.sort((a,b) => a.jumlah_peserta - b.jumlah_peserta);

  /* ---- PAGING ---- */
  const total      = pvtFiltered.length;
  const totalPages = Math.max(1, Math.ceil(total / perPage));
  if (pvtCurrentPage > totalPages) pvtCurrentPage = totalPages;

  const start    = (pvtCurrentPage - 1) * perPage;
  const pageData = pvtFiltered.slice(start, start + perPage);

  /* ---- RENDER TABEL ---- */
  const tbody = document.getElementById('pvt-table-body');
  tbody.innerHTML = '';

  pageData.forEach((row, idx) => {
    const no = start + idx + 1;
    const tr = document.createElement('tr');
    tr.innerHTML =
      `<td>${no}</td>` +
      `<td>
         <strong>${row.nama}</strong><br>
         <small style="color:#6b3df5;">@${row.username}</small>
       </td>` +
      `<td>${row.tujuan}</td>` +
      `<td><span class="pax-badge">${row.jumlah_peserta} Orang</span></td>` +
      `<td>${pvtFmtDate(row.tgl_berangkat)}</td>` +
      `<td>${pvtFmtDate(row.tgl_booking)}</td>` +
      `<td>${pvtTripBadge(row.status_trip)}</td>` +
      `<td>${pvtBayarBadge(row.status_bayar)}</td>` +
      `<td><a class="pvt-detail-btn" href="detail_private.php?id=${row.id_private}">Detail</a></td>`;
    tbody.appendChild(tr);
  });

  /* ---- COUNTER ---- */
  const countEl = document.getElementById('pvt-result-count');
  countEl.textContent = (total === PVT_DATA.length)
    ? `Total ${total} pengajuan`
    : `Menampilkan ${total} dari ${PVT_DATA.length} pengajuan`;

  /* ---- NO RESULT ---- */
  document.getElementById('pvt-no-result').style.display = (total === 0) ? 'block' : 'none';

  /* ---- PAGINATION ---- */
  pvtRenderPagination(totalPages);
}

/* ---- RENDER PAGINATION ---- */
function pvtRenderPagination(totalPages) {
  const pg = document.getElementById('pvt-pagination');
  pg.innerHTML = '';
  if (totalPages <= 1) return;

  const make = (label, page, disabled, active, ellipsis) => {
    const btn = document.createElement('button');
    btn.className = 'pg-btn' + (active ? ' active' : '') + (ellipsis ? ' pg-ellipsis' : '');
    btn.innerHTML = label;
    btn.disabled  = disabled;
    if (!disabled && !ellipsis) {
      btn.onclick = () => { pvtCurrentPage = page; pvtFilter(false); };
    }
    return btn;
  };

  /* Prev */
  pg.appendChild(make('&laquo;', pvtCurrentPage - 1, pvtCurrentPage === 1, false, false));

  /* Nomor halaman dengan ellipsis */
  const range = [];
  if (totalPages <= 7) {
    for (let i = 1; i <= totalPages; i++) range.push(i);
  } else {
    range.push(1);
    if (pvtCurrentPage > 4) range.push('...');
    for (let i = Math.max(2, pvtCurrentPage-1); i <= Math.min(totalPages-1, pvtCurrentPage+1); i++) range.push(i);
    if (pvtCurrentPage < totalPages - 3) range.push('...');
    range.push(totalPages);
  }
  range.forEach(r => {
    if (r === '...') pg.appendChild(make('...', 0, true, false, true));
    else pg.appendChild(make(r, r, false, r === pvtCurrentPage, false));
  });

  /* Next */
  pg.appendChild(make('&raquo;', pvtCurrentPage + 1, pvtCurrentPage === totalPages, false, false));
}

/* ---- Inisialisasi ---- */
document.addEventListener('DOMContentLoaded', function() {
  pvtFilter();
});
</script>


<!-- ============================================================
     SUB-TAB: PENGAJUAN PERUBAHAN
     ============================================================ -->
<?php elseif ($sub_tab == 'pengajuan_perubahan'): ?>

<?php
/* =============================================================
   PENGAJUAN PERUBAHAN — embed semua data ke JS, filter/sort/
   paginate di client-side persis seperti tab Pengajuan Baru.
   ============================================================= */
$res_ubah = kueri("SELECT
    up.id_ubah,
    up.id_private,
    up.nama        AS nama_baru,
    up.tujuan      AS tujuan_baru,
    up.tgl_berangkat AS tgl_berangkat_baru,
    up.tgl_pulang    AS tgl_pulang_baru,
    up.jumlah_peserta AS jumlah_baru,
    up.catatan     AS catatan_baru,
    up.tgl_pengajuan,
    pt.nama        AS nama_asli,
    pt.tujuan      AS tujuan_asli,
    pt.tgl_berangkat AS tgl_berangkat_asli,
    pt.tgl_pulang    AS tgl_pulang_asli,
    pt.jumlah_peserta AS jumlah_asli,
    a.username
FROM ubah_private up
JOIN private_trip pt ON up.id_private = pt.id_private
JOIN akun a ON pt.id_akun = a.id_akun
WHERE up.status = 0
ORDER BY up.tgl_pengajuan DESC");

$ubah_rows = [];
while ($r = ambil($res_ubah)) {
    $ubah_rows[] = [
        'id_ubah'            => (int)$r['id_ubah'],
        'id_private'         => (int)$r['id_private'],
        'nama_baru'          => $r['nama_baru'],
        'nama_asli'          => $r['nama_asli'],
        'username'           => $r['username'],
        'tujuan_baru'        => $r['tujuan_baru'],
        'tujuan_asli'        => $r['tujuan_asli'],
        'jumlah_baru'        => (int)$r['jumlah_baru'],
        'jumlah_asli'        => (int)$r['jumlah_asli'],
        'tgl_berangkat_baru' => $r['tgl_berangkat_baru'],
        'tgl_berangkat_asli' => $r['tgl_berangkat_asli'],
        'tgl_pulang_baru'    => $r['tgl_pulang_baru'],
        'tgl_pulang_asli'    => $r['tgl_pulang_asli'],
        'catatan_baru'       => $r['catatan_baru'] ?? '',
        'tgl_pengajuan'      => $r['tgl_pengajuan'],
    ];
}
?>

<!-- TOOLBAR LIVE FILTER — Pengajuan Perubahan -->
<div class="pvt-toolbar">
  <!-- SEARCH -->
  <div class="pvt-search-wrap">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#a0aec0" stroke-width="2">
      <circle cx="11" cy="11" r="8"/>
      <line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="ubah-search"
           placeholder="Cari nama akun, penanggung jawab, atau tujuan..."
           oninput="ubahFilter()">
  </div>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Filter:</span>

  <!-- FILTER DESTINASI -->
  <select id="ubah-filter-destinasi" onchange="ubahFilter()">
    <option value="">Semua Destinasi</option>
    <?php
    $dest_ubah = kueri("SELECT DISTINCT tujuan FROM private_trip ORDER BY tujuan ASC");
    while ($drow = ambil($dest_ubah)) {
        $val = htmlspecialchars(strtolower($drow['tujuan']));
        $lbl = htmlspecialchars($drow['tujuan']);
        echo "<option value=\"$val\">$lbl</option>";
    }
    ?>
  </select>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Urutkan:</span>

  <!-- SORT TANGGAL -->
  <select id="ubah-sort-date" onchange="ubahFilter()">
    <option value="">Urutkan Tanggal</option>
    <option value="ajuan-desc">Pengajuan Terbaru</option>
    <option value="ajuan-asc">Pengajuan Terlama</option>
    <option value="berangkat-desc">Berangkat Terbaru</option>
    <option value="berangkat-asc">Berangkat Terlama</option>
  </select>

  <!-- SORT PESERTA -->
  <select id="ubah-sort-pax" onchange="ubahFilter()">
    <option value="">Urutkan Peserta</option>
    <option value="pax-desc">Peserta Terbanyak</option>
    <option value="pax-asc">Peserta Paling Sedikit</option>
  </select>
</div>

<!-- INFO BAR -->
<div class="pvt-info-bar">
  <div id="ubah-result-count" class="pvt-result-count"></div>
  <div class="pvt-entries-wrap">
    <span>Tampilkan</span>
    <select id="ubah-per-page" onchange="ubahFilter()">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
    <span>entri per halaman</span>
  </div>
</div>

<!-- TABEL -->
<div class="pvt-table-wrap">
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Pemesan</th>
        <th>Destinasi / Tujuan</th>
        <th>Jumlah Peserta</th>
        <th>Rencana Tanggal</th>
        <th>Catatan Alasan</th>
        <th>Waktu Ajuan</th>
        <th style="text-align:center;">Aksi</th>
      </tr>
    </thead>
    <tbody id="ubah-table-body">
      <!-- diisi JS -->
    </tbody>
  </table>
</div>

<!-- NO RESULT -->
<div id="ubah-no-result" class="pvt-no-result">
  🔍 Tidak ada pengajuan perubahan yang sesuai dengan pencarian / filter Anda.
</div>

<!-- PAGINATION -->
<div id="ubah-pagination" class="pvt-pagination"></div>

<script>
/* ---- Data dari PHP ---- */
const UBAH_DATA = <?php echo json_encode($ubah_rows, JSON_UNESCAPED_UNICODE); ?>;

let ubahCurrentPage = 1;
let ubahFiltered    = [];

/* ---- Format tanggal dd/mm/yyyy ---- */
function ubahFmtDate(str) {
  if (!str) return '-';
  const d = new Date(str);
  const dd = String(d.getDate()).padStart(2,'0');
  const mm = String(d.getMonth()+1).padStart(2,'0');
  return dd+'/'+mm+'/'+d.getFullYear();
}

/* ---- Format datetime dd/mm/yyyy HH:MM WIB ---- */
function ubahFmtDatetime(str) {
  if (!str) return '-';
  const d = new Date(str);
  const dd  = String(d.getDate()).padStart(2,'0');
  const mm  = String(d.getMonth()+1).padStart(2,'0');
  const hh  = String(d.getHours()).padStart(2,'0');
  const min = String(d.getMinutes()).padStart(2,'0');
  return `${dd}/${mm}/${d.getFullYear()}<br><small style="color:#999;">${hh}:${min} WIB</small>`;
}

/* ---- Render sel perubahan (coret lama → baru hijau) ---- */
function ubahCellChanged(oldVal, newVal, suffix) {
  suffix = suffix || '';
  if (oldVal !== newVal && String(oldVal) !== String(newVal)) {
    return `<span style="color:#888;text-decoration:line-through;font-size:11px;display:block;margin-bottom:2px;">${oldVal}${suffix}</span>`
         + `<span style="color:#2e7d32;font-weight:bold;background:#e8f5e9;padding:2px 7px;border-radius:4px;display:inline-block;">${newVal}${suffix}</span>`;
  }
  return `<span style="color:#333;">${newVal}${suffix}</span>`;
}

/* ---- Render sel tanggal (berangkat–pulang) ---- */
function ubahCellDate(baru_b, asli_b, baru_p, asli_p) {
  const tglAsli = ubahFmtDate(asli_b) + ' - ' + ubahFmtDate(asli_p);
  const tglBaru = ubahFmtDate(baru_b) + ' - ' + ubahFmtDate(baru_p);
  if (baru_b !== asli_b || baru_p !== asli_p) {
    return `<span style="color:#888;text-decoration:line-through;font-size:11px;display:block;margin-bottom:2px;">${tglAsli}</span>`
         + `<span style="color:#2e7d32;font-weight:bold;background:#e8f5e9;padding:2px 7px;border-radius:4px;display:inline-block;font-size:12px;">${tglBaru}</span>`;
  }
  return `<span style="color:#333;font-size:13px;">${tglBaru}</span>`;
}

/* ---- UTAMA: filter + sort + paginate + render ---- */
function ubahFilter(resetPage) {
  if (resetPage === undefined) resetPage = true;
  if (resetPage) ubahCurrentPage = 1;

  const keyword    = document.getElementById('ubah-search').value.toLowerCase().trim();
  const sortDate   = document.getElementById('ubah-sort-date').value;
  const sortPax    = document.getElementById('ubah-sort-pax').value;
  const filterDest = document.getElementById('ubah-filter-destinasi').value.toLowerCase();
  const perPage    = parseInt(document.getElementById('ubah-per-page').value) || 10;

  /* ---- Filter: nama akun, nama penanggung jawab (nama_baru), tujuan ---- */
  ubahFiltered = UBAH_DATA.filter(row => {
    if (keyword && !(
      row.username.toLowerCase().includes(keyword)
      || ('@' + row.username).toLowerCase().includes(keyword)
      || row.nama_baru.toLowerCase().includes(keyword)
      || row.nama_asli.toLowerCase().includes(keyword)
      || row.tujuan_baru.toLowerCase().includes(keyword)
      || row.tujuan_asli.toLowerCase().includes(keyword)
    )) return false;

    if (filterDest && !(
      row.tujuan_baru.toLowerCase() === filterDest
      || row.tujuan_asli.toLowerCase() === filterDest
    )) return false;

    return true;
  });

  /* ---- Sort ---- */
  if (sortDate === 'ajuan-desc')
    ubahFiltered.sort((a,b) => new Date(b.tgl_pengajuan)      - new Date(a.tgl_pengajuan));
  else if (sortDate === 'ajuan-asc')
    ubahFiltered.sort((a,b) => new Date(a.tgl_pengajuan)      - new Date(b.tgl_pengajuan));
  else if (sortDate === 'berangkat-desc')
    ubahFiltered.sort((a,b) => new Date(b.tgl_berangkat_baru) - new Date(a.tgl_berangkat_baru));
  else if (sortDate === 'berangkat-asc')
    ubahFiltered.sort((a,b) => new Date(a.tgl_berangkat_baru) - new Date(b.tgl_berangkat_baru));
  else if (sortPax === 'pax-desc')
    ubahFiltered.sort((a,b) => b.jumlah_baru - a.jumlah_baru);
  else if (sortPax === 'pax-asc')
    ubahFiltered.sort((a,b) => a.jumlah_baru - b.jumlah_baru);

  /* ---- Paging ---- */
  const total      = ubahFiltered.length;
  const totalPages = Math.max(1, Math.ceil(total / perPage));
  if (ubahCurrentPage > totalPages) ubahCurrentPage = totalPages;

  const start    = (ubahCurrentPage - 1) * perPage;
  const pageData = ubahFiltered.slice(start, start + perPage);

  /* ---- Render tbody ---- */
  const tbody = document.getElementById('ubah-table-body');
  tbody.innerHTML = '';

  pageData.forEach((row, idx) => {
    const no = start + idx + 1;

    /* Nama pemesan: coret asli jika berubah */
    const namaBaru = `<strong>${row.nama_baru}</strong>` +
      (row.nama_baru !== row.nama_asli
        ? `<br><span style="color:#888;text-decoration:line-through;font-size:11px;">Asli: ${row.nama_asli}</span>`
        : '') +
      `<small style="color:#6b3df5;display:block;margin-top:2px;">@${row.username}</small>`;

    /* Catatan alasan */
    const catatan = row.catatan_baru
      ? `<span style="padding:3px 8px;background:#ffeeba;color:#856404;border-radius:5px;font-size:11px;font-weight:bold;display:inline-block;margin-bottom:4px;">Alasan</span><br>`
        + `<small style="color:#555;line-height:1.4;display:block;">${row.catatan_baru}</small>`
      : `<span style="color:#aaa;font-size:12px;">-</span>`;

    const tr = document.createElement('tr');
    tr.innerHTML =
      `<td>${no}</td>` +
      `<td>${namaBaru}</td>` +
      `<td>${ubahCellChanged(row.tujuan_asli, row.tujuan_baru, '')}</td>` +
      `<td style="text-align:center;">${ubahCellChanged(row.jumlah_asli, row.jumlah_baru, ' Orang')}</td>` +
      `<td>${ubahCellDate(row.tgl_berangkat_baru, row.tgl_berangkat_asli, row.tgl_pulang_baru, row.tgl_pulang_asli)}</td>` +
      `<td class="col-catatan">${catatan}</td>` +
      `<td style="color:#666;font-size:12px;">${ubahFmtDatetime(row.tgl_pengajuan)}</td>` +
      `<td style="text-align:center;vertical-align:middle;">
         <a class="pvt-detail-btn" href="kelola_perubahan.php?id_ubah=${row.id_ubah}">Review</a>
       </td>`;
    tbody.appendChild(tr);
  });

  /* ---- Counter ---- */
  const countEl = document.getElementById('ubah-result-count');
  countEl.textContent = (total === UBAH_DATA.length)
    ? `Total ${total} pengajuan perubahan`
    : `Menampilkan ${total} dari ${UBAH_DATA.length} pengajuan perubahan`;

  /* ---- No result ---- */
  document.getElementById('ubah-no-result').style.display = (total === 0) ? 'block' : 'none';

  /* ---- Pagination ---- */
  ubahRenderPagination(totalPages);
}

/* ---- Pagination ---- */
function ubahRenderPagination(totalPages) {
  const pg = document.getElementById('ubah-pagination');
  pg.innerHTML = '';
  if (totalPages <= 1) return;

  const make = (label, page, disabled, active, ellipsis) => {
    const btn = document.createElement('button');
    btn.className = 'pg-btn' + (active ? ' active' : '') + (ellipsis ? ' pg-ellipsis' : '');
    btn.innerHTML = label;
    btn.disabled  = disabled;
    if (!disabled && !ellipsis) {
      btn.onclick = () => { ubahCurrentPage = page; ubahFilter(false); };
    }
    return btn;
  };

  pg.appendChild(make('&laquo;', ubahCurrentPage - 1, ubahCurrentPage === 1, false, false));

  const range = [];
  if (totalPages <= 7) {
    for (let i = 1; i <= totalPages; i++) range.push(i);
  } else {
    range.push(1);
    if (ubahCurrentPage > 4) range.push('...');
    for (let i = Math.max(2, ubahCurrentPage-1); i <= Math.min(totalPages-1, ubahCurrentPage+1); i++) range.push(i);
    if (ubahCurrentPage < totalPages - 3) range.push('...');
    range.push(totalPages);
  }
  range.forEach(r => {
    if (r === '...') pg.appendChild(make('...', 0, true, false, true));
    else pg.appendChild(make(r, r, false, r === ubahCurrentPage, false));
  });

  pg.appendChild(make('&raquo;', ubahCurrentPage + 1, ubahCurrentPage === totalPages, false, false));
}

/* ---- Init ---- */
document.addEventListener('DOMContentLoaded', function() { ubahFilter(); });
</script>

<?php endif; // end sub_tab ?>


<?php elseif($menu == "payment"): ?>

<?php
  /* =============================================
     PAYMENT: ambil SEMUA data sesuai filter
     PERIODE dari server. Filter lain dikerjakan
     JS secara live — sama seperti tab Pesanan.
     ============================================= */
  $type   = $_GET['type']   ?? 'open';
  $filter = $_GET['filter'] ?? 'semua';

  /* --- WHERE untuk filter periode saja --- */
  $where_srv = [];
  if ($filter == 'hari_ini') {
      $where_srv[] = "DATE(pay.tgl_bayar) = CURDATE()";
  } elseif ($filter == 'minggu_ini') {
      $where_srv[] = "YEARWEEK(pay.tgl_bayar, 1) = YEARWEEK(CURDATE(), 1)";
  } elseif ($filter == 'bulan_ini') {
      $where_srv[] = "MONTH(pay.tgl_bayar) = MONTH(CURDATE()) AND YEAR(pay.tgl_bayar) = YEAR(CURDATE())";
  } elseif ($filter == 'tahun_ini') {
      $where_srv[] = "YEAR(pay.tgl_bayar) = YEAR(CURDATE())";
  }
  $kond_srv = count($where_srv) ? "WHERE " . implode(" AND ", $where_srv) : "";

  /* --- Query berbeda untuk Open vs Private --- */
  if ($type == 'open') {
      $sql_all = "SELECT pay.id_payment, pay.nominal, pay.tgl_bayar, pay.bukti_bayar, pay.status,
                         a.username AS nama_pemesan, tj.tujuan, b.tgl_booking, b.jumlah_peserta
                  FROM payment_open pay
                  JOIN booking b      ON pay.id_booking = b.id_booking
                  JOIN trip t         ON b.id_trip = t.id_trip
                  JOIN tujuan tj      ON t.id_tujuan = tj.id_tujuan
                  JOIN akun a         ON b.id_akun = a.id_akun
                  $kond_srv
                  ORDER BY pay.id_payment DESC";
      $link_detail_php = 'detail_payment.php';
  } else {
      $sql_all = "SELECT pay.id_payment, pay.nominal, pay.tgl_bayar, pay.bukti_bayar, pay.status,
                         a.username AS nama_pemesan, pt.tujuan, pt.tgl_booking, pt.jumlah_peserta
                  FROM payment_private pay
                  JOIN private_trip pt ON pay.id_private = pt.id_private
                  JOIN akun a          ON pt.id_akun = a.id_akun
                  $kond_srv
                  ORDER BY pay.id_payment DESC";
      $link_detail_php = 'detail_payment_private.php';
  }

  /* --- Kumpulkan ke array untuk embed JS --- */
  $res_pay  = kueri($sql_all);
  $pay_rows = [];
  while ($r = ambil($res_pay)) {
      $pay_rows[] = [
          'id_payment'     => (int)$r['id_payment'],
          'tujuan'         => $r['tujuan'],
          'nama_pemesan'   => $r['nama_pemesan'],
          'jumlah_peserta' => (int)$r['jumlah_peserta'],
          'tgl_bayar'      => $r['tgl_bayar'],
          'tgl_booking'    => $r['tgl_booking'],
          'nominal'        => (int)$r['nominal'],
          'bukti_bayar'    => $r['bukti_bayar'],
          'status'         => $r['status'],
      ];
  }

  /* --- Daftar destinasi untuk dropdown --- */
  $q_dest_pay = ($type == 'open')
      ? "SELECT DISTINCT tujuan FROM tujuan ORDER BY tujuan ASC"
      : "SELECT DISTINCT tujuan FROM private_trip ORDER BY tujuan ASC";
  $res_dest_pay = kueri($q_dest_pay);
  $dest_list_pay = [];
  while ($dd = ambil($res_dest_pay)) $dest_list_pay[] = $dd['tujuan'];
?>

<style>
/* =============================================
   PAYMENT ADMIN — seragam dengan tab Pesanan
   ============================================= */

/* TAB SWITCH OPEN / PRIVATE */
.pay-type-bar {
  display: flex;
  gap: 8px;
  margin-bottom: 14px;
}
.pay-type-bar a {
  padding: 9px 22px;
  border-radius: 10px;
  text-decoration: none !important;
  color: white;
  font-size: 13px;
  font-weight: 800;
  letter-spacing: 0.5px;
  transition: opacity 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px rgba(107,61,245,0.18);
}
.pay-type-bar a:hover { opacity: 0.88; text-decoration: none !important; }
.pay-type-bar a.active-tab {
  background: linear-gradient(135deg,#321180,#6b3df5) !important;
  box-shadow: 0 4px 14px rgba(107,61,245,0.35);
}
.pay-type-bar a.inactive-tab { background: #b0b8d1; box-shadow: none; }

/* PERIODE BAR — identik booking */
.pay-period-bar { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:12px; }
.pay-period-bar a {
  padding:8px 13px; border-radius:8px; text-decoration:none; color:white;
  font-size:13px; font-weight:700; transition:0.2s; white-space:nowrap;
}
.pay-period-bar a:hover { opacity:0.85; }

/* TOOLBAR */
.pay-toolbar {
  display:flex; gap:10px; align-items:center; flex-wrap:wrap;
  background:white; padding:13px 16px; border-radius:12px;
  box-shadow:0 2px 10px rgba(0,0,0,0.07); border:1px solid #eee;
  margin-bottom:14px;
}

/* SEARCH */
.pay-search-wrap { position:relative; flex:1; min-width:200px; }
.pay-search-wrap svg {
  position:absolute; left:11px; top:50%;
  transform:translateY(-50%); pointer-events:none;
}
.pay-toolbar input[type="text"] {
  width:100%; padding:9px 14px 9px 36px;
  border:1.5px solid #e2e8f0; border-radius:8px;
  font-size:13px; outline:none; color:#2d3748;
  background:#f8fafc; transition:border 0.2s;
}
.pay-toolbar input[type="text"]:focus { border-color:#6b3df5; background:#fff; }

/* SELECT */
.pay-toolbar select {
  padding:9px 32px 9px 11px; border:1.5px solid #e2e8f0;
  border-radius:8px; font-size:13px; outline:none;
  background:#f8fafc; color:#2d3748; cursor:pointer;
  appearance:none; -webkit-appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat:no-repeat; background-position:right 10px center;
  transition:border 0.2s; min-width:155px;
}
.pay-toolbar select:focus { border-color:#6b3df5; background-color:#fff; }

/* DIVIDER & LABEL */
.pay-toolbar .toolbar-divider { width:1px; height:30px; background:#e2e8f0; flex-shrink:0; }
.pay-toolbar .toolbar-lbl {
  font-size:11px; font-weight:700; color:#a0aec0;
  text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;
}

/* INFO BAR */
.pay-info-bar {
  display:flex; justify-content:space-between; align-items:center;
  flex-wrap:wrap; gap:10px; margin-bottom:10px; padding:0 2px;
}
.pay-result-count { font-size:12px; color:#718096; font-weight:600; }
.pay-entries-wrap {
  display:flex; align-items:center; gap:8px;
  font-size:12px; color:#718096; font-weight:600;
}
.pay-entries-wrap select {
  padding:5px 28px 5px 10px; border:1.5px solid #e2e8f0;
  border-radius:7px; font-size:12px; font-weight:700;
  outline:none; background:#f8fafc; color:#4a5568; cursor:pointer;
  appearance:none; -webkit-appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat:no-repeat; background-position:right 8px center;
}

/* NO RESULT */
.pay-no-result {
  display:none; text-align:center; padding:45px 20px;
  color:#a0aec0; background:white; border-radius:12px;
  border:1px dashed #e2e8f0; font-size:14px; margin-top:10px;
}

/* PAGINATION */
.pay-pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 5px;
  margin-top: 18px;
  flex-wrap: wrap;
}
.pay-pagination .pg-btn {
  min-width: 34px;
  height: 34px;
  padding: 0 10px;
  border: 1.5px solid #e2e8f0;
  background: white;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  color: #4a5568;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}
.pay-pagination .pg-btn:hover { background: #f0ebff; border-color: #6b3df5; color: #6b3df5; }
.pay-pagination .pg-btn.active { background: #6b3df5; color: white; border-color: #6b3df5; box-shadow: 0 3px 8px rgba(107,61,245,0.3); }
.pay-pagination .pg-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.pay-pagination .pg-btn.pg-ellipsis { cursor: default; border-color: transparent; background: transparent; color: #a0aec0; }

/* TABLE — identik .bk-table-wrap */
.pay-table-wrap {
  overflow-x:auto; border-radius:14px;
  box-shadow:0 4px 24px rgba(107,61,245,0.10),0 1.5px 6px rgba(0,0,0,0.05);
  border:1.5px solid #ede9fe; background:white;
}
.pay-table-wrap table {
  width:100%; border-collapse:collapse; margin-top:0; font-size:13.5px;
}
.pay-table-wrap table thead {
  background:linear-gradient(135deg,#6b3df5 0%,#321180 100%);
}
.pay-table-wrap table thead th {
  color:white; padding:13px 14px; font-size:12px; font-weight:700;
  text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap;
  border:none; position:relative;
}
.pay-table-wrap table thead th:not(:last-child)::after {
  content:''; position:absolute; right:0; top:25%; height:50%;
  width:1px; background:rgba(255,255,255,0.18);
}
.pay-table-wrap table tbody tr {
  border-bottom:1px solid #f0ebff;
  transition:background 0.15s, transform 0.12s;
}
.pay-table-wrap table tbody tr:last-child { border-bottom:none; }
.pay-table-wrap table tbody tr:hover {
  background:#f8f5ff; transform:scale(1.001);
  box-shadow:0 2px 8px rgba(107,61,245,0.07);
}
.pay-table-wrap table tbody tr:nth-child(even) { background:#fcfaff; }
.pay-table-wrap table tbody tr:nth-child(even):hover { background:#f3eeff; }
.pay-table-wrap table td {
  padding:11px 14px; color:#2d3748; border:none; vertical-align:middle;
}
/* No */
.pay-table-wrap table td:first-child {
  font-weight:800; color:#a0aec0; font-size:12px; text-align:center; width:40px;
}
/* Trip */
.pay-table-wrap table td:nth-child(2) { font-weight:700; color:#321180; }
/* Pemesan */
.pay-table-wrap table td:nth-child(3) { color:#4a5568; font-weight:600; }
/* Pax */
.pay-table-wrap table td:nth-child(4) { text-align:center; }
/* Tgl Bayar */
.pay-table-wrap table td:nth-child(5) { font-size:12.5px; color:#718096; white-space:nowrap; }
/* Tgl Booking */
.pay-table-wrap table td:nth-child(6) { font-size:12.5px; color:#718096; white-space:nowrap; }
/* Nominal */
.pay-table-wrap table td:nth-child(7) { font-size:12.5px; font-weight:700; white-space:nowrap; color:#2d3748; }
/* Bukti, Status, Aksi */
.pay-table-wrap table td:nth-child(8),
.pay-table-wrap table td:nth-child(9),
.pay-table-wrap table td:nth-child(10) { text-align:center; white-space:nowrap; }
/* Bukti thumbnail */
.pay-thumb {
  width:72px; height:52px; object-fit:cover;
  border-radius:7px; border:1.5px solid #e2e8f0;
  transition:transform 0.2s, box-shadow 0.2s; cursor:pointer; display:block;
}
.pay-thumb:hover { transform:scale(1.08); box-shadow:0 4px 14px rgba(107,61,245,0.2); }
</style>

<!-- ===== TAB SWITCH OPEN / PRIVATE ===== -->
<div class="pay-type-bar">
  <a href="index.php?menu=payment&type=open&filter=<?php echo $filter; ?>"
     class="<?php echo ($type=='open') ? 'active-tab' : 'inactive-tab'; ?>">
     OPEN TRIP
  </a>
  <a href="index.php?menu=payment&type=private&filter=<?php echo $filter; ?>"
     class="<?php echo ($type=='private') ? 'active-tab' : 'inactive-tab'; ?>">
     PRIVATE TRIP
  </a>
</div>

<!-- ===== BARIS PERIODE ===== -->
<div class="pay-period-bar">
<?php
$periods_pay = [
  'hari_ini'   => 'Hari Ini',
  'minggu_ini' => 'Minggu Ini',
  'bulan_ini'  => 'Bulan Ini',
  'tahun_ini'  => 'Tahun Ini',
  'semua'      => 'Semua Data',
];
foreach ($periods_pay as $k => $lbl) {
    $bg = ($filter == $k) ? '#321180' : '#6b3df5';
    echo "<a href=\"index.php?menu=payment&type=$type&filter=$k\" style=\"background:$bg;\">$lbl</a>";
}
?>
</div>

<!-- ===== TOOLBAR LIVE FILTER ===== -->
<div class="pay-toolbar">
  <!-- SEARCH -->
  <div class="pay-search-wrap">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#a0aec0" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="pay-search"
           placeholder="Cari destinasi atau nama pemesan..."
           oninput="payFilter()">
  </div>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Urutkan:</span>

  <!-- SORT NOMINAL -->
  <select id="pay-sort" onchange="payFilter()">
    <option value="">Urutkan Nominal</option>
    <option value="nom-asc">Nominal Terkecil</option>
    <option value="nom-desc">Nominal Terbesar</option>
  </select>

  <!-- SORT PAX -->
  <select id="pay-pax" onchange="payFilter()">
    <option value="">Semua Pax</option>
    <option value="pax-asc">Pax Paling Sedikit</option>
    <option value="pax-desc">Pax Terbanyak</option>
  </select>

  <!-- FILTER DESTINASI -->
  <select id="pay-destinasi" onchange="payFilter()">
    <option value="">Semua Destinasi</option>
    <?php foreach ($dest_list_pay as $dn): ?>
      <option value="<?php echo htmlspecialchars(strtolower($dn)); ?>">
        <?php echo htmlspecialchars($dn); ?>
      </option>
    <?php endforeach; ?>
  </select>

  <!-- FILTER STATUS -->
  <select id="pay-status" onchange="payFilter()">
    <option value="">Semua Status</option>
    <option value="belum diverifikasi">Belum Diverifikasi</option>
    <option value="diverifikasi">Diverifikasi</option>
    <option value="ditolak">Ditolak</option>
  </select>
</div>

<!-- ===== INFO BAR ===== -->
<div class="pay-info-bar">
  <div id="pay-result-count" class="pay-result-count"></div>
  <div class="pay-entries-wrap">
    <span>Tampilkan</span>
    <select id="pay-per-page" onchange="payFilter()">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
    <span>entri per halaman</span>
  </div>
</div>

<!-- ===== DOWNLOAD EXCEL ===== -->
<a href="export_pembayaran.php?type=<?php echo $type; ?>&filter=<?php echo $filter; ?>"
   style="display:inline-block; margin-bottom:12px; padding:8px 15px; background:green; color:white; border-radius:8px; text-decoration:none; font-size:13px; font-weight:bold;">
   Download Excel (<?php echo strtoupper($type); ?>)
</a>

<!-- ===== TABEL PAYMENT ===== -->
<div class="pay-table-wrap">
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Trip (<?php echo ucfirst($type); ?>)</th>
        <th>Nama Pemesan</th>
        <th>Jumlah Peserta</th>
        <th>Tanggal Bayar</th>
        <th>Tanggal Booking</th>
        <th>Nominal</th>
        <th>Bukti</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody id="pay-table-body">
      <!-- diisi JavaScript -->
    </tbody>
  </table>
</div>

<!-- NO RESULT -->
<div id="pay-no-result" class="pay-no-result">
  🔍 Tidak ada pembayaran yang sesuai dengan pencarian / filter Anda.
</div>

<!-- PAGINATION -->
<div id="pay-pagination" class="pay-pagination"></div>

<!-- ===== EMBED DATA PHP → JS ===== -->
<script>
const PAY_DATA        = <?php echo json_encode($pay_rows, JSON_UNESCAPED_UNICODE); ?>;
const PAY_LINK_DETAIL = <?php echo json_encode($link_detail_php); ?>;
const PAY_IMG_BASE    = '../gambar/payment/';

let payCurrentPage = 1;
let payFiltered    = [];

/* ---- STATUS BADGE ---- */
function payStatusBadge(s) {
  const lc = s.toLowerCase();
  if (lc === 'diverifikasi')
    return `<span style="color:#2c7a7b;background:#e6fffa;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #b2f5ea;white-space:nowrap;">${s}</span>`;
  if (lc === 'ditolak')
    return `<span style="color:#c53030;background:#fff5f5;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #feb2b2;white-space:nowrap;">${s}</span>`;
  /* Belum Diverifikasi */
  return `<span style="color:#b7791f;background:#fffaf0;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:800;border:1px solid #fbe3a1;white-space:nowrap;">${s}</span>`;
}

/* ---- UTAMA ---- */
function payFilter(resetPage) {
  if (resetPage === undefined) resetPage = true;
  if (resetPage) payCurrentPage = 1;

  const keyword  = document.getElementById('pay-search').value.toLowerCase().trim();
  const sortVal  = document.getElementById('pay-sort').value;
  const paxVal   = document.getElementById('pay-pax').value;
  const destVal  = document.getElementById('pay-destinasi').value.toLowerCase();
  const statVal  = document.getElementById('pay-status').value.toLowerCase();
  const perPage  = parseInt(document.getElementById('pay-per-page').value) || 10;

  /* -- Filter -- */
  payFiltered = PAY_DATA.filter(row => {
    const matchSearch = !keyword ||
                        row.tujuan.toLowerCase().includes(keyword) ||
                        row.nama_pemesan.toLowerCase().includes(keyword);
    const matchDest   = !destVal || row.tujuan.toLowerCase() === destVal;
    const matchStat   = !statVal || row.status.toLowerCase() === statVal;
    return matchSearch && matchDest && matchStat;
  });

  /* -- Sort -- */
  if      (sortVal === 'nom-asc')  payFiltered.sort((a,b) => a.nominal - b.nominal);
  else if (sortVal === 'nom-desc') payFiltered.sort((a,b) => b.nominal - a.nominal);
  else if (paxVal  === 'pax-asc')  payFiltered.sort((a,b) => a.jumlah_peserta - b.jumlah_peserta);
  else if (paxVal  === 'pax-desc') payFiltered.sort((a,b) => b.jumlah_peserta - a.jumlah_peserta);

  /* -- Paging -- */
  const total      = payFiltered.length;
  const totalPages = Math.max(1, Math.ceil(total / perPage));
  if (payCurrentPage > totalPages) payCurrentPage = totalPages;

  const start    = (payCurrentPage - 1) * perPage;
  const pageData = payFiltered.slice(start, start + perPage);

  /* -- Render -- */
  const tbody = document.getElementById('pay-table-body');
  tbody.innerHTML = '';

  pageData.forEach((row, idx) => {
    const no  = start + idx + 1;
    const tglBayar   = row.tgl_bayar   ? new Date(row.tgl_bayar).toLocaleString('id-ID',{day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '-';
    const tglBooking = row.tgl_booking ? new Date(row.tgl_booking).toLocaleDateString('id-ID',{day:'2-digit',month:'2-digit',year:'numeric'}) : '-';
    const nominal    = row.nominal.toLocaleString('id-ID');
    const imgPath    = PAY_IMG_BASE + row.bukti_bayar;

    const tr = document.createElement('tr');
    tr.innerHTML =
      `<td>${no}</td>` +
      `<td>${row.tujuan}</td>` +
      `<td>${row.nama_pemesan}</td>` +
      `<td><span class="pax-badge">${row.jumlah_peserta} pax</span></td>` +
      `<td>${tglBayar}</td>` +
      `<td>${tglBooking}</td>` +
      `<td>Rp ${nominal}</td>` +
      `<td><a href="${imgPath}" target="_blank"><img src="${imgPath}" class="pay-thumb" alt="bukti"></a></td>` +
      `<td>${payStatusBadge(row.status)}</td>` +
      `<td><a class="bk-detail-btn" href="${PAY_LINK_DETAIL}?id=${row.id_payment}">Detail</a></td>`;
    tbody.appendChild(tr);
  });

  /* -- Counter -- */
  const countEl = document.getElementById('pay-result-count');
  countEl.textContent = (total === PAY_DATA.length)
    ? `Total ${total} pembayaran`
    : `Menampilkan ${total} dari ${PAY_DATA.length} pembayaran`;

  /* -- No result -- */
  document.getElementById('pay-no-result').style.display = (total === 0) ? 'block' : 'none';

  /* -- Pagination -- */
  payRenderPagination(totalPages);
}

/* ---- PAGINATION ---- */
function payRenderPagination(totalPages) {
  const pg = document.getElementById('pay-pagination');
  pg.innerHTML = '';
  if (totalPages <= 1) return;

  const make = (label, page, disabled, active, ellipsis) => {
    const btn = document.createElement('button');
    btn.className = 'pg-btn' + (active ? ' active' : '') + (ellipsis ? ' pg-ellipsis' : '');
    btn.innerHTML = label;
    btn.disabled  = disabled;
    if (!disabled && !ellipsis) {
      btn.onclick = () => { payCurrentPage = page; payFilter(false); };
    }
    return btn;
  };

  pg.appendChild(make('&laquo;', payCurrentPage - 1, payCurrentPage === 1, false, false));

  const range = [];
  if (totalPages <= 7) {
    for (let i = 1; i <= totalPages; i++) range.push(i);
  } else {
    range.push(1);
    if (payCurrentPage > 4) range.push('...');
    for (let i = Math.max(2, payCurrentPage-1); i <= Math.min(totalPages-1, payCurrentPage+1); i++) range.push(i);
    if (payCurrentPage < totalPages - 3) range.push('...');
    range.push(totalPages);
  }
  range.forEach(r => {
    if (r === '...') pg.appendChild(make('...', 0, true, false, true));
    else pg.appendChild(make(r, r, false, r === payCurrentPage, false));
  });

  pg.appendChild(make('&raquo;', payCurrentPage + 1, payCurrentPage === totalPages, false, false));
}

document.addEventListener('DOMContentLoaded', function() { payFilter(); });
</script>

<?php elseif($menu == "peserta"): ?>

<div style="margin-bottom: 20px;">
  <a href="index.php?menu=peserta&tab=open" class="btn-tambah" style="background: <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'open') ? '#321180' : '#6b3df5'; ?>; text-decoration: none; padding: 10px 20px; border-radius: 8px; color: white; display: inline-block;">Open Trip</a>
  <a href="index.php?menu=peserta&tab=private" class="btn-tambah" style="background: <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'private') ? '#321180' : '#6b3df5'; ?>; text-decoration: none; padding: 10px 20px; border-radius: 8px; color: white; display: inline-block;">Private Trip</a>
</div>

<a href="export_peserta_open.php" 
   style="padding: 8px 15px; background: green; color: white; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold;">
   Download Open Trip
</a>

<a href="export_peserta_private.php" 
   style="padding: 8px 15px; background: orange; color: white; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold;">
   Download Private Trip
</a>

<?php
$tab = $_GET['tab'] ?? 'open';

if($tab == "open"):
  // Query untuk Peserta Open Trip
  $data_peserta = kueri("SELECT p.*, a.username, 
                (SELECT tj.tujuan 
                 FROM booking b 
                 JOIN trip t ON b.id_trip = t.id_trip 
                 JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                 WHERE b.id_akun = a.id_akun AND b.status = 'Lunas' 
                 ORDER BY t.tgl_berangkat DESC LIMIT 1) AS trip_terakhir,
                (SELECT COUNT(*) 
                 FROM booking b 
                 WHERE b.id_akun = a.id_akun AND b.status = 'Lunas') AS total_trip
                FROM peserta_open p 
                JOIN akun a ON p.id_akun = a.id_akun");
?>

<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Username</th>
      <th>Nama Lengkap</th>
      <th>No. HP</th>
      <th>Usia</th>
      <th>Alamat</th>
      <th>Riwayat Kesehatan</th>
      <th>Trip Terakhir</th>
      <th>Total Trip</th>
    </tr>
  </thead>
  <tbody>
    <?php
      $no = 1;
      while($row = ambil($data_peserta)){
        $trip = $row['trip_terakhir'] ?? '-';
        echo "<tr>";
        echo "<td>$no</td>";
        echo "<td>{$row['username']}</td>";
        echo "<td>{$row['nama']}</td>";
        echo "<td>{$row['no_hp']}</td>";
        echo "<td>{$row['usia']}</td>";
        echo "<td>{$row['alamat']}</td>";
        echo "<td>{$row['riwayat']}</td>";
        echo "<td>$trip</td>";
        echo "<td>{$row['total_trip']} Kali</td>";
        echo "</tr>";
        $no++;
      }
    ?>
  </tbody>
</table>

<?php else: ?>

<?php
  /**
   * QUERY PRIVATE TRIP:
   * Memisahkan data peserta, info trip, dan info akun pemesan.
   */
  $data_private = kueri("SELECT 
                          pp.*, 
                          pt.tujuan, 
                          pt.nama AS penanggung_jawab, 
                          a.username AS akun_pemesan 
                         FROM peserta_private pp 
                         JOIN private_trip pt ON pp.id_private = pt.id_private
                         JOIN akun a ON pt.id_akun = a.id_akun
                         ORDER BY pt.tgl_booking DESC");
?>

<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Nama Peserta</th>
      <th>Usia</th>
      <th>Alamat</th>
      <th>Riwayat Kesehatan</th>
      <th>Tujuan Trip</th>
      <th>Penanggung Jawab</th>
      <th>Username Pemesan</th>
    </tr>
  </thead>
  <tbody>
    <?php
      $no = 1;
      if(mysqli_num_rows($data_private) > 0){
        while($row = ambil($data_private)){
          echo "<tr>";
          echo "<td>$no</td>";
          echo "<td>{$row['nama']}</td>";
          echo "<td>{$row['usia']} Thn</td>";
          echo "<td>{$row['alamat']}</td>";
          echo "<td>{$row['riwayat']}</td>";
          echo "<td>{$row['tujuan']}</td>";
          echo "<td>{$row['penanggung_jawab']}</td>"; // Kolom Penanggung Jawab (dari private_trip)
          echo "<td><span style='color: #6b3df5; font-weight: bold;'>@{$row['akun_pemesan']}</span></td>"; // Kolom Username Pemesan (dari akun)
          echo "</tr>";
          $no++;
        }
      } else {
        echo "<tr><td colspan='8' align='center' style='padding: 20px;'>Belum ada data peserta untuk Private Trip.</td></tr>";
      }
    ?>
  </tbody>
</table>

<?php endif; ?>
<?php elseif($menu == "pembatalan"): ?>

<div style="margin-bottom: 20px;">
  <a href="index.php?menu=pembatalan&tab=open" class="btn-tambah" style="background: <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'open') ? '#321180' : '#6b3df5'; ?>; text-decoration: none; padding: 10px 20px; border-radius: 8px; color: white; display: inline-block;">Pembatalan Open</a>
  
  <a href="index.php?menu=pembatalan&tab=peserta" class="btn-tambah" style="background: <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'peserta') ? '#321180' : '#6b3df5'; ?>; text-decoration: none; padding: 10px 20px; border-radius: 8px; color: white; display: inline-block;">Pembatalan Peserta</a>
  
  <a href="index.php?menu=pembatalan&tab=private" class="btn-tambah" style="background: <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'private') ? '#321180' : '#6b3df5'; ?>; text-decoration: none; padding: 10px 20px; border-radius: 8px; color: white; display: inline-block;">Pembatalan Private</a>
</div>

<?php
$tab = $_GET['tab'] ?? 'open';


if($tab == "open"):
  $data_batal = kueri("SELECT bo.*, tj.tujuan, a.username, b.status AS status_booking
                       FROM batal_open bo
                       JOIN booking b ON bo.id_booking = b.id_booking
                       JOIN trip t ON b.id_trip = t.id_trip
                       JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                       JOIN akun a ON b.id_akun = a.id_akun
                       ORDER BY bo.tgl_pembatalan DESC");
?>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Username</th>
        <th>Tujuan Trip</th>
        <th>Tanggal Batal</th>
        <th>Alasan Pembatalan</th>
        <th>Status Verifikasi</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $no = 1;
        if(mysqli_num_rows($data_batal) > 0){
          while($row = ambil($data_batal)){
            $status_teks = $row['status'] ? "Disetujui" : "Menunggu Persetujuan";
            $status_color = $row['status'] ? "green" : "orange";
      ?>
        <tr>
          <td><?php echo $no; ?></td>
          <td><strong><?php echo $row['username']; ?></strong></td>
          <td><?php echo $row['tujuan']; ?></td>
          <td><?php echo date('d/m/Y H:i', strtotime($row['tgl_pembatalan'])); ?></td>
          <td><i style="font-size: 13px;"><?php echo $row['alasan']; ?></i></td>
          <td style="color: <?php echo $status_color; ?>; font-weight: bold;"><?php echo $status_teks; ?></td>
          <td><a href="detail_batal_open.php?id=<?php echo $row['id_batal']; ?>" style="color: #321180;">Kelola</a></td>
        </tr>
      <?php $no++; } } else { echo "<tr><td colspan='7' align='center' style='padding: 20px;'>Tidak ada pengajuan pembatalan open trip.</td></tr>"; } ?>
    </tbody>
  </table>


<?php elseif($tab == "peserta"): 
  $data_batal_p = kueri("SELECT bp.*, p.nama AS nama_peserta, tj.tujuan, a.username
                         FROM batal_peserta bp
                         JOIN detail d ON bp.id_detail = d.id_detail
                         JOIN peserta_open p ON d.id_peserta = p.id_peserta
                         JOIN booking b ON d.id_booking = b.id_booking
                         JOIN trip t ON b.id_trip = t.id_trip
                         JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                         JOIN akun a ON b.id_akun = a.id_akun
                         ORDER BY bp.tgl_pengajuan DESC");
?>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Username Pemesan</th>
        <th>Nama Peserta</th>
        <th>Tujuan Trip</th>
        <th>Tanggal Pengajuan</th>
        <th>Alasan</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $no = 1;
        if(mysqli_num_rows($data_batal_p) > 0){
          while($row = ambil($data_batal_p)){
            $st = $row['status_verifikasi'];
            $color = ($st == 'Disetujui') ? "green" : (($st == 'Menunggu') ? "orange" : "red");
      ?>
        <tr>
          <td><?php echo $no; ?></td>
          <td><strong><?php echo $row['username']; ?></strong></td>
          <td><?php echo $row['nama_peserta']; ?></td>
          <td><?php echo $row['tujuan']; ?></td>
          <td><?php echo date('d/m/Y H:i', strtotime($row['tgl_pengajuan'])); ?></td>
          <td><i style="font-size: 13px;"><?php echo $row['alasan_batal']; ?></i></td>
          <td style="color: <?php echo $color; ?>; font-weight: bold;"><?php echo $st; ?></td>
          <td><a href="detail_batal_peserta.php?id=<?php echo $row['id_pembatalan']; ?>" style="color: #321180;">Kelola</a></td>
        </tr>
      <?php $no++; } } else { echo "<tr><td colspan='8' align='center' style='padding: 20px;'>Tidak ada pengajuan pembatalan peserta.</td></tr>"; } ?>
    </tbody>
  </table>


<?php elseif($tab == "private"): 
  $data_batal_pr = kueri("SELECT bp.*, pt.tujuan, pt.nama AS penanggung_jawab, a.username AS akun_pemesan
                          FROM batal_private bp
                          JOIN private_trip pt ON bp.id_private = pt.id_private
                          JOIN akun a ON pt.id_akun = a.id_akun
                          ORDER BY bp.tgl_pembatalan DESC");
?>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Pemesan (Akun)</th>
        <th>Tujuan Trip</th>
        <th>Tanggal Batal</th>
        <th>Alasan Pembatalan</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $no = 1;
        if(mysqli_num_rows($data_batal_pr) > 0){
          while($row = ambil($data_batal_pr)){
            $status_teks = $row['status'] ? "Disetujui" : "Menunggu Persetujuan";
            $status_color = $row['status'] ? "green" : "orange";
      ?>
        <tr>
          <td><?php echo $no; ?></td>
          <td><?php echo $row['penanggung_jawab']; ?> (<?php echo $row['akun_pemesan']; ?>)</td>
          <td><?php echo $row['tujuan']; ?></td>
          <td><?php echo date('d/m/Y H:i', strtotime($row['tgl_pembatalan'])); ?></td>
          <td><i style="font-size: 13px;"><?php echo $row['alasan']; ?></i></td>
          <td style="color: <?php echo $status_color; ?>; font-weight: bold;"><?php echo $status_teks; ?></td>
          <td><a href="detail_batal_private.php?id=<?php echo $row['id_batal']; ?>" style="color: #321180;">Kelola</a></td>
        </tr>
      <?php $no++; } } else { echo "<tr><td colspan='7' align='center' style='padding: 20px;'>Tidak ada pengajuan pembatalan private trip.</td></tr>"; } ?>
    </tbody>
  </table>

<?php endif; ?>

<?php elseif($menu == "galeri"): ?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    
    <div>
        <h2 style="color:#321180;">Galeri Gunung</h2>
        <p style="font-size:14px; color:#666;">
            Kelola album dan foto galeri.
        </p>
    </div>

    <a href="tambah_album.php"
       style="padding:10px 15px; background:#6b3df5; color:white; border-radius:8px; text-decoration:none;">
       Tambah Album
    </a>

</div>

<?php
$data = kueri("
SELECT a.*,
(
    SELECT nama_file
    FROM galeri g
    WHERE g.id_album = a.id_album
    LIMIT 1
) AS cover
FROM album a
ORDER BY a.id_album DESC
");
?>

<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:20px;">

<?php while($row = ambil($data)): ?>

<div style="
background:white;
border-radius:12px;
overflow:hidden;
box-shadow:0 4px 10px rgba(0,0,0,0.08);
">

<img src="../gambar/galeri/<?php echo $row['cover'] ?: 'default.jpg'; ?>"
     style="width:100%; height:220px; object-fit:cover;">

<div style="padding:15px;">

<h3 style="margin-bottom:15px; color:#321180;">
    <?php echo $row['nama']; ?>
</h3>

<div style="display:flex; gap:10px;">

<a href="detail_album.php?id=<?php echo $row['id_album']; ?>"
   style="
   flex:1;
   text-align:center;
   padding:8px;
   background:#321180;
   color:white;
   border-radius:8px;
   text-decoration:none;
   ">
   Detail
</a>

<a href="hapus_album.php?id=<?php echo $row['id_album']; ?>"
   onclick="return confirm('Hapus album?')"
   style="
   flex:1;
   text-align:center;
   padding:8px;
   background:red;
   color:white;
   border-radius:8px;
   text-decoration:none;
   ">
   Hapus
</a>

</div>

</div>

</div>

<?php endwhile; ?>

</div>

  <?php elseif($menu == "laporan"): ?>

<h2 style="margin-bottom:15px;">Laporan Keuangan</h2>

<div style="display:flex; gap:15px; flex-wrap:wrap;">

  <!-- LAPORAN KEUANGAN -->
  <div style="flex:1; min-width:250px; background:white; padding:20px; border-radius:12px;">
    <h3>Laporan Keuangan Trip</h3>
    <p>Lihat total tagihan, pembayaran, dan sisa.</p>

    <a href="cetak_laporan_trip.php"
       style="display:inline-block; margin-top:10px; padding:10px 15px; background:#6b3df5; color:white; border-radius:8px; text-decoration:none;">
       Lihat & Cetak
    </a>

    <a href="export_laporan.php"
       style="display:inline-block; margin-top:10px; padding:10px 15px; background:green; color:white; border-radius:8px; text-decoration:none;">
       Export Excel
    </a>
  </div>


<?php endif; ?>

</div>
</div>
</div>

</body>
</html>
