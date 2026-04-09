<?php
require 'fungsi.php';
// 1. Tangkap ID Trip dari URL
$id_trip = $_GET['id'];

// 2. Ambil data utama dari tabel TRIP
$query_trip = kueri("SELECT * FROM trip WHERE id_trip = $id_trip");
$trip = ambil($query_trip);

// 3. Ambil data dari tabel-tabel relasi (Foreign Key)
$data_katalog   = kueri("SELECT * FROM katalog WHERE id_trip = $id_trip");
$data_gambar    = kueri("SELECT * FROM gambar WHERE id_trip = $id_trip");
$data_itinerary = kueri("SELECT * FROM itenerary WHERE id_trip = $id_trip ORDER BY mulai ASC");
$data_meetpoint = kueri("SELECT * FROM meetpoint WHERE id_trip = $id_trip");
$data_fasilitas = kueri("SELECT * FROM fasilitas WHERE id_trip = $id_trip");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Trip - <?php echo $trip['tujuan']; ?></title>
</head>
<body>

    <h1>Detail Trip: <?php echo $trip['tujuan']; ?></h1>
    <a href="index.php">Kembali ke Daftar Trip</a>
    <hr>

    <h3>Informasi Utama Trip</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr><th>ID Trip</th><td><?php echo $trip['id_trip']; ?></td></tr>
        <tr><th>Tujuan</th><td><?php echo $trip['tujuan']; ?></td></tr>
        <tr><th>Tanggal Berangkat</th><td><?php echo $trip['tgl_berangkat']; ?></td></tr>
        <tr><th>Tanggal Pulang</th><td><?php echo $trip['tgl_pulang']; ?></td></tr>
        <tr><th>Harga</th><td>Rp <?php echo number_format($trip['harga'], 0, ',', '.'); ?></td></tr>
        <tr><th>Kuota</th><td><?php echo $trip['kuota']; ?></td></tr>
        <tr><th>Catatan</th><td><?php echo $trip['catatan']; ?></td></tr>
    </table>

    <hr>

    <h3>Katalog & Deskripsi</h3>
    <ul>
        <?php while ($kat = ambil($data_katalog)): ?>
            <li><?php echo $kat['deskripsi']; ?></li>
        <?php endwhile; ?>
    </ul>

    <h3>Galeri Gambar</h3>
    <table border="1" cellpadding="5">
        <tr>
            <?php while ($img = ambil($data_gambar)): ?>
                <td>
                    <p><?php echo $img['nama_file']; ?></p>
                    </td>
            <?php endwhile; ?>
        </tr>
    </table>

    <h3>Itinerary (Jadwal Kegiatan)</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($itiner = ambil($data_itinerary)): ?>
                <tr>
                    <td><?php echo $itiner['mulai']; ?></td>
                    <td><?php echo $itiner['selesai']; ?></td>
                    <td><?php echo $itiner['kegiatan']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3>Meetpoint (Titik Kumpul)</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Kota</th>
                <th>Daerah</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($mp = ambil($data_meetpoint)): ?>
                <tr>
                    <td><?php echo $mp['waktu']; ?></td>
                    <td><?php echo $mp['kota']; ?></td>
                    <td><?php echo $mp['daerah']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3>Fasilitas</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Nama Fasilitas</th>
                <th>Jenis</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fas = ambil($data_fasilitas)): ?>
                <tr>
                    <td><?php echo $fas['fasilitas']; ?></td>
                    <td><?php echo $fas['jenis']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>