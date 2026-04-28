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
    <a href="index.php?menu=booking">Pesanan</a>
    <a href="index.php?menu=payment">Pembayaran</a>
    <a href="index.php?menu=peserta">Peserta</a>
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

$hasil = kueri("SELECT * FROM trip");
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

$data = kueri("SELECT (t.kuota - COUNT(b.id_trip)) sisa
FROM trip t JOIN booking b ON t.id_trip = b.id_trip
WHERE t.id_trip = $id AND status != 'Dibatalkan'");
$sisa = ambil($data)['sisa'] ?? $row['kuota'];

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
      <option value="DESC" <?php if($sort == 'DESC') echo 'selected'; ?>>Terbesar ke Terkecil</option>
    </select>

    <select name="destinasi" style="padding: 7px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px; outline: none;">
      <option value="">Semua Destinasi</option>
      <?php
      $list_trip = kueri("SELECT DISTINCT tujuan FROM trip");
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

if ($destinasi != '') $where[] = "t.tujuan = '$destinasi'";
if ($status != '') $where[] = "b.status = '$status'";

$kondisi = (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";
$order = ($sort != '') ? "ORDER BY total_bayar $sort" : "";

$data_booking = kueri("SELECT b.*, t.tujuan, t.harga, t.tgl_berangkat, p.nama,
(SELECT SUM(nominal) FROM payment WHERE id_booking=b.id_booking AND status='Diverifikasi') total_bayar
FROM booking b
JOIN trip t ON b.id_trip=t.id_trip
JOIN peserta p ON b.id_peserta=p.id_peserta
$kondisi
$order");
?>

<table>
<tr>
  <th>No</th>
  <th>Trip</th>
  <th>Peserta</th>
  <th>Tanggal Booking</th>
  <th>Berangkat</th>
  <th>Pembayaran</th>
  <th>Status</th>
  <th>Aksi</th>
</tr>

<?php
$no=1;
while($row=ambil($data_booking)){
  $bayar = $row['total_bayar'] ?? 0;
  echo "<tr>";
  echo "<td>$no</td>";
  echo "<td>{$row['tujuan']}</td>";
  echo "<td>{$row['nama']}</td>";
  echo "<td>{$row['tgl_booking']}</td>";
  echo "<td>{$row['tgl_berangkat']}</td>";
  echo "<td>Rp ".number_format($bayar)." / ".number_format($row['harga'])."</td>";
  echo "<td>{$row['status']}</td>";
  echo "<td><a href='detail_booking.php?id={$row['id_booking']}'>Detail</a></td>";
  echo "</tr>";
  $no++;
}
?>
</table>






<?php elseif($menu == "payment"): ?>

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
      <a href="index.php?menu=payment&filter=<?php echo $key; ?>&sort=<?php echo $sort; ?>&destinasi=<?php echo $destinasi; ?>&status=<?php echo $status; ?>" 
         style="<?php echo $btn_base; ?> background: <?php echo $bg; ?>;">
        <?php echo $label; ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div style="width: 1px; height: 30px; background: #ddd; margin: 0 5px;"></div>

  <form action="index.php" method="get" style="display: flex; gap: 10px; align-items: center; flex: 1;">
    <input type="hidden" name="menu" value="payment">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">

    <select name="sort" style="padding: 7px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px; outline: none;">
      <option value="">Urutkan Nominal</option>
      <option value="ASC" <?php if($sort == 'ASC') echo 'selected'; ?>>Terkecil ke Terbesar</option>
      <option value="DESC" <?php if($sort == 'DESC') echo 'selected'; ?>>Terbesar ke Terkecil</option>
    </select>

    <select name="destinasi" style="padding: 7px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px; outline: none;">
      <option value="">Semua Destinasi</option>
      <?php
      $list_trip = kueri("SELECT DISTINCT tujuan FROM trip");
      while($t = ambil($list_trip)){
        $selected = ($destinasi == $t['tujuan']) ? "selected" : "";
        echo "<option value='{$t['tujuan']}' $selected>{$t['tujuan']}</option>";
      }
      ?>
    </select>

    <select name="status" style="padding: 7px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px; outline: none;">
      <option value="">Semua Status</option>
      <?php
      $list_status = kueri("SELECT DISTINCT status FROM payment");
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
    $where[] = "DATE(pay.tgl_bayar) = CURDATE()";
} elseif ($filter == 'minggu_ini') {
    $where[] = "YEARWEEK(pay.tgl_bayar, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter == 'bulan_ini') {
    $where[] = "MONTH(pay.tgl_bayar) = MONTH(CURDATE()) AND YEAR(pay.tgl_bayar) = YEAR(CURDATE())";
} elseif ($filter == 'tahun_ini') {
    $where[] = "YEAR(pay.tgl_bayar) = YEAR(CURDATE())";
}

if ($destinasi != '') $where[] = "t.tujuan = '$destinasi'";
if ($status != '') $where[] = "pay.status = '$status'";

$kondisi = (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";
$order = ($sort != '') ? "ORDER BY pay.nominal $sort" : "";

$data = kueri("SELECT pay.*, p.nama, t.tujuan, b.tgl_booking
FROM payment pay
JOIN booking b ON pay.id_booking=b.id_booking
JOIN trip t ON b.id_trip=t.id_trip
JOIN peserta p ON b.id_peserta=p.id_peserta
$kondisi
$order");
?>

<table>
<tr>
  <th>No</th>
  <th>Trip</th>
  <th>Peserta</th>
  <th>Tanggal Bayar</th>
  <th>Tanggal Booking</th>
  <th>Nominal</th>
  <th>Bukti</th>
  <th>Status</th>
  <th>Aksi</th>
</tr>

<?php
$no=1;
while($row=ambil($data)){
  echo "<tr>";
  echo "<td>$no</td>";
  echo "<td>{$row['tujuan']}</td>";
  echo "<td>{$row['nama']}</td>";
  echo "<td>{$row['tgl_bayar']}</td>";
  echo "<td>{$row['tgl_booking']}</td>";
  echo "<td>Rp ".number_format($row['nominal'])."</td>";
  echo "<td><img src='../gambar/payment/{$row['bukti_bayar']}'></td>";
  echo "<td>{$row['status']}</td>";
  echo "<td><a href='detail_payment.php?id={$row['id_payment']}'>Detail</a></td>";
  echo "</tr>";
  $no++;
}
?>
</table>



<?php elseif($menu == "peserta"): ?>

<div style="margin-bottom: 20px;">
  <a href="index.php?menu=peserta&tab=open" class="btn-tambah" style="background: <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'open') ? '#321180' : '#6b3df5'; ?>;">Open Trip</a>
  <a href="index.php?menu=peserta&tab=private" class="btn-tambah" style="background: <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'private') ? '#321180' : '#6b3df5'; ?>;">Private Trip</a>
</div>

<?php
$tab = $_GET['tab'] ?? 'open';

if($tab == "open"):
  $data_peserta = kueri("SELECT p.*, a.username, 
  (SELECT t.tujuan FROM booking b JOIN trip t ON b.id_trip = t.id_trip WHERE b.id_peserta = p.id_peserta AND b.status = 'Lunas' ORDER BY t.tgl_berangkat DESC LIMIT 1) AS trip_terakhir,
  (SELECT COUNT(*) FROM booking b WHERE b.id_peserta = p.id_peserta AND b.status = 'Lunas') AS total_trip
  FROM peserta p 
  JOIN akun a ON p.id_akun = a.id_akun");
?>

<table>
<tr>
  <th>No</th>
  <th>Username</th>
  <th>Nama Lengkap</th>
  <th>No. HP</th>
  <th>Tanggal Lahir</th>
  <th>Alamat</th>
  <th>Riwayat Kesehatan</th>
  <th>Trip Terakhir</th>
  <th>Total Trip</th>
</tr>

<?php
  $no = 1;
  while($row = ambil($data_peserta)){
    $trip = $row['trip_terakhir'] ?? '-';
    echo "<tr>";
    echo "<td>$no</td>";
    echo "<td>{$row['username']}</td>";
    echo "<td>{$row['nama']}</td>";
    echo "<td>{$row['no_hp']}</td>";
    echo "<td>{$row['tgl_lahir']}</td>";
    echo "<td>{$row['alamat']}</td>";
    echo "<td>{$row['riwayat']}</td>";
    echo "<td>$trip</td>";
    echo "<td>{$row['total_trip']} Kali</td>";
    echo "</tr>";
    $no++;
  }
?>
</table>

<?php else: ?>

<?php
  $data_member = kueri("SELECT m.*, pr.tujuan, pr.nama AS penanggung_jawab 
  FROM member m 
  JOIN private pr ON m.id_private = pr.id_private");
?>

<table>
<tr>
  <th>No</th>
  <th>Nama Member</th>
  <th>Tanggal Lahir</th>
  <th>Alamat</th>
  <th>Riwayat Kesehatan</th>
  <th>Tujuan Trip</th>
  <th>Penanggung Jawab</th>
</tr>

<?php
  $no = 1;
  while($row = ambil($data_member)){
    echo "<tr>";
    echo "<td>$no</td>";
    echo "<td>{$row['nama']}</td>";
    echo "<td>{$row['tgl_lahir']}</td>";
    echo "<td>{$row['alamat']}</td>";
    echo "<td>{$row['riwayat']}</td>";
    echo "<td>{$row['tujuan']}</td>";
    echo "<td>{$row['penanggung_jawab']}</td>";
    echo "</tr>";
    $no++;
  }
?>
</table>

<?php endif; ?>

<?php endif; ?>


</div>
</div>
</div>

</body>
</html>