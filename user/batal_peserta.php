<?php
session_start();
require 'konek.php';
require 'fungsi.php';

if (!isset($_SESSION['id_akun'])) {
    header("Location: login.php");
    exit;
}

$id_akun = $_SESSION['id_akun'];
$id_detail = $_GET['id_detail'];
$id_booking = $_GET['id_booking'];

if (isset($_POST['proses_batal'])) {
    $alasan = mysqli_real_escape_string($konek, $_POST['alasan_batal']);

    // Hanya insert ke tabel batal_peserta dengan status default 'Menunggu'
    $query_batal = "INSERT INTO batal_peserta (id_detail, alasan_batal, tgl_pengajuan, status_verifikasi) 
                    VALUES ('$id_detail', '$alasan', NOW(), 'Menunggu')";
    
    $simpan = mysqli_query($konek, $query_batal);

    if ($simpan) {
        echo "<script>
                alert('Pengajuan pembatalan telah dikirim. Menunggu verifikasi admin.');
                window.location='detail_pesanan.php?id_booking=$id_booking';
              </script>";
    } else {
        echo "<script>alert('Gagal mengirim pengajuan.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pembatalan Peserta</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background: #f0f2f9; 
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card { 
            background: white; 
            padding: 40px; 
            width: 100%;
            max-width: 400px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2); 
            text-align: center;
        }
        h3 { color: #4b307b; margin-bottom: 10px; font-weight: 600; }
        p { color: #777; font-size: 14px; margin-bottom: 25px; }
        
        label { 
            display: block; 
            text-align: left; 
            font-size: 14px; 
            font-weight: 600; 
            color: #555;
            margin-bottom: 8px;
        }

        textarea { 
            width: 100%; 
            height: 120px; 
            padding: 15px; 
            border-radius: 12px; 
            border: 2px solid #e0e0e0; 
            box-sizing: border-box; 
            resize: none;
            font-family: inherit;
            transition: 0.3s;
        }
        textarea:focus {
            border-color: #a29bfe;
            outline: none;
            box-shadow: 0 0 8px rgba(162, 155, 254, 0.4);
        }

        .btn-submit { 
            background: #6c5ce7; 
            color: white; 
            border: none; 
            padding: 14px; 
            border-radius: 12px; 
            cursor: pointer; 
            margin-top: 20px; 
            width: 100%; 
            font-size: 16px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-submit:hover { 
            background: #a29bfe; 
            transform: translateY(-2px);
        }

        .btn-cancel { 
            display: inline-block; 
            text-align: center; 
            color: #aaa; 
            text-decoration: none; 
            margin-top: 20px; 
            font-size: 14px; 
            transition: 0.3s;
        }
        .btn-cancel:hover { color: #6c5ce7; }

        .icon-box {
            background: #efecfd;
            width: 60px;
            height: 60px;
            line-height: 60px;
            border-radius: 50%;
            margin: 0 auto 20px;
            font-size: 24px;
            color: #6c5ce7;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="icon-box">✕</div>
    <h3>Batalkan Peserta</h3>
    <p>Pengajuan pembatalan akan ditinjau terlebih dahulu oleh admin kami.</p>
    
    <form action="" method="post">
        <label>Alasan Pembatalan</label>
        <textarea name="alasan_batal" placeholder="Contoh: Ada keperluan mendadak..." required></textarea>
        
        <button type="submit" name="proses_batal" class="btn-submit">Kirim Pengajuan</button>
    </form>

    <a href="detail_pesanan.php?id_booking=<?= $id_booking; ?>" class="btn-cancel">Kembali</a>
</div>

</body>
</html>