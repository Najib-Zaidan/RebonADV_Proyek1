<?php
session_start();
require "konek.php";

//CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("Location: ../login_user.html");
    exit;
}

$nama = $_POST['nama'];
$dd = $_POST['dd'];
$mm = $_POST['mm'];
$yyyy = $_POST['yyyy'];
$alamat = $_POST['alamat'];
$telepon = $_POST['telepon'];
$penyakit = $_POST['penyakit'];
$detail = $_POST['detail'];

$tanggal = $yyyy . "-" . $mm . "-" . $dd;
var_dump($_POST);
die();
$query = "INSERT INTO pendaftaran 
(nama, tanggal_lahir, alamat, telepon, penyakit, detail)
VALUES 
('$nama','$tanggal','$alamat','$telepon','$penyakit','$detail')";

// var_dump($_POST);
// die();

?>