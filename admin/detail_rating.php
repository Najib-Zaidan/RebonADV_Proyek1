<?php 
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'konek.php';
require 'fungsi.php';

// Validasi ID Rating dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?menu=destinasi");
    exit;
}

$id_rating = intval($_GET['id']);

// Query detail rating utama beserta relasi akun, tujuan, dan trip
$query_detail = kueri("SELECT r.*, a.username, tgl_berangkat, tgl_pulang, kuota, harga, tujuan, kota, provinsi
                       FROM rating r
                       JOIN akun a ON r.id_akun = a.id_akun
                       JOIN trip t ON r.id_trip = t.id_trip
                       JOIN tujuan tj ON r.id_tujuan = tj.id_tujuan
                       WHERE r.id_rating = $id_rating");
$detail = ambil($query_detail);

if (!$detail) {
    echo "<script>alert('Data rating tidak ditemukan!'); window.location='index.php?menu=destinasi';</script>";
    exit;
}

$id_akun_pemilik = $detail['id_akun'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Detail Ulasan & Rating - Admin Panel</title>
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial;
}

body {
  background: #d9d9d9;
  padding: 30px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

/* LINK KEMBALI */
.btn-kembali {
  display: inline-flex;
  align-items: center;
  margin-bottom: 25px;
  text-decoration: none;
  color: #321180;
  font-weight: bold;
  font-size: 14px;
  background: white;
  padding: 10px 18px;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  border-left: 4px solid #6b3df5;
  transition: 0.3s;
}

.btn-kembali:hover {
  background: #f4f0ff;
  transform: translateX(-3px);
}

/* HEADER GRADIENT UNGU */
.header-title {
  background: linear-gradient(135deg, #321180, #6b3df5);
  padding: 30px;
  border-radius: 12px;
  color: white;
  margin-bottom: 30px;
  box-shadow: 0 4px 15px rgba(50, 17, 128, 0.2);
}

.header-title h1 {
  font-size: 28px;
  margin-bottom: 8px;
}

.header-title p {
  font-size: 14px;
  color: #e1d5ff;
}

/* MAIN GRID LAYOUT */
.main-grid {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

/* CARD SYSTEM */
.card {
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.card h3 {
  color: #321180;
  margin-bottom: 20px;
  border-bottom: 2px solid #f4f0ff;
  padding-bottom: 10px;
}

/* INFO GRID SYSTEM */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

.info-item {
  background: #f9f7ff;
  padding: 15px;
  border-radius: 8px;
  border-left: 4px solid #6b3df5;
}

.info-item label {
  display: block;
  font-size: 12px;
  color: #888;
  text-transform: uppercase;
  margin-bottom: 5px;
  font-weight: bold;
}

.info-item span {
  font-size: 15px;
  color: #333;
  font-weight: bold;
}

/* STYLING KHUSUS TAMPILAN REVIEW UTAMA */
.review-box {
  background: #f4f0ff;
  border: 1px solid #e1d5ff;
  padding: 20px;
  border-radius: 10px;
  margin-top: 15px;
}

.rating-stars {
  color: #ff9800;
  font-size: 22px;
  margin-bottom: 10px;
}

.rating-text-content {
  font-size: 15px;
  color: #333;
  line-height: 1.6;
  background: white;
  padding: 15px;
  border-radius: 6px;
  border-left: 4px solid #ff9800;
}

/* BADGE STATISTIK */
.badge-info {
  display: inline-block;
  background: #6b3df5;
  color: white;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: bold;
}

/* TABLE SYSTEM FOR HISTORY */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

thead {
  background: #321180;
  color: white;
}

th, td {
  padding: 12px;
  font-size: 14px;
  text-align: left;
}

tbody tr {
  border-bottom: 1px solid #eee;
}

tbody tr:hover {
  background: #f9f7ff;
}

.btn-aksi {
  color: #6b3df5;
  text-decoration: none;
  font-weight: bold;
}

.btn-aksi:hover {
  text-decoration: underline;
}
</style>
</head>
<body>

<div class="container">

  <!-- Tombol Dinamis Kembali ke Halaman Detail Destinasi Sebelumnya -->
  <a href="detail_destinasi.php?id=<?php echo $detail['id_tujuan']; ?>" class="btn-kembali"><strong>&larr;</strong> &nbsp; Kembali ke Detail Destinasi</a>

  <div class="header-title">
    <h1>Moderasi & Detail Rating Pelanggan</h1>
    <p>Memeriksa ulasan spesifik serta rekam jejak penilaian konsekutif yang dikirimkan oleh akun pengguna.</p>
  </div>

  <div class="main-grid">

    <!-- CARD 1: DETAIL RATING YANG DIKLIK -->
    <div class="card">
      <h3>Informasi Penilaian Transaksi</h3>
      <div class="info-grid">
        
        <div class="info-item">
          <label>ID Ulasan</label>
          <span>#<?php echo $detail['id_rating']; ?></span>
        </div>

        <div class="info-item" style="border-left-color: #321180;">
          <label>Penulis Ulasan</label>
          <span style="color: #321180;">@<?php echo $detail['username']; ?> (ID #<?php echo $detail['id_akun']; ?>)</span>
        </div>

        <div class="info-item">
          <label>Destinasi Target</label>
          <span><?php echo $detail['tujuan']; ?></span>
        </div>

        <div class="info-item">
          <label>Lokasi Wilayah</label>
          <span><?php echo $detail['kota']; ?>, <?php echo $detail['provinsi']; ?></span>
        </div>

        <div class="info-item">
          <label>Program Trip Terkait</label>
          <span>ID Trip #<?php echo $detail['id_trip']; ?></span>
        </div>

        <div class="info-item">
          <label>Tanggal Pelaksanaan</label>
          <span><?php echo date('d/m/Y', strtotime($detail['tgl_berangkat'])); ?></span>
        </div>

      </div>

      <!-- Konten Ulasan Tulisan & Nilai Bintang -->
      <div class="review-box">
        <label style="display: block; font-size: 12px; color: #6b3df5; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;">Skor & Isi Ulasan Pelanggan</label>
        <div class="rating-stars">
          <?php 
          echo str_repeat("★", $detail['rating']) . str_repeat("☆", 5 - $detail['rating']); 
          ?>
          <span style="font-size: 16px; color: #555; font-weight: bold; margin-left: 5px;">(Skor: <?php echo $detail['rating']; ?>)</span>
        </div>
        <div class="rating-text-content">
          <?php echo !empty($detail['ulasan']) ? $detail['ulasan'] : "Pengguna tidak meninggalkan deskripsi teks tertulis."; ?>
        </div>
      </div>
    </div>

    <!-- CARD 2: RIWAYAT RATING LAIN OLEH AKUN YANG SAMA -->
    <div class="card">
      <?php
      // Mengambil semua riwayat ulasan dari akun ini, mengecualikan ulasan utama yang sedang dibuka di atas
      $query_riwayat = kueri("SELECT r.*, tj.tujuan, tj.kota, t.tgl_berangkat 
                             FROM rating r
                             JOIN tujuan tj ON r.id_tujuan = tj.id_tujuan
                             JOIN trip t ON r.id_trip = t.id_trip
                             WHERE r.id_akun = $id_akun_pemilik AND r.id_rating != $id_rating
                             ORDER BY r.id_rating DESC");
      $total_riwayat = mysqli_num_rows($query_riwayat);
      ?>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">Riwayat Aktivitas Rating Lain dari Akun Ini</h3>
        <span class="badge-info" style="background: #321180;">Ulasan Lainnya: <?php echo $total_riwayat; ?> Kontribusi</span>
      </div>

      <table>
        <thead>
          <tr>
            <th style="width: 60px;">ID</th>
            <th>Destinasi / Kota Tujuan</th>
            <th>ID Trip</th>
            <th>Tanggal Trip</th>
            <th>Skor Rating</th>
            <th>Isi Potongan Ulasan</th>
            <th style="text-align: center; width: 120px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($total_riwayat > 0) {
              while ($row_riwayat = ambil($query_riwayat)) {
                  $bintang_riwayat = str_repeat("★", $row_riwayat['rating']) . str_repeat("☆", 5 - $row_riwayat['rating']);
                  $potongan_ulasan = !empty($row_riwayat['ulasan']) ? $row_riwayat['ulasan'] : "Tanpa ulasan teks.";
                  
                  // Memotong teks jika terlalu panjang agar tabel tetap rapi
                  if (strlen($potongan_ulasan) > 45) {
                      $potongan_ulasan = substr($potongan_ulasan, 0, 42) . "...";
                  }

                  echo "<tr>";
                  echo "<td>#{$row_riwayat['id_rating']}</td>";
                  echo "<td><strong>{$row_riwayat['tujuan']}</strong> <br> <small style='color: #777;'>{$row_riwayat['kota']}</small></td>";
                  echo "<td>#{$row_riwayat['id_trip']}</td>";
                  echo "<td>" . date('d/m/Y', strtotime($row_riwayat['tgl_berangkat'])) . "</td>";
                  echo "<td><span style='color: #ff9800; font-size: 15px;'>$bintang_riwayat</span></td>";
                  echo "<td style='color: #555;'>$potongan_ulasan</td>";
                  echo "<td align='center'><a href='detail_rating.php?id={$row_riwayat['id_rating']}' class='btn-aksi'>Lihat Detail</a></td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='7' align='center' style='padding: 30px; color: #666;'>Akun ini belum pernah memberikan ulasan atau rating pada program paket open trip lainnya.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

</body>
</html>
