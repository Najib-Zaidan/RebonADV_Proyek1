<?php
require 'fungsi.php';
// 1. Tangkap ID Trip dari URL
$id_trip = $_GET['id'];

// 2. Ambil data utama dari tabel TRIP
$query_trip = kueri("SELECT * FROM trip WHERE id_trip = $id_trip");
$trip = ambil($query_trip);

// 3. Ambil data dari tabel-tabel relasi (Foreign Key)
$katalog   = ambil(kueri("SELECT * FROM katalog WHERE id_trip = $id_trip"));
$gambar    = (kueri("SELECT * FROM gambar WHERE id_trip = $id_trip"));
$itenerary = kueri("SELECT * FROM itenerary WHERE id_trip = $id_trip ORDER BY mulai ASC");
$meetpoint = kueri("SELECT * FROM meetpoint WHERE id_trip = $id_trip");
$fasilitas = kueri("SELECT * FROM fasilitas WHERE id_trip = $id_trip");
$peserta = kueri("SELECT p.*, b.tgl_booking
           FROM booking b
           JOIN peserta p 
           ON b.id_peserta = p.id_peserta
           WHERE b.id_trip = $id_trip");
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
    <table border="1" cellpadding="8" cellspacing="0">
        <tr><th>Tujuan</th><td><?php echo $trip['tujuan']; ?></td></tr>
        <tr><th>Tanggal Berangkat</th><td><?php echo $trip['tgl_berangkat']; ?></td></tr>
        <tr><th>Tanggal Pulang</th><td><?php echo $trip['tgl_pulang']; ?></td></tr>
        <tr><th>Harga</th><td>Rp <?php echo number_format($trip['harga'], 0, ',', '.'); ?></td></tr>
        <tr><th>Kuota</th><td><?php echo $trip['kuota']; ?></td></tr>
        <tr><th>Catatan</th><td><?php echo $trip['catatan']; ?></td></tr>
        <tr><th>Deskripsi Trip</th><td><?php echo $katalog['deskripsi']; ?></td></tr>
    </table>

    <table border="1" cellpadding="5">
        <tr>
            <?php while ($img = ambil($gambar)): ?>
                <td>
                    <p><?php echo $img['nama_file']; ?></p>
                    </td>
            <?php endwhile; ?>
        </tr>
    </table>
    
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($itn = ambil($itenerary)): ?>
                <tr>
                    <td><?php echo $itn['mulai']; ?></td>
                    <td><?php echo $itn['selesai']; ?></td>
                    <td><?php echo $itn['kegiatan']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Kota</th>
                <th>Daerah</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($mepo = ambil($meetpoint)): ?>
                <tr>
                    <td><?php echo $mepo['waktu']; ?></td>
                    <td><?php echo $mepo['kota']; ?></td>
                    <td><?php echo $mepo['daerah']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Nama Fasilitas</th>
                <th>Jenis</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fas = ambil($fasilitas)): ?>
                <tr>
                    <td><?php echo $fas['fasilitas']; ?></td>
                    <td><?php echo $fas['jenis']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <table border="1" cellpadding="8" cellspacing="0">
        <th>Daftar Peserta</th>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Tanggal Lahir</th>
                <th>Nomor HP</th>
                <th>Riwayat</th>
                <th>Tanggal Pesan</th>
            </tr>
        </thead>
        <tbody>
            <?php $nomer = 1; ?>
            <?php while ($row = ambil($peserta)): ?>
                <tr>
                    <td><?= $nomer ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['alamat']; ?></td>
                    <td><?php echo $row['tgl_lahir']; ?></td>
                    <td><?php echo $row['no_hp']; ?></td>
                    <td><?php echo $row['riwayat']; ?></td>
                    <td><?php echo $row['tgl_booking']; ?></td>
                    <?php $nomer++; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>