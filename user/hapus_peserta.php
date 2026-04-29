<?php
session_start();
require 'konek.php';

if(isset($_GET['id'])){

    $id = $_GET['id'];

    $query = "DELETE FROM peserta WHERE id_peserta = '$id'";

    if(mysqli_query($konek, $query)){
        echo "<script>
                alert('Data berhasil dihapus!');
                window.location='profiluser.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($konek);
    }

} else {
    echo "ID tidak ditemukan!";
}
?>