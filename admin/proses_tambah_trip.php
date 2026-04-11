<?php
require 'fungsi.php';

$tujuan = $_POST['tujuan'];
$tgl_berangkat = $_POST['tgl_berangkat'];
$tgl_pulang = $_POST['tgl_pulang'];
$harga = $_POST['harga'];
$kuota = $_POST['kuota'];
$catatan = $_POST['catatan'];

$query_trip = "INSERT INTO trip (tujuan, tgl_berangkat, tgl_pulang, harga, kuota, catatan) 
VALUES ('$tujuan', '$tgl_berangkat', '$tgl_pulang', '$harga', '$kuota', '$catatan')";
kueri($query_trip);
$id_trip = mysqli_insert_id($konek);

$deskripsi = $_POST['deskripsi'];
kueri("INSERT INTO katalog (id_trip, deskripsi) 
VALUES ('$id_trip', '$deskripsi')");

foreach ($_POST['mulai'] as $key => $val) {
    $mulai = $_POST['mulai'][$key];
    $selesai = $_POST['selesai'][$key];
    $kegiatan = $_POST['kegiatan'][$key];
    kueri("INSERT INTO itenerary (id_trip, mulai, selesai, kegiatan) 
    VALUES ('$id_trip', '$mulai', '$selesai', '$kegiatan')");
}

foreach ($_POST['waktu_mp'] as $key => $val) {
    $waktu = $_POST['waktu_mp'][$key];
    $kota = $_POST['kota_mp'][$key];
    $daerah = $_POST['daerah_mp'][$key];
    kueri("INSERT INTO meetpoint (id_trip, waktu, kota, daerah) 
    VALUES ('$id_trip', '$waktu', '$kota', '$daerah')");
}

foreach ($_POST['fasilitas'] as $key => $val) {
    $fasil = $_POST['fasilitas'][$key];
    $jenis = $_POST['jenis_fasilitas'][$key];
    kueri("INSERT INTO fasilitas (id_trip, fasilitas, jenis) 
    VALUES ('$id_trip', '$fasil', '$jenis')");
}

if (!empty($_FILES['files']['name'][0])) {
    $ekstensi_diperbolehkan = ['jpg', 'jpeg', 'png', 'webp'];
    foreach ($_FILES['files']['name'] as $key => $val) {
        $nama_asli = $_FILES['files']['name'][$key];
        $tmp_name  = $_FILES['files']['tmp_name'][$key];
        $ekstensi = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
        if (in_array($ekstensi, $ekstensi_diperbolehkan)) {
            $nama_file_baru = time() . "_" . $key . "." . $ekstensi;
            $path_tujuan = "../gambar/upload/" . $nama_file_baru;
            if (move_uploaded_file($tmp_name, $path_tujuan)) {
                kueri("INSERT INTO gambar (id_trip, nama_file) 
                       VALUES ('$id_trip', '$nama_file_baru')");
            } else {
                echo "Gagal mengunggah file: $nama_asli";
            }
        } else {
            echo "Format file tidak didukung: $nama_asli";
        }
    }
}


header("Location: index.php");
?>
