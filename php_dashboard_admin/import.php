<?php
$konek = mysqli_connect("127.0.0.1", "root", "");
$buat_db = "CREATE DATABASE IF NOT EXISTS rebon_adventure";
mysqli_query($konek, $buat_db);
mysqli_select_db($konek, "rebon_adventure");
$katalog = "CREATE TABLE IF NOT EXISTS katalog (
    id_katalog INT AUTO_INCREMENT PRIMARY KEY,
    deskripsi TEXT NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$trip = "CREATE TABLE IF NOT EXISTS trip (
    id_trip INT AUTO_INCREMENT PRIMARY KEY,
    tujuan VARCHAR(50) NOT NULL,
    tgl_berangkat DATE NOT NULL,
    tgl_pulang DATE NOT NULL,
    harga INT NOT NULL,
    kuota INT NOT NULL,
    catatan TEXT
)";
$gambar = "CREATE TABLE IF NOT EXISTS gambar(
    id_gambar INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    nama_file VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$itenerary = "CREATE TABLE IF NOT EXISTS itenerary(
    id_itenerary INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    mulai TIME NOT NULL,
    selesai TIME NOT NULL,
    kegiatan VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$meetpoint = "CREATE TABLE IF NOT EXISTS meetpoint(
    id_meetoint INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    waktu TIME NOT NULL,
    kota VARCHAR(50) NOT NULL,
    daerah VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$fasilitas = "CREATE TABLE IF NOT EXISTS fasilitas(
    id_fasilitas INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    fasilitas VARCHAR(100) NOT NULL,
    jenis VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$booking = "CREATE TABLE IF NOT EXISTS booking(
    id_booking INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    id_peserta INT,
    tgl_booking TIME NOT NULL,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE,
    FOREIGN KEY (id_peserta) REFERENCES peserta(id_peserta) ON DELETE CASCADE
)";
$payment = "CREATE TABLE IF NOT EXISTS payment(
    id_payment INT AUTO_INCREMENT PRIMARY KEY,
    id_booking INT,
    tgl_bayar TIME NOT NULL,
    nominal INT NOT NULL,
    bukti_bayar VARCHAR(100) NOT NULL,
    status VARCHAR(30) NOT NULL,
    FOREIGN KEY (id_booking) REFERENCES booking(id_booking) ON DELETE CASCADE
)";
$peserta = "CREATE TABLE IF NOT EXISTS peserta(
    id_peserta INT AUTO_INCREMENT PRIMARY KEY,
    id_akun INT,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    tgl_lahir DATE NOT NULL,
    alamat VARCHAR(100) NOT NULL,
    riwayat VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_akun) REFERENCES akun(id_akun) ON DELETE CASCADE
)";
$akun = "CREATE TABLE IF NOT EXISTS akun(
    id_akun INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    role VARCHAR(10) NOT NULL,
)";
$private = "CREATE TABLE IF NOT EXISTS private(
    id_private INT AUTO_INCREMENT PRIMARY KEY,
    id_akun INT,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    tujuan VARCHAR(100) NOT NULL,
    tgl_berangkat DATE NOT NULL,
    tgl_pulang DATE NOT NULL,
    catatan TEXT,
    jumlah_peserta INT NOT NULL,
    FOREIGN KEY (id_akun) REFERENCES akun(id_akun) ON DELETE CASCADE
)";
$member = "CREATE TABLE IF NOT EXISTS member(
    id_member INT AUTO_INCREMENT PRIMARY KEY,
    id_private INT,
    nama VARCHAR(100) NOT NULL,
    tgl_lahir DATE NOT NULL,
    alamat VARCHAR(100) NOT NULL,
    riwayat VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_private) REFERENCES private(id_private) ON DELETE CASCADE
)";

mysqli_query($konek, $trip);
mysqli_query($konek, $katalog);
mysqli_query($konek, $akun);
mysqli_query($konek, $peserta);
mysqli_query($konek, $private);
mysqli_query($konek, $member);
mysqli_query($konek, $gambar);
mysqli_query($konek, $itenerary);
mysqli_query($konek, $meetpoint);
mysqli_query($konek, $fasilitas);
mysqli_query($konek, $booking);
mysqli_query($konek, $payment);


$insert_trip = "INSERT INTO trip (tujuan, tgl_berangkat, tgl_pulang, harga, kuota, catatan) VALUES 
('Gunung Ciremai', '2026-04-03', '2026-04-04', 500000, 30, 'Harap Membawa Perlengkapan Tidur'),
('Gunung Slamet', '2026-04-05', '2026-04-05', 350000, 10, 'Harap Membawa Perlengkapan Memasak'),
('Gunung Prau', '2026-04-01', '2026-04-02', 200000, 25, ''),
('Gunung Lawu', '2026-04-10', '2026-04-12', 720000, 15, ''),
('Gunung Merapi', '2026-04-07', '2026-04-07', 400000, 45, 'Dilarang Membuang Sampah di kawah')";

$insert_katalog = "INSERT INTO katalog (id_trip, deskripsi) VALUES 
(1, 'Gunung Ciremai memiliki tinggi lebih dari 3000 Mdpl dan menawarkan pemandangan yang luar biasa ...'),
(2, 'Gunung Slamet merupakan salah satu gunung dengan pemandangan yang asri di Jawa Tengah ...'),
(3, 'Gunung Prau berada di Dieng, Wonosobo, gunung ini memiliki tinggi lebih 2500 Mdpl ...'),
(4, 'Gunung Lawu berada di Jawa Timur dan menjadi gunung paling favorit untuk didaki ...'),
(5, 'Gunung Merapi merupakan gunung yang sudah lama menjadi primadona bagi para pendaki ...')";

$insert_akun = "INSERT INTO akun (username, password, role) VALUES 
('admin', 'admin', 'admin'),
('123', '123', 'admin'),
('user', 'user', 'user'),
('123', '123', 'user'),
('radza', 'radza', 'user'),
('orang', 'orang', 'user'),
('bot', 'bot', 'admin')";

$insert_peserta = "INSERT INTO peserta (id_akun, nama, no_hp, tgl_lahir, alamat, riwayat) VALUES 
(3, 'najib', '0896', '2006-02-12', 'Cirebon Kota', ''),
(5, 'yayat', '0831', '2007-05-01', 'Kecamatan Kroya', ''),
(5, 'angga', '0858', '2004-03-17', 'Desa Bunder', 'Maag'),
(6, 'dai', '0878', '2005-07-21', 'Kecamatan Indramayu', 'Alergi dingin'),
(7, 'aryadi', '0821', '2009-08-17', 'Kabupaten Cirebon', 'Tulang geser')";

$insert_private = "INSERT INTO private (id_akun, nama, no_hp, tujuan, tgl_berangkat, tgl_pulang, catatan, jumlah_peserta) VALUES 
(6, 'gilang', '0896', 'Gunung Prau', '2026-02-12', '2026-02-14', '', 3),
(6, 'rohman', '0831', 'Gunung Slamet', '2027-05-01', '2027-05-02', 'Menggunakan Mobil Toyota Hiace', 2),
(7, 'adinda', '0858', 'Gunung Semeru', '2025-03-17', '2025-03-17', '', 4),
(5, 'sintia', '0878', 'Gunung Gede', '2025-07-21', '2025-07-22', 'Makan di RM Cita Rasa', 2),
(5, 'wildan', '0821', 'Gunung Sumbing', '2026-03-17', '2026-03-17', 'Berangkat via Full Tol', 3)";

$insert_member = "INSERT INTO member (id_private, nama, tgl_lahir, alamat, riwayat) VALUES 
(1, 'najib', '2006-02-12', 'Cirebon Kota', ''),
(2, 'yayat', '2007-05-01', 'Kecamatan Kroya', ''),
(3, 'angga', '2004-03-17', 'Desa Bunder', 'Maag'),
(4, 'dai', '2005-07-21', 'Kecamatan Indramayu', 'Alergi dingin'),
(5, 'aryadi', '2009-08-17', 'Kabupaten Cirebon', 'Tulang geser')
(1, 'Budi', '1990-05-12', 'Kabupaten Indramayu', ''),
(1, 'Siti', '1985-11-20', 'Kecamatan Sliyeg', 'Alergi debu'),
(2, 'Andi', '1998-02-28', 'Cirebon', 'Pernah operasi'),
(3, 'Dewi', '2001-07-15', 'Kecamatan Jatibarang', ''),
(3, 'Rian', '1992-09-30', 'Kabupaten Majalengka', ''),
(3, 'Eka', '1988-12-05', 'Kecamatan Balongan', 'Asma'),
(4, 'Gita', '1995-03-14', 'Kabupaten Subang', ''),
(5, 'Fajar', '1993-06-22', 'Kecamatan Karangampel', 'Alergi seafood'),
(5, 'Hendra', '1980-01-10', 'Kabupaten Kuningan', 'Hipertensi')";

$insert_gambar = "INSERT INTO gambar (id_trip, nama_file) VALUES 
(1, 'gunung1a'),
(1, 'gunung1b'),
(1, 'gunung1c'),
(2, 'gunung2a'),
(2, 'gunung2b'),
(2, 'gunung2c'),
(3, 'gunung3a'),
(3, 'gunung3b'),
(3, 'gunung3c'),
(4, 'gunung4a'),
(4, 'gunung4b'),
(4, 'gunung4c'),
(5, 'gunung5a'),
(5, 'gunung5b'),
(5, 'gunung5c')";

$insert_itenerary  = "INSERT INTO itenerary (id_trip, mulai, selesai, kegiatan)
VALUES 
(1, '04:00:00', '06:00:00', 'Pendakian Menuju Pos 1'),
(1, '07:00:00', '10:00:00', 'Tracking Jalur Hutan'),
(1, '11:00:00', '13:00:00', 'Istirahat di Pos Bayangan'),
(2, '03:00:00', '05:30:00', 'Summit Attack'),
(2, '06:00:00', '08:00:00', 'Menikmati Matahari Terbit'),
(2, '09:00:00', '12:00:00', 'Perjalanan Turun ke Basecamp'),
(3, '08:00:00', '11:00:00', 'Packing Logistik Puncak'),
(3, '12:00:00', '15:00:00', 'Lintas Punggungan Gunung'),
(3, '16:00:00', '18:00:00', 'Mendirikan Tenda di Sabana'),
(4, '05:00:00', '09:00:00', 'Eksplorasi Kawah Gunung'),
(4, '10:00:00', '12:00:00', 'Masak Logistik di Camp'),
(4, '13:00:00', '16:00:00', 'Navigasi Jalur Vegetasi'),
(5, '07:00:00', '10:00:00', 'Treking Lembah Hijau'),
(5, '11:00:00', '14:00:00', 'Pengamatan Flora Fauna'),
(5, '15:00:00', '17:30:00', 'Bongkar Tenda dan Turun')";

$insert_meetpoint = "INSERT INTO meetpoint (id_trip, waktu, kota, daerah)
VALUES 
(1, '01:15:00', 'Indramayu', 'Terminal Sindang'),
(1, '05:30:00', 'Cirebon', 'Stasiun Kejaksan'),
(1, '09:00:00', 'Majalengka', 'Basecamp Apuy'),
(2, '18:45:00', 'Jakarta', 'Kampung Rambutan'),
(2, '22:15:00', 'Bandung', 'Terminal Leuwi Panjang'),
(2, '02:00:00', 'Garut', 'Basecamp Bambu Runcing'),
(3, '06:30:00', 'Semarang', 'Stasiun Poncol'),
(3, '10:45:00', 'Wonosobo', 'Plaza Wonosobo'),
(3, '14:00:00', 'Wonosobo', 'Basecamp Patak Banteng'),
(4, '08:15:00', 'Surabaya', 'Terminal Purabaya'),
(4, '12:30:00', 'Malang', 'Stasiun Kota Baru'),
(4, '17:45:00', 'Lumajang', 'Pos Ranupani'),
(5, '07:00:00', 'Bekasi', 'Stasiun Bekasi'),
(5, '11:15:00', 'Bogor', 'Terminal Baranangsiang'),
(5, '15:30:00', 'Cianjur', 'Basecamp Gunung Putri')";

$insert_fasilitas = "INSERT INTO fasilitas (id_trip, fasilitas, jenis)
VALUES 
(1, 'Transportasi AC', 'include'),
(1, 'Simaksi & Perizinan', 'include'),
(1, 'Makan Selama Pendakian', 'include'),
(1, 'Peralatan Camping Pribadi', 'exclude'),
(1, 'Porter Pribadi', 'exclude'),
(1, 'Camilan/Snack Pribadi', 'exclude'),

(2, 'Tenda & Alat Masak', 'include'),
(2, 'Pemandu/Guide Gunung', 'include'),
(2, 'Asuransi Pendakian', 'include'),
(2, 'Transportasi ke Basecamp', 'exclude'),
(2, 'Jaket & Sepatu Gunung', 'exclude'),
(2, 'Oleh-oleh', 'exclude'),

(3, 'Dokumentasi Drone', 'include'),
(3, 'Tiket Masuk Wisata', 'include'),
(3, 'P3K & Safety Kit', 'include'),
(3, 'Makan di Luar Program', 'exclude'),
(3, 'Penginapan Hotel', 'exclude'),
(3, 'Tips Guide', 'exclude')

(4, 'Sewa Jeep 4x4', 'include'),
(4, 'Tiket Masuk Taman Nasional', 'include'),
(4, 'Makan Siang Box', 'include'),
(4, 'Sewa Kuda di Gunung', 'exclude'),
(4, 'Pengeluaran Pribadi', 'exclude'),
(4, 'Jaket Tebal/Winter', 'exclude'),

(5, 'Tenda Kapasitas 4 Orang', 'include'),
(5, 'Matras & Sleeping Bag', 'include'),
(5, 'Logistik Grup', 'include'),
(5, 'Senter/Headlamp', 'exclude'),
(5, 'Obat-obatan Khusus', 'exclude'),
(5, 'Biaya Kamar Mandi Basecamp', 'exclude')";

$insert_booking = "INSERT INTO booking (id_trip, id_peserta, tgl_booking, status)
VALUES 
(1, 3, '2026-04-01', 'Lunas'),
(1, 1, '2026-04-02', 'DP'),
(1, 5, '2026-04-03', 'Belum Bayar'),
(2, 2, '2026-04-01', 'Lunas'),
(2, 4, '2026-04-04', 'Dibatalkan'),
(2, 1, '2026-04-05', 'DP'),
(3, 5, '2026-04-02', 'Lunas'),
(3, 3, '2026-04-03', 'Belum Bayar'),
(3, 2, '2026-04-06', 'DP'),
(4, 4, '2026-04-01', 'Dibatalkan'),
(4, 1, '2026-04-04', 'Lunas'),
(4, 5, '2026-04-05', 'Belum Bayar'),
(5, 2, '2026-04-02', 'DP'),
(5, 4, '2026-04-03', 'Lunas'),
(5, 3, '2026-04-06', 'Dibatalkan')";

$insert_payment = "INSERT INTO payment (id_booking, tgl_bayar, nominal, bukti_bayar, status)
VALUES 
(1, '2026-04-02', 500000, '', 'Diverifikasi'),
(2, '2026-04-03', 250000, '', 'Diverifikasi'),
(3, '2026-04-03', 150000, '', 'Belum Diverifikasi'),
(4, '2026-04-04', 500000, '', 'Diverifikasi'),
(6, '2026-04-05', 350000, '', 'Diverifikasi'),
(7, '2026-04-05', 500000, '', 'Diverifikasi'),
(8, '2026-04-06', 200000, '', 'Belum Diverifikasi'),
(9, '2026-04-06', 300000, '', 'Diverifikasi'),
(11, '2026-04-07', 500000, '', 'Diverifikasi'),
(12, '2026-04-07', 450000, '', 'Diverifikasi'),
(13, '2026-04-08', 250000, '', 'Belum Diverifikasi'),
(14, '2026-04-08', 500000, '', 'Diverifikasi')";

mysqli_query($konek, $insert_trip);
mysqli_query($konek, $insert_katalog);
mysqli_query($konek, $insert_akun);
mysqli_query($konek, $insert_peserta);
mysqli_query($konek, $insert_private);
mysqli_query($konek, $insert_member);
mysqli_query($konek, $insert_gambar);
mysqli_query($konek, $insert_itenerary);
mysqli_query($konek, $insert_meetpoint);
mysqli_query($konek, $insert_fasilitas);
mysqli_query($konek, $insert_booking);
mysqli_query($konek, $insert_payment);

/* For Admin Only 
   DON'T USE THIS
   IF YOU NOT AN ADMIN
   PLEASE READ THE
   NOTE BELLOW !! */
   
mysqli_close($konek);
?>