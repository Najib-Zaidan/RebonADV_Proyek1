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
    <a href="index.php?menu=pembatalan">Pembatalan</a>
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

$data_booking = kueri("SELECT b.*, t.tujuan, t.harga, t.tgl_berangkat, a.username AS nama,
                (SELECT SUM(nominal) FROM payment_open 
                 WHERE id_booking = b.id_booking AND status = 'Diverifikasi') AS total_bayar
                FROM booking b
                JOIN trip t ON b.id_trip = t.id_trip
                JOIN akun a ON b.id_akun = a.id_akun
                $kondisi
                $order");
?>

<a href="export_booking.php?filter=<?php echo $filter; ?>&sort=<?php echo $sort; ?>&destinasi=<?php echo $destinasi; ?>&status=<?php echo $status; ?>" 
   style="padding: 8px 15px; background: green; color: white; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold;">
   ⬇ Download Excel
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





<?php elseif($menu == "payment"): ?>

<?php
  // Ambil parameter filter dan sorting dari URL
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

    <!-- Dropdown Sorting yang Diperbarui -->
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
      $list_trip = kueri("SELECT DISTINCT tujuan FROM trip");
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

if ($destinasi != '') $where[] = "t.tujuan = '$destinasi'";
if ($status != '') $where[] = "pay.status = '$status'";

$kondisi = (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";

// Logika Sorting Baru
switch($sort) {
    case 'NOM_ASC': $order = "ORDER BY pay.nominal ASC"; break;
    case 'NOM_DESC': $order = "ORDER BY pay.nominal DESC"; break;
    case 'PES_ASC': $order = "ORDER BY b.jumlah_peserta ASC"; break;
    case 'PES_DESC': $order = "ORDER BY b.jumlah_peserta DESC"; break;
    default: $order = "ORDER BY pay.id_payment DESC"; break;
}

/* Kueri SQL Full */
$data = kueri("SELECT 
                pay.*, 
                a.username AS nama_pemesan, 
                t.tujuan, 
                b.tgl_booking, 
                b.jumlah_peserta 
              FROM payment_open pay
              JOIN booking b ON pay.id_booking = b.id_booking
              JOIN trip t ON b.id_trip = t.id_trip
              JOIN akun a ON b.id_akun = a.id_akun
              $kondisi
              $order");
?>

<a href="export_pembayaran.php?filter=<?php echo $filter; ?>&sort=<?php echo $sort; ?>&destinasi=<?php echo $destinasi; ?>&status=<?php echo $status; ?>" 
   style="padding: 8px 15px; background: green; color: white; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold;">
   ⬇ Download Excel
</a>

<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Trip</th>
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
    while($row = ambil($data)){
      // Styling status
      $status_style = "";
      if($row['status'] == 'Diverifikasi') $status_style = "color: green; font-weight: bold;";
      elseif($row['status'] == 'Ditolak') $status_style = "color: red; font-weight: bold;";
      else $status_style = "color: orange; font-weight: bold;";
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
          <a href="detail_payment.php?id=<?php echo $row['id_payment']; ?>" style="text-decoration: underline; color: #321180;">Detail</a>
        </td>
      </tr>
    <?php
      $no++;
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
   ⬇ Download Open Trip
</a>

<a href="export_peserta_private.php" 
   style="padding: 8px 15px; background: orange; color: white; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold;">
   ⬇ Download Private Trip
</a>

<?php
$tab = $_GET['tab'] ?? 'open';

if($tab == "open"):
  // Query untuk Peserta Open Trip
  $data_peserta = kueri("SELECT p.*, a.username, 
                (SELECT t.tujuan 
                 FROM booking b 
                 JOIN trip t ON b.id_trip = t.id_trip 
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
  <a href="index.php?menu=pembatalan&tab=private" class="btn-tambah" style="background: <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'private') ? '#321180' : '#6b3df5'; ?>; text-decoration: none; padding: 10px 20px; border-radius: 8px; color: white; display: inline-block;">Pembatalan Private</a>
</div>

<?php
$tab = $_GET['tab'] ?? 'open';

if($tab == "open"):
  // Query Pembatalan Open Trip
  // Menampilkan data pembatalan, tujuan trip, dan nama akun pemesan
  $data_batal = kueri("SELECT 
                        bo.*, 
                        t.tujuan, 
                        a.username, 
                        b.status AS status_booking
                      FROM batal_open bo
                      JOIN booking b ON bo.id_booking = b.id_booking
                      JOIN trip t ON b.id_trip = t.id_trip
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
          // Status pembatalan (BOOLEAN di database)
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
        <td>
          <a href="detail_batal_open.php?id=<?php echo $row['id_batal']; ?>" style="color: #321180;">Kelola</a>
        </td>
      </tr>
    <?php
          $no++;
        }
      } else {
        echo "<tr><td colspan='7' align='center' style='padding: 20px;'>Tidak ada pengajuan pembatalan open trip.</td></tr>";
      }
    ?>
  </tbody>
</table>

<?php else: ?>

<?php
  // Query Pembatalan Private Trip
  $data_batal_p = kueri("SELECT 
                          bp.*, 
                          pt.tujuan, 
                          pt.nama AS penanggung_jawab, 
                          a.username AS akun_pemesan
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
      if(mysqli_num_rows($data_batal_p) > 0){
        while($row = ambil($data_batal_p)){
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
        <td>
          <a href="detail_batal_private.php?id=<?php echo $row['id_batal']; ?>" style="color: #321180;">Kelola</a>
        </td>
      </tr>
    <?php
          $no++;
        }
      } else {
        echo "<tr><td colspan='7' align='center' style='padding: 20px;'>Tidak ada pengajuan pembatalan private trip.</td></tr>";
      }
    ?>
  </tbody>
</table>

<?php endif; ?>


<?php endif; ?>

</div>
</div>
</div>

</body>
</html>