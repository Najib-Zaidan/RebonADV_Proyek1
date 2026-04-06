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
$trip = "CREATE TABLE IF NOT EXIST trip(
    id_trip INT AUTO_INCREMENT PRIMARY KEY,
    tujuan VARCHAR(50) NOT NULL,
    tgl_berangkat DATE NOT NULL,
    tgl_pulang DATE NOT NULL,
    harga INT NOT NULL,
    kuota INT NOT NULL,
    catatan TEXT
)";
$gambar = "CREATE TABLE IF NOT EXIST gambar(
    id_gambar INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    nama_file VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$itenerary = "CREATE TABLE IF NOT EXIST itenerary(
    id_itenerary INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    mulai TIME NOT NULL,
    selesai TIME NOT NULL,
    kegiatan VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$meetpoint = "CREATE TABLE IF NOT EXIST meetpoint(
    id_meetoint INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    waktu TIME NOT NULL,
    kota VARCHAR(50) NOT NULL,
    daerah VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$fasilitas = "CREATE TABLE IF NOT EXIST fasilitas(
    id_fasilitas INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    fasilitas VARCHAR(100) NOT NULL,
    jenis VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE
)";
$booking = "CREATE TABLE IF NOT EXIST booking(
    id_booking INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    id_peserta INT,
    tgl_booking TIME NOT NULL,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE,
    FOREIGN KEY (id_peserta) REFERENCES peserta(id_peserta) ON DELETE CASCADE
)";
$payment = "CREATE TABLE IF NOT EXIST payment(
    id_payment INT AUTO_INCREMENT PRIMARY KEY,
    id_trip INT,
    id_booking INT,
    tgl_bayar TIME NOT NULL,
    nominal INT NOT NULL,
    bukti_bayar VARCHAR(100) NOT NULL,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_trip) REFERENCES trip(id_trip) ON DELETE CASCADE,
    FOREIGN KEY (id_booking) REFERENCES booking(id_booking) ON DELETE CASCADE
)";
$peserta = "CREATE TABLE IF NOT EXIST peserta(
    id_peserta INT AUTO_INCREMENT PRIMARY KEY,
    id_akun INT,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    tgl_lahir DATE NOT NULL,
    alamat VARCHAR(100) NOT NULL,
    riwayat VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_akun) REFERENCES akun(id_akun) ON DELETE CASCADE
)";
$akun = "CREATE TABLE IF NOT EXIST akun(
    id_akun INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    role VARCHAR(10) NOT NULL,
)";
$private = "CREATE TABLE IF NOT EXIST private(
    id_private INT AUTO_INCREMENT PRIMARY KEY,
    id_akun INT,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    tgl_berangkat DATE NOT NULL,
    tgl_pulang DATE NOT NULL,
    catatan TEXT,
    jumlah_peserta INT NOT NULL,
    FOREIGN KEY (id_akun) REFERENCES akun(id_akun) ON DELETE CASCADE
)";
mysqli_query($konek, $trip);
mysqli_query($konek, $katalog);
mysqli_query($konek, $akun);
mysqli_query($konek, $peserta);
mysqli_query($konek, $private);
mysqli_query($konek, $gambar);
mysqli_query($konek, $itenerary);
mysqli_query($konek, $meetpoint);
mysqli_query($konek, $fasilitas);
mysqli_query($konek, $booking);
mysqli_query($konek, $payment);


$insert_trip = "INSERT INTO trip (id_trip, tujuan, tgl_berangkat, tgl_pulang, harga, kuota, catatan) VALUES 
('', 'Gunung Ciremai', '2026-04-03', '2026-04-04', 500000, 30, 'Harap Membawa Perlengkapan Tidur'),
('', 'Gunung Slamet', '2026-04-05', '2026-04-05', 350000, 10, 'Harap Membawa Perlengkapan Memasak'),
('', 'Gunung Prau', '2026-04-01', '2026-04-02', 200000, 25, ''),
('', 'Gunung Lawu', '2026-04-10', '2026-04-12', 720000, 15, ''),
('', 'Gunung Merapi', '2026-04-07', '2026-04-07', 400000, 45, 'Dilarang Membuang Sampah di kawah'),";

$insert_katalog = "INSERT INTO katalog (id_katalog, id_trip, deskripsi) VALUES 
('', 1, 'Gunung Ciremai merupakan salah satu gunung dengan tinggi lebih dari 3000 Mdpl yang menawarkan pemandangan yang luar biasa ...'),
";

mysqli_query($konek, $katalog);
$insert_katalog = "INSERT INTO katalog (Id_Trip, Nama_Trip, Jadwal_Trip, Tujuan_Destinasi, Itinerary, Meeting_Point, Harga_Trip, Fasilitas_Trip, Kapasitas_Peserta, Sisa_Kuota, Catatan_Trip) VALUES 
('TR000001', 'GN. Ciremai', '2025-09-25', 'Gunung Ciremai', 'pukul 10.00 start, 13.00 istirahat', 'Lohbener, Indramayu', 200000, 'Transportasi PP Full Tol', 10, 7, 'Harap membawa peralatan tidur'),
('TR000002', 'GN. Prau', '2025-01-25', 'Gunung Prau', 'pukul 08.00 start, 10.00 istirahat', 'Jatibarang, Indramayu', 500000, 'Transportasi, Makan', 15, 10, 'Harap membawa peralatan mandi'),
('TR000003', 'GN. Slamet', '2025-08-25', 'Gunung Slamet', 'pukul 14.00 start, 18.00 istirahat', 'Karangampel, Indramayu', 250000, 'Transportasi PP', 30, 27, 'tidak termasuk makan minum'),
('TR000004', 'Pantai Karangsong', '2026-04-10', 'Indramayu', '07.00 kumpul, 08.00 susur mangrove', 'Sindang, Indramayu', 50000, 'Tiket masuk & Perahu', 50, 45, 'Bawa baju ganti'),
('TR000005', 'GN. Merbabu', '2026-05-15', 'Boyolali', '10.00 basecamp, 12.00 mulai pendakian', 'Haurgeulis, Indramayu', 650000, 'Simaksi, Tenda, Makan', 12, 5, 'Khusus pendaki berpengalaman'),
('TR000006', 'GN. Gede', '2026-06-20', 'Cianjur', '21.00 kumpul, 01.00 mulai mendaki', 'Sliyeg, Indramayu', 400000, 'Transportasi & Simaksi', 20, 18, 'Wajib surat keterangan sehat'),
('TR000007', 'GN. Papandayan', '2026-07-05', 'Garut', '06.00 start trek, 10.00 camp', 'Losarang, Indramayu', 350000, 'Makan 2x, Guide', 25, 20, 'Cocok untuk pemula'),
('TR000008', 'GN. Lawu', '2026-08-12', 'Magetan', '08.00 via Cetho, 17.00 camp', 'Widasari, Indramayu', 700000, 'Transportasi & Porter Tim', 10, 4, 'Suhu sangat dingin'),
('TR000009', 'Pulau Biawak', '2026-09-01', 'Laut Jawa', '00.00 dermaga, 04.00 sampai pulau', 'Pasekan, Indramayu', 450000, 'Kapal, Makan, Alat Snorkel', 15, 8, 'Siapkan obat anti mabuk laut'),
('TR000010', 'GN. Sindoro', '2026-10-10', 'Temanggung', '07.00 ojek pos 1, 08.00 mulai jalan', 'Kandanghaur, Indramayu', 550000, 'Transportasi & Logistik', 12, 12, 'Bawa air minum tambahan'),
('TR000011', 'GN. Sumbing', '2026-11-20', 'Wonosobo', '09.00 start via Bowongso', 'Cikedung, Indramayu', 550000, 'Transportasi PP', 15, 10, 'Trek cukup terjal'),
('TR000012', 'GN. Arjuno', '2026-12-05', 'Malang', '10.00 kumpul basecamp Purwosari', 'Gabuswetan, Indramayu', 800000, 'Full Service', 8, 3, 'Durasi 3 hari 2 malam'),
('TR000013', 'GN. Salak', '2026-01-15', 'Bogor', '08.00 mulai tracking kawah ratu', 'Terisi, Indramayu', 300000, 'Tiket & Guide Lokal', 20, 15, 'Hati-hati jalur licin'),
('TR000014', 'GN. Merapi', '2026-02-10', 'Sleman', '23.00 start pendakian malam', 'Kroya, Indramayu', 450000, 'Transportasi & Snack', 15, 15, 'Melihat sunrise Merapi'),
('TR000015', 'GN. Kerinci', '2026-03-25', 'Jambi', '08.00 kumpul di Kersik Tuo', 'Lelea, Indramayu', 2500000, 'Tiket Pesawat & Porter', 5, 2, 'Ekspedisi Atap Sumatera')";
mysqli_query($konek, $insert_katalog);

$pengguna = "CREATE TABLE IF NOT EXISTS pengguna (
    Id_User VARCHAR(8) PRIMARY KEY,
    Username VARCHAR(25) NOT NULL,
    Password VARCHAR(25) NOT NULL
)";
mysqli_query($konek, $pengguna);
$insert_pengguna = "INSERT INTO pengguna (Id_User, Username, Password) VALUES 
('US000001', 'kuroneko', 'sikuromalastidur'),
('US000002', 'priasolo', 'wiwokdetok'),
('US000003', 'tukangsawit', 'hidupsawit'),
('US000004', 'gunung_lovers', 'naikgunung77'),
('US000005', 'pendaki_santai', 'santai_aja'),
('US000006', 'petualang_sejati', 'petualang_01'),
('US000007', 'ransel_biru', 'biru_langit'),
('US000008', 'jejak_kaki', 'jejak_petualang'),
('US000009', 'alam_bebas', 'bebas_lepas'),
('US000010', 'kopi_pagi', 'kopi_hitam')";
mysqli_query($konek, $insert_pengguna);

$pelanggan = "CREATE TABLE IF NOT EXISTS  data_pelanggan (
    Id_Pelanggan VARCHAR(8) PRIMARY KEY,
    Nama_Lengkap VARCHAR(100) NOT NULL,
    Alamat TEXT,
    Tanggal_Lahir DATE,
    Nomor_HP_No_Darurat VARCHAR(15),
    Riwayat_Penyakit TEXT
)";
mysqli_query($konek, $pelanggan);
$insert_pelanggan = "INSERT INTO data_pelanggan (Id_Pelanggan, Nama_Lengkap, Alamat, Tanggal_Lahir, Nomor_HP_No_Darurat, Riwayat_Penyakit) VALUES 
('PL000001', 'NAZIB JERUK', 'Jl. Jeruk No. 1, Jakarta', '1997-03-27', '08912326818', '-'),
('PL000002', 'DAI TRI WIBOWO', 'Jl. Merdeka No. 45, Bandung', '2007-01-30', '08212332212', 'ASMA'),
('PL000003', 'YAYAT AJI', 'Jl. Melati No. 12, Surabaya', '1986-09-12', '08128755288', 'LAMBUNG'),
('PL000004', 'BUDI SANTOSO', 'Jl. Mawar No. 3, Semarang', '1995-05-20', '08134455667', '-'),
('PL000005', 'SITI AMINAH', 'Jl. Anggrek No. 8, Medan', '1998-11-05', '08192233445', 'ALERGI DEBU'),
('PL000006', 'AGUS KURNIAWAN', 'Jl. Kenanga No. 2, Yogyakarta', '1992-07-15', '08567788990', 'MAAG'),
('PL000007', 'DEWI LESTARI', 'Jl. Kamboja No. 10, Bali', '2000-12-12', '08112233445', '-'),
('PL000008', 'EKO PRASETYO', 'Jl. Dahlia No. 5, Makassar', '1989-02-28', '08776655443', 'VERTIGO'),
('PL000009', 'RINA WATI', 'Jl. Tulip No. 7, Malang', '1996-06-18', '08223344556', '-'),
('PL000010', 'FAJAR SIDIK', 'Jl. Sakura No. 9, Solo', '2001-09-09', '08129988776', '-')";
mysqli_query($konek, $insert_pelanggan);

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
$insert_booking = "INSERT INTO booking (Id_Booking, Id_User, Id_Pelanggan, Id_Katalog, Tanggal_Booking) VALUES 
('BK000001', 'US000002', 'PL000001', 'TR000003', '2025-08-17 11:02:34'),
('BK000002', 'US000002', 'PL000002', 'TR000002', '2025-01-17 14:35:20'),
('BK000003', 'US000001', 'PL000003', 'TR000001', '2025-09-17 20:02:03'),
('BK000004', 'US000003', 'PL000004', 'TR000005', '2026-03-10 09:15:00'),
('BK000005', 'US000004', 'PL000005', 'TR000004', '2026-03-10 10:30:00'),
('BK000006', 'US000005', 'PL000006', 'TR000009', '2026-03-10 13:45:00'),
('BK000007', 'US000006', 'PL000007', 'TR000007', '2026-03-11 08:20:00'),
('BK000008', 'US000007', 'PL000008', 'TR000008', '2026-03-11 11:10:00'),
('BK000009', 'US000008', 'PL000009', 'TR000006', '2026-03-11 14:00:00'),
('BK000010', 'US000009', 'PL000010', 'TR000015', '2026-03-11 16:20:00'), 
('BK000011', 'US000001', 'PL000001', 'TR000010', '2026-03-12 08:00:00'),
('BK000012', 'US000003', 'PL000002', 'TR000011', '2026-03-12 09:30:00'),
('BK000013', 'US000005', 'PL000003', 'TR000012', '2026-03-12 10:45:00'),
('BK000014', 'US000007', 'PL000004', 'TR000013', '2026-03-12 13:15:00'),
('BK000015', 'US000010', 'PL000005', 'TR000014', '2026-03-12 15:00:00'),
('BK000016', 'US000002', 'PL000006', 'TR000005', '2026-03-13 07:20:00'),
('BK000017', 'US000004', 'PL000007', 'TR000008', '2026-03-13 09:10:00'),
('BK000018', 'US000006', 'PL000008', 'TR000004', '2026-03-13 11:55:00'),
('BK000019', 'US000008', 'PL000009', 'TR000001', '2026-03-13 14:40:00'),
('BK000020', 'US000009', 'PL000010', 'TR000002', '2026-03-13 16:05:00')";
mysqli_query($konek, $insert_booking);

$payment = "CREATE TABLE IF NOT EXISTS  payment (
    Id_Bayar VARCHAR(8) PRIMARY KEY,
    Id_Booking VARCHAR(8),
    Tanggal_Bayar DATETIME,
    Status_Bayar VARCHAR(20),
    FOREIGN KEY (Id_Booking) REFERENCES booking(Id_Booking) ON DELETE CASCADE
)";
mysqli_query($konek, $payment);
$insert_payment = "INSERT INTO payment (Id_Bayar, Id_Booking, Tanggal_Bayar, Status_Bayar) VALUES 
('BY000001', 'BK000001', '2025-08-21 10:59:12', 'Lunas'),
('BY000002', 'BK000002', '2025-01-21 17:12:34', 'DP'),
('BY000003', 'BK000003', '2025-09-21 21:23:01', 'Dibatalkan'),
('BY000004', 'BK000004', '2026-03-11 09:30:00', 'Lunas'),
('BY000005', 'BK000005', '2026-03-11 11:45:00', 'DP'),
('BY000006', 'BK000006', '2026-03-11 14:00:00', 'Lunas'),
('BY000007', 'BK000007', '2026-03-11 15:20:00', 'Dibatalkan'),
('BY000008', 'BK000008', '2026-03-12 10:10:00', 'Lunas'),
('BY000009', 'BK000009', '2026-03-12 14:50:00', 'DP'),
('BY000010', 'BK000010', '2026-03-12 17:00:00', 'Lunas'),
('BY000011', 'BK000011', '2026-03-13 08:30:00', 'Lunas'),
('BY000012', 'BK000012', '2026-03-13 10:00:00', 'DP'),
('BY000013', 'BK000013', '2026-03-13 11:15:00', 'Dibatalkan'),
('BY000014', 'BK000014', '2026-03-13 13:45:00', 'Lunas'),
('BY000015', 'BK000015', '2026-03-13 15:30:00', 'Lunas'),
('BY000016', 'BK000016', '2026-03-14 08:00:00', 'DP'),
('BY000017', 'BK000017', '2026-03-14 10:20:00', 'Lunas'),
('BY000018', 'BK000018', '2026-03-14 12:45:00', 'Dibatalkan'),
('BY000019', 'BK000019', '2026-03-14 15:00:00', 'Lunas'),
('BY000020', 'BK000020', '2026-03-14 17:10:00', 'DP')";
mysqli_query($konek, $insert_payment);

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