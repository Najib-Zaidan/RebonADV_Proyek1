<?php
require 'fungsi.php';

$id_trip = $_POST['id_trip'];
$tujuan = $_POST['tujuan'];
$tgl_berangkat = $_POST['tgl_berangkat'];
$tgl_pulang = $_POST['tgl_pulang'];
$harga = $_POST['harga'];
$kuota = $_POST['kuota'];
$catatan = $_POST['catatan'];
$deskripsi = $_POST['deskripsi'];

kueri("UPDATE trip SET tujuan='$tujuan', tgl_berangkat='$tgl_berangkat', tgl_pulang='$tgl_pulang', harga='$harga', kuota='$kuota', catatan='$catatan' WHERE id_trip=$id_trip");

kueri("UPDATE katalog SET deskripsi='$deskripsi' WHERE id_trip=$id_trip");

kueri("DELETE FROM itenerary WHERE id_trip=$id_trip");
foreach ($_POST['mulai'] as $key => $val) {
    $mulai = $_POST['mulai'][$key];
    $selesai = $_POST['selesai'][$key];
    $kegiatan = $_POST['kegiatan'][$key];
    kueri("INSERT INTO itenerary (id_trip, mulai, selesai, kegiatan) VALUES ($id_trip, '$mulai', '$selesai', '$kegiatan')");
}

kueri("DELETE FROM meetpoint WHERE id_trip=$id_trip");
foreach ($_POST['waktu_mp'] as $key => $val) {
    $waktu = $_POST['waktu_mp'][$key];
    $kota = $_POST['kota_mp'][$key];
    $daerah = $_POST['daerah_mp'][$key];
    kueri("INSERT INTO meetpoint (id_trip, waktu, kota, daerah) VALUES ($id_trip, '$waktu', '$kota', '$daerah')");
}

kueri("DELETE FROM fasilitas WHERE id_trip=$id_trip");
foreach ($_POST['fasilitas'] as $key => $val) {
    $fasil = $_POST['fasilitas'][$key];
    $jenis = $_POST['jenis_fasilitas'][$key];
    kueri("INSERT INTO fasilitas (id_trip, fasilitas, jenis) VALUES ($id_trip, '$fasil', '$jenis')");
}

$gambar_tetap = isset($_POST['gambar_lama']) ? $_POST['gambar_lama'] : [];
$res_db_gambar = kueri("SELECT nama_file FROM gambar WHERE id_trip=$id_trip");
while ($row = ambil($res_db_gambar)) {
    if (!in_array($row['nama_file'], $gambar_tetap)) {
        unlink("../gambar/upload/" . $row['nama_file']);
    }
}
kueri("DELETE FROM gambar WHERE id_trip=$id_trip");

if (!empty($gambar_tetap)) {
    foreach ($gambar_tetap as $nama_lama) {
        kueri("INSERT INTO gambar (id_trip, nama_file) VALUES ($id_trip, '$nama_lama')");
    }
}

if (isset($_FILES['files'])){
    foreach ($_FILES['files']['name'] as $key => $val) {
        if ($_FILES['files']['name'][$key] != "") {
            $nama_asli = $_FILES['files']['name'][$key];
            $ekstensi = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
            $nama_file_baru = time() . "_" . $key . "." . $ekstensi;
            /* $nama_file = time() . "_" . $_FILES['files']['name'][$key]; */
            $tmp_name = $_FILES['files']['tmp_name'][$key];
            move_uploaded_file($tmp_name, "../gambar/upload/" . $nama_file_baru);
            kueri("INSERT INTO gambar (id_trip, nama_file) VALUES ($id_trip, '$nama_file_baru')");
        }
    }
}

header("Location: index.php");
?>
