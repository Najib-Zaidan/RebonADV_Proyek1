<?php 
session_start();
//echo "index php";
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
require 'konek.php';
require 'fungsi.php';
/* $katalog = mysqli_query($konek, "SELECT * FROM katalog"); */
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
</head>
<style>
  table{
    width: 70%;
    justify-content: flex-end;
  }
  table, tr, td, th{
    border: 1px solid black;
    border-collapse: collapse;
    padding: 0 5px;
    text-align: left;
    color: aliceblue;
    background-color: crimson;
  }
</style>
<body>
    <h1>Selamat Datang, <?php echo $_SESSION['username']; ?></h1>
    <p>Ini adalah halaman admin Rebon Adventure.</p>
    <a href="logout.php" onclick="return confirm('Yakin Ingin Keluar?')">Logout</a>
    <ul>
      <li>
        <a href="index.php?menu=trip">Open Trip</a>
      </li>
      <li>
        <a href="index.php?menu=booking">Pesanan</a>
      </li>
      <li>
        <a href="index.php?menu=payment">Pembayaran</a>
      </li>
    </ul>
    <?php
    if(!isset($_GET['menu'])){
      $_GET['menu'] = "trip";
    }
    $menu = $_GET['menu'];
    /*var_dump($menu);
    die();*/
    $hasil = kueri("SELECT * FROM $menu");
    if($menu == "trip"): 
    
    $hasil = kueri("SELECT * FROM trip")
    ?>
      <a href="tambah_trip_v2.php">Tambah Trip</a>
      <table cellspacing = 0>
      <tr>
        <th>No.</th>
        <th>Destinasi</th>
        <th>Jadwal Berangkat</th>
        <th>Durasi</th>
        <th>Titik Jemput</th>
        <th>Harga</th>
        <th>Sisa Kuota</th>
        <th>Aksi</th>
      </tr>
      <?php
        if(mysqli_num_rows($hasil)){
          $nomer = 1;
          while($row=ambil($hasil)){
            $id = $row['id_trip'];
            $data = kueri("SELECT
            (DATEDIFF(tgl_pulang, tgl_berangkat) + 1) durasi,
            (t.kuota - COUNT(b.id_trip)) sisa
            FROM trip t
            JOIN booking b
            ON t.id_trip = b.id_trip
            WHERE t.id_trip = $id AND status != 'Dibatalkan'");
            $ambil = ambil($data);
            $durasi = $ambil['durasi'];
            $sisa = $ambil['sisa'];
            $data = kueri("SELECT 
            m.kota 
            FROM trip t 
            JOIN meetpoint m 
            ON t.id_trip = m.id_trip 
            WHERE t.id_trip = $id");
            
            echo "<tr>";
            echo "<td>" . $nomer . "</td>";
            echo "<td>" . $row['tujuan'] . "</td>";
            echo "<td>" . $row['tgl_berangkat'] . "</td>";
            echo "<td>" . $durasi . "</td>";
            echo "<td>"; 
            while ($row1 = ambil($data)) {
            echo $row1['kota'] . "<br>"; 
            }
            echo "</td>";
            echo "<td>" . $row['harga'] . "</td>";
            echo "<td>" . $sisa . " / " . $row['kuota'] . "</td>";
            echo "<td>";
            echo "<a href='detail_trip.php?id=" . $id . "'>Detail</a> | ";
            echo "<a href='ubah_tripv2.php?id=" . $id . "'>Ubah</a> | ";
            echo "<a href='hapus_trip.php?id=" . $id . "' onclick=\"return confirm('Yakin ingin menghapus trip ke " . $row['tujuan'] . "?')\">Hapus</a>";
            echo "</td>";
            echo "</tr>";
            $nomer++;
          }
        }
      ?>
    </table>
    <?php elseif($menu == "booking"):
      
    $sql = "SELECT 
            b.id_booking,
            t.tujuan, 
            p.nama AS nama_peserta, 
            b.tgl_booking, 
            t.tgl_berangkat, 
            t.harga, 
            b.status AS status_pemesanan,
            (SELECT SUM(nominal) FROM payment WHERE id_booking = b.id_booking) AS total_bayar
        FROM booking b
        JOIN trip t ON b.id_trip = t.id_trip
        JOIN peserta p ON b.id_peserta = p.id_peserta
        ORDER BY b.tgl_booking DESC";

$data_booking = kueri($sql);

echo "<table border='1'>";
echo "<thead>
        <tr>
            <th>No</th>
            <th>Tujuan</th>
            <th>Nama Peserta</th>
            <th>Tanggal Pemesanan</th>
            <th>Jadwal Trip</th>
            <th>Progress Pembayaran</th>
            <th>Status</th>
        </tr>
      </thead>";
echo "<tbody>";

$no = 1;
while ($row = ambil($data_booking)) {
    // Logika untuk menangani jika belum ada pembayaran sama sekali (NULL jadi 0)
    $sudah_bayar = $row['total_bayar'] ?? 0;
    $harga_total = $row['harga'];

    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . $row['tujuan'] . "</td>";
    echo "<td>" . $row['nama_peserta'] . "</td>";
    echo "<td>" . $row['tgl_booking'] . "</td>";
    echo "<td>" . $row['tgl_berangkat'] . "</td>";
    
    // Menampilkan progress pembayaran: Terbayar / Harga Total
    echo "<td>Rp. " . number_format($sudah_bayar, 0, ',', '.') . " / " . number_format($harga_total, 0, ',', '.') . "</td>";
    
    echo "<td>" . $row['status_pemesanan'] . "</td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>"; ?>
    <?php elseif($menu = "payment"): ?>
    <?php
// Query untuk mengambil data pembayaran dan menghubungkannya ke trip, booking, dan peserta
$sql = "SELECT 
            t.tujuan AS nama_trip, 
            p.nama AS nama_peserta, 
            b.tgl_booking, 
            b.status AS status_book,
            pay.*
        FROM payment pay
        JOIN booking b ON pay.id_booking = b.id_booking
        JOIN trip t ON b.id_trip = t.id_trip
        JOIN peserta p ON b.id_peserta = p.id_peserta
        ORDER BY pay.tgl_bayar DESC";

$data_pembayaran = kueri($sql);

echo "<table border='1' cellpadding='10' cellspacing='0'>";
echo "<thead>
        <tr>
            <th>No</th>
            <th>Nama Trip</th>
            <th>Nama Peserta</th>
            <th>Tanggal Bayar</th>
            <th>Tanggal Booking</th>
            <th>Nominal</th>
            <th>Bukti Bayar</th>
            <th>Status Verifikasi</th>
            <th>Aksi</th>
        </tr>
      </thead>";
echo "<tbody>";

$no = 1;
while ($row = ambil($data_pembayaran)) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . $row['nama_trip'] . "</td>";
    echo "<td>" . $row['nama_peserta'] . "</td>";
    echo "<td>" . $row['tgl_bayar'] . "</td>";
    echo "<td>" . $row['tgl_booking'] . "</td>";
    echo "<td>Rp " . number_format($row['nominal'], 0, ',', '.') . "</td>";
    echo "<td>" . $row['bukti_bayar'] . "</td>";
    echo "<td>" . $row['status'] . "</td>";
    echo "<td><a href='detail_payment.php?id=" . $row['id_payment'] . "'>Detail</a></td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
?>
    <?php endif; ?>
      
</body>
</html>
