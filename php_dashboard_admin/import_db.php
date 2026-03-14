<?php
$konek = mysqli_connect("127.0.0.1", "root", "");
$buat_db = "CREATE DATABASE IF NOT EXISTS rebon_adventure";
mysqli_query($konek, $buat_db);
mysqli_select_db($konek, "rebon_adventure");
$katalog = "CREATE TABLE IF NOT EXISTS katalog (
    Id_Trip VARCHAR(8) PRIMARY KEY,
    Nama_Trip VARCHAR(50) NOT NULL,
    Jadwal_Trip DATE,
    Itinerary TEXT,
    Meeting_Point TEXT,
    Harga_Trip INT,
    Kapasitas_Peserta INT DEFAULT 0,
    Sisa_Kuota INT DEFAULT 0,
    Tujuan_Destinasi VARCHAR(50),
    Fasilitas_Trip TEXT,
    Catatan_Trip TEXT
)";
mysqli_query($konek, $katalog);

$admin = "CREATE TABLE IF NOT EXISTS admin (
    Id_Admin VARCHAR(8) PRIMARY KEY,
    Username VARCHAR(25) NOT NULL,
    Password VARCHAR(25) NOT NULL
)";
mysqli_query($konek, $admin);

$insert_admin = "INSERT IGNORE INTO admin (Id_Admin, Username, Password) VALUES 
('AD001', 'password', 'admin'),
('AD002', 'rebon2019', 'rebongasjos'),
('AD003', 'wongganteng', 'masmaskampus123'),
('AD004', 'admin_utama', 'rahasia123'),
('AD005', 'operator_rebon', 'op_rebon')";
mysqli_query($konek, $insert_admin);

mysqli_close($konek);
?>