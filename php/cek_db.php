<?php
$konek = mysqli_connect("127.0.0.1", "root", "");
$buat_db = "CREATE DATABASE IF NOT EXISTS rebon_adventure";
mysqli_query($konek, $buat_db);
mysqli_select_db($konek, "rebon_adventure");
$katalog = "CREATE TABLE IF NOT EXISTS Katalog (
    Id_Trip VARCHAR(8) PRIMARY KEY,
    Nama_Trip VARCHAR(50) NOT NULL,
    Jadwal_Trip DATE,
    Itinerary TEXT,
    Meeting_Point TEXT,
    Harga_Trip INT,
    Kapasitas_Peserta INT,
    Sisa_Kuota INT,
    Tujuan_Destinasi VARCHAR(50),
    Fasilitas_Trip TEXT,
    Catatan_Trip TEXT
)";
mysqli_query($konek, $katalog);
mysqli_close($konek);
?>