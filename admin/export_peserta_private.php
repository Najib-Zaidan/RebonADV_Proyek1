<?php
require 'konek.php';
require 'fungsi.php';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_Peserta_PrivateTrip.xls");

$data = kueri("SELECT 
                pp.*, 
                pt.tujuan, 
                pt.nama AS penanggung_jawab, 
                a.username AS akun_pemesan 
              FROM peserta_private pp 
              JOIN private_trip pt ON pp.id_private = pt.id_private
              JOIN akun a ON pt.id_akun = a.id_akun
              ORDER BY pt.tgl_booking DESC");

echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Nama Peserta</th>
        <th>Usia</th>
        <th>Alamat</th>
        <th>Riwayat</th>
        <th>Tujuan</th>
        <th>Penanggung Jawab</th>
        <th>Username Pemesan</th>
      </tr>";

$no = 1;
while($row = ambil($data)){
    echo "<tr>
            <td>$no</td>
            <td>{$row['nama']}</td>
            <td>{$row['usia']} Thn</td>
            <td>{$row['alamat']}</td>
            <td>{$row['riwayat']}</td>
            <td>{$row['tujuan']}</td>
            <td>{$row['penanggung_jawab']}</td>
            <td>{$row['akun_pemesan']}</td>
          </tr>";
    $no++;
}

echo "</table>";
?>