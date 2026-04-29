<?php 
$konek = mysqli_connect("127.0.0.1", "root", "");
$buat_db = "CREATE DATABASE IF NOT EXISTS rebon_adventure";
mysqli_query($konek, $buat_db);
mysqli_select_db($konek, "rebon_adventure");
$katalog = "CREATE TABLE IF NOT EXISTS katalog (
    id_katalog INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    deskripsi TEXT NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$trip = "CREATE TABLE IF NOT EXISTS trip (
    id_trip INT AUTO_INCREMENT PRIMARY KEY,
    tujuan VARCHAR(50) NOT NULL,
    tgl_berangkat DATE NOT NULL,
    tgl_pulang DATE NOT NULL,
    harga INT NOT NULL,
    harga_dp INT NOT NULL,
    kuota INT NOT NULL,
    rute VARCHAR(100) NOT NULL,
    publik BOOLEAN NOT NULL DEFAULT FALSE,
    catatan TEXT
)";
$gambar = "CREATE TABLE IF NOT EXISTS gambar (
    id_gambar INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    nama_file VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$itenerary = "CREATE TABLE IF NOT EXISTS itenerary (
    id_itenerary INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    mulai TIME NOT NULL,
    selesai TIME NOT NULL,
    kegiatan VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$meetpoint = "CREATE TABLE IF NOT EXISTS meetpoint (
    id_meetoint INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    waktu TIME NOT NULL,
    kota VARCHAR(50) NOT NULL,
    daerah VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$fasilitas = "CREATE TABLE IF NOT EXISTS fasilitas (
    id_fasilitas INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    fasilitas VARCHAR(100) NOT NULL,
    jenis ENUM('include', 'exclude') NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$booking = "CREATE TABLE IF NOT EXISTS booking (
    id_booking INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    id_akun INT,
    jumlah_peserta INT NOT NULL,
    tgl_booking DATETIME NOT NULL DEFAULT NOW(),
    status ENUM('Belum Bayar', 'Bayar non-DP', 'DP', 'Lunas', 'Dibatalkan', 'Refund') NOT NULL DEFAULT 'Belum Bayar',
    FOREIGN KEY (id_akun) REFERENCES akun(id_akun) ON DELETE CASCADE,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$detail = "CREATE TABLE IF NOT EXISTS detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_booking INT,
    id_peserta INT,
    FOREIGN KEY (id_booking) REFERENCES booking(id_booking) ON DELETE CASCADE,
    FOREIGN KEY (id_peserta) REFERENCES peserta_open(id_peserta) ON DELETE CASCADE
)";
$peserta_ot = "CREATE TABLE IF NOT EXISTS peserta_open (
    id_peserta INT AUTO_INCREMENT PRIMARY KEY,
    id_akun INT,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    usia INT NOT NULL,
    alamat VARCHAR(100) NOT NULL,
    riwayat VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_akun) REFERENCES akun(id_akun) ON DELETE CASCADE
)";
$payment_ot = "CREATE TABLE IF NOT EXISTS payment_open (
    id_payment INT AUTO_INCREMENT PRIMARY KEY,
    id_booking INT,
    tgl_bayar DATETIME NOT NULL DEFAULT NOW(),
    nominal INT NOT NULL,
    bukti_bayar VARCHAR(100) NOT NULL,
    status ENUM('Belum Diverifikasi', 'Diverifikasi', 'Ditolak') NOT NULL DEFAULT 'Belum Diverifikasi',
    catatan TEXT,
    FOREIGN KEY (id_booking) REFERENCES booking(id_booking) ON DELETE CASCADE
)";
$batal_ot = "CREATE TABLE IF NOT EXISTS batal_open (
    id_batal INT AUTO_INCREMENT PRIMARY KEY,
    id_booking INT,
    status BOOLEAN NOT NULL DEFAULT FALSE,
    tgl_pembatalan DATETIME NOT NULL DEFAULT NOW(),
    alasan TEXT NOT NULL,
    FOREIGN KEY (id_booking) REFERENCES booking(id_booking) ON DELETE CASCADE
)";
$akun = "CREATE TABLE IF NOT EXISTS akun (
    id_akun INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    role VARCHAR(10) NOT NULL
)";
$private = "CREATE TABLE IF NOT EXISTS private_trip (
    id_private INT AUTO_INCREMENT PRIMARY KEY,
    id_akun INT,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    tujuan VARCHAR(100) NOT NULL,
    tgl_berangkat DATE NOT NULL,
    tgl_pulang DATE NOT NULL,
    tgl_booking DATETIME NOT NULL DEFAULT NOW(),
    catatan TEXT,
    jumlah_peserta INT NOT NULL,
    harga INT DEFAULT NULL,
    harga_dp INT DEFAULT NULL,
    status_trip ENUM('Belum Disetujui', 'Disetujui', 'Ditolak') NOT NULL DEFAULT 'Belum Disetujui',
    status_bayar ENUM('Belum Bayar', 'DP', 'Lunas', 'Dibatalkan') NOT NULL DEFAULT 'Belum Bayar',
    FOREIGN KEY (id_akun) REFERENCES akun(id_akun) ON DELETE CASCADE
)";
$peserta_pt = "CREATE TABLE IF NOT EXISTS peserta_private (
    id_peserta INT AUTO_INCREMENT PRIMARY KEY,
    id_private INT,
    nama VARCHAR(100) NOT NULL,
    usia INT NOT NULL,
    alamat VARCHAR(100) NOT NULL,
    riwayat VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_private) REFERENCES private_trip(id_private) ON DELETE CASCADE
)";
$payment_pt = "CREATE TABLE IF NOT EXISTS payment_private (
    id_payment INT AUTO_INCREMENT PRIMARY KEY,
    id_private INT,
    tgl_bayar DATETIME NOT NULL DEFAULT NOW(),
    nominal INT NOT NULL,
    bukti_bayar VARCHAR(100) NOT NULL,
    status ENUM('Belum Diverifikasi', 'Diverifikasi', 'Ditolak') NOT NULL DEFAULT 'Belum Diverifikasi',
    catatan TEXT,
    FOREIGN KEY (id_private) REFERENCES private_trip(id_private) ON DELETE CASCADE
)";
$batal_pt = "CREATE TABLE IF NOT EXISTS batal_private (
    id_batal INT AUTO_INCREMENT PRIMARY KEY,
    id_private INT,
    status BOOLEAN NOT NULL DEFAULT FALSE,
    tgl_pembatalan DATETIME NOT NULL DEFAULT NOW(),
    alasan TEXT NOT NULL,
    FOREIGN KEY (id_private) REFERENCES private_trip(id_private) ON DELETE CASCADE
)";


mysqli_query($konek, $trip);
mysqli_query($konek, $katalog);
mysqli_query($konek, $gambar);
mysqli_query($konek, $itenerary);
mysqli_query($konek, $meetpoint);
mysqli_query($konek, $fasilitas);
mysqli_query($konek, $akun);
mysqli_query($konek, $peserta_ot);
mysqli_query($konek, $booking);
mysqli_query($konek, $detail);
mysqli_query($konek, $payment_ot);
mysqli_query($konek, $batal_ot);
mysqli_query($konek, $private);
mysqli_query($konek, $peserta_pt);
mysqli_query($konek, $payment_pt);
mysqli_query($konek, $batal_pt);

mysql_close($konek);
?>