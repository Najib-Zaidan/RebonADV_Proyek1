<?php
require 'fungsi.php';

// 1. Tangkap ID Trip dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('ID Trip tidak ditemukan!');
            window.location.href = 'index.php';
          </script>";
    exit;
}

$id_trip = mysqli_real_escape_string($konek, $_GET['id']);

// 2. Ambil data utama dari tabel trip dengan JOIN ke tabel tujuan
$query_trip = kueri("SELECT t.*, tj.tujuan, tj.kota, tj.provinsi 
                     FROM trip t 
                     JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan 
                     WHERE t.id_trip = $id_trip");

if (mysqli_num_rows($query_trip) > 0) {
    $trip = ambil($query_trip);
} else {
    echo "<script>
            alert('Data trip tidak ditemukan!');
            window.location.href = 'index.php';
          </script>";
    exit;
}

// 3. Ambil data dari tabel-tabel relasi (Foreign Key) sesuai skema baru
$gambar    = kueri("SELECT * FROM gambar WHERE id_trip = $id_trip");
$itenerary = kueri("SELECT * FROM itenerary WHERE id_trip = $id_trip ORDER BY mulai ASC");
$meetpoint = kueri("SELECT * FROM meetpoint WHERE id_trip = $id_trip ORDER BY waktu ASC");
$fasilitas = kueri("SELECT * FROM fasilitas WHERE id_trip = $id_trip");

// 4. Ambil daftar peserta berdasarkan relasi baru (booking -> detail -> peserta_open)
$peserta   = kueri("SELECT po.*, b.tgl_booking, b.status AS status_bayar
                     FROM booking b
                     JOIN detail d ON b.id_booking = d.id_booking
                     JOIN peserta_open po ON d.id_peserta = po.id_peserta
                     WHERE b.id_trip = $id_trip 
                     ORDER BY b.tgl_booking DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Trip - <?php echo htmlspecialchars($trip['tujuan']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --purple-main: #6f42c1;
            --purple-hover: #5a32a3;
            --purple-bg: #f4f2f7;
            --purple-border: #e1d8f5;
            --text-dark: #333;
            --text-muted: #495057;
            --card-white: #ffffff;
            --shadow: 0 4px 12px rgba(111, 66, 193, 0.08);
            --radius: 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--purple-bg);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
        }

        .container {
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Header Style */
        .header-container {
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            background: var(--card-white);
            padding: 15px 20px;
            border-radius: var(--radius);
            border: 1px solid var(--purple-border);
            box-shadow: var(--shadow);
        }

        .header-container h1 {
            margin: 0;
            color: var(--purple-main);
            font-size: 1.4rem;
            font-weight: 700;
        }

        .btn-kembali {
            background: var(--card-white);
            color: var(--purple-main);
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: 0.2s;
            border: 1px solid var(--purple-main);
            text-align: center;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-kembali:hover {
            background: var(--purple-bg);
        }

        /* Card Section */
        .section-card {
            background: var(--card-white);
            border: 1px solid var(--purple-border);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
        }

        h3 {
            margin: 0 0 20px 0;
            color: var(--purple-main);
            font-size: 1.25rem;
            font-weight: 700;
            border-bottom: 2px solid var(--purple-border);
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Grid Detail Informasi */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .info-item {
            background: #fdfbff;
            border: 1px solid var(--purple-border);
            border-radius: 12px;
            padding: 14px;
        }

        .info-item.full-width {
            grid-column: span 2;
        }

        .info-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Tombol Aksi */
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            color: white;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }

        .btn-ubah {
            background: linear-gradient(180deg, #ff5722 0%, #ff9800 100%);
            box-shadow: 0 4px 10px rgba(255, 87, 34, 0.2);
        }

        .btn-ubah:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(255, 87, 34, 0.3);
        }

        .btn-cetak {
            background: linear-gradient(180deg, #28a745 0%, #20c997 100%);
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.2);
        }

        .btn-cetak:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(40, 167, 69, 0.3);
        }

        /* Galeri Gambar */
        .galeri-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .galeri-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid var(--purple-border);
            aspect-ratio: 3 / 2;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .galeri-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }

        .galeri-item:hover img {
            transform: scale(1.05);
        }

        /* Table Styling */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid var(--purple-border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card-white);
            text-align: left;
            font-size: 0.95rem;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--purple-border);
        }

        th {
            background-color: #fdfbff;
            color: var(--purple-main);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background-color: #fcfbfe;
        }

        /* Badge Status */
        .badge-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
        }
        .status-lunas { background: #e6f4ea; color: #137333; }
        .status-dp { background: #fef7e0; color: #b06000; }
        .status-belum { background: #fce8e6; color: #c5221f; }
        .status-default { background: #f1f3f4; color: #3c4043; }

        /* Empty State text */
        .empty-text {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px 0;
            margin: 0;
        }

        /* Responsif Mobile */
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .info-item.full-width {
                grid-column: span 1;
            }
            .header-container {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
                text-align: center;
            }
            .btn-kembali {
                justify-content: center;
            }
            .action-buttons {
                justify-content: center;
                width: 100%;
            }
            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header-container">
        <h1><i class="fa-solid fa-mountain-sun"></i> Detail Trip Pendakian</h1>
        <a href="index.php" class="btn-kembali">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Trip
        </a>
    </div>

    <div class="section-card">
        <h3><i class="fa-solid fa-circle-info"></i> Informasi Utama</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Destinasi / Gunung</span>
                <span class="info-value"><?php echo htmlspecialchars($trip['tujuan']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Lokasi Wilayah</span>
                <span class="info-value"><?php echo htmlspecialchars($trip['kota'] . ', ' . $trip['provinsi']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Berangkat</span>
                <span class="info-value"><i class="fa-regular fa-calendar-check"></i> <?php echo date('d M Y', strtotime($trip['tgl_berangkat'])); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Pulang</span>
                <span class="info-value"><i class="fa-regular fa-calendar-times"></i> <?php echo date('d M Y', strtotime($trip['tgl_pulang'])); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Harga Paket</span>
                <span class="info-value" style="color: var(--purple-main);">Rp <?php echo number_format($trip['harga'], 0, ',', '.'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Minimal Down Payment (DP)</span>
                <span class="info-value">Rp <?php echo number_format($trip['harga_dp'], 0, ',', '.'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Sisa Kuota Peserta</span>
                <span class="info-value"><?php echo htmlspecialchars($trip['kuota']); ?> Orang</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jalur / Rute Perjalanan</span>
                <span class="info-value"><?php echo htmlspecialchars($trip['rute']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Status Visibilitas</span>
                <span class="info-value">
                    <?php echo $trip['publik'] ? '<span style="color:#137333;"><i class="fa-solid fa-eye"></i> Publik</span>' : '<span style="color:#c5221f;"><i class="fa-solid fa-eye-slash"></i> Privat</span>'; ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Catatan Tambahan</span>
                <span class="info-value" style="font-weight: 400; font-size: 0.95rem;">
                    <?php echo !empty($trip['catatan']) ? nl2br(htmlspecialchars($trip['catatan'])) : '-'; ?>
                </span>
            </div>
        </div>

        <div class="action-buttons">
            <a href="ubah_tripv2.php?id=<?php echo $id_trip; ?>" class="btn-action btn-ubah">
                <i class="fa-solid fa-pen-to-square"></i> Ubah Trip
            </a>
<<<<<<< HEAD
            <a href="cetak_laporan.php?id=<?php echo $id_trip; ?>" class="btn-action btn-cetak">
=======
            <a href="laporan_per_trip.php?id=<?php echo $id_trip; ?>" class="btn-action btn-cetak">
>>>>>>> 112096f2d4cea1aa43f694d2d1f4cf0ef23aae82
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </a>
        </div>
    </div>

    <div class="section-card">
        <h3><i class="fa-solid fa-images"></i> Galeri Dokumentasi</h3>
        <?php if (mysqli_num_rows($gambar) > 0): ?>
            <div class="galeri-container">
                <?php while ($img = ambil($gambar)): ?>
                    <div class="galeri-item">
                        <img src="../gambar/upload/<?php echo htmlspecialchars($img['nama_file']); ?>" alt="Foto Trip">
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="empty-text">Belum ada foto dokumentasi yang diupload untuk trip ini.</p>
        <?php endif; ?>
    </div>

    <div class="section-card">
        <h3><i class="fa-solid fa-route"></i> Rencana Perjalanan (Itinerary)</h3>
        <?php if (mysqli_num_rows($itenerary) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Agenda / Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($itn = ambil($itenerary)): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--purple-main);"><?php echo date('H:i', strtotime($itn['mulai'])); ?> WIB</td>
                                <td style="font-weight: 600; color: #6c757d;"><?php echo date('H:i', strtotime($itn['selesai'])); ?> WIB</td>
                                <td><?php echo htmlspecialchars($itn['kegiatan']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty-text">Susunan susunan rencana perjalanan belum diatur.</p>
        <?php endif; ?>
    </div>

    <div class="section-card">
        <h3><i class="fa-solid fa-map-location-dot"></i> Titik Kumpul (Meeting Point)</h3>
        <?php if (mysqli_num_rows($meetpoint) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Jam Penjemputan</th>
                            <th>Kota</th>
                            <th>Lokasi Spesifik / Daerah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($mepo = ambil($meetpoint)): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--purple-main);"><?php echo date('H:i', strtotime($mepo['waktu'])); ?> WIB</td>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($mepo['kota']); ?></td>
                                <td><?php echo htmlspecialchars($mepo['daerah']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty-text">Lokasi titik kumpul penjemputan belum ditentukan.</p>
        <?php endif; ?>
    </div>

    <div class="section-card">
        <h3><i class="fa-solid fa-kitchen-set"></i> Fasilitas Layanan</h3>
        <?php if (mysqli_num_rows($fasilitas) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Layanan / Fasilitas</th>
                            <th>Kategori Ketersediaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fas = ambil($fasilitas)): ?>
                            <tr>
                                <td style="font-weight: 500;"><?php echo htmlspecialchars($fas['fasilitas']); ?></td>
                                <td>
                                    <?php if ($fas['jenis'] === 'include'): ?>
                                        <span class="badge-status status-lunas"><i class="fa-solid fa-circle-check"></i> Termasuk (Include)</span>
                                    <?php else: ?>
                                        <span class="badge-status status-belum"><i class="fa-solid fa-circle-xmark"></i> Di Luar Paket (Exclude)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty-text">Rincian fasilitas pendakian belum dimasukkan.</p>
        <?php endif; ?>
    </div>

    <div class="section-card">
        <h3><i class="fa-solid fa-users"></i> Manifest Peserta (Open Trip)</h3>
        <?php if (mysqli_num_rows($peserta) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">No.</th>
                            <th>Nama Lengkap</th>
                            <th style="text-align: center;">Usia</th>
                            <th>Nomor Telepon</th>
                            <th>Alamat Domisili</th>
                            <th>Riwayat Medis</th>
                            <th style="text-align: center;">Status Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomer = 1; ?>
                        <?php while ($row = ambil($peserta)): ?>
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: #6c757d;"><?= $nomer ?></td>
                                <td style="font-weight: 600; color: var(--text-dark);"><?php echo htmlspecialchars($row['nama']); ?></td>
                                <td style="text-align: center;"><?php echo htmlspecialchars($row['usia']); ?> Thn</td>
                                <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                                <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                                <td style="color: #c5221f; font-weight: 500;"><?php echo htmlspecialchars($row['riwayat']); ?></td>
                                <td style="text-align: center;">
                                    <?php 
                                        $st = $row['status_bayar'];
                                        if($st == 'Lunas') $class = 'status-lunas';
                                        elseif($st == 'DP') $class = 'status-dp';
                                        elseif($st == 'Belum Bayar') $class = 'status-belum';
                                        else $class = 'status-default';
                                    ?>
                                    <span class="badge-status <?= $class ?>"><?= htmlspecialchars($st) ?></span>
                                </td>
                            </tr>
                            <?php $nomer++; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty-text">Belum ada data pendaftar atau manifes peserta untuk trip ini.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>