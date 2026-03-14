<?php
require 'cek_db.php';
$konek = mysqli_connect("127.0.0.1", "root", "");
$cek_db = mysqli_select_db($konek, "rebon_adventure");
if (!$cek_db){
  echo "db belum ada, harap jalankan cek_db.php dulu";
  $konek = mysqli_connect("localhost", "root", "", "rebon_adventure");
  if(!$konek){
    die("Gagal terhubung ke db rebon: " . mysqli_connect_error());
  }
}
else{
  echo "berhasil masuk db rebon";
}
?>