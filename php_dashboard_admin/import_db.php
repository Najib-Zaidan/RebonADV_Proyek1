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

$pengguna = "CREATE TABLE IF NOT EXISTS pengguna (
    Id_User VARCHAR(8) PRIMARY KEY,
    Username VARCHAR(25) NOT NULL,
    Password VARCHAR(25) NOT NULL
)";
mysqli_query($konek, $pengguna);

$pelanggan = "CREATE TABLE IF NOT EXISTS  data_pelanggan (
    Id_Pelanggan VARCHAR(8) PRIMARY KEY,
    Nama_Lengkap VARCHAR(100) NOT NULL,
    Alamat TEXT,
    Tanggal_Lahir DATE,
    Nomor_HP_No_Darurat VARCHAR(15),
    Riwayat_Penyakit TEXT
)";
mysqli_query($konek, $pelanggan);

$booking = "CREATE TABLE IF NOT EXISTS  booking (
    Id_Booking VARCHAR(8) PRIMARY KEY,
    Id_Katalog VARCHAR(8),
    Id_User VARCHAR(8),
    Id_Pelanggan VARCHAR(8),
    Tanggal_Booking DATETIME,
    FOREIGN KEY (Id_Katalog) REFERENCES katalog(Id_Trip) ON DELETE CASCADE,
    FOREIGN KEY (Id_User) REFERENCES pengguna(Id_User) ON DELETE CASCADE,
    FOREIGN KEY (Id_Pelanggan) REFERENCES data_pelanggan(Id_Pelanggan) ON DELETE CASCADE
)";
mysqli_query($konek, $booking);

$payment = "CREATE TABLE IF NOT EXISTS  payment (
    Id_Bayar VARCHAR(8) PRIMARY KEY,
    Id_Booking VARCHAR(8),
    Tanggal_Bayar DATETIME,
    Status_Bayar VARCHAR(20),
    FOREIGN KEY (Id_Booking) REFERENCES booking(Id_Booking) ON DELETE CASCADE
)";
mysqli_query($konek, $payment);

$admin = "CREATE TABLE IF NOT EXISTS admin (
    Id_Admin VARCHAR(8) PRIMARY KEY,
    Username VARCHAR(25) NOT NULL,
    Password VARCHAR(25) NOT NULL,
    nama VARCHAR(25) NOT NULL
)";
mysqli_query($konek, $admin);

$insert_admin = "INSERT IGNORE INTO admin (Id_Admin, Username, Password, nama) VALUES 
('AD001', 'password', 'admin', 'Rebon Admin 1'),
('AD002', 'rebon2019', 'rebongasjos', 'Rebon Admin 2'),
('AD003', 'wongganteng', 'masmaskampus123', 'Najib Zeruk Purut'),
('AD004', 'admin_utama', 'rahasia123', 'Ibnu Rebon'),
('AD005', 'operator_rebon', 'op_rebon', 'Operator')";
mysqli_query($konek, $insert_admin);

mysqli_close($konek);
?>