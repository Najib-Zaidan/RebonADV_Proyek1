<?php
require 'fungsi.php';

$id_trip = $_POST['id_trip'];
$id_peserta = $_POST['id_peserta']; // sekarang array
$tgl_booking = date('Y-m-d H:i:s');
$status = "Belum Bayar";

// VALIDASI
if(empty($id_peserta)){
    echo "<script>alert('Pilih minimal 1 peserta');history.back();</script>";
    exit;
}

// LOOP SIMPAN 🔥
foreach($id_peserta as $p){
    $query = "INSERT INTO booking (id_trip, id_peserta, tgl_booking, status) 
              VALUES ('$id_trip', '$p', '$tgl_booking', '$status')";
    
    kueri($query);
}

 echo "<script>
        alert('Data berhasil disimpan!, Silahkan cek profile dan menu pesanan');
        window.location='profiluser.php';
      </script>";

?>


