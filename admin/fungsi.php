<?php
require 'konek.php';
function kueri($kueri){
  global $konek;
  $hasil = mysqli_query($konek, $kueri);
  return $hasil;
}
function ambil($data){
  $hasil = mysqli_fetch_assoc($data);
  return $hasil;
}
?>