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
    <h1>Selamat Datang, <?php echo $_SESSION['nama']; ?></h1>
    <p>Ini adalah halaman admin Rebon Adventure.</p>
    <a href="logout.php" onclick="return confirm('Yakin Ingin Keluar?')">Logout</a>
    <ul>
      <li>
        <a href="index.php?menu=katalog">Katalog</a>
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
      $_GET['menu'] = "katalog";
    }
    $menu = $_GET['menu'];
    /*var_dump($menu);
    die();*/
    $hasil = kueri("SELECT * FROM $menu");
    if($menu == "katalog"): ?>
      <table cellspacing = 0>
      <tr>
        <th>No.</th>
        <th>Id Trip</th>
        <th>Destinasi</th>
        <th>Jadwal Berangkat</th>
        <th>Titik Jemput</th>
        <th>Harga</th>
        <th>Kapasitas Peserta</th>
        <th>Sisa Kuota</th>
      </tr>
      <?php
        if(mysqli_num_rows($hasil)){
          $nomer = 1;
          while($row=ambil($hasil)){
            echo "<tr>";
            echo "<td>" . $nomer . "</td>";
            echo "<td>" . $row['Id_Trip'] . "</td>";
            echo "<td>" . $row['Tujuan_Destinasi'] . "</td>";
            echo "<td>" . $row['Jadwal_Trip'] . "</td>";
            echo "<td>" . $row['Meeting_Point'] . "</td>";
            echo "<td>" . $row['Harga_Trip'] . "</td>";
            echo "<td>" . $row['Kapasitas_Peserta'] . "</td>";
            echo "<td>" . $row['Sisa_Kuota'] . "</td>";
            echo "</tr>";
            $nomer++;
          }
        }
      ?>
    </table>
    <?php elseif($menu == "booking"):
      ?>
    <table>
      <tr>
        <th>No.</th>
        <th>ID Booking</th>
        <th>Tujuan Trip</th>
        <th>Harga Trip</th>
        <th>Nama Pelanggan</th>
        <th>No. HP</th>
        <th>Tanggal Booking</th>
      </tr>
    
    <?php
    if(mysqli_num_rows($hasil)){
      $nomer = 1;
      while($row = ambil($hasil)){
        $id = $row['Id_Booking'];
        $data = kueri("SELECT 
        k.Tujuan_Destinasi,
        k.Harga_Trip,
        d.Nama_Lengkap,
        d.Nomor_HP_No_Darurat
        FROM booking b 
        JOIN katalog k
        ON b.Id_Katalog = k.Id_Trip
        JOIN data_pelanggan d
        ON b.Id_Pelanggan = d.Id_Pelanggan
        WHERE Id_Booking = '$id'");
        
        $ambil = ambil($data);
        $tujuan = $ambil['Tujuan_Destinasi'];
        $harga = $ambil['Harga_Trip'];
        
        $nama = $ambil['Nama_Lengkap'];
        $nope = $ambil['Nomor_HP_No_Darurat'];
        
        echo "<tr>";
        echo "<td>" . $nomer . "</td>";
        echo "<td>" . $id . "</td>";
        echo "<td>" . $tujuan . "</td>";
        echo "<td>" . $harga . "</td>";
        echo "<td>" . $nama . "</td>";
        echo "<td>" . $nope . "</td>";
        echo "<td>" . $row['Tanggal_Booking'] . "</td>";
        echo "</tr>";
        $nomer++;
      }
        
    }
    ?>
    </table>
    <?php elseif($menu = "payment"): ?>
    <table>
      <tr>
        <th>No.</th>
        <th>ID Bayar</th>
        <th>Nominal</th>
        <th>ID Booking</th>
        <th>Tanggal Bayar</th>
        <th>Status</th>
      </tr>
    <?php
    if(mysqli_num_rows($hasil)){
      $nomer = 1;
      while($row = mysqli_fetch_assoc($hasil)){
        echo "<tr>";
        echo "<td>" . $nomer . "</td>";
        echo "<td>" . $row['Id_Bayar'] . "</td>";
        echo "<td>100.000</td>";
        echo "<td>" . $row['Id_Booking'] . "</td>";
        echo "<td>" . $row['Tanggal_Bayar'] . "</td>";
        echo "<td>Diverifikasi</td>";
        echo "</tr>";
        $nomer++;
      }
    }
    ?>
    <?php endif; ?>
    </table>
      
</body>
</html>
