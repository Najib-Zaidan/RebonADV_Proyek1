<?php
require 'konek.php';
require 'fungsi.php';

    $hasil = kueri("SELECT * FROM peserta")
    ?>
      <table cellspacing = 0>
      <tr>
        <th>No.</th>
        <th>Nama</th>
        <th>No Telepon</th>
        <th>Tanggal Lahir</th>
        <th>Alamat</th>
        <th>Riwayat Penyakit</th>
      </tr>
      <?php
        if(mysqli_num_rows($hasil)){
          $nomer = 1;
          while($row=ambil($hasil)){
           
            echo "<tr>";
            echo "<td>" . $nomer . "</td>";
            echo "<td>" . $row['nama'] . "</td>";
            echo "<td>" . $row['no_hp'] . "</td>";
            echo "<td>" . $row['tgl_lahir'] . "</td>";
            echo "<td>" . $row['alamat'] . "</td>";
            echo "<td>" . $row['riwayat'] . "</td>";
            echo "</tr>";
            $nomer++;
          }
        }

      ?>

      </table>