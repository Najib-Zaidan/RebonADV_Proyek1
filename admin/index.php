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

/* ---- Ambil semua data trip + hitung terisi + meetpoint ---- */
$res_trip = kueri("SELECT t.*, tj.tujuan,
    (DATEDIFF(t.tgl_pulang, t.tgl_berangkat)+1) AS durasi,
    COALESCE((SELECT SUM(b.jumlah_peserta) FROM booking b
              WHERE b.id_trip = t.id_trip AND b.status != 'Dibatalkan'), 0) AS terisi
  FROM trip t
  JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
  ORDER BY t.tgl_berangkat DESC");

$trip_rows = [];
while ($r = ambil($res_trip)) {
    /* Kumpulkan meeting points */
    $mp_res  = kueri("SELECT kota FROM meetpoint WHERE id_trip = {$r['id_trip']}");
    $mp_list = [];
    while ($mp = ambil($mp_res)) $mp_list[] = $mp['kota'];

    $trip_rows[] = [
        'id_trip'       => (int)$r['id_trip'],
        'tujuan'        => $r['tujuan'],
        'tgl_berangkat' => $r['tgl_berangkat'],
        'tgl_pulang'    => $r['tgl_pulang'],
        'durasi'        => (int)$r['durasi'],
        'harga'         => (int)$r['harga'],
        'harga_dp'      => (int)$r['harga_dp'],
        'kuota'         => (int)$r['kuota'],
        'terisi'        => (int)$r['terisi'],
        'sisa'          => (int)$r['kuota'] - (int)$r['terisi'],
        'publik'        => (bool)$r['publik'],
        'meetpoints'    => $mp_list,
    ];
}

/* ---- Daftar destinasi unik untuk dropdown ---- */
$dest_trip = kueri("SELECT DISTINCT tj.tujuan FROM tujuan tj
                    JOIN trip t ON t.id_tujuan = tj.id_tujuan ORDER BY tj.tujuan ASC");
$dest_trip_list = [];
while ($dt = ambil($dest_trip)) $dest_trip_list[] = $dt['tujuan'];
?>

<!-- ================================================================
     CSS — OPEN TRIP — senada dengan tab Pesanan/Pembayaran/Private
     ================================================================ -->
<style>
/* ---- HEADER ROW ---- */
.trip-header-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 20px;
}
.trip-header-row h2 {
  color: #321180;
  margin-bottom: 3px;
  font-size: 20px;
}
.trip-header-row p { font-size: 13px; color: #718096; }

/* ---- TOMBOL TAMBAH TRIP ---- */
.btn-trip-tambah {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 10px 20px;
  background: linear-gradient(135deg, #6b3df5, #321180);
  color: white !important;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none !important;
  box-shadow: 0 4px 14px rgba(107,61,245,0.35);
  transition: opacity 0.18s, box-shadow 0.18s, transform 0.15s;
  white-space: nowrap;
  letter-spacing: 0.2px;
}
.btn-trip-tambah:hover {
  opacity: 0.9;
  box-shadow: 0 6px 20px rgba(107,61,245,0.45);
  transform: translateY(-1px);
  text-decoration: none !important;
}

/* ---- TOOLBAR ---- */
.trip-toolbar {
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
.trip-search-wrap {
  position: relative;
  flex: 1;
  min-width: 220px;
}
.trip-search-wrap svg {
  position: absolute;
  left: 11px; top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}
.trip-toolbar input[type="text"] {
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
.trip-toolbar input[type="text"]:focus {
  border-color: #6b3df5;
  background: #fff;
}
.trip-toolbar select {
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
  min-width: 150px;
}
.trip-toolbar select:focus { border-color: #6b3df5; background-color: #fff; }
.trip-toolbar .toolbar-divider { width: 1px; height: 30px; background: #e2e8f0; flex-shrink: 0; }
.trip-toolbar .toolbar-lbl {
  font-size: 11px; font-weight: 700; color: #a0aec0;
  text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap;
}

/* ---- INFO BAR ---- */
.trip-info-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 10px;
  padding: 0 2px;
}
.trip-result-count { font-size: 12px; color: #718096; font-weight: 600; }
.trip-entries-wrap {
  display: flex; align-items: center; gap: 8px;
  font-size: 12px; color: #718096; font-weight: 600;
}
.trip-entries-wrap select {
  padding: 5px 28px 5px 10px;
  border: 1.5px solid #e2e8f0; border-radius: 7px;
  font-size: 12px; font-weight: 700; outline: none;
  background: #f8fafc; color: #4a5568; cursor: pointer;
  appearance: none; -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 8px center;
}

/* ---- TABLE WRAP ---- */
.trip-table-wrap {
  overflow-x: auto;
  max-width: 100%;
  width: 100%;
  border-radius: 14px;
  box-shadow: 0 4px 24px rgba(107,61,245,0.10), 0 1.5px 6px rgba(0,0,0,0.05);
  border: 1.5px solid #ede9fe;
  background: white;
}
.trip-table-wrap table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 0;
  font-size: 13.5px;
}
.trip-table-wrap table thead {
  background: linear-gradient(135deg, #6b3df5 0%, #321180 100%);
}
.trip-table-wrap table thead th {
  color: white;
  padding: 13px 14px;
  font-size: 11.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
  border: none;
  position: relative;
}
.trip-table-wrap table thead th:not(:last-child)::after {
  content: '';
  position: absolute;
  right: 0; top: 25%; height: 50%;
  width: 1px;
  background: rgba(255,255,255,0.18);
}
.trip-table-wrap table tbody tr {
  border-bottom: 1px solid #f0ebff;
  transition: background 0.15s, transform 0.12s;
}
.trip-table-wrap table tbody tr:last-child { border-bottom: none; }
.trip-table-wrap table tbody tr:hover {
  background: #f8f5ff;
  transform: scale(1.001);
  box-shadow: 0 2px 8px rgba(107,61,245,0.07);
}
.trip-table-wrap table tbody tr:nth-child(even) { background: #fcfaff; }
.trip-table-wrap table tbody tr:nth-child(even):hover { background: #f3eeff; }
.trip-table-wrap table td {
  padding: 12px 14px;
  color: #2d3748;
  border: none;
  vertical-align: middle;
}

/* ---- BADGE & CHIPS ---- */
.trip-badge-publik {
  display: inline-flex; align-items: center; gap: 4px;
  background: #e8f5e9; color: #276749;
  border: 1px solid #9ae6b4;
  border-radius: 20px; padding: 3px 10px;
  font-size: 11px; font-weight: 800; white-space: nowrap;
}
.trip-badge-draft {
  display: inline-flex; align-items: center; gap: 4px;
  background: #fff8e1; color: #b7791f;
  border: 1px solid #fbd38d;
  border-radius: 20px; padding: 3px 10px;
  font-size: 11px; font-weight: 800; white-space: nowrap;
}
.trip-kuota-bar-wrap {
  min-width: 110px;
}
.trip-kuota-bar-wrap .kuota-label {
  font-size: 11.5px; font-weight: 700; color: #4a5568;
  margin-bottom: 4px; display: flex; justify-content: space-between;
}
.trip-kuota-bar {
  height: 6px; border-radius: 99px;
  background: #e9d8fd; overflow: hidden;
}
.trip-kuota-bar .kuota-fill {
  height: 100%; border-radius: 99px;
  background: linear-gradient(90deg, #6b3df5, #321180);
  transition: width 0.4s;
}

/* ---- AKSI BUTTONS ---- */
.trip-aksi-wrap {
  display: flex; gap: 5px; align-items: center; flex-wrap: nowrap; justify-content: center;
}
.trip-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 5px 11px;
  border-radius: 7px;
  font-size: 11.5px;
  font-weight: 700;
  text-decoration: none !important;
  transition: opacity 0.18s, box-shadow 0.18s, transform 0.12s;
  white-space: nowrap;
  letter-spacing: 0.1px;
  border: none;
  cursor: pointer;
  line-height: 1.3;
}
.trip-btn:hover { opacity: 0.86; transform: translateY(-1px); text-decoration: none !important; }
.trip-btn-detail {
  background: linear-gradient(135deg, #6b3df5, #321180);
  color: white !important;
  box-shadow: 0 2px 7px rgba(107,61,245,0.25);
}
.trip-btn-detail:hover { box-shadow: 0 4px 12px rgba(107,61,245,0.38); }
.trip-btn-ubah {
  background: linear-gradient(135deg, #3182ce, #1a56db);
  color: white !important;
  box-shadow: 0 2px 7px rgba(49,130,206,0.25);
}
.trip-btn-ubah:hover { box-shadow: 0 4px 12px rgba(49,130,206,0.38); }
.trip-btn-hapus {
  background: linear-gradient(135deg, #e53e3e, #c53030);
  color: white !important;
  box-shadow: 0 2px 7px rgba(229,62,62,0.25);
}
.trip-btn-hapus:hover { box-shadow: 0 4px 12px rgba(229,62,62,0.38); }

/* ---- NO RESULT ---- */
.trip-no-result {
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

/* ---- PAGINATION ---- */
.trip-pagination {
  display: flex; justify-content: center; align-items: center;
  gap: 5px; margin-top: 18px; flex-wrap: wrap;
}
.trip-pagination .pg-btn {
  min-width: 34px; height: 34px; padding: 0 10px;
  border: 1.5px solid #e2e8f0;
  background: white; border-radius: 8px;
  font-size: 13px; font-weight: 700; color: #4a5568;
  cursor: pointer; transition: all 0.2s;
  display: flex; align-items: center; justify-content: center; line-height: 1;
}
.trip-pagination .pg-btn:hover { background: #f0ebff; border-color: #6b3df5; color: #6b3df5; }
.trip-pagination .pg-btn.active {
  background: #6b3df5; color: white;
  border-color: #6b3df5;
  box-shadow: 0 3px 8px rgba(107,61,245,0.3);
}
.trip-pagination .pg-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.trip-pagination .pg-btn.pg-ellipsis {
  cursor: default; border-color: transparent;
  background: transparent; color: #a0aec0;
}
</style>

<!-- ---- HEADER ---- -->
<div class="trip-header-row">
  <div>
    <h2>Daftar Open Trip</h2>
    <p>Kelola semua paket open trip yang tersedia untuk publik.</p>
  </div>
  <a href="tambah_trip_v2.php" class="btn-trip-tambah">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Tambah Trip
  </a>
</div>

<!-- ---- TOOLBAR ---- -->
<div class="trip-toolbar">
  <!-- SEARCH -->
  <div class="trip-search-wrap">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#a0aec0" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="trip-search"
           placeholder="Cari destinasi, meeting point..."
           oninput="tripFilter()">
  </div>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Filter:</span>

  <!-- FILTER DESTINASI -->
  <select id="trip-filter-dest" onchange="tripFilter()">
    <option value="">Semua Destinasi</option>
    <?php foreach ($dest_trip_list as $d): ?>
      <option value="<?php echo htmlspecialchars(strtolower($d)); ?>"><?php echo htmlspecialchars($d); ?></option>
    <?php endforeach; ?>
  </select>

  <!-- FILTER STATUS PUBLIK -->
  <select id="trip-filter-publik" onchange="tripFilter()">
    <option value="">Semua Status</option>
    <option value="publik">Publik</option>
    <option value="draft">Draft</option>
  </select>

  <!-- FILTER KUOTA -->
  <select id="trip-filter-kuota" onchange="tripFilter()">
    <option value="">Semua Kuota</option>
    <option value="tersedia">Masih Ada Kuota</option>
    <option value="penuh">Penuh</option>
  </select>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Urutkan:</span>

  <!-- SORT -->
  <select id="trip-sort" onchange="tripFilter()">
    <option value="berangkat-asc">Berangkat Terdekat</option>
    <option value="berangkat-desc">Berangkat Terjauh</option>
    <option value="harga-asc">Harga Terendah</option>
    <option value="harga-desc">Harga Tertinggi</option>
    <option value="kuota-desc">Kuota Terbanyak</option>
    <option value="sisa-asc">Sisa Kuota Sedikit</option>
  </select>
</div>

<!-- ---- INFO BAR ---- -->
<div class="trip-info-bar">
  <div id="trip-result-count" class="trip-result-count"></div>
  <div class="trip-entries-wrap">
    <span>Tampilkan</span>
    <select id="trip-per-page" onchange="tripFilter()">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
    <span>entri per halaman</span>
  </div>
</div>

<!-- ---- TABEL ---- -->
<div class="trip-table-wrap">
  <table>
    <thead>
      <tr>
        <th style="width:40px;text-align:center;">No</th>
        <th>Destinasi</th>
        <th>Tanggal Keberangkatan</th>
        <th>Durasi</th>
        <th>Meeting Point</th>
        <th>Harga</th>
        <th>Kuota</th>
        <th style="text-align:center;">Status</th>
        <th style="text-align:center;">Aksi</th>
      </tr>
    </thead>
    <tbody id="trip-table-body">
      <!-- diisi JS -->
    </tbody>
  </table>
</div>

<!-- NO RESULT -->
<div id="trip-no-result" class="trip-no-result">
  🔍 Tidak ada trip yang sesuai dengan pencarian / filter Anda.
</div>

<!-- PAGINATION -->
<div id="trip-pagination" class="trip-pagination"></div>

<!-- ---- EMBED DATA PHP → JS ---- -->
<script>
const TRIP_DATA = <?php echo json_encode($trip_rows, JSON_UNESCAPED_UNICODE); ?>;

let tripCurrentPage = 1;
let tripFiltered    = [];

/* Format tanggal dd/mm/yyyy */
function tripFmtDate(str) {
  if (!str) return '-';
  const d = new Date(str);
  const dd = String(d.getDate()).padStart(2,'0');
  const mm = String(d.getMonth()+1).padStart(2,'0');
  return dd+'/'+mm+'/'+d.getFullYear();
}

/* Format rupiah */
function tripFmtRp(n) {
  return 'Rp ' + Number(n).toLocaleString('id-ID');
}

/* Kuota progress bar */
function tripKuotaBar(terisi, kuota) {
  const pct  = kuota > 0 ? Math.min(100, Math.round(terisi / kuota * 100)) : 0;
  const sisa = kuota - terisi;
  const fill = pct >= 90 ? '#e53e3e' : pct >= 60 ? '#dd6b20' : '#6b3df5';
  return `<div class="trip-kuota-bar-wrap">
    <div class="kuota-label">
      <span>${sisa} sisa</span>
      <span style="color:#a0aec0;">${terisi}/${kuota}</span>
    </div>
    <div class="trip-kuota-bar">
      <div class="kuota-fill" style="width:${pct}%;background:${fill};"></div>
    </div>
  </div>`;
}

/* FILTER + SORT + PAGINATE + RENDER */
function tripFilter(resetPage) {
  if (resetPage === undefined) resetPage = true;
  if (resetPage) tripCurrentPage = 1;

  const keyword   = document.getElementById('trip-search').value.toLowerCase().trim();
  const filterDst = document.getElementById('trip-filter-dest').value.toLowerCase();
  const filterPub = document.getElementById('trip-filter-publik').value;
  const filterKuo = document.getElementById('trip-filter-kuota').value;
  const sortVal   = document.getElementById('trip-sort').value;
  const perPage   = parseInt(document.getElementById('trip-per-page').value) || 10;

  tripFiltered = TRIP_DATA.filter(row => {
    /* Search: destinasi atau meeting point */
    if (keyword) {
      const inDest = row.tujuan.toLowerCase().includes(keyword);
      const inMP   = row.meetpoints.some(mp => mp.toLowerCase().includes(keyword));
      if (!inDest && !inMP) return false;
    }
    /* Filter destinasi */
    if (filterDst && row.tujuan.toLowerCase() !== filterDst) return false;
    /* Filter publik */
    if (filterPub === 'publik' && !row.publik) return false;
    if (filterPub === 'draft'  && row.publik)  return false;
    /* Filter kuota */
    if (filterKuo === 'tersedia' && row.sisa <= 0) return false;
    if (filterKuo === 'penuh'    && row.sisa > 0)  return false;

    return true;
  });

  /* Sort */
  if (sortVal === 'berangkat-asc')
    tripFiltered.sort((a,b) => new Date(a.tgl_berangkat) - new Date(b.tgl_berangkat));
  else if (sortVal === 'berangkat-desc')
    tripFiltered.sort((a,b) => new Date(b.tgl_berangkat) - new Date(a.tgl_berangkat));
  else if (sortVal === 'harga-asc')
    tripFiltered.sort((a,b) => a.harga - b.harga);
  else if (sortVal === 'harga-desc')
    tripFiltered.sort((a,b) => b.harga - a.harga);
  else if (sortVal === 'kuota-desc')
    tripFiltered.sort((a,b) => b.kuota - a.kuota);
  else if (sortVal === 'sisa-asc')
    tripFiltered.sort((a,b) => a.sisa - b.sisa);

  /* Paging */
  const total      = tripFiltered.length;
  const totalPages = Math.max(1, Math.ceil(total / perPage));
  if (tripCurrentPage > totalPages) tripCurrentPage = totalPages;
  const start    = (tripCurrentPage - 1) * perPage;
  const pageData = tripFiltered.slice(start, start + perPage);

  /* Render */
  const tbody = document.getElementById('trip-table-body');
  tbody.innerHTML = '';

  pageData.forEach((row, idx) => {
    const no = start + idx + 1;

    const tglCell = `<div style="font-weight:700;color:#321180;font-size:13px;">${tripFmtDate(row.tgl_berangkat)}</div>`
      + `<div style="font-size:11.5px;color:#a0aec0;margin-top:2px;">s/d ${tripFmtDate(row.tgl_pulang)}</div>`;

    const mpCell = row.meetpoints.length
      ? row.meetpoints.map(mp => `<span style="display:inline-block;background:#ede9fe;color:#5a1ee6;border-radius:5px;padding:2px 7px;font-size:11px;font-weight:700;margin:2px 2px 2px 0;">${mp}</span>`).join('')
      : '<span style="color:#ccc;font-size:12px;">—</span>';

    const hargaCell = `<div style="font-weight:800;color:#321180;font-size:13px;">${tripFmtRp(row.harga)}</div>`
      + `<div style="font-size:11px;color:#a0aec0;margin-top:1px;">DP: ${tripFmtRp(row.harga_dp)}</div>`;

    const statusBadge = row.publik
      ? `<span class="trip-badge-publik"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Publik</span>`
      : `<span class="trip-badge-draft"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Draft</span>`;

    const tr = document.createElement('tr');
    tr.innerHTML =
      `<td style="font-weight:800;color:#a0aec0;font-size:12px;text-align:center;">${no}</td>` +
      `<td style="font-weight:700;color:#321180;font-size:14px;">${row.tujuan}</td>` +
      `<td>${tglCell}</td>` +
      `<td style="text-align:center;"><span style="background:#ede9fe;color:#5a1ee6;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:800;border:1px solid #c4b5fd;white-space:nowrap;">${row.durasi} Hari</span></td>` +
      `<td>${mpCell}</td>` +
      `<td style="white-space:nowrap;">${hargaCell}</td>` +
      `<td>${tripKuotaBar(row.terisi, row.kuota)}</td>` +
      `<td style="text-align:center;">${statusBadge}</td>` +
      `<td>
        <div class="trip-aksi-wrap">
          <a class="trip-btn trip-btn-detail" href="detail_trip.php?id=${row.id_trip}">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Detail
          </a>
          <a class="trip-btn trip-btn-ubah" href="ubah_tripv2.php?id=${row.id_trip}">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Ubah
          </a>
          <a class="trip-btn trip-btn-hapus" href="hapus_trip.php?id=${row.id_trip}"
             onclick="return confirm('Yakin ingin menghapus trip ini?')">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            Hapus
          </a>
        </div>
      </td>`;
    tbody.appendChild(tr);
  });

  /* Counter */
  const cEl = document.getElementById('trip-result-count');
  cEl.textContent = (total === TRIP_DATA.length)
    ? `Total ${total} open trip`
    : `Menampilkan ${total} dari ${TRIP_DATA.length} open trip`;

  /* No result */
  document.getElementById('trip-no-result').style.display = (total === 0) ? 'block' : 'none';

  /* Pagination */
  tripRenderPagination(totalPages);
}

function tripRenderPagination(totalPages) {
  const pg = document.getElementById('trip-pagination');
  pg.innerHTML = '';
  if (totalPages <= 1) return;

  const make = (label, page, disabled, active, ellipsis) => {
    const btn = document.createElement('button');
    btn.className = 'pg-btn' + (active ? ' active' : '') + (ellipsis ? ' pg-ellipsis' : '');
    btn.innerHTML  = label;
    btn.disabled   = disabled;
    if (!disabled && !ellipsis) {
      btn.onclick = () => { tripCurrentPage = page; tripFilter(false); };
    }
    return btn;
  };

  pg.appendChild(make('&laquo;', tripCurrentPage - 1, tripCurrentPage === 1, false, false));

  const range = [];
  if (totalPages <= 7) {
    for (let i = 1; i <= totalPages; i++) range.push(i);
  } else {
    range.push(1);
    if (tripCurrentPage > 4) range.push('...');
    for (let i = Math.max(2, tripCurrentPage-1); i <= Math.min(totalPages-1, tripCurrentPage+1); i++) range.push(i);
    if (tripCurrentPage < totalPages - 3) range.push('...');
    range.push(totalPages);
  }
  range.forEach(r => {
    if (r === '...') pg.appendChild(make('...', 0, true, false, true));
    else pg.appendChild(make(r, r, false, r === tripCurrentPage, false));
  });

  pg.appendChild(make('&raquo;', tripCurrentPage + 1, tripCurrentPage === totalPages, false, false));
}

document.addEventListener('DOMContentLoaded', function() { tripFilter(); });
</script>


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

<?php
/* ================================================================
   PESERTA — kumpulkan data ke PHP array, embed ke JS untuk
   filter / sort / search / paginate client-side (pola yang sama
   dengan Open Trip, Pesanan, Pembayaran, dan Private Trip).
   ================================================================ */
$tab_peserta = $_GET['tab'] ?? 'open';

/* ---------- TAB OPEN ---------- */
if ($tab_peserta === 'open') {
    $res_open = kueri("SELECT
        p.id_peserta,
        p.nama,
        p.no_hp,
        p.usia,
        p.alamat,
        p.riwayat,
        a.username,
        COALESCE((SELECT tj.tujuan
                  FROM booking b
                  JOIN trip t  ON b.id_trip   = t.id_trip
                  JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                  WHERE b.id_akun = a.id_akun AND b.status = 'Lunas'
                  ORDER BY t.tgl_berangkat DESC LIMIT 1), '-') AS trip_terakhir,
        COALESCE((SELECT COUNT(*)
                  FROM booking b
                  WHERE b.id_akun = a.id_akun AND b.status = 'Lunas'), 0) AS total_trip
      FROM peserta_open p
      JOIN akun a ON p.id_akun = a.id_akun
      ORDER BY p.nama ASC");

    $open_rows = [];
    while ($r = ambil($res_open)) {
        $open_rows[] = [
            'id_peserta'   => (int)$r['id_peserta'],
            'nama'         => $r['nama'],
            'no_hp'        => $r['no_hp'],
            'usia'         => (int)$r['usia'],
            'alamat'       => $r['alamat'],
            'riwayat'      => $r['riwayat'],
            'username'     => $r['username'],
            'trip_terakhir'=> $r['trip_terakhir'],
            'total_trip'   => (int)$r['total_trip'],
        ];
    }

    /* Daftar riwayat unik untuk filter — skip null/kosong */
    $riwayat_open = array_values(array_unique(array_filter(
        array_column($open_rows, 'riwayat'),
        fn($v) => $v !== null && trim($v) !== ''
    )));
    sort($riwayat_open);

    /* Daftar destinasi unik trip terakhir */
    $dest_open = array_values(array_filter(array_unique(array_column($open_rows, 'trip_terakhir')), fn($v) => $v !== '-'));
    sort($dest_open);
}

/* ---------- TAB PRIVATE ---------- */
if ($tab_peserta === 'private') {
    $res_priv = kueri("SELECT
        pp.id_peserta,
        pp.nama,
        pp.usia,
        pp.alamat,
        pp.riwayat,
        pp.status_peserta,
        pt.tujuan,
        pt.nama AS penanggung_jawab,
        a.username AS akun_pemesan
      FROM peserta_private pp
      JOIN private_trip pt ON pp.id_private = pt.id_private
      JOIN akun a ON pt.id_akun = a.id_akun
      ORDER BY pt.tgl_booking DESC");

    $priv_rows = [];
    while ($r = ambil($res_priv)) {
        $priv_rows[] = [
            'id_peserta'      => (int)$r['id_peserta'],
            'nama'            => $r['nama'],
            'usia'            => (int)$r['usia'],
            'alamat'          => $r['alamat'],
            'riwayat'         => $r['riwayat'],
            'status_peserta'  => $r['status_peserta'],
            'tujuan'          => $r['tujuan'],
            'penanggung_jawab'=> $r['penanggung_jawab'],
            'akun_pemesan'    => $r['akun_pemesan'],
        ];
    }

    /* Filter lists */
    $riwayat_priv = array_values(array_unique(array_filter(
        array_column($priv_rows, 'riwayat'),
        fn($v) => $v !== null && trim($v) !== ''
    )));
    sort($riwayat_priv);
    $dest_priv = array_values(array_unique(array_column($priv_rows, 'tujuan')));
    sort($dest_priv);
    $status_priv = array_values(array_unique(array_column($priv_rows, 'status_peserta')));
    sort($status_priv);
}
?>

<!-- ================================================================
     CSS — PESERTA ADMIN
     ================================================================ -->
<style>
/* ---- HEADER ---- */
.pst-header-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 18px;
}
.pst-header-row h2 { color: #321180; font-size: 20px; margin-bottom: 3px; }
.pst-header-row p  { font-size: 13px; color: #718096; }

/* ---- TAB BAR ---- */
.pst-tab-bar {
  display: flex;
  gap: 0;
  border-bottom: 2.5px solid #e1d8f5;
  margin-bottom: 20px;
}
.pst-tab-bar a {
  text-decoration: none !important;
  padding: 10px 24px;
  font-weight: 800;
  font-size: 14px;
  color: #a0aec0;
  border-bottom: 3px solid transparent;
  margin-bottom: -2.5px;
  transition: color 0.2s, border-color 0.2s;
  letter-spacing: 0.2px;
}
.pst-tab-bar a:hover { color: #6b3df5; text-decoration: none !important; }
.pst-tab-active { color: #321180 !important; border-bottom-color: #321180 !important; }

/* ---- DOWNLOAD BUTTONS ---- */
.pst-dl-wrap { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
.pst-dl-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 16px; border-radius: 9px;
  font-size: 12.5px; font-weight: 700;
  text-decoration: none !important; white-space: nowrap;
  transition: opacity 0.18s, transform 0.13s, box-shadow 0.18s;
}
.pst-dl-btn:hover { opacity: 0.87; transform: translateY(-1px); text-decoration: none !important; }
.pst-dl-open {
  background: linear-gradient(135deg,#276749,#1a4731);
  color: white !important;
  box-shadow: 0 3px 10px rgba(39,103,73,0.30);
}
.pst-dl-private {
  background: linear-gradient(135deg,#c97b00,#a05e00);
  color: white !important;
  box-shadow: 0 3px 10px rgba(201,123,0,0.30);
}

/* ---- TOOLBAR ---- */
.pst-toolbar {
  display: flex; gap: 10px; align-items: center;
  flex-wrap: wrap; background: white;
  padding: 13px 16px; border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.07);
  border: 1px solid #eee; margin-bottom: 14px;
}
.pst-search-wrap { position: relative; flex: 1; min-width: 220px; }
.pst-search-wrap svg {
  position: absolute; left: 11px; top: 50%;
  transform: translateY(-50%); pointer-events: none;
}
.pst-toolbar input[type="text"] {
  width: 100%; padding: 9px 14px 9px 36px;
  border: 1.5px solid #e2e8f0; border-radius: 8px;
  font-size: 13px; outline: none; color: #2d3748;
  background: #f8fafc; transition: border 0.2s;
}
.pst-toolbar input[type="text"]:focus { border-color: #6b3df5; background: #fff; }
.pst-toolbar select {
  padding: 9px 32px 9px 11px;
  border: 1.5px solid #e2e8f0; border-radius: 8px;
  font-size: 13px; outline: none; background: #f8fafc;
  color: #2d3748; cursor: pointer;
  appearance: none; -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 10px center;
  transition: border 0.2s; min-width: 145px;
}
.pst-toolbar select:focus { border-color: #6b3df5; background-color: #fff; }
.pst-toolbar .toolbar-divider { width:1px; height:30px; background:#e2e8f0; flex-shrink:0; }
.pst-toolbar .toolbar-lbl {
  font-size:11px; font-weight:700; color:#a0aec0;
  text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;
}

/* ---- INFO BAR ---- */
.pst-info-bar {
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; gap: 10px; margin-bottom: 10px; padding: 0 2px;
}
.pst-result-count { font-size: 12px; color: #718096; font-weight: 600; }
.pst-entries-wrap {
  display: flex; align-items: center; gap: 8px;
  font-size: 12px; color: #718096; font-weight: 600;
}
.pst-entries-wrap select {
  padding: 5px 28px 5px 10px;
  border: 1.5px solid #e2e8f0; border-radius: 7px;
  font-size: 12px; font-weight: 700; outline: none;
  background: #f8fafc; color: #4a5568; cursor: pointer;
  appearance: none; -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 8px center;
}

/* ---- TABLE WRAP ---- */
.pst-table-wrap {
  overflow-x: auto; max-width: 100%; width: 100%;
  border-radius: 14px;
  box-shadow: 0 4px 24px rgba(107,61,245,0.10), 0 1.5px 6px rgba(0,0,0,0.05);
  border: 1.5px solid #ede9fe; background: white;
}
.pst-table-wrap table {
  width: 100%; border-collapse: collapse;
  margin-top: 0; font-size: 13.5px;
}
.pst-table-wrap thead {
  background: linear-gradient(135deg,#6b3df5 0%,#321180 100%);
}
.pst-table-wrap thead th {
  color: white; padding: 13px 14px;
  font-size: 11.5px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.5px;
  white-space: nowrap; border: none; position: relative;
}
.pst-table-wrap thead th:not(:last-child)::after {
  content:''; position:absolute;
  right:0; top:25%; height:50%; width:1px;
  background: rgba(255,255,255,0.18);
}
.pst-table-wrap tbody tr {
  border-bottom: 1px solid #f0ebff;
  transition: background 0.15s, transform 0.12s;
}
.pst-table-wrap tbody tr:last-child { border-bottom: none; }
.pst-table-wrap tbody tr:hover {
  background: #f8f5ff;
  transform: scale(1.001);
  box-shadow: 0 2px 8px rgba(107,61,245,0.07);
}
.pst-table-wrap tbody tr:nth-child(even)       { background: #fcfaff; }
.pst-table-wrap tbody tr:nth-child(even):hover { background: #f3eeff; }
.pst-table-wrap td {
  padding: 11px 14px; color: #2d3748;
  border: none; vertical-align: middle;
}

/* ---- BADGE STATUS PESERTA ---- */
.pst-badge {
  display: inline-flex; align-items: center; gap: 4px;
  border-radius: 20px; padding: 3px 10px;
  font-size: 11px; font-weight: 800; white-space: nowrap;
  border: 1px solid;
}
.pst-badge-aktif   { background:#e8f5e9; color:#276749; border-color:#9ae6b4; }
.pst-badge-pending { background:#fff8e1; color:#b7791f; border-color:#fbd38d; }
.pst-badge-pengajuan { background:#ebf8ff; color:#2b6cb0; border-color:#bee3f8; }

/* ---- RIWAYAT KESEHATAN BADGE ---- */
.riwayat-badge {
  display: inline-block;
  padding: 3px 9px; border-radius: 6px;
  font-size: 11.5px; font-weight: 700;
  background: #f0ebff; color: #5a1ee6;
  border: 1px solid #ddd6fe; word-break: break-word;
  max-width: 180px; white-space: normal; line-height: 1.4;
}
.riwayat-badge.none { background:#f0fff4; color:#276749; border-color:#9ae6b4; }

/* ---- TOTAL TRIP BADGE ---- */
.trip-count-badge {
  display: inline-flex; align-items: center; justify-content: center;
  width: 36px; height: 36px; border-radius: 50%;
  font-size: 13px; font-weight: 900;
  background: linear-gradient(135deg,#ede9fe,#ddd6fe);
  color: #5a1ee6; border: 2px solid #c4b5fd;
}
.trip-count-badge.zero { background:#f7fafc; color:#a0aec0; border-color:#e2e8f0; }

/* ---- NO RESULT ---- */
.pst-no-result {
  display:none; text-align:center; padding:45px 20px;
  color:#a0aec0; background:white; border-radius:12px;
  border:1px dashed #e2e8f0; font-size:14px; margin-top:10px;
}

/* ---- PAGINATION ---- */
.pst-pagination {
  display:flex; justify-content:center; align-items:center;
  gap:5px; margin-top:18px; flex-wrap:wrap;
}
.pst-pagination .pg-btn {
  min-width:34px; height:34px; padding:0 10px;
  border:1.5px solid #e2e8f0; background:white; border-radius:8px;
  font-size:13px; font-weight:700; color:#4a5568;
  cursor:pointer; transition:all 0.2s;
  display:flex; align-items:center; justify-content:center; line-height:1;
}
.pst-pagination .pg-btn:hover { background:#f0ebff; border-color:#6b3df5; color:#6b3df5; }
.pst-pagination .pg-btn.active {
  background:#6b3df5; color:white; border-color:#6b3df5;
  box-shadow:0 3px 8px rgba(107,61,245,0.3);
}
.pst-pagination .pg-btn:disabled { opacity:0.4; cursor:not-allowed; }
.pst-pagination .pg-btn.pg-ellipsis {
  cursor:default; border-color:transparent; background:transparent; color:#a0aec0;
}
</style>

<!-- ---- HEADER ---- -->
<div class="pst-header-row">
  <div>
    <h2>Data Peserta</h2>
    <p>Kelola dan pantau semua peserta Open Trip maupun Private Trip.</p>
  </div>
  <div class="pst-dl-wrap">
    <a href="export_peserta_open.php" class="pst-dl-btn pst-dl-open">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export Open Trip
    </a>
    <a href="export_peserta_private.php" class="pst-dl-btn pst-dl-private">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export Private Trip
    </a>
  </div>
</div>

<!-- ---- TAB BAR ---- -->
<div class="pst-tab-bar">
  <a href="index.php?menu=peserta&tab=open"
     class="<?php echo $tab_peserta === 'open' ? 'pst-tab-active' : ''; ?>">
     Peserta Open Trip
  </a>
  <a href="index.php?menu=peserta&tab=private"
     class="<?php echo $tab_peserta === 'private' ? 'pst-tab-active' : ''; ?>">
     Peserta Private Trip
  </a>
</div>

<?php if ($tab_peserta === 'open'): ?>
<!-- ================================================================
     TAB: OPEN TRIP
     ================================================================ -->

<!-- TOOLBAR -->
<div class="pst-toolbar">
  <div class="pst-search-wrap">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#a0aec0" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="open-search"
           placeholder="Cari nama, username, alamat, HP..."
           oninput="openFilter()">
  </div>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Filter:</span>

  <!-- Filter destinasi trip terakhir -->
  <select id="open-filter-dest" onchange="openFilter()">
    <option value="">Semua Destinasi</option>
    <?php foreach ($dest_open as $d): ?>
      <option value="<?php echo htmlspecialchars(strtolower($d)); ?>"><?php echo htmlspecialchars($d); ?></option>
    <?php endforeach; ?>
  </select>

  <!-- Filter riwayat kesehatan -->
  <select id="open-filter-riwayat" onchange="openFilter()">
    <option value="">Semua Riwayat Kesehatan</option>
    <?php foreach ($riwayat_open as $rw): ?>
      <option value="<?php echo htmlspecialchars(strtolower($rw)); ?>"><?php echo htmlspecialchars($rw); ?></option>
    <?php endforeach; ?>
  </select>

  <!-- Filter total trip -->
  <select id="open-filter-trip" onchange="openFilter()">
    <option value="">Semua Total Trip</option>
    <option value="aktif">Sudah Ada Trip (≥1)</option>
    <option value="belum">Belum Ada Trip</option>
  </select>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Urutkan:</span>

  <select id="open-sort" onchange="openFilter()">
    <option value="nama-asc">Nama A–Z</option>
    <option value="nama-desc">Nama Z–A</option>
    <option value="usia-asc">Usia Termuda</option>
    <option value="usia-desc">Usia Tertua</option>
    <option value="trip-desc">Trip Terbanyak</option>
    <option value="trip-asc">Trip Paling Sedikit</option>
  </select>
</div>

<!-- INFO BAR -->
<div class="pst-info-bar">
  <div id="open-result-count" class="pst-result-count"></div>
  <div class="pst-entries-wrap">
    <span>Tampilkan</span>
    <select id="open-per-page" onchange="openFilter()">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
    <span>entri per halaman</span>
  </div>
</div>

<!-- TABEL -->
<div class="pst-table-wrap">
  <table>
    <thead>
      <tr>
        <th style="width:40px;text-align:center;">No</th>
        <th>Peserta</th>
        <th>Kontak</th>
        <th style="text-align:center;">Usia</th>
        <th>Alamat</th>
        <th>Riwayat Kesehatan</th>
        <th>Trip Terakhir</th>
        <th style="text-align:center;">Total Trip</th>
      </tr>
    </thead>
    <tbody id="open-tbody"></tbody>
  </table>
</div>
<div id="open-no-result" class="pst-no-result">🔍 Tidak ada peserta yang sesuai.</div>
<div id="open-pagination" class="pst-pagination"></div>

<script>
const OPEN_DATA = <?php echo json_encode($open_rows, JSON_UNESCAPED_UNICODE); ?>;
let openPage = 1, openFiltered = [];

function openFilter(reset) {
  if (reset === undefined) reset = true;
  if (reset) openPage = 1;

  const kw       = document.getElementById('open-search').value.toLowerCase().trim();
  const fDest    = document.getElementById('open-filter-dest').value.toLowerCase();
  const fRiwayat = document.getElementById('open-filter-riwayat').value.toLowerCase();
  const fTrip    = document.getElementById('open-filter-trip').value;
  const sort     = document.getElementById('open-sort').value;
  const perPage  = parseInt(document.getElementById('open-per-page').value) || 10;

  openFiltered = OPEN_DATA.filter(r => {
    if (kw && !(
      r.nama.toLowerCase().includes(kw) ||
      r.username.toLowerCase().includes(kw) ||
      ('@'+r.username).toLowerCase().includes(kw) ||
      r.alamat.toLowerCase().includes(kw) ||
      r.no_hp.toLowerCase().includes(kw)
    )) return false;
    if (fDest    && r.trip_terakhir.toLowerCase() !== fDest)    return false;
    if (fRiwayat && (r.riwayat == null || r.riwayat.toLowerCase() !== fRiwayat)) return false;
    if (fTrip === 'aktif'  && r.total_trip < 1)  return false;
    if (fTrip === 'belum'  && r.total_trip >= 1) return false;
    return true;
  });

  if (sort === 'nama-asc')   openFiltered.sort((a,b)=>a.nama.localeCompare(b.nama));
  else if (sort === 'nama-desc')  openFiltered.sort((a,b)=>b.nama.localeCompare(a.nama));
  else if (sort === 'usia-asc')   openFiltered.sort((a,b)=>a.usia-b.usia);
  else if (sort === 'usia-desc')  openFiltered.sort((a,b)=>b.usia-a.usia);
  else if (sort === 'trip-desc')  openFiltered.sort((a,b)=>b.total_trip-a.total_trip);
  else if (sort === 'trip-asc')   openFiltered.sort((a,b)=>a.total_trip-b.total_trip);

  const total = openFiltered.length;
  const pages = Math.max(1, Math.ceil(total/perPage));
  if (openPage > pages) openPage = pages;
  const start = (openPage-1)*perPage;
  const slice = openFiltered.slice(start, start+perPage);

  const tbody = document.getElementById('open-tbody');
  tbody.innerHTML = '';
  slice.forEach((r,i) => {
    const no = start+i+1;
    const rBadgeClass = (r.riwayat.toLowerCase() === 'tidak ada' || r.riwayat.toLowerCase() === '-' || r.riwayat.trim() === '') ? 'none' : '';
    const tripBadgeClass = r.total_trip === 0 ? 'zero' : '';
    const destChip = r.trip_terakhir !== '-'
      ? `<span style="display:inline-block;background:#ede9fe;color:#5a1ee6;border-radius:5px;padding:2px 7px;font-size:11px;font-weight:700;border:1px solid #c4b5fd;">${r.trip_terakhir}</span>`
      : `<span style="color:#cbd5e0;font-size:12px;">Belum ada</span>`;
    const tr = document.createElement('tr');
    tr.innerHTML =
      `<td style="font-weight:800;color:#a0aec0;font-size:12px;text-align:center;">${no}</td>` +
      `<td>
        <div style="font-weight:700;color:#321180;font-size:13.5px;">${r.nama}</div>
        <div style="font-size:11.5px;color:#6b3df5;margin-top:2px;">@${r.username}</div>
      </td>` +
      `<td style="white-space:nowrap;">
        <div style="font-size:13px;font-weight:600;color:#2d3748;">📱 ${r.no_hp}</div>
      </td>` +
      `<td style="text-align:center;">
        <span style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#ede9fe,#ddd6fe);color:#5a1ee6;font-weight:900;font-size:13px;border:2px solid #c4b5fd;">${r.usia}</span>
      </td>` +
      `<td style="font-size:12.5px;color:#4a5568;max-width:160px;word-break:break-word;">${r.alamat}</td>` +
      `<td><span class="riwayat-badge ${rBadgeClass}">${r.riwayat}</span></td>` +
      `<td>${destChip}</td>` +
      `<td style="text-align:center;">
        <span class="trip-count-badge ${tripBadgeClass}">${r.total_trip}</span>
        <div style="font-size:10.5px;color:#a0aec0;margin-top:3px;">kali</div>
      </td>`;
    tbody.appendChild(tr);
  });

  document.getElementById('open-result-count').textContent =
    total === OPEN_DATA.length
      ? `Total ${total} peserta open trip`
      : `Menampilkan ${total} dari ${OPEN_DATA.length} peserta`;
  document.getElementById('open-no-result').style.display = total===0?'block':'none';
  pstRenderPagination('open-pagination', pages, openPage, p=>{openPage=p;openFilter(false);});
}

document.addEventListener('DOMContentLoaded', openFilter);
</script>

<?php else: /* tab private */ ?>
<!-- ================================================================
     TAB: PRIVATE TRIP
     ================================================================ -->

<!-- TOOLBAR -->
<div class="pst-toolbar">
  <div class="pst-search-wrap">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#a0aec0" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="priv-search"
           placeholder="Cari nama peserta, penanggung jawab, akun pemesan..."
           oninput="privFilter()">
  </div>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Filter:</span>

  <!-- Filter destinasi -->
  <select id="priv-filter-dest" onchange="privFilter()">
    <option value="">Semua Destinasi</option>
    <?php foreach ($dest_priv as $d): ?>
      <option value="<?php echo htmlspecialchars(strtolower($d)); ?>"><?php echo htmlspecialchars($d); ?></option>
    <?php endforeach; ?>
  </select>

  <!-- Filter riwayat -->
  <select id="priv-filter-riwayat" onchange="privFilter()">
    <option value="">Semua Riwayat Kesehatan</option>
    <?php foreach ($riwayat_priv as $rw): ?>
      <option value="<?php echo htmlspecialchars(strtolower($rw)); ?>"><?php echo htmlspecialchars($rw); ?></option>
    <?php endforeach; ?>
  </select>

  <!-- Filter status peserta -->
  <select id="priv-filter-status" onchange="privFilter()">
    <option value="">Semua Status</option>
    <?php foreach ($status_priv as $sp): ?>
      <option value="<?php echo htmlspecialchars(strtolower($sp)); ?>"><?php echo htmlspecialchars($sp); ?></option>
    <?php endforeach; ?>
  </select>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Urutkan:</span>

  <select id="priv-sort" onchange="privFilter()">
    <option value="nama-asc">Nama A–Z</option>
    <option value="nama-desc">Nama Z–A</option>
    <option value="usia-asc">Usia Termuda</option>
    <option value="usia-desc">Usia Tertua</option>
    <option value="dest-asc">Destinasi A–Z</option>
  </select>
</div>

<!-- INFO BAR -->
<div class="pst-info-bar">
  <div id="priv-result-count" class="pst-result-count"></div>
  <div class="pst-entries-wrap">
    <span>Tampilkan</span>
    <select id="priv-per-page" onchange="privFilter()">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
    <span>entri per halaman</span>
  </div>
</div>

<!-- TABEL -->
<div class="pst-table-wrap">
  <table>
    <thead>
      <tr>
        <th style="width:40px;text-align:center;">No</th>
        <th>Nama Peserta</th>
        <th style="text-align:center;">Usia</th>
        <th>Alamat</th>
        <th>Riwayat Kesehatan</th>
        <th>Status Peserta</th>
        <th>Destinasi Trip</th>
        <th>Penanggung Jawab</th>
        <th>Akun Pemesan</th>
      </tr>
    </thead>
    <tbody id="priv-tbody"></tbody>
  </table>
</div>
<div id="priv-no-result" class="pst-no-result">🔍 Tidak ada peserta yang sesuai.</div>
<div id="priv-pagination" class="pst-pagination"></div>

<script>
const PRIV_DATA = <?php echo json_encode($priv_rows, JSON_UNESCAPED_UNICODE); ?>;
let privPage = 1, privFiltered = [];

function privFilter(reset) {
  if (reset === undefined) reset = true;
  if (reset) privPage = 1;

  const kw      = document.getElementById('priv-search').value.toLowerCase().trim();
  const fDest   = document.getElementById('priv-filter-dest').value.toLowerCase();
  const fRw     = document.getElementById('priv-filter-riwayat').value.toLowerCase();
  const fSt     = document.getElementById('priv-filter-status').value.toLowerCase();
  const sort    = document.getElementById('priv-sort').value;
  const perPage = parseInt(document.getElementById('priv-per-page').value) || 10;

  privFiltered = PRIV_DATA.filter(r => {
    if (kw && !(
      r.nama.toLowerCase().includes(kw) ||
      r.penanggung_jawab.toLowerCase().includes(kw) ||
      r.akun_pemesan.toLowerCase().includes(kw) ||
      ('@'+r.akun_pemesan).toLowerCase().includes(kw) ||
      r.alamat.toLowerCase().includes(kw)
    )) return false;
    if (fDest && r.tujuan.toLowerCase() !== fDest) return false;
    if (fRw   && (r.riwayat == null || r.riwayat.toLowerCase() !== fRw))  return false;
    if (fSt   && r.status_peserta.toLowerCase() !== fSt) return false;
    return true;
  });

  if (sort === 'nama-asc')   privFiltered.sort((a,b)=>a.nama.localeCompare(b.nama));
  else if (sort === 'nama-desc') privFiltered.sort((a,b)=>b.nama.localeCompare(a.nama));
  else if (sort === 'usia-asc')  privFiltered.sort((a,b)=>a.usia-b.usia);
  else if (sort === 'usia-desc') privFiltered.sort((a,b)=>b.usia-a.usia);
  else if (sort === 'dest-asc')  privFiltered.sort((a,b)=>a.tujuan.localeCompare(b.tujuan));

  const total = privFiltered.length;
  const pages = Math.max(1, Math.ceil(total/perPage));
  if (privPage > pages) privPage = pages;
  const start = (privPage-1)*perPage;
  const slice = privFiltered.slice(start, start+perPage);

  const tbody = document.getElementById('priv-tbody');
  tbody.innerHTML = '';
  slice.forEach((r,i) => {
    const no = start+i+1;
    /* status badge */
    const lc = r.status_peserta.toLowerCase();
    const badgeClass = lc==='aktif' ? 'pst-badge-aktif'
                     : lc==='pending hapus' ? 'pst-badge-pending'
                     : 'pst-badge-pengajuan';
    const dot = lc==='aktif' ? '🟢' : lc==='pending hapus' ? '🟡' : '🔵';
    const rBadgeClass = (r.riwayat.toLowerCase() === 'tidak ada' || r.riwayat.toLowerCase() === '-' || r.riwayat.trim() === '') ? 'none' : '';
    const tr = document.createElement('tr');
    tr.innerHTML =
      `<td style="font-weight:800;color:#a0aec0;font-size:12px;text-align:center;">${no}</td>` +
      `<td style="font-weight:700;color:#321180;font-size:13.5px;">${r.nama}</td>` +
      `<td style="text-align:center;">
        <span style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#ede9fe,#ddd6fe);color:#5a1ee6;font-weight:900;font-size:13px;border:2px solid #c4b5fd;">${r.usia}</span>
      </td>` +
      `<td style="font-size:12.5px;color:#4a5568;max-width:150px;word-break:break-word;">${r.alamat}</td>` +
      `<td><span class="riwayat-badge ${rBadgeClass}">${r.riwayat}</span></td>` +
      `<td style="text-align:center;"><span class="pst-badge ${badgeClass}">${dot} ${r.status_peserta}</span></td>` +
      `<td><span style="display:inline-block;background:#ede9fe;color:#5a1ee6;border-radius:5px;padding:2px 7px;font-size:11px;font-weight:700;border:1px solid #c4b5fd;">${r.tujuan}</span></td>` +
      `<td style="font-weight:600;color:#2d3748;font-size:13px;">${r.penanggung_jawab}</td>` +
      `<td><span style="color:#6b3df5;font-weight:800;font-size:13px;">@${r.akun_pemesan}</span></td>`;
    tbody.appendChild(tr);
  });

  document.getElementById('priv-result-count').textContent =
    total === PRIV_DATA.length
      ? `Total ${total} peserta private trip`
      : `Menampilkan ${total} dari ${PRIV_DATA.length} peserta`;
  document.getElementById('priv-no-result').style.display = total===0?'block':'none';
  pstRenderPagination('priv-pagination', pages, privPage, p=>{privPage=p;privFilter(false);});
}

document.addEventListener('DOMContentLoaded', privFilter);
</script>

<?php endif; /* end tab */ ?>

<!-- Shared pagination helper -->
<script>
function pstRenderPagination(containerId, totalPages, currentPage, onPageClick) {
  const pg = document.getElementById(containerId);
  if (!pg) return;
  pg.innerHTML = '';
  if (totalPages <= 1) return;

  const make = (label, page, disabled, active, ellipsis) => {
    const btn = document.createElement('button');
    btn.className = 'pg-btn' + (active?' active':'') + (ellipsis?' pg-ellipsis':'');
    btn.innerHTML = label;
    btn.disabled  = disabled;
    if (!disabled && !ellipsis) btn.onclick = () => onPageClick(page);
    return btn;
  };

  pg.appendChild(make('&laquo;', currentPage-1, currentPage===1, false, false));
  const range = [];
  if (totalPages <= 7) { for(let i=1;i<=totalPages;i++) range.push(i); }
  else {
    range.push(1);
    if (currentPage > 4) range.push('...');
    for(let i=Math.max(2,currentPage-1);i<=Math.min(totalPages-1,currentPage+1);i++) range.push(i);
    if (currentPage < totalPages-3) range.push('...');
    range.push(totalPages);
  }
  range.forEach(r => {
    if (r==='...') pg.appendChild(make('...', 0, true, false, true));
    else pg.appendChild(make(r, r, false, r===currentPage, false));
  });
  pg.appendChild(make('&raquo;', currentPage+1, currentPage===totalPages, false, false));
}
</script>
<?php elseif($menu == "pembatalan"): ?>

<?php
/* ================================================================
   PEMBATALAN — kumpulkan semua data ke PHP array, embed ke JS
   untuk filter / sort / search / paginate client-side.
   Tiga tab: open | peserta | private
   ================================================================ */
$batal_tab = $_GET['tab'] ?? 'open';

/* ---------- TAB OPEN ---------- */
if ($batal_tab === 'open') {
    $res_bo = kueri("SELECT
        bo.id_batal,
        bo.tgl_pembatalan,
        bo.alasan,
        bo.status,
        tj.tujuan,
        a.username,
        b.status AS status_booking
      FROM batal_open bo
      JOIN booking b   ON bo.id_booking = b.id_booking
      JOIN trip t      ON b.id_trip     = t.id_trip
      JOIN tujuan tj   ON t.id_tujuan   = tj.id_tujuan
      JOIN akun a      ON b.id_akun     = a.id_akun
      ORDER BY bo.tgl_pembatalan DESC");
    $bo_rows = [];
    while ($r = ambil($res_bo)) {
        $bo_rows[] = [
            'id_batal'        => (int)$r['id_batal'],
            'tgl_pembatalan'  => $r['tgl_pembatalan'],
            'alasan'          => $r['alasan'],
            'status'          => (bool)$r['status'],
            'tujuan'          => $r['tujuan'],
            'username'        => $r['username'],
            'status_booking'  => $r['status_booking'],
        ];
    }
    /* Filter lists */
    $bo_dest   = array_values(array_unique(array_column($bo_rows, 'tujuan')));
    sort($bo_dest);
}

/* ---------- TAB PESERTA ---------- */
if ($batal_tab === 'peserta') {
    $res_bp = kueri("SELECT
        bp.id_pembatalan,
        bp.alasan_batal,
        bp.tgl_pengajuan,
        bp.tgl_verifikasi,
        bp.status_verifikasi,
        p.nama  AS nama_peserta,
        tj.tujuan,
        a.username
      FROM batal_peserta bp
      JOIN detail d         ON bp.id_detail  = d.id_detail
      JOIN peserta_open p   ON d.id_peserta   = p.id_peserta
      JOIN booking b        ON d.id_booking   = b.id_booking
      JOIN trip t           ON b.id_trip      = t.id_trip
      JOIN tujuan tj        ON t.id_tujuan    = tj.id_tujuan
      JOIN akun a           ON b.id_akun      = a.id_akun
      ORDER BY bp.tgl_pengajuan DESC");
    $bp_rows = [];
    while ($r = ambil($res_bp)) {
        $bp_rows[] = [
            'id_pembatalan'    => (int)$r['id_pembatalan'],
            'alasan_batal'     => $r['alasan_batal'],
            'tgl_pengajuan'    => $r['tgl_pengajuan'],
            'tgl_verifikasi'   => $r['tgl_verifikasi'],
            'status_verifikasi'=> $r['status_verifikasi'],
            'nama_peserta'     => $r['nama_peserta'],
            'tujuan'           => $r['tujuan'],
            'username'         => $r['username'],
        ];
    }
    /* Filter lists */
    $bp_dest   = array_values(array_unique(array_column($bp_rows, 'tujuan')));
    sort($bp_dest);
}

/* ---------- TAB PRIVATE ---------- */
if ($batal_tab === 'private') {
    $res_bpr = kueri("SELECT
        bpr.id_batal,
        bpr.tgl_pembatalan,
        bpr.alasan,
        bpr.status,
        pt.tujuan,
        pt.nama   AS penanggung_jawab,
        pt.no_hp,
        pt.jumlah_peserta,
        a.username AS akun_pemesan
      FROM batal_private bpr
      JOIN private_trip pt ON bpr.id_private = pt.id_private
      JOIN akun a          ON pt.id_akun     = a.id_akun
      ORDER BY bpr.tgl_pembatalan DESC");
    $bpr_rows = [];
    while ($r = ambil($res_bpr)) {
        $bpr_rows[] = [
            'id_batal'         => (int)$r['id_batal'],
            'tgl_pembatalan'   => $r['tgl_pembatalan'],
            'alasan'           => $r['alasan'],
            'status'           => (bool)$r['status'],
            'tujuan'           => $r['tujuan'],
            'penanggung_jawab' => $r['penanggung_jawab'],
            'no_hp'            => $r['no_hp'],
            'jumlah_peserta'   => (int)$r['jumlah_peserta'],
            'akun_pemesan'     => $r['akun_pemesan'],
        ];
    }
    /* Filter lists */
    $bpr_dest = array_values(array_unique(array_column($bpr_rows, 'tujuan')));
    sort($bpr_dest);
}
?>

<!-- ================================================================
     CSS — PEMBATALAN (selaras dengan tab Peserta, Pesanan, dll.)
     ================================================================ -->
<style>
/* ---- HEADER ROW ---- */
.btl-header-row {
  display:flex; justify-content:space-between; align-items:flex-start;
  flex-wrap:wrap; gap:12px; margin-bottom:18px;
}
.btl-header-row h2 { color:#321180; font-size:20px; margin-bottom:3px; }
.btl-header-row p  { font-size:13px; color:#718096; }

/* ---- STAT CHIPS ---- */
.btl-stat-chips { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px; }
.btl-stat-chip {
  display:inline-flex; align-items:center; gap:7px;
  padding:8px 16px; border-radius:10px;
  font-size:12.5px; font-weight:700; border:1.5px solid;
}
.btl-chip-total   { background:#f0ebff; color:#5a1ee6; border-color:#c4b5fd; }
.btl-chip-menunggu{ background:#fffaf0; color:#b7791f; border-color:#fbd38d; }
.btl-chip-selesai { background:#f0fff4; color:#276749; border-color:#9ae6b4; }

/* ---- TAB BAR ---- */
.btl-tab-bar {
  display:flex; gap:0;
  border-bottom:2.5px solid #e1d8f5;
  margin-bottom:20px;
}
.btl-tab-bar a {
  text-decoration:none !important;
  padding:10px 26px; font-weight:800; font-size:14px;
  color:#a0aec0; border-bottom:3px solid transparent;
  margin-bottom:-2.5px;
  transition:color 0.2s, border-color 0.2s;
  letter-spacing:0.2px; display:inline-flex; align-items:center; gap:7px;
}
.btl-tab-bar a:hover { color:#6b3df5; text-decoration:none !important; }
.btl-tab-active { color:#321180 !important; border-bottom-color:#321180 !important; }
.btl-tab-count {
  display:inline-flex; align-items:center; justify-content:center;
  min-width:20px; height:20px; padding:0 5px;
  border-radius:20px; font-size:10.5px; font-weight:900;
  background:#e9d8fd; color:#5a1ee6;
}
.btl-tab-active .btl-tab-count { background:#321180; color:white; }

/* ---- TOOLBAR ---- */
.btl-toolbar {
  display:flex; gap:10px; align-items:center;
  flex-wrap:wrap; background:white;
  padding:13px 16px; border-radius:12px;
  box-shadow:0 2px 10px rgba(0,0,0,0.07);
  border:1px solid #eee; margin-bottom:14px;
}
.btl-search-wrap { position:relative; flex:1; min-width:220px; }
.btl-search-wrap svg {
  position:absolute; left:11px; top:50%;
  transform:translateY(-50%); pointer-events:none;
}
.btl-toolbar input[type="text"] {
  width:100%; padding:9px 14px 9px 36px;
  border:1.5px solid #e2e8f0; border-radius:8px;
  font-size:13px; outline:none; color:#2d3748;
  background:#f8fafc; transition:border 0.2s;
}
.btl-toolbar input[type="text"]:focus { border-color:#6b3df5; background:#fff; }
.btl-toolbar select {
  padding:9px 32px 9px 11px;
  border:1.5px solid #e2e8f0; border-radius:8px;
  font-size:13px; outline:none; background:#f8fafc;
  color:#2d3748; cursor:pointer;
  appearance:none; -webkit-appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat:no-repeat; background-position:right 10px center;
  transition:border 0.2s; min-width:145px;
}
.btl-toolbar select:focus { border-color:#6b3df5; background-color:#fff; }
.btl-toolbar .toolbar-divider { width:1px; height:30px; background:#e2e8f0; flex-shrink:0; }
.btl-toolbar .toolbar-lbl {
  font-size:11px; font-weight:700; color:#a0aec0;
  text-transform:uppercase; letter-spacing:0.4px; white-space:nowrap;
}

/* ---- INFO BAR ---- */
.btl-info-bar {
  display:flex; justify-content:space-between; align-items:center;
  flex-wrap:wrap; gap:10px; margin-bottom:10px; padding:0 2px;
}
.btl-result-count { font-size:12px; color:#718096; font-weight:600; }
.btl-entries-wrap {
  display:flex; align-items:center; gap:8px;
  font-size:12px; color:#718096; font-weight:600;
}
.btl-entries-wrap select {
  padding:5px 28px 5px 10px;
  border:1.5px solid #e2e8f0; border-radius:7px;
  font-size:12px; font-weight:700; outline:none;
  background:#f8fafc; color:#4a5568; cursor:pointer;
  appearance:none; -webkit-appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b3df5' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat:no-repeat; background-position:right 8px center;
}

/* ---- TABLE WRAP ---- */
.btl-table-wrap {
  overflow-x:auto; max-width:100%; width:100%;
  border-radius:14px;
  box-shadow:0 4px 24px rgba(107,61,245,0.10),0 1.5px 6px rgba(0,0,0,0.05);
  border:1.5px solid #ede9fe; background:white;
}
.btl-table-wrap table {
  width:100%; border-collapse:collapse;
  margin-top:0; font-size:13.5px;
}
.btl-table-wrap thead {
  background:linear-gradient(135deg,#6b3df5 0%,#321180 100%);
}
.btl-table-wrap thead th {
  color:white; padding:13px 14px;
  font-size:11.5px; font-weight:700;
  text-transform:uppercase; letter-spacing:0.5px;
  white-space:nowrap; border:none; position:relative;
}
.btl-table-wrap thead th:not(:last-child)::after {
  content:''; position:absolute;
  right:0; top:25%; height:50%; width:1px;
  background:rgba(255,255,255,0.18);
}
.btl-table-wrap tbody tr {
  border-bottom:1px solid #f0ebff;
  transition:background 0.15s,transform 0.12s;
}
.btl-table-wrap tbody tr:last-child { border-bottom:none; }
.btl-table-wrap tbody tr:hover {
  background:#f8f5ff;
  transform:scale(1.001);
  box-shadow:0 2px 8px rgba(107,61,245,0.07);
}
.btl-table-wrap tbody tr:nth-child(even)       { background:#fcfaff; }
.btl-table-wrap tbody tr:nth-child(even):hover { background:#f3eeff; }
.btl-table-wrap td {
  padding:11px 14px; color:#2d3748;
  border:none; vertical-align:middle;
}

/* ---- STATUS BADGES ---- */
.btl-badge {
  display:inline-flex; align-items:center; gap:5px;
  border-radius:20px; padding:4px 11px;
  font-size:11px; font-weight:800; white-space:nowrap; border:1px solid;
}
.btl-badge-menunggu  { background:#fffaf0; color:#b7791f; border-color:#fbd38d; }
.btl-badge-disetujui { background:#f0fff4; color:#276749; border-color:#9ae6b4; }
.btl-badge-ditolak   { background:#fff5f5; color:#c53030; border-color:#feb2b2; }

/* ---- ALASAN CHIP ---- */
.btl-alasan {
  display:inline-block; max-width:220px;
  background:#f8f5ff; border:1px solid #e9d8fd;
  border-radius:7px; padding:5px 9px;
  font-size:12px; color:#4a5568; line-height:1.45;
  word-break:break-word; white-space:normal;
  font-style:italic;
}

/* ---- AKSI TOMBOL ---- */
.btl-aksi-wrap {
  display:flex; gap:5px; align-items:center;
  flex-wrap:nowrap; justify-content:center;
}
.btl-btn {
  display:inline-flex; align-items:center; gap:4px;
  padding:5px 12px; border-radius:7px;
  font-size:11.5px; font-weight:700;
  text-decoration:none !important;
  transition:opacity 0.18s,box-shadow 0.18s,transform 0.12s;
  white-space:nowrap; letter-spacing:0.1px;
  border:none; cursor:pointer; line-height:1.3;
}
.btl-btn:hover { opacity:0.86; transform:translateY(-1px); text-decoration:none !important; }
.btl-btn-kelola {
  background:linear-gradient(135deg,#6b3df5,#321180);
  color:white !important;
  box-shadow:0 2px 8px rgba(107,61,245,0.28);
}
.btl-btn-kelola:hover { box-shadow:0 4px 14px rgba(107,61,245,0.40); }
.btl-btn-setujui {
  background:linear-gradient(135deg,#38a169,#276749);
  color:white !important;
  box-shadow:0 2px 8px rgba(56,161,105,0.28);
}
.btl-btn-setujui:hover { box-shadow:0 4px 14px rgba(56,161,105,0.40); }

/* ---- NO RESULT ---- */
.btl-no-result {
  display:none; text-align:center; padding:45px 20px;
  color:#a0aec0; background:white; border-radius:12px;
  border:1px dashed #e2e8f0; font-size:14px; margin-top:10px;
}

/* ---- PAGINATION ---- */
.btl-pagination {
  display:flex; justify-content:center; align-items:center;
  gap:5px; margin-top:18px; flex-wrap:wrap;
}
.btl-pagination .pg-btn {
  min-width:34px; height:34px; padding:0 10px;
  border:1.5px solid #e2e8f0; background:white; border-radius:8px;
  font-size:13px; font-weight:700; color:#4a5568;
  cursor:pointer; transition:all 0.2s;
  display:flex; align-items:center; justify-content:center; line-height:1;
}
.btl-pagination .pg-btn:hover { background:#f0ebff; border-color:#6b3df5; color:#6b3df5; }
.btl-pagination .pg-btn.active {
  background:#6b3df5; color:white; border-color:#6b3df5;
  box-shadow:0 3px 8px rgba(107,61,245,0.30);
}
.btl-pagination .pg-btn:disabled { opacity:0.4; cursor:not-allowed; }
.btl-pagination .pg-btn.pg-ellipsis {
  cursor:default; border-color:transparent; background:transparent; color:#a0aec0;
}
</style>

<!-- ---- HEADER ---- -->
<div class="btl-header-row">
  <div>
    <h2>Pembatalan Trip</h2>
    <p>Kelola semua pengajuan pembatalan Open Trip, Peserta, dan Private Trip.</p>
  </div>
</div>

<!-- ---- TAB BAR ---- -->
<div class="btl-tab-bar">
  <a href="index.php?menu=pembatalan&tab=open"
     class="<?php echo $batal_tab === 'open' ? 'btl-tab-active' : ''; ?>">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
    Pembatalan Open
    <?php if ($batal_tab === 'open'): ?>
      <span class="btl-tab-count"><?php echo count($bo_rows); ?></span>
    <?php endif; ?>
  </a>
  <a href="index.php?menu=pembatalan&tab=peserta"
     class="<?php echo $batal_tab === 'peserta' ? 'btl-tab-active' : ''; ?>">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    Pembatalan Peserta
    <?php if ($batal_tab === 'peserta'): ?>
      <span class="btl-tab-count"><?php echo count($bp_rows); ?></span>
    <?php endif; ?>
  </a>
  <a href="index.php?menu=pembatalan&tab=private"
     class="<?php echo $batal_tab === 'private' ? 'btl-tab-active' : ''; ?>">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
    Pembatalan Private
    <?php if ($batal_tab === 'private'): ?>
      <span class="btl-tab-count"><?php echo count($bpr_rows); ?></span>
    <?php endif; ?>
  </a>
</div>

<?php if ($batal_tab === 'open'): ?>
<!-- ================================================================
     TAB: PEMBATALAN OPEN TRIP
     ================================================================ -->

<!-- STAT CHIPS -->
<?php
  $bo_menunggu = count(array_filter($bo_rows, fn($r) => !$r['status']));
  $bo_selesai  = count(array_filter($bo_rows, fn($r) =>  $r['status']));
?>
<div class="btl-stat-chips">
  <div class="btl-stat-chip btl-chip-total">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    Total: <?php echo count($bo_rows); ?>
  </div>
  <div class="btl-stat-chip btl-chip-menunggu">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    Menunggu: <?php echo $bo_menunggu; ?>
  </div>
  <div class="btl-stat-chip btl-chip-selesai">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    Disetujui: <?php echo $bo_selesai; ?>
  </div>
</div>

<!-- TOOLBAR -->
<div class="btl-toolbar">
  <div class="btl-search-wrap">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#a0aec0" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="bo-search" placeholder="Cari username atau alasan pembatalan..." oninput="boFilter()">
  </div>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Filter:</span>

  <select id="bo-filter-dest" onchange="boFilter()">
    <option value="">Semua Destinasi</option>
    <?php foreach ($bo_dest as $d): ?>
      <option value="<?php echo htmlspecialchars(strtolower($d)); ?>"><?php echo htmlspecialchars($d); ?></option>
    <?php endforeach; ?>
  </select>

  <select id="bo-filter-status" onchange="boFilter()">
    <option value="">Semua Status</option>
    <option value="menunggu">Menunggu Persetujuan</option>
    <option value="disetujui">Disetujui</option>
  </select>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Urutkan:</span>

  <select id="bo-sort" onchange="boFilter()">
    <option value="tgl-desc">Terbaru</option>
    <option value="tgl-asc">Terlama</option>
    <option value="dest-asc">Destinasi A–Z</option>
    <option value="user-asc">Username A–Z</option>
  </select>
</div>

<!-- INFO BAR -->
<div class="btl-info-bar">
  <div id="bo-result-count" class="btl-result-count"></div>
  <div class="btl-entries-wrap">
    <span>Tampilkan</span>
    <select id="bo-per-page" onchange="boFilter()">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
    <span>entri per halaman</span>
  </div>
</div>

<!-- TABEL -->
<div class="btl-table-wrap">
  <table>
    <thead>
      <tr>
        <th style="width:40px;text-align:center;">No</th>
        <th>Akun Pemesan</th>
        <th>Destinasi Trip</th>
        <th>Tanggal Batal</th>
        <th>Alasan Pembatalan</th>
        <th style="text-align:center;">Status</th>
        <th style="text-align:center;">Aksi</th>
      </tr>
    </thead>
    <tbody id="bo-tbody"></tbody>
  </table>
</div>
<div id="bo-no-result" class="btl-no-result">🔍 Tidak ada pembatalan yang sesuai dengan pencarian / filter Anda.</div>
<div id="bo-pagination" class="btl-pagination"></div>

<script>
const BO_DATA = <?php echo json_encode($bo_rows, JSON_UNESCAPED_UNICODE); ?>;
let boPage = 1, boFiltered = [];

function boFmtDatetime(str) {
  if (!str) return '-';
  const d = new Date(str);
  const dd = String(d.getDate()).padStart(2,'0');
  const mm = String(d.getMonth()+1).padStart(2,'0');
  const hh = String(d.getHours()).padStart(2,'0');
  const mn = String(d.getMinutes()).padStart(2,'0');
  return `${dd}/${mm}/${d.getFullYear()} <span style="color:#a0aec0;font-size:11px;">${hh}:${mn}</span>`;
}

function boStatusBadge(status) {
  return status
    ? `<span class="btl-badge btl-badge-disetujui"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Disetujui</span>`
    : `<span class="btl-badge btl-badge-menunggu"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Menunggu</span>`;
}

function boFilter(reset) {
  if (reset === undefined) reset = true;
  if (reset) boPage = 1;

  const kw      = document.getElementById('bo-search').value.toLowerCase().trim();
  const fDest   = document.getElementById('bo-filter-dest').value.toLowerCase();
  const fStatus = document.getElementById('bo-filter-status').value;
  const sort    = document.getElementById('bo-sort').value;
  const perPage = parseInt(document.getElementById('bo-per-page').value) || 10;

  boFiltered = BO_DATA.filter(r => {
    if (kw && !(
      r.username.toLowerCase().includes(kw) ||
      ('@'+r.username).toLowerCase().includes(kw) ||
      r.alasan.toLowerCase().includes(kw) ||
      r.tujuan.toLowerCase().includes(kw)
    )) return false;
    if (fDest   && r.tujuan.toLowerCase() !== fDest) return false;
    if (fStatus === 'menunggu'  && r.status)  return false;
    if (fStatus === 'disetujui' && !r.status) return false;
    return true;
  });

  if (sort === 'tgl-desc')  boFiltered.sort((a,b)=>new Date(b.tgl_pembatalan)-new Date(a.tgl_pembatalan));
  else if (sort === 'tgl-asc')  boFiltered.sort((a,b)=>new Date(a.tgl_pembatalan)-new Date(b.tgl_pembatalan));
  else if (sort === 'dest-asc') boFiltered.sort((a,b)=>a.tujuan.localeCompare(b.tujuan));
  else if (sort === 'user-asc') boFiltered.sort((a,b)=>a.username.localeCompare(b.username));

  const total = boFiltered.length;
  const pages = Math.max(1, Math.ceil(total/perPage));
  if (boPage > pages) boPage = pages;
  const start = (boPage-1)*perPage;
  const slice = boFiltered.slice(start, start+perPage);

  const tbody = document.getElementById('bo-tbody');
  tbody.innerHTML = '';
  slice.forEach((r,i) => {
    const no = start+i+1;
    const tr = document.createElement('tr');
    tr.innerHTML =
      `<td style="font-weight:800;color:#a0aec0;font-size:12px;text-align:center;">${no}</td>` +
      `<td>
        <div style="font-weight:700;color:#321180;font-size:13.5px;">@${r.username}</div>
      </td>` +
      `<td>
        <span style="display:inline-block;background:#ede9fe;color:#5a1ee6;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700;border:1px solid #c4b5fd;">${r.tujuan}</span>
      </td>` +
      `<td style="white-space:nowrap;font-size:13px;color:#2d3748;">${boFmtDatetime(r.tgl_pembatalan)}</td>` +
      `<td><span class="btl-alasan">${r.alasan}</span></td>` +
      `<td style="text-align:center;">${boStatusBadge(r.status)}</td>` +
      `<td>
        <div class="btl-aksi-wrap">
          <a class="btl-btn btl-btn-kelola" href="detail_batal_open.php?id=${r.id_batal}">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Kelola
          </a>
        </div>
      </td>`;
    tbody.appendChild(tr);
  });

  document.getElementById('bo-result-count').textContent =
    total === BO_DATA.length
      ? `Total ${total} pengajuan pembatalan open trip`
      : `Menampilkan ${total} dari ${BO_DATA.length} pengajuan`;
  document.getElementById('bo-no-result').style.display = total===0?'block':'none';
  btlRenderPagination('bo-pagination', pages, boPage, p=>{boPage=p;boFilter(false);});
}
document.addEventListener('DOMContentLoaded', boFilter);
</script>

<?php elseif ($batal_tab === 'peserta'): ?>
<!-- ================================================================
     TAB: PEMBATALAN PESERTA
     ================================================================ -->

<!-- STAT CHIPS -->
<?php
  $bp_menunggu  = count(array_filter($bp_rows, fn($r) => $r['status_verifikasi'] === 'Menunggu'));
  $bp_disetujui = count(array_filter($bp_rows, fn($r) => $r['status_verifikasi'] === 'Disetujui'));
  $bp_ditolak   = count(array_filter($bp_rows, fn($r) => $r['status_verifikasi'] === 'Ditolak'));
?>
<div class="btl-stat-chips">
  <div class="btl-stat-chip btl-chip-total">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    Total: <?php echo count($bp_rows); ?>
  </div>
  <div class="btl-stat-chip btl-chip-menunggu">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    Menunggu: <?php echo $bp_menunggu; ?>
  </div>
  <div class="btl-stat-chip btl-chip-selesai">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    Disetujui: <?php echo $bp_disetujui; ?>
  </div>
  <?php if ($bp_ditolak > 0): ?>
  <div class="btl-stat-chip" style="background:#fff5f5;color:#c53030;border-color:#feb2b2;">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    Ditolak: <?php echo $bp_ditolak; ?>
  </div>
  <?php endif; ?>
</div>

<!-- TOOLBAR -->
<div class="btl-toolbar">
  <div class="btl-search-wrap">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#a0aec0" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="bp-search" placeholder="Cari nama peserta, username, destinasi..." oninput="bpFilter()">
  </div>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Filter:</span>

  <select id="bp-filter-dest" onchange="bpFilter()">
    <option value="">Semua Destinasi</option>
    <?php foreach ($bp_dest as $d): ?>
      <option value="<?php echo htmlspecialchars(strtolower($d)); ?>"><?php echo htmlspecialchars($d); ?></option>
    <?php endforeach; ?>
  </select>

  <select id="bp-filter-status" onchange="bpFilter()">
    <option value="">Semua Status</option>
    <option value="menunggu">Menunggu</option>
    <option value="disetujui">Disetujui</option>
    <option value="ditolak">Ditolak</option>
  </select>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Urutkan:</span>

  <select id="bp-sort" onchange="bpFilter()">
    <option value="tgl-desc">Pengajuan Terbaru</option>
    <option value="tgl-asc">Pengajuan Terlama</option>
    <option value="dest-asc">Destinasi A–Z</option>
    <option value="nama-asc">Nama Peserta A–Z</option>
  </select>
</div>

<!-- INFO BAR -->
<div class="btl-info-bar">
  <div id="bp-result-count" class="btl-result-count"></div>
  <div class="btl-entries-wrap">
    <span>Tampilkan</span>
    <select id="bp-per-page" onchange="bpFilter()">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
    <span>entri per halaman</span>
  </div>
</div>

<!-- TABEL -->
<div class="btl-table-wrap">
  <table>
    <thead>
      <tr>
        <th style="width:40px;text-align:center;">No</th>
        <th>Akun Pemesan</th>
        <th>Nama Peserta</th>
        <th>Destinasi Trip</th>
        <th>Tanggal Pengajuan</th>
        <th>Alasan Batal</th>
        <th style="text-align:center;">Status Verifikasi</th>
        <th style="text-align:center;">Aksi</th>
      </tr>
    </thead>
    <tbody id="bp-tbody"></tbody>
  </table>
</div>
<div id="bp-no-result" class="btl-no-result">🔍 Tidak ada pembatalan peserta yang sesuai.</div>
<div id="bp-pagination" class="btl-pagination"></div>

<script>
const BP_DATA = <?php echo json_encode($bp_rows, JSON_UNESCAPED_UNICODE); ?>;
let bpPage = 1, bpFiltered = [];

function bpFmtDatetime(str) {
  if (!str) return '-';
  const d = new Date(str);
  const dd = String(d.getDate()).padStart(2,'0');
  const mm = String(d.getMonth()+1).padStart(2,'0');
  const hh = String(d.getHours()).padStart(2,'0');
  const mn = String(d.getMinutes()).padStart(2,'0');
  return `${dd}/${mm}/${d.getFullYear()} <span style="color:#a0aec0;font-size:11px;">${hh}:${mn}</span>`;
}

function bpVerifBadge(s) {
  const lc = (s||'').toLowerCase();
  if (lc === 'disetujui') return `<span class="btl-badge btl-badge-disetujui"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>${s}</span>`;
  if (lc === 'ditolak')   return `<span class="btl-badge btl-badge-ditolak"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>${s}</span>`;
  return `<span class="btl-badge btl-badge-menunggu"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>${s}</span>`;
}

function bpFilter(reset) {
  if (reset === undefined) reset = true;
  if (reset) bpPage = 1;

  const kw      = document.getElementById('bp-search').value.toLowerCase().trim();
  const fDest   = document.getElementById('bp-filter-dest').value.toLowerCase();
  const fStatus = document.getElementById('bp-filter-status').value.toLowerCase();
  const sort    = document.getElementById('bp-sort').value;
  const perPage = parseInt(document.getElementById('bp-per-page').value) || 10;

  bpFiltered = BP_DATA.filter(r => {
    if (kw && !(
      r.nama_peserta.toLowerCase().includes(kw) ||
      r.username.toLowerCase().includes(kw) ||
      ('@'+r.username).toLowerCase().includes(kw) ||
      r.tujuan.toLowerCase().includes(kw) ||
      r.alasan_batal.toLowerCase().includes(kw)
    )) return false;
    if (fDest   && r.tujuan.toLowerCase() !== fDest) return false;
    if (fStatus && r.status_verifikasi.toLowerCase() !== fStatus) return false;
    return true;
  });

  if (sort === 'tgl-desc')  bpFiltered.sort((a,b)=>new Date(b.tgl_pengajuan)-new Date(a.tgl_pengajuan));
  else if (sort === 'tgl-asc')  bpFiltered.sort((a,b)=>new Date(a.tgl_pengajuan)-new Date(b.tgl_pengajuan));
  else if (sort === 'dest-asc') bpFiltered.sort((a,b)=>a.tujuan.localeCompare(b.tujuan));
  else if (sort === 'nama-asc') bpFiltered.sort((a,b)=>a.nama_peserta.localeCompare(b.nama_peserta));

  const total = bpFiltered.length;
  const pages = Math.max(1, Math.ceil(total/perPage));
  if (bpPage > pages) bpPage = pages;
  const start = (bpPage-1)*perPage;
  const slice = bpFiltered.slice(start, start+perPage);

  const tbody = document.getElementById('bp-tbody');
  tbody.innerHTML = '';
  slice.forEach((r,i) => {
    const no = start+i+1;
    const tr = document.createElement('tr');
    tr.innerHTML =
      `<td style="font-weight:800;color:#a0aec0;font-size:12px;text-align:center;">${no}</td>` +
      `<td>
        <div style="font-weight:700;color:#321180;font-size:13.5px;">@${r.username}</div>
      </td>` +
      `<td style="font-weight:600;color:#2d3748;font-size:13.5px;">${r.nama_peserta}</td>` +
      `<td>
        <span style="display:inline-block;background:#ede9fe;color:#5a1ee6;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700;border:1px solid #c4b5fd;">${r.tujuan}</span>
      </td>` +
      `<td style="white-space:nowrap;font-size:13px;color:#2d3748;">${bpFmtDatetime(r.tgl_pengajuan)}</td>` +
      `<td><span class="btl-alasan">${r.alasan_batal}</span></td>` +
      `<td style="text-align:center;">${bpVerifBadge(r.status_verifikasi)}</td>` +
      `<td>
        <div class="btl-aksi-wrap">
          <a class="btl-btn btl-btn-kelola" href="detail_batal_peserta.php?id=${r.id_pembatalan}">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Kelola
          </a>
        </div>
      </td>`;
    tbody.appendChild(tr);
  });

  document.getElementById('bp-result-count').textContent =
    total === BP_DATA.length
      ? `Total ${total} pengajuan pembatalan peserta`
      : `Menampilkan ${total} dari ${BP_DATA.length} pengajuan`;
  document.getElementById('bp-no-result').style.display = total===0?'block':'none';
  btlRenderPagination('bp-pagination', pages, bpPage, p=>{bpPage=p;bpFilter(false);});
}
document.addEventListener('DOMContentLoaded', bpFilter);
</script>

<?php elseif ($batal_tab === 'private'): ?>
<!-- ================================================================
     TAB: PEMBATALAN PRIVATE TRIP
     ================================================================ -->

<!-- STAT CHIPS -->
<?php
  $bpr_menunggu = count(array_filter($bpr_rows, fn($r) => !$r['status']));
  $bpr_selesai  = count(array_filter($bpr_rows, fn($r) =>  $r['status']));
?>
<div class="btl-stat-chips">
  <div class="btl-stat-chip btl-chip-total">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    Total: <?php echo count($bpr_rows); ?>
  </div>
  <div class="btl-stat-chip btl-chip-menunggu">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    Menunggu: <?php echo $bpr_menunggu; ?>
  </div>
  <div class="btl-stat-chip btl-chip-selesai">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    Disetujui: <?php echo $bpr_selesai; ?>
  </div>
</div>

<!-- TOOLBAR -->
<div class="btl-toolbar">
  <div class="btl-search-wrap">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#a0aec0" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="bpr-search" placeholder="Cari penanggung jawab, akun, destinasi, alasan..." oninput="bprFilter()">
  </div>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Filter:</span>

  <select id="bpr-filter-dest" onchange="bprFilter()">
    <option value="">Semua Destinasi</option>
    <?php foreach ($bpr_dest as $d): ?>
      <option value="<?php echo htmlspecialchars(strtolower($d)); ?>"><?php echo htmlspecialchars($d); ?></option>
    <?php endforeach; ?>
  </select>

  <select id="bpr-filter-status" onchange="bprFilter()">
    <option value="">Semua Status</option>
    <option value="menunggu">Menunggu Persetujuan</option>
    <option value="disetujui">Disetujui</option>
  </select>

  <div class="toolbar-divider"></div>
  <span class="toolbar-lbl">Urutkan:</span>

  <select id="bpr-sort" onchange="bprFilter()">
    <option value="tgl-desc">Terbaru</option>
    <option value="tgl-asc">Terlama</option>
    <option value="dest-asc">Destinasi A–Z</option>
    <option value="pj-asc">Penanggung Jawab A–Z</option>
    <option value="pax-desc">Peserta Terbanyak</option>
  </select>
</div>

<!-- INFO BAR -->
<div class="btl-info-bar">
  <div id="bpr-result-count" class="btl-result-count"></div>
  <div class="btl-entries-wrap">
    <span>Tampilkan</span>
    <select id="bpr-per-page" onchange="bprFilter()">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
    <span>entri per halaman</span>
  </div>
</div>

<!-- TABEL -->
<div class="btl-table-wrap">
  <table>
    <thead>
      <tr>
        <th style="width:40px;text-align:center;">No</th>
        <th>Penanggung Jawab</th>
        <th>Akun Pemesan</th>
        <th>Destinasi Trip</th>
        <th>Peserta</th>
        <th>Tanggal Batal</th>
        <th>Alasan Pembatalan</th>
        <th style="text-align:center;">Status</th>
        <th style="text-align:center;">Aksi</th>
      </tr>
    </thead>
    <tbody id="bpr-tbody"></tbody>
  </table>
</div>
<div id="bpr-no-result" class="btl-no-result">🔍 Tidak ada pembatalan private trip yang sesuai.</div>
<div id="bpr-pagination" class="btl-pagination"></div>

<script>
const BPR_DATA = <?php echo json_encode($bpr_rows, JSON_UNESCAPED_UNICODE); ?>;
let bprPage = 1, bprFiltered = [];

function bprFmtDatetime(str) {
  if (!str) return '-';
  const d = new Date(str);
  const dd = String(d.getDate()).padStart(2,'0');
  const mm = String(d.getMonth()+1).padStart(2,'0');
  const hh = String(d.getHours()).padStart(2,'0');
  const mn = String(d.getMinutes()).padStart(2,'0');
  return `${dd}/${mm}/${d.getFullYear()} <span style="color:#a0aec0;font-size:11px;">${hh}:${mn}</span>`;
}

function bprStatusBadge(status) {
  return status
    ? `<span class="btl-badge btl-badge-disetujui"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Disetujui</span>`
    : `<span class="btl-badge btl-badge-menunggu"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Menunggu</span>`;
}

function bprFilter(reset) {
  if (reset === undefined) reset = true;
  if (reset) bprPage = 1;

  const kw      = document.getElementById('bpr-search').value.toLowerCase().trim();
  const fDest   = document.getElementById('bpr-filter-dest').value.toLowerCase();
  const fStatus = document.getElementById('bpr-filter-status').value;
  const sort    = document.getElementById('bpr-sort').value;
  const perPage = parseInt(document.getElementById('bpr-per-page').value) || 10;

  bprFiltered = BPR_DATA.filter(r => {
    if (kw && !(
      r.penanggung_jawab.toLowerCase().includes(kw) ||
      r.akun_pemesan.toLowerCase().includes(kw) ||
      ('@'+r.akun_pemesan).toLowerCase().includes(kw) ||
      r.tujuan.toLowerCase().includes(kw) ||
      r.alasan.toLowerCase().includes(kw) ||
      r.no_hp.toLowerCase().includes(kw)
    )) return false;
    if (fDest   && r.tujuan.toLowerCase() !== fDest) return false;
    if (fStatus === 'menunggu'  && r.status)  return false;
    if (fStatus === 'disetujui' && !r.status) return false;
    return true;
  });

  if (sort === 'tgl-desc')  bprFiltered.sort((a,b)=>new Date(b.tgl_pembatalan)-new Date(a.tgl_pembatalan));
  else if (sort === 'tgl-asc')  bprFiltered.sort((a,b)=>new Date(a.tgl_pembatalan)-new Date(b.tgl_pembatalan));
  else if (sort === 'dest-asc') bprFiltered.sort((a,b)=>a.tujuan.localeCompare(b.tujuan));
  else if (sort === 'pj-asc')   bprFiltered.sort((a,b)=>a.penanggung_jawab.localeCompare(b.penanggung_jawab));
  else if (sort === 'pax-desc') bprFiltered.sort((a,b)=>b.jumlah_peserta-a.jumlah_peserta);

  const total = bprFiltered.length;
  const pages = Math.max(1, Math.ceil(total/perPage));
  if (bprPage > pages) bprPage = pages;
  const start = (bprPage-1)*perPage;
  const slice = bprFiltered.slice(start, start+perPage);

  const tbody = document.getElementById('bpr-tbody');
  tbody.innerHTML = '';
  slice.forEach((r,i) => {
    const no = start+i+1;
    const tr = document.createElement('tr');
    tr.innerHTML =
      `<td style="font-weight:800;color:#a0aec0;font-size:12px;text-align:center;">${no}</td>` +
      `<td>
        <div style="font-weight:700;color:#321180;font-size:13.5px;">${r.penanggung_jawab}</div>
        <div style="font-size:11.5px;color:#718096;margin-top:2px;">📱 ${r.no_hp}</div>
      </td>` +
      `<td>
        <span style="font-weight:700;color:#6b3df5;font-size:13px;">@${r.akun_pemesan}</span>
      </td>` +
      `<td>
        <span style="display:inline-block;background:#ede9fe;color:#5a1ee6;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700;border:1px solid #c4b5fd;">${r.tujuan}</span>
      </td>` +
      `<td style="text-align:center;">
        <span style="display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 8px;border-radius:50%;background:linear-gradient(135deg,#ede9fe,#ddd6fe);color:#5a1ee6;font-weight:900;font-size:13px;border:2px solid #c4b5fd;">${r.jumlah_peserta}</span>
        <div style="font-size:10.5px;color:#a0aec0;margin-top:2px;">orang</div>
      </td>` +
      `<td style="white-space:nowrap;font-size:13px;color:#2d3748;">${bprFmtDatetime(r.tgl_pembatalan)}</td>` +
      `<td><span class="btl-alasan">${r.alasan}</span></td>` +
      `<td style="text-align:center;">${bprStatusBadge(r.status)}</td>` +
      `<td>
        <div class="btl-aksi-wrap">
          <a class="btl-btn btl-btn-kelola" href="detail_batal_private.php?id=${r.id_batal}">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Kelola
          </a>
        </div>
      </td>`;
    tbody.appendChild(tr);
  });

  document.getElementById('bpr-result-count').textContent =
    total === BPR_DATA.length
      ? `Total ${total} pengajuan pembatalan private trip`
      : `Menampilkan ${total} dari ${BPR_DATA.length} pengajuan`;
  document.getElementById('bpr-no-result').style.display = total===0?'block':'none';
  btlRenderPagination('bpr-pagination', pages, bprPage, p=>{bprPage=p;bprFilter(false);});
}
document.addEventListener('DOMContentLoaded', bprFilter);
</script>

<?php endif; /* end batal_tab */ ?>

<!-- ---- Shared pagination helper for pembatalan ---- -->
<script>
function btlRenderPagination(containerId, totalPages, currentPage, onPageClick) {
  const pg = document.getElementById(containerId);
  if (!pg) return;
  pg.innerHTML = '';
  if (totalPages <= 1) return;

  const make = (label, page, disabled, active, ellipsis) => {
    const btn = document.createElement('button');
    btn.className = 'pg-btn' + (active?' active':'') + (ellipsis?' pg-ellipsis':'');
    btn.innerHTML = label;
    btn.disabled  = disabled;
    if (!disabled && !ellipsis) btn.onclick = () => onPageClick(page);
    return btn;
  };

  pg.appendChild(make('&laquo;', currentPage-1, currentPage===1, false, false));
  const range = [];
  if (totalPages <= 7) { for(let i=1;i<=totalPages;i++) range.push(i); }
  else {
    range.push(1);
    if (currentPage > 4) range.push('...');
    for(let i=Math.max(2,currentPage-1);i<=Math.min(totalPages-1,currentPage+1);i++) range.push(i);
    if (currentPage < totalPages-3) range.push('...');
    range.push(totalPages);
  }
  range.forEach(r => {
    if (r==='...') pg.appendChild(make('...', 0, true, false, true));
    else pg.appendChild(make(r, r, false, r===currentPage, false));
  });
  pg.appendChild(make('&raquo;', currentPage+1, currentPage===totalPages, false, false));
}
</script>

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
