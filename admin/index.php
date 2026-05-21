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

<?php
  $filter = $_GET['filter'] ?? 'hari_ini';
  $sort = $_GET['sort'] ?? '';
  $destinasi = $_GET['destinasi'] ?? '';
  $status = $_GET['status'] ?? '';
?>

<div style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: nowrap; background: white; padding: 10px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
  
  <div style="display: flex; gap: 5px;">
    <?php
    $btn_base = "padding: 8px 12px; border-radius: 8px; text-decoration: none; color: white; font-size: 13px; transition: 0.3s; white-space: nowrap;";
    $periods = [
      'hari_ini' => 'Hari Ini',
      'minggu_ini' => 'Minggu Ini',
      'bulan_ini' => 'Bulan Ini',
      'tahun_ini' => 'Tahun Ini',
      'semua' => 'Semua Data'
    ];

    foreach($periods as $key => $label):
      $bg = ($filter == $key) ? '#321180' : '#6b3df5';
    ?>
      <a href="index.php?menu=booking&filter=<?php echo $key; ?>&sort=<?php echo $sort; ?>&destinasi=<?php echo $destinasi; ?>&status=<?php echo $status; ?>" 
         style="<?php echo $btn_base; ?> background: <?php echo $bg; ?>;">
        <?php echo $label; ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div style="width: 1px; height: 30px; background: #ddd; margin: 0 5px;"></div>

  <form action="index.php" method="get" style="display: flex; gap: 10px; align-items: center; flex: 1;">
    <input type="hidden" name="menu" value="booking">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">

    <select name="sort" style="padding: 7px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px; outline: none;">
      <option value="">Urutkan Pembayaran</option>
      <option value="ASC" <?php if($sort == 'ASC') echo 'selected'; ?>>Terkecil ke Terbesar</option>
      <option value="DESC" <?php if($sort == 'DESC') echo 'selected'; ?>>Terbesar ke Kecil</option>
    </select>

    <select name="destinasi" style="padding: 7px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px; outline: none;">
      <option value="">Semua Destinasi</option>
      <?php
      $list_trip = kueri("SELECT DISTINCT tujuan FROM tujuan");
      while($t = ambil($list_trip)){
        $selected = ($destinasi == $t['tujuan']) ? "selected" : "";
        echo "<option value='{$t['tujuan']}' $selected>{$t['tujuan']}</option>";
      }
      ?>
    </select>

    <select name="status" style="padding: 7px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px; outline: none;">
      <option value="">Semua Status</option>
      <?php
      $list_status = kueri("SELECT DISTINCT status FROM booking");
      while($s_row = ambil($list_status)){
        $selected = ($status == $s_row['status']) ? "selected" : "";
        echo "<option value='{$s_row['status']}' $selected>{$s_row['status']}</option>";
      }
      ?>
    </select>

    <button type="submit" style="padding: 8px 15px; background: #321180; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: bold;">
      Terapkan
    </button>
  </form>
</div>

<?php
$where = [];
if ($filter == 'hari_ini') {
    $where[] = "DATE(b.tgl_booking) = CURDATE()";
} elseif ($filter == 'minggu_ini') {
    $where[] = "YEARWEEK(b.tgl_booking, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter == 'bulan_ini') {
    $where[] = "MONTH(b.tgl_booking) = MONTH(CURDATE()) AND YEAR(b.tgl_booking) = YEAR(CURDATE())";
} elseif ($filter == 'tahun_ini') {
    $where[] = "YEAR(b.tgl_booking) = YEAR(CURDATE())";
}

if ($destinasi != '') $where[] = "tj.tujuan = '$destinasi'";
if ($status != '') $where[] = "b.status = '$status'";

$kondisi = (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";
$order = ($sort != '') ? "ORDER BY total_bayar $sort" : "";

$data_booking = kueri("SELECT b.*, tj.tujuan, t.harga, t.tgl_berangkat, a.username AS nama,
                (SELECT SUM(nominal) FROM payment_open 
                 WHERE id_booking = b.id_booking AND status = 'Diverifikasi') AS total_bayar
                FROM booking b
                JOIN trip t ON b.id_trip = t.id_trip
                JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                JOIN akun a ON b.id_akun = a.id_akun
                $kondisi
                $order");
?>

<a href="export_booking.php?filter=<?php echo $filter; ?>&sort=<?php echo $sort; ?>&destinasi=<?php echo $destinasi; ?>&status=<?php echo $status; ?>" 
   style="padding: 8px 15px; background: green; color: white; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold;">
   Download Excel
</a>

<table>
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

<?php
$no=1;
while($row=ambil($data_booking)){
  $bayar = $row['total_bayar'] ?? 0;
  $total_tagihan = $row['harga'] * $row['jumlah_peserta'];
  
  echo "<tr>";
  echo "<td>$no</td>";
  echo "<td>{$row['tujuan']}</td>";
  echo "<td>{$row['nama']}</td>";
  echo "<td>{$row['jumlah_peserta']}</td>";
  echo "<td>{$row['tgl_booking']}</td>";
  echo "<td>{$row['tgl_berangkat']}</td>";
  echo "<td>Rp ".number_format($bayar)." / ".number_format($total_tagihan)."</td>";
  echo "<td>{$row['status']}</td>";
  echo "<td><a href='detail_booking.php?id={$row['id_booking']}'>Detail</a></td>";
  echo "</tr>";
  $no++;
}
?>
</table>

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
// Mengatur switch tab internal (default ke 'pengajuan_baru')
$sub_tab = $_GET['sub'] ?? 'pengajuan_baru';
?>

<div style="margin-bottom: 20px;">
    <h2 style="color: #321180;">Daftar Pengajuan Private Trip</h2>
    <p style="font-size: 14px; color: #666;">Kelola permintaan private trip dan log pengajuan perubahan data dari pengguna di sini.</p>
</div>

<div style="margin-bottom: 20px; border-bottom: 2px solid #e1d8f5; padding-bottom: 10px;">
    <a href="index.php?menu=private&sub=pengajuan_baru" 
       style="text-decoration: none; padding: 8px 16px; font-weight: bold; font-size: 14px; margin-right: 10px; color: <?php echo ($sub_tab == 'pengajuan_baru') ? '#321180; border-bottom: 3px solid #321180;' : '#888'; ?>; display: inline-block;">
       Pengajuan Baru
    </a>
    <a href="index.php?menu=private&sub=pengajuan_perubahan" 
       style="text-decoration: none; padding: 8px 16px; font-weight: bold; font-size: 14px; color: <?php echo ($sub_tab == 'pengajuan_perubahan') ? '#321180; border-bottom: 3px solid #321180;' : '#888'; ?>; display: inline-block;">
       Pengajuan Perubahan
    </a>
</div>

<?php if($sub_tab == 'pengajuan_baru'): ?>
    <?php
    // Query asli untuk mengambil data pengajuan private trip utama
    $data_private = kueri("SELECT pt.*, a.username 
                           FROM private_trip pt 
                           JOIN akun a ON pt.id_akun = a.id_akun 
                           ORDER BY pt.tgl_booking DESC");
    ?>

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
        <tbody>
            <?php
            $no = 1;
            if(mysqli_num_rows($data_private) > 0){
                while($row = ambil($data_private)){
                    // Styling Warna Status Trip
                    $st_trip_color = "orange";
                    if($row['status_trip'] == 'Disetujui') $st_trip_color = "green";
                    if($row['status_trip'] == 'Ditolak') $st_trip_color = "red";

                    // Styling Warna Status Bayar
                    $st_bayar_color = "#666";
                    if($row['status_bayar'] == 'Lunas') $st_bayar_color = "green";
                    if($row['status_bayar'] == 'Belum Bayar') $st_bayar_color = "red";
            ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td>
                        <strong><?php echo $row['nama']; ?></strong><br>
                        <small style="color: #6b3df5;">@<?php echo $row['username']; ?></small>
                    </td>
                    <td><?php echo $row['tujuan']; ?></td>
                    <td><?php echo $row['jumlah_peserta']; ?> Orang</td>
                    <td><?php echo date('d/m/Y', strtotime($row['tgl_berangkat'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['tgl_booking'])); ?></td>
                    <td style="color: <?php echo $st_trip_color; ?>; font-weight: bold;">
                        <?php echo $row['status_trip']; ?>
                    </td>
                    <td>
                        <span style="padding: 4px 8px; background: #f0f0f0; border-radius: 5px; font-size: 12px; color: <?php echo $st_bayar_color; ?>;">
                            <?php echo $row['status_bayar']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="detail_private.php?id=<?php echo $row['id_private']; ?>" 
                           style="background: #321180; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px;">
                           Detail
                        </a>
                    </td>
                </tr>
            <?php
                    $no++;
                }
            } else {
                echo "<tr><td colspan='9' align='center' style='padding: 20px;'>Belum ada pengajuan Private Trip.</td></tr>";
            }
            ?>
        </tbody>
    </table>

<?php elseif($sub_tab == 'pengajuan_perubahan'): ?>
    <?php
    /**
     * Query data pengajuan perubahan dengan membandingkan data asli:
     * Mengambil semua kolom baru dari ubah_private (up) dan kolom asli dari private_trip (pt)
     * untuk mendeteksi perubahan data secara real-time di sisi admin.
     */
    $data_perubahan = kueri("SELECT 
                                up.id_ubah,
                                up.id_private,
                                up.nama AS nama_baru,
                                up.no_hp AS no_hp_baru,
                                up.tujuan AS tujuan_baru,
                                up.tgl_berangkat AS tgl_berangkat_baru,
                                up.tgl_pulang AS tgl_pulang_baru,
                                up.jumlah_peserta AS jumlah_baru,
                                up.catatan AS catatan_baru,
                                up.tgl_pengajuan,
                                pt.nama AS nama_asli,
                                pt.tujuan AS tujuan_asli,
                                pt.tgl_berangkat AS tgl_berangkat_asli,
                                pt.tgl_pulang AS tgl_pulang_asli,
                                pt.jumlah_peserta AS jumlah_asli,
                                a.username 
                             FROM ubah_private up
                             JOIN private_trip pt ON up.id_private = pt.id_private
                             JOIN akun a ON pt.id_akun = a.id_akun
                             WHERE up.status = 0
                             ORDER BY up.tgl_pengajuan DESC");
    ?>

    <style>
        .table-perubahan th {
            background-color: #321180;
            color: white;
            padding: 12px 10px;
        }
        .table-perubahan td {
            padding: 12px 10px;
            vertical-align: top;
            font-size: 13px;
        }
        .data-asli {
            color: #888;
            text-decoration: line-through;
            font-size: 11px;
            display: block;
            margin-bottom: 2px;
        }
        .data-baru-berubah {
            color: #2e7d32;
            font-weight: bold;
            background-color: #e8f5e9;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }
        .data-tetap {
            color: #333;
        }
        .badge-info-perubahan {
            padding: 4px 8px; 
            background: #ffeeba; 
            color: #856404; 
            border-radius: 5px; 
            font-size: 11px; 
            font-weight: bold;
            display: inline-block;
        }
    </style>

    <table class="table-perubahan" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Pemesan</th>
                <th width="15%">Destinasi / Tujuan</th>
                <th width="12%">Jumlah Peserta</th>
                <th width="20%">Rencana Tanggal</th>
                <th width="18%">Catatan Alasan</th>
                <th width="10%">Waktu Ajuan</th>
                <th width="5%" style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if(mysqli_num_rows($data_perubahan) > 0){
                while($row = ambil($data_perubahan)){
                    $catatan_alasan = !empty($row['catatan_baru']) ? htmlspecialchars($row['catatan_baru']) : '-';
                    
                    // Cek Perubahan Tujuan
                    $tujuan_berubah = ($row['tujuan_baru'] !== $row['tujuan_asli']);
                    
                    // Cek Perubahan Jumlah Peserta
                    $jumlah_berubah = ($row['jumlah_baru'] != $row['jumlah_asli']);
                    
                    // Cek Perubahan Tanggal Berangkat / Pulang
                    $tanggal_berubah = ($row['tgl_berangkat_baru'] !== $row['tgl_berangkat_asli'] || $row['tgl_pulang_baru'] !== $row['tgl_pulang_asli']);
            ?>
                <tr style="border-bottom: 1px solid #e1d8f5;">
                    <td><?php echo $no; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['nama_baru']); ?></strong>
                        <?php if($row['nama_baru'] !== $row['nama_asli']): ?>
                            <br><span class="data-asli">Asli: <?php echo htmlspecialchars($row['nama_asli']); ?></span>
                        <?php endif; ?>
                        <small style="color: #6b3df5; display: block; margin-top: 2px;">@<?php echo $row['username']; ?></small>
                    </td>
                    <td>
                        <?php if($tujuan_berubah): ?>
                            <span class="data-asli"><?php echo htmlspecialchars($row['tujuan_asli']); ?></span>
                            <span class="data-baru-berubah"><i class="fa-solid fa-arrow-right" style="font-size: 10px;"></i> <?php echo htmlspecialchars($row['tujuan_baru']); ?></span>
                        <?php else: ?>
                            <span class="data-tetap"><?php echo htmlspecialchars($row['tujuan_baru']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($jumlah_berubah): ?>
                            <span class="data-asli"><?php echo $row['jumlah_asli']; ?> Orang</span>
                            <span class="data-baru-berubah"><?php echo $row['jumlah_baru']; ?> Orang</span>
                        <?php else: ?>
                            <span class="data-tetap"><?php echo $row['jumlah_baru']; ?> Orang</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                        $tgl_asli_format = date('d/m/Y', strtotime($row['tgl_berangkat_asli'])) . " - " . date('d/m/Y', strtotime($row['tgl_pulang_asli']));
                        $tgl_baru_format = date('d/m/Y', strtotime($row['tgl_berangkat_baru'])) . " - " . date('d/m/Y', strtotime($row['tgl_pulang_baru']));
                        ?>
                        <?php if($tanggal_berubah): ?>
                            <span class="data-asli"><?php echo $tgl_asli_format; ?></span>
                            <span class="data-baru-berubah" style="font-size: 12px;"><?php echo $tgl_baru_format; ?></span>
                        <?php else: ?>
                            <span class="data-tetap"><?php echo $tgl_baru_format; ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="max-height: 60px; overflow-y: auto; font-style: normal;">
                            <span class="badge-info-perubahan" style="margin-bottom: 4px;">Alasan Perubahan:</span><br>
                            <small style="color: #555; line-height: 1.3; display: block;"><?php echo $catatan_alasan; ?></small>
                        </div>
                    </td>
                    <td>
                        <span style="color: #666; font-size: 12px;">
                            <?php echo date('d/m/Y', strtotime($row['tgl_pengajuan'])); ?><br>
                            <small style="color: #999;"><?php echo date('H:i', strtotime($row['tgl_pengajuan'])); ?> WIB</small>
                        </span>
                    </td>
                    <td align="center" style="vertical-align: middle;">
                        <a href="kelola_perubahan.php?id_ubah=<?php echo $row['id_ubah']; ?>" 
                           style="background: #6b3df5; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; display: inline-block; box-shadow: 0 2px 4px rgba(107,61,245,0.2);">
                           Review
                        </a>
                    </td>
                </tr>
            <?php
                    $no++;
                }
            } else {
                echo "<tr><td colspan='8' align='center' style='padding: 30px; color: #888; font-weight: bold;'>Tidak ada berkas pengajuan perubahan data trip baru saat ini.</td></tr>";
            }
            ?>
        </tbody>
    </table>



<?php endif; ?>




<?php elseif($menu == "payment"): ?>

<?php
  // Ambil parameter tipe (default: open), filter, dan sorting dari URL
  $type = $_GET['type'] ?? 'open'; 
  $filter = $_GET['filter'] ?? 'hari_ini';
  $sort = $_GET['sort'] ?? '';
  $destinasi = $_GET['destinasi'] ?? '';
  $status = $_GET['status'] ?? '';

  $btn_base = "padding: 8px 12px; border-radius: 8px; text-decoration: none; color: white; font-size: 13px; transition: 0.3s; white-space: nowrap;";
?>

<!-- SWITCH ANTARA OPEN TRIP DAN PRIVATE TRIP -->
<div style="margin-bottom: 15px; display: flex; gap: 10px;">
    <a href="index.php?menu=payment&type=open&filter=<?php echo $filter; ?>" 
       style="<?php echo $btn_base; ?> background: <?php echo ($type == 'open') ? '#321180' : '#ccc'; ?>; font-weight: bold;">
       OPEN TRIP
    </a>
    <a href="index.php?menu=payment&type=private&filter=<?php echo $filter; ?>" 
       style="<?php echo $btn_base; ?> background: <?php echo ($type == 'private') ? '#321180' : '#ccc'; ?>; font-weight: bold;">
       PRIVATE TRIP
    </a>
</div>

<div style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: nowrap; background: white; padding: 10px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
  
  <div style="display: flex; gap: 5px;">
    <?php
    $periods = [
      'hari_ini' => 'Hari Ini',
      'minggu_ini' => 'Minggu Ini',
      'bulan_ini' => 'Bulan Ini',
      'tahun_ini' => 'Tahun Ini',
      'semua' => 'Semua Data'
    ];

    foreach($periods as $key => $label):
      $bg = ($filter == $key) ? '#321180' : '#6b3df5';
    ?>
      <a href="index.php?menu=payment&type=<?php echo $type; ?>&filter=<?php echo $key; ?>&sort=<?php echo $sort; ?>&destinasi=<?php echo $destinasi; ?>&status=<?php echo $status; ?>" 
         style="<?php echo $btn_base; ?> background: <?php echo $bg; ?>;">
        <?php echo $label; ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div style="width: 1px; height: 30px; background: #ddd; margin: 0 5px;"></div>

  <form action="index.php" method="get" style="display: flex; gap: 10px; align-items: center; flex: 1;">
    <input type="hidden" name="menu" value="payment">
    <input type="hidden" name="type" value="<?php echo $type; ?>">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">

    <select name="sort" style="padding: 7px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px; outline: none;">
      <option value="">Urutkan Berdasarkan</option>
      <option value="NOM_ASC" <?php if($sort == 'NOM_ASC') echo 'selected'; ?>>Nominal Terkecil</option>
      <option value="NOM_DESC" <?php if($sort == 'NOM_DESC') echo 'selected'; ?>>Nominal Terbesar</option>
      <option value="PES_ASC" <?php if($sort == 'PES_ASC') echo 'selected'; ?>>Peserta Paling Sedikit</option>
      <option value="PES_DESC" <?php if($sort == 'PES_DESC') echo 'selected'; ?>>Peserta Terbanyak</option>
    </select>

    <select name="destinasi" style="padding: 7px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px; outline: none;">
      <option value="">Semua Destinasi</option>
      <?php
      // Mengambil destinasi berdasarkan tipe trip
      $query_dest = ($type == 'open') ? "SELECT DISTINCT tujuan FROM tujuan" : "SELECT DISTINCT tujuan FROM private_trip";
      $list_trip = kueri($query_dest);
      while($t = ambil($list_trip)){
        $selected = ($destinasi == $t['tujuan']) ? "selected" : "";
        echo "<option value='{$t['tujuan']}' $selected>{$t['tujuan']}</option>";
      }
      ?>
    </select>

    <select name="status" style="padding: 7px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px; outline: none;">
      <option value="">Semua Status</option>
      <option value="Belum Diverifikasi" <?php if($status == 'Belum Diverifikasi') echo 'selected'; ?>>Belum Diverifikasi</option>
      <option value="Diverifikasi" <?php if($status == 'Diverifikasi') echo 'selected'; ?>>Diverifikasi</option>
      <option value="Ditolak" <?php if($status == 'Ditolak') echo 'selected'; ?>>Ditolak</option>
    </select>

    <button type="submit" style="padding: 8px 15px; background: #321180; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: bold;">
      Terapkan
    </button>
  </form>
</div>

<?php
$where = [];
if ($filter == 'hari_ini') {
    $where[] = "DATE(pay.tgl_bayar) = CURDATE()";
} elseif ($filter == 'minggu_ini') {
    $where[] = "YEARWEEK(pay.tgl_bayar, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter == 'bulan_ini') {
    $where[] = "MONTH(pay.tgl_bayar) = MONTH(CURDATE()) AND YEAR(pay.tgl_bayar) = YEAR(CURDATE())";
} elseif ($filter == 'tahun_ini') {
    $where[] = "YEAR(pay.tgl_bayar) = YEAR(CURDATE())";
}

if ($status != '') $where[] = "pay.status = '$status'";

// Query bersyarat berdasarkan tipe (Open atau Private)
if ($type == 'open') {
    if ($destinasi != '') $where[] = "tj.tujuan = '$destinasi'";
    $kondisi = (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";
    
    switch($sort) {
        case 'NOM_ASC': $order = "ORDER BY pay.nominal ASC"; break;
        case 'NOM_DESC': $order = "ORDER BY pay.nominal DESC"; break;
        case 'PES_ASC': $order = "ORDER BY b.jumlah_peserta ASC"; break;
        case 'PES_DESC': $order = "ORDER BY b.jumlah_peserta DESC"; break;
        default: $order = "ORDER BY pay.id_payment DESC"; break;
    }

    $sql = "SELECT pay.*, a.username AS nama_pemesan, tj.tujuan, b.tgl_booking, b.jumlah_peserta 
            FROM payment_open pay
            JOIN booking b ON pay.id_booking = b.id_booking
            JOIN trip t ON b.id_trip = t.id_trip
            JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
            JOIN akun a ON b.id_akun = a.id_akun
            $kondisi $order";
} else {
    // Logika untuk Private Trip
    if ($destinasi != '') $where[] = "pt.tujuan = '$destinasi'";
    $kondisi = (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";

    switch($sort) {
        case 'NOM_ASC': $order = "ORDER BY pay.nominal ASC"; break;
        case 'NOM_DESC': $order = "ORDER BY pay.nominal DESC"; break;
        case 'PES_ASC': $order = "ORDER BY pt.jumlah_peserta ASC"; break;
        case 'PES_DESC': $order = "ORDER BY pt.jumlah_peserta DESC"; break;
        default: $order = "ORDER BY pay.id_payment DESC"; break;
    }

    $sql = "SELECT pay.*, a.username AS nama_pemesan, pt.tujuan, pt.tgl_booking, pt.jumlah_peserta 
            FROM payment_private pay
            JOIN private_trip pt ON pay.id_private = pt.id_private
            JOIN akun a ON pt.id_akun = a.id_akun
            $kondisi $order";
}

$data = kueri($sql);
?>

<a href="export_pembayaran.php?type=<?php echo $type; ?>&filter=<?php echo $filter; ?>&sort=<?php echo $sort; ?>&destinasi=<?php echo $destinasi; ?>&status=<?php echo $status; ?>" 
   style="padding: 8px 15px; background: green; color: white; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold;">
   Download Excel (<?php echo strtoupper($type); ?>)
</a>

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
  <tbody>
    <?php
    $no = 1;
    if(mysqli_num_rows($data) > 0){
        while($row = ambil($data)){
          $status_style = "";
          if($row['status'] == 'Diverifikasi') $status_style = "color: green; font-weight: bold;";
          elseif($row['status'] == 'Ditolak') $status_style = "color: red; font-weight: bold;";
          else $status_style = "color: orange; font-weight: bold;";

          // Tentukan link detail berdasarkan tipe
          $link_detail = ($type == 'open') ? "detail_payment.php" : "detail_payment_private.php";
    ?>
          <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $row['tujuan']; ?></td>
            <td><?php echo $row['nama_pemesan']; ?></td>
            <td><?php echo $row['jumlah_peserta']; ?> Orang</td>
            <td><?php echo date('d/m/Y H:i', strtotime($row['tgl_bayar'])); ?></td>
            <td><?php echo date('d/m/Y', strtotime($row['tgl_booking'])); ?></td>
            <td>Rp <?php echo number_format($row['nominal']); ?></td>
            <td>
              <a href="../gambar/payment/<?php echo $row['bukti_bayar']; ?>" target="_blank">
                <img src="../gambar/payment/<?php echo $row['bukti_bayar']; ?>" width="80" style="border-radius: 4px;">
              </a>
            </td>
            <td style="<?php echo $status_style; ?>"><?php echo $row['status']; ?></td>
            <td>
              <a href="<?php echo $link_detail; ?>?id=<?php echo $row['id_payment']; ?>" style="text-decoration: underline; color: #321180;">Detail</a>
            </td>
          </tr>
    <?php
          $no++;
        }
    } else {
        echo "<tr><td colspan='10' align='center' style='padding: 20px;'>Data pembayaran tidak ditemukan.</td></tr>";
    }
    ?>
  </tbody>
</table>




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
