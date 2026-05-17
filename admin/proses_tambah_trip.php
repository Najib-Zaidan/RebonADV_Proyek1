<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'konek.php';
require 'fungsi.php';

// 1. Ambil Data Utama Trip dari Form (Sesuai Struktur Baru)
$id_tujuan     = $_POST['id_tujuan'];
$tgl_berangkat = $_POST['tgl_berangkat'];
$tgl_pulang    = $_POST['tgl_pulang'];
$harga         = $_POST['harga'];
$harga_dp      = $_POST['harga_dp'];
$kuota         = $_POST['kuota'];
$rute          = $_POST['rute'];
$publik        = $_POST['publik']; // Berisi '0' (Private) atau '1' (Publik)
$catatan       = $_POST['catatan'];

// 2. Insert ke Tabel Trip Terbaru
$query_trip = "INSERT INTO trip (id_tujuan, tgl_berangkat, tgl_pulang, harga, harga_dp, kuota, rute, publik, catatan) 
               VALUES ('$id_tujuan', '$tgl_berangkat', '$tgl_pulang', '$harga', '$harga_dp', '$kuota', '$rute', '$publik', '$catatan')";
kueri($query_trip);

// Ambil ID Trip yang baru saja dihasilkan
$id_trip = mysqli_insert_id($konek);

// 3. Insert ke Tabel Katalog Terbaru
$deskripsi = $_POST['deskripsi'];
$query_katalog = "INSERT INTO katalog (id_trip, deskripsi) VALUES ('$id_trip', '$deskripsi')";
kueri($query_katalog);

// 4. Insert Data Itinerary (Looping)
if (isset($_POST['mulai']) && is_array($_POST['mulai'])) {
    foreach ($_POST['mulai'] as $key => $val) {
        $mulai    = $_POST['mulai'][$key];
        $selesai  = $_POST['selesai'][$key];
        $kegiatan = $_POST['kegiatan'][$key];
        
        $query_itenerary = "INSERT INTO itenerary (id_trip, mulai, selesai, kegiatan) 
                            VALUES ('$id_trip', '$mulai', '$selesai', '$kegiatan')";
        kueri($query_itenerary);
    }
}

// 5. Insert Data Meetpoint (Looping - Menyesuaikan Nama Kolom ID 'id_meetoint' & 'waktu')
if (isset($_POST['waktu_mp']) && is_array($_POST['waktu_mp'])) {
    foreach ($_POST['waktu_mp'] as $key => $val) {
        $waktu  = $_POST['waktu_mp'][$key];
        $kota   = $_POST['kota_mp'][$key];
        $daerah = $_POST['daerah_mp'][$key];
        
        $query_meetpoint = "INSERT INTO meetpoint (id_trip, waktu, kota, daerah) 
                            VALUES ('$id_trip', '$waktu', '$kota', '$daerah')";
        kueri($query_meetpoint);
    }
}

// 6. Insert Data Fasilitas (Looping)
if (isset($_POST['fasilitas']) && is_array($_POST['fasilitas'])) {
    foreach ($_POST['fasilitas'] as $key => $val) {
        $fasil = $_POST['fasilitas'][$key];
        $jenis = $_POST['jenis_fasilitas'][$key];
        
        $query_fasilitas = "INSERT INTO fasilitas (id_trip, fasilitas, jenis) 
                            VALUES ('$id_trip', '$fasil', '$jenis')";
        kueri($query_fasilitas);
    }
}

// 7. Proses Unggah Gambar Trip (Tetap dengan Pengaman Ekstensi Ringan)
if (!empty($_FILES['files']['name'][0])) {
    $target_dir = "../gambar/upload/";

    // Validasi folder tujuan
    if (!is_dir($target_dir)) {
        die("Error: Folder tujuan tidak ditemukan.");
    }
    if (!is_writable($target_dir)) {
        die("Error: Folder tidak memiliki izin tulis (Permission Denied).");
    }

    $ekstensi_aman = ['jpg', 'jpeg', 'png', 'webp'];

    foreach ($_FILES['files']['name'] as $key => $val) {
        $nama_asli = $_FILES['files']['name'][$key];
        $tmp_name  = $_FILES['files']['tmp_name'][$key];
        $ekstensi  = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));

        // Cek ekstensi di server untuk mencegah file berbahaya lolos
        if (in_array($ekstensi, $ekstensi_aman)) {
            // Skema penamaan berkas baru menggunakan timestamp agar unik
            $nama_file_baru = time() . "_" . $key . "." . $ekstensi;
            $path_tujuan    = $target_dir . $nama_file_baru;

            if (move_uploaded_file($tmp_name, $path_tujuan)) {
                $query_gambar = "INSERT INTO gambar (id_trip, nama_file) 
                                 VALUES ('$id_trip', '$nama_file_baru')";
                kueri($query_gambar);
            }
        }
    }
}

// Alihkan kembali ke halaman utama jika berhasil
header("Location: index.php");
exit;
?>
