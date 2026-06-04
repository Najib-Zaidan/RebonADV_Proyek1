<?php
// Memanggil koneksi dan fungsi bawaan Anda
require 'konek.php';
require 'fungsi.php'; 

// Mengambil id_booking dari URL
$id_booking = $_GET['id_booking'] ?? null;

if (!$id_booking) {
    die("Akses ilegal: ID Booking tidak ditemukan.");
}

// 1. Kueri yang disesuaikan dengan skema database rebon_adventure Anda
$query_booking = kueri("SELECT b.*, t.id_trip, tj.id_tujuan, tj.tujuan AS nama_tujuan 
                       FROM booking b 
                       JOIN trip t ON b.id_trip = t.id_trip 
                       JOIN tujuan tj ON t.id_tujuan = tj.id_tujuan
                       WHERE b.id_booking = '$id_booking'");
$data_b = ambil($query_booking);

if (!$data_b) {
    die("Data pesanan tidak ditemukan.");
}

$id_trip    = $data_b['id_trip'];
$id_tujuan  = $data_b['id_tujuan'];
$tujuan_nama= $data_b['nama_tujuan'];
$id_akun    = $data_b['id_akun']; 


// 2. Proses Simpan Rating saat Form di-Submit
$pesan_sukses = "";
if (isset($_POST['kirim_rating'])) {
    $rating_skor = intval($_POST['rating_skor']);
    $ulasan = mysqli_real_escape_string($konek, $_POST['ulasan']); 

    // Validasi input minimal bintang
    if ($rating_skor < 1 || $rating_skor > 5) {
        $error = "Silakan pilih rating bintang terlebih dahulu.";
    } else {
        $insert = kueri("INSERT INTO rating (rating, ulasan, id_tujuan, id_trip, id_akun) 
                         VALUES ('$rating_skor', '$ulasan', '$id_tujuan', '$id_trip', '$id_akun')");
        
        if ($insert) {
            $pesan_sukses = "Terima kasih! Penilaian Anda berhasil disimpan.";
            echo "<script>
                    setTimeout(function(){
                        window.location.href = 'riwayat_pemesanan.php'; 
                    }, 2200);
                  </script>";
        } else {
            $error = "Gagal menyimpan penilaian. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berikan Penilaian - Rebon Adventure</title>
    <style>
        :root {
            --primary: #6b3df5;
            --primary-hover: #542cd1;
            --gold: #f59e0b;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            /* Latar belakang menggunakan aset gambar gunung lokal Anda */
            background: linear-gradient(rgba(15, 12, 41, 0.65), rgba(48, 43, 99, 0.7)), 
                        url('../gambar/gunung.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Container dengan Efek Kaca (Glassmorphism) Modern */
        .rating-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            max-width: 520px;
            width: 100%;
            border-radius: 28px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            padding: 35px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            box-sizing: border-box;
            position: relative;
        }

        /* Tombol Kembali ke Profil di Pojok Kiri Atas */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 14px;
            border-radius: 10px;
            background: #f1f5f9;
            transition: all 0.2s ease;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            color: var(--primary);
            background: #e2e8f0;
            transform: translateX(-3px);
        }

        .header {
            text-align: center;
            margin-bottom: 28px;
        }

        .header h2 {
            color: #1a0b40;
            margin: 0 0 10px 0;
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .header p {
            color: var(--text-muted);
            font-size: 14px;
            margin: 0;
            line-height: 1.5;
        }

        .trip-box {
            background: linear-gradient(135deg, rgba(107, 61, 245, 0.06) 0%, rgba(107, 61, 245, 0.01) 100%);
            border: 1.5px dashed rgba(107, 61, 245, 0.4);
            padding: 14px 20px;
            border-radius: 16px;
            text-align: center;
            font-weight: 700;
            font-size: 15px;
            color: var(--primary);
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            box-shadow: inset 0 2px 4px rgba(107, 61, 245, 0.02);
        }

        /* --- STYLING BINTANG --- */
        .star-container {
            background: #f8fafc;
            border-radius: 18px;
            padding: 22px;
            border: 1px solid #e2e8f0;
            margin-bottom: 25px;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 14px;
            margin-top: 10px;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 48px;
            color: #cbd5e1;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            user-select: none;
        }

        .star-rating label:hover {
            transform: scale(1.3) rotate(8deg);
            text-shadow: 0 0 20px rgba(245, 158, 11, 0.5);
        }

        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: var(--gold);
        }

        .star-rating input:checked + label {
            animation: hitStar 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes hitStar {
            0% { transform: scale(1); }
            50% { transform: scale(1.45) rotate(-10deg); }
            100% { transform: scale(1); }
        }

        /* --- INPUT FORM --- */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label.title-label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .form-group textarea {
            width: 100%;
            box-sizing: border-box;
            border: 1.5px solid #cbd5e1;
            border-radius: 16px;
            padding: 16px;
            font-size: 14.5px;
            color: var(--text-dark);
            resize: vertical;
            min-height: 125px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(107, 61, 245, 0.15);
        }

        /* --- ACTION UTAMA BUTTON --- */
        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 8px 24px rgba(107, 61, 245, 0.3);
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(107, 61, 245, 0.4);
        }

        .btn-submit:active {
            transform: translateY(1px);
        }

        /* --- NOTIFIKASI --- */
        .alert {
            padding: 16px;
            border-radius: 14px;
            font-size: 14.5px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 25px;
            line-height: 1.4;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: #fff5f5;
            color: #9b2c2c;
            border: 1px solid #fed2d2;
        }
    </style>
</head>
<body>

<div class="rating-container">
    <a href="profiluser.php" class="btn-back">
        ⬅ Kembali ke Profil
    </a>

    <div class="header">
        <h2>Berikan Penilaian Anda</h2>
        <p>Bagikan pengalaman seru Anda bersama kami selama perjalanan ini</p>
    </div>

    <div class="trip-box">
        <span>📍</span> <span><?= htmlspecialchars($tujuan_nama); ?> (#BK-O<?= $id_booking; ?>)</span>
    </div>

    <?php if (!empty($pesan_sukses)): ?>
        <div class="alert alert-success">🎉 <?= $pesan_sukses; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">⚠️ <?= $error; ?></div>
    <?php endif; ?>

    <?php if (empty($pesan_sukses)): ?>
    <form action="" method="POST">
        
        <div class="form-group star-container" style="text-align: center;">
            <label class="title-label">Ketuk Bintang untuk Menilai</label>
            <div class="star-rating">
                <input type="radio" id="star5" name="rating_skor" value="5" />
                <label for="star5" title="Sangat Puas">★</label>
                
                <input type="radio" id="star4" name="rating_skor" value="4" />
                <label for="star4" title="Puas">★</label>
                
                <input type="radio" id="star3" name="rating_skor" value="3" />
                <label for="star3" title="Cukup">★</label>
                
                <input type="radio" id="star2" name="rating_skor" value="2" />
                <label for="star2" title="Buruk">★</label>
                
                <input type="radio" id="star1" name="rating_skor" value="1" />
                <label for="star1" title="Sangat Buruk">★</label>
            </div>
        </div>

        <div class="form-group">
            <label for="ulasan" class="title-label">Ulasan Perjalanan</label>
            <textarea id="ulasan" name="ulasan" placeholder="Ceritakan keseruan destinasi, ramahnya tour guide, atau kenyamanan armada Rebon Adventure... (opsional)"></textarea>
        </div>

        <button type="submit" name="kirim_rating" class="btn-submit">Kirim Penilaian Terbaik</button>
    </form>
    <?php endif; ?>
</div>

</body>
</html>