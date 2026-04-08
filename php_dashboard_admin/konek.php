<?php
$konek = mysqli_connect("127.0.0.1", "root", "", "rebon_adventure");
var_dump($konek);  
if($konek){
    echo "koneksi berhasilll";
  }
  else {
    die("Gagal terhubung ke db rebon: " . mysqli_connect_error());
  }
?>