<?php
require 'konek.php';
require 'fungsi.php';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_Peserta_OpenTrip.xls");

$data = kueri("SELECT p.*, a.username, 
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

echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Username</th>
        <th>Nama</th>
        <th>No HP</th>
        <th>Usia</th>
        <th>Alamat</th>
        <th>Riwayat</th>
        <th>Trip Terakhir</th>
        <th>Total Trip</th>
      </tr>";

$no = 1;
while($row = ambil($data)){
    $trip = $row['trip_terakhir'] ?? '-';

    echo "<tr>
            <td>$no</td>
            <td>{$row['username']}</td>
            <td>{$row['nama']}</td>
            <td>{$row['no_hp']}</td>
            <td>{$row['usia']}</td>
            <td>{$row['alamat']}</td>
            <td>{$row['riwayat']}</td>
            <td>$trip</td>
            <td>{$row['total_trip']} Kali</td>
          </tr>";
    $no++;
}

echo "</table>";
?>