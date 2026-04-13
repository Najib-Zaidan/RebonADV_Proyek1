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
$jumlah = mysqli_num_rows($gambar);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Detail Trip - <?php echo $trip['tujuan']; ?></title>
</head>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 20px;
    min-height: 100vh;
    color: #fff;
    background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                url('bg1.jpeg') no-repeat center center fixed;
    background-size: cover;
}

h1 {
    text-align: center;
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

a {
    display: inline-block;
    color: #fff;
    text-decoration: none;
    background-color: crimson;
    padding: 8px 16px;
    border-radius: 5px;
    margin-bottom: 20px;
    transition: 0.3s;
    font-size: 0.9em;
}

a:hover {
    background-color: #a80a2c;
    box-shadow: 0 4px 15px rgba(220, 20, 60, 0.4);
}

hr {
    border: none;
    height: 1px;
    background: rgba(255, 255, 255, 0.3);
    margin: 20px 0;
}

table {
    width: 100%;
    max-width: 900px;
    margin: 25px auto;
    border-collapse: collapse;
    border: none !important; 
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
}

th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

th {
    background-color: rgba(220, 20, 60, 0.8);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85em;
}

thead th[colspan], tr th[colspan] {
    background-color: rgba(220, 20, 60, 0.9);
    text-align: center;
    font-size: 1.1em;
    letter-spacing: 1px;
}

tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.05);
    transition: 0.2s;
}

table:nth-of-type(2) td {
    text-align: center;
}

table:nth-of-type(2) p {
    background: rgba(220, 20, 60, 0.2);
    padding: 40px 10px; 
    border: 2px dashed rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    margin: 0;
}

.galeri-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
}

img {
    width: calc(50% - 8px); 
    aspect-ratio: 3 / 2;
    object-fit: cover;
    object-position: center;
    display: block;
    border-radius: 8px;
}

@media screen and (max-width: 600px) {
    table {
        font-size: 12px;
    }
    th, td {
        padding: 8px;
    }
}

</style>
<body>
    <h1>Detail Trip: <?php echo $trip['tujuan']; ?></h1>
    <a href="index.php">Kembali ke Daftar Trip</a>
    <hr>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr><th colspan="2">Informasi Trip</th></tr>
        <tr><th>Tujuan</th><td><?php echo $trip['tujuan']; ?></td></tr>
        <tr><th>Tanggal Berangkat</th><td><?php echo $trip['tgl_berangkat']; ?></td></tr>
        <tr><th>Tanggal Pulang</th><td><?php echo $trip['tgl_pulang']; ?></td></tr>
        <tr><th>Harga</th><td>Rp <?php echo number_format($trip['harga'], 0, ',', '.'); ?></td></tr>
        <tr><th>Kuota</th><td><?php echo $trip['kuota']; ?></td></tr>
        <tr><th>Catatan</th><td><?php echo $trip['catatan']; ?></td></tr>
        <tr><th>Deskripsi Trip</th><td><?php echo $katalog['deskripsi']; ?></td></tr>
    </table>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr><th colspan="<?= $jumlah ?>">Galeri Trip</th></tr>
        <tr>
            <td>
              <div class="galeri-container">
              <?php while ($img = ambil($gambar)): ?>
                
                    <img src="../gambar/upload/<?php echo $img['nama_file']; ?>"></img>
                
              <?php endwhile; ?>
            </div>
          </td>
        </tr>
    </table>
    
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr><th colspan="3">Itinerary</th></tr>
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

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr><th colspan="3">Meeting Point</th></tr>
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

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr><th colspan="2">Fasilitas Trip</th></tr>
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
    
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr><th colspan="7">Daftar Peserta</th></tr>
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