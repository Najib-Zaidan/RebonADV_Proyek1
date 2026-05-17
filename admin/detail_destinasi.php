<?php 
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'konek.php';
require 'fungsi.php';

// Validasi ID Tujuan dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?menu=destinasi");
    exit;
}

$id_tujuan = intval($_GET['id']);

// Query data detail destinasi
$query_destinasi = kueri("SELECT * FROM tujuan WHERE id_tujuan = $id_tujuan");
$destinasi = ambil($query_destinasi);

if (!$destinasi) {
    echo "<script>alert('Data destinasi tidak ditemukan!'); window.location='index.php?menu=destinasi';</script>";
    exit;
}

// Menghitung total trip yang menggunakan destinasi ini
$query_hitung_trip = kueri("SELECT COUNT(*) AS total_trip FROM trip WHERE id_tujuan = $id_tujuan");
$total_trip = ambil($query_hitung_trip)['total_trip'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Detail Destinasi - Admin Panel</title>
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
  display: inline-block;
  margin-bottom: 20px;
  text-decoration: none;
  color: #321180;
  font-weight: bold;
  font-size: 14px;
}

.btn-kembali:hover {
  text-decoration: underline;
}

/* HEADER */
.header-title {
  color: #321180;
  margin-bottom: 25px;
}

/* LAYOUT GRID UTAMA */
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

/* INFO GRID (UNTUK DETAIL DESTINASI) */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

/* TABLE SYSTEM */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

thead {
  background: #6b3df5;
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

/* AKSI BUTTON DI DETAIL TRIP */
.btn-aksi {
  color: #6b3df5;
  text-decoration: none;
  font-weight: bold;
}

.btn-aksi:hover {
  text-decoration: underline;
}

/* BADGE STATUS PUBLIK */
.status-publik {
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: bold;
}
.publik-true { background: #e8f5e9; color: green; }
.publik-false { background: #ffeffee; color: red; }

/* REBON RATING STYLING (TAMBAHAN BARU) */
.rating-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
  margin-top: 15px;
}

.rating-item {
  background: #f9f7ff;
  border: 1px solid #e1d5ff;
  border-radius: 10px;
  padding: 15px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.rating-stars {
  color: #ff9800;
  font-size: 18px;
  margin-bottom: 8px;
}

.rating-user {
  font-size: 14px;
  color: #321180;
  font-weight: bold;
}

.rating-trip {
  font-size: 12px;
  color: #6b3df5;
  margin-bottom: 10px;
  background: #eee5ff;
  padding: 3px 8px;
  border-radius: 4px;
  display: inline-block;
}

.rating-text {
  font-size: 13px;
  color: #444;
  line-height: 1.4;
  margin-bottom: 15px;
}

.btn-detail-rating {
  align-self: flex-start;
  font-size: 12px;
  color: white;
  background: #6b3df5;
  text-decoration: none;
  padding: 6px 12px;
  border-radius: 6px;
  font-weight: bold;
  transition: 0.3s;
}

.btn-detail-rating:hover {
  background: #321180;
}
</style>
</head>
<body>

<div class="container">

  <!-- Tombol Kembali ke Dashboard Menu Destinasi -->
  <a href="index.php?menu=destinasi" class="btn-kembali">&larr; Kembali ke Manajamen Destinasi</a>

  <div class="header-title">
    <h1>Detail Master Destinasi</h1>
    <p>Informasi lengkap mengenai destinasi dan riwayat penggunaannya pada program open trip.</p>
  </div>

  <div class="main-grid">
    
    <!-- CARD 1: DETAIL KOLOM TABEL DESTINASI -->
    <div class="card">
      <h3>Spesifikasi Destinasi</h3>
      <div class="info-grid">
        
        <div class="info-item">
          <label>ID Destinasi</label>
          <span>#<?php echo $destinasi['id_tujuan']; ?></span>
        </div>

        <div class="info-item">
          <label>Nama Destinasi / Tujuan</label>
          <span><?php echo $destinasi['tujuan']; ?></span>
        </div>

        <div class="info-item">
          <label>Wilayah Kota</label>
          <span><?php echo $destinasi['kota']; ?></span>
        </div>

        <div class="info-item">
          <label>Wilayah Provinsi</label>
          <span><?php echo $destinasi['provinsi']; ?></span>
        </div>

        <div class="info-item">
          <label>Harga Default</label>
          <span><?php echo $destinasi['harga_def'] ? "Rp " . number_format($destinasi['harga_def']) : "Belum Diatur"; ?></span>
        </div>

        <div class="info-item">
          <label>Harga DP Default</label>
          <span><?php echo $destinasi['harga_dp_def'] ? "Rp " . number_format($destinasi['harga_dp_def']) : "Belum Diatur"; ?></span>
        </div>

        <div class="info-item" style="grid-column: span 2;">
          <label>Rute Default</label>
          <span><?php echo $destinasi['rute_def'] ?? "Belum Diatur"; ?></span>
        </div>

      </div>
    </div>

    <!-- CARD 2: KETERANGAN PENGGUNAAN DAN DAFTAR TRIP -->
    <div class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">Riwayat Penggunaan Pada Trip</h3>
        <span class="badge-info">Digunakan di: <?php echo $total_trip; ?> Pembuatan Trip</span>
      </div>

      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal Berangkat</th>
            <th>Durasi</th>
            <th>Harga / DP</th>
            <th>Kuota Sisa / Total</th>
            <th>Total Booking (Aktif)</th>
            <th>Status Publik</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Query mengambil semua program trip yang merujuk ke id_tujuan ini
          $data_trip = kueri("SELECT *, (DATEDIFF(tgl_pulang, tgl_berangkat) + 1) AS durasi 
                              FROM trip 
                              WHERE id_tujuan = $id_tujuan 
                              ORDER BY tgl_berangkat DESC");

          if (mysqli_num_rows($data_trip) > 0) {
              $no = 1;
              while ($row = ambil($data_trip)) {
                  $id_trip = $row['id_trip'];

                  // Hitung total peserta dari tabel booking yang statusnya valid (bukan Dibatalkan/Refund)
                  $query_terisi = kueri("SELECT SUM(jumlah_peserta) AS total_terisi 
                                        FROM booking 
                                        WHERE id_trip = $id_trip AND status != 'Dibatalkan' AND status != 'Refund'");
                  $terisi = ambil($query_terisi)['total_terisi'] ?? 0;
                  
                  $sisa_kuota = $row['kuota'] - $terisi;

                  // Hitung berapa kali kode trip ini telah masuk ke baris transaksi booking (frekuensi transaksi)
                  $query_frekuensi = kueri("SELECT COUNT(id_booking) AS total_transaksi 
                                            FROM booking 
                                            WHERE id_trip = $id_trip");
                  $total_transaksi = ambil($query_frekuensi)['total_transaksi'] ?? 0;

                  // Menentukan label status publikasi trip ke pengguna luar
                  $status_class = $row['publik'] ? 'publik-true' : 'publik-false';
                  $status_text = $row['publik'] ? 'Publik' : 'Draft/Privat';
                  
                  echo "<tr>";
                  echo "<td>$no</td>";
                  echo "<td>" . date('d/m/Y', strtotime($row['tgl_berangkat'])) . "</td>";
                  echo "<td>{$row['durasi']} Hari</td>";
                  echo "<td>Rp " . number_format($row['harga']) . " <br> <small style='color: #888;'>DP: Rp " . number_format($row['harga_dp']) . "</small></td>";
                  echo "<td><strong>$sisa_kuota</strong> / {$row['kuota']} Pax</td>";
                  echo "<td>$total_transaksi Kali Transaksi</td>";
                  echo "<td><span class='status-publik $status_class'>$status_text</span></td>";
                  echo "<td><a href='detail_trip.php?id=$id_trip' class='btn-aksi'>Detail Trip</a></td>";
                  echo "</tr>";
                  
                  $no++;
              }
          } else {
              echo "<tr><td colspan='8' align='center' style='padding: 30px; color: #666;'>Destinasi master ini belum pernah dimasukkan ke dalam program paket open trip manapun.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

   
    <div class="card">
      <?php
      // Mengambil data ulasan dari tabel rating, digabungkan dengan data akun dan data tanggal trip terkait
      $query_rating = kueri("SELECT r.*, a.username, t.tgl_berangkat 
                             FROM rating r 
                             JOIN akun a ON r.id_akun = a.id_akun 
                             JOIN trip t ON r.id_trip = t.id_trip 
                             WHERE r.id_tujuan = $id_tujuan 
                             ORDER BY r.id_rating DESC");
      $total_ulasan = mysqli_num_rows($query_rating);
      ?>
      
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">Ulasan & Rating Pengguna</h3>
        <span class="badge-info" style="background: #ff9800;">Total Ulasan: <?php echo $total_ulasan; ?></span>
      </div>

      <div class="rating-grid">
        <?php
        if ($total_ulasan > 0) {
            while ($row_rating = ambil($query_rating)) {
                // Membuat visualisasi karakter bintang sesuai nilai integer di database
                $bintang = str_repeat("★", $row_rating['rating']) . str_repeat("☆", 5 - $row_rating['rating']);
                $format_tgl_trip = date('d/m/Y', strtotime($row_rating['tgl_berangkat']));
        ?>
            <div class="rating-item">
              <div>
                <!-- Baris Identitas User -->
                <div class="rating-user">@<?php echo $row_rating['username']; ?></div>
                
                <!-- Keterangan Program Pelaksanaan Trip -->
                <div class="rating-trip">Trip Berangkat: <?php echo $format_tgl_trip; ?> (ID Trip #<?php echo $row_rating['id_trip']; ?>)</div>
                
                <!-- Representasi Angka Bintang -->
                <div class="rating-stars"><?php echo $bintang; ?></div>
                
                <!-- Tulisan Deskripsi Ulasan User -->
                <div class="rating-text">
                  <?php echo !empty($row_rating['ulasan']) ? $row_rating['ulasan'] : "User tidak memberikan ulasan tulisan."; ?>
                </div>
              </div>
              
              <!-- Tombol Aksi Menuju Halaman Detail Target Transaksi Rating -->
              <a href="detail_rating.php?id=<?php echo $row_rating['id_rating']; ?>" class="btn-detail-rating">
                Detail Rating
              </a>
            </div>
        <?php
            }
        } else {
            echo "<div style='grid-column: 1/-1; background: #f9f7ff; padding: 30px; text-align: center; border-radius: 8px; color: #666; border: 1px dashed #e1d5ff;'>Belum ada rating atau ulasan dari pengguna untuk destinasi ini.</div>";
        }
        ?>
      </div>
    </div>
    <!-- =================================================================================== -->

  </div>
</div>

</body>
</html>
