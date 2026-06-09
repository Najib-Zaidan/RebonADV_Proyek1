<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peserta Open Trip</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body{
            min-height:100vh;
            background:#e7e2c8;
        }

        .form-section{
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:30px 15px;
            background:linear-gradient(135deg,#4e2bbf,#8b6cf6);
        }

        .form-container{
            width:100%;
            max-width:520px;
            background:#ffffff;
            padding:35px;
            border-radius:20px;
            box-shadow:0 15px 40px rgba(0,0,0,.15);
        }

        .btn-kembali{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            text-decoration:none;
            width:100%;
            padding:12px;
            margin-bottom:20px;
            border-radius:12px;
            background:#f3f3f3;
            color:#333;
            font-weight:600;
            transition:.3s;
        }

        .btn-kembali:hover{
            background:#e6e6e6;
        }

        .form-header{
            text-align:center;
            margin-bottom:25px;
        }

        .form-header h2{
            color:#222;
            font-size:28px;
            margin-bottom:8px;
        }

        .form-header p{
            color:#666;
            font-size:14px;
        }

        form{
            display:flex;
            flex-direction:column;
            gap:15px;
        }

        .input-group{
            display:flex;
            flex-direction:column;
            gap:6px;
        }

        .input-group label{
            font-size:14px;
            font-weight:600;
            color:#333;
        }

        input{
            width:100%;
            padding:14px 16px;
            border:1px solid #ddd;
            border-radius:12px;
            font-size:14px;
            background:#fafafa;
            transition:.3s;
        }

        input:focus{
            outline:none;
            border-color:#6b3df5;
            background:#fff;
            box-shadow:0 0 0 4px rgba(107,61,245,.15);
        }

        .btn-submit{
            margin-top:10px;
            border:none;
            padding:14px;
            border-radius:12px;
            background:#4e2bbf;
            color:white;
            font-size:15px;
            font-weight:600;
            cursor:pointer;
            transition:.3s;
        }

        .btn-submit:hover{
            background:#6b3df5;
            transform:translateY(-2px);
        }

        @media(max-width:600px){

            .form-container{
                padding:25px;
            }

            .form-header h2{
                font-size:24px;
            }

        }
    </style>

</head>
<body>

<section class="form-section">

    <div class="form-container">

        <a href="profiluser.php" class="btn-kembali">
             Kembali ke Profil
        </a>

        <div class="form-header">
            <h2>Tambah Peserta Open Trip</h2>
            <p>Lengkapi data peserta sebelum melakukan pemesanan trip.</p>
        </div>

        <form id="formPendaftaran" action="proses_daftar.php" method="POST">

            <div class="input-group">
                <label>Nama Lengkap</label>
                <input
                    type="text"
                    name="nama"
                    id="nama"
                    placeholder="Masukkan nama lengkap"
                    required>
            </div>

            <div class="input-group">
                <label>Usia</label>
                <input
                    type="number"
                    name="usia"
                    id="usia"
                    placeholder="Masukkan usia"
                    min="1"
                    required>
            </div>

            <div class="input-group">
                <label>Alamat Lengkap</label>
                <input
                    type="text"
                    name="alamat"
                    id="alamat"
                    placeholder="Masukkan alamat lengkap"
                    required>
            </div>

            <div class="input-group">
                <label>Nomor Telepon</label>
                <input
                    type="tel"
                    name="telepon"
                    id="telepon"
                    placeholder="Contoh: 081234567890"
                    pattern="[0-9]+"
                    title="Hanya boleh angka"
                    required>
            </div>

            <div class="input-group">
                <label>Riwayat Penyakit</label>
                <input
                    type="text"
                    name="detail"
                    id="detail"
                    placeholder="Kosongkan jika tidak ada">
            </div>

            <button type="submit" class="btn-submit">
                Simpan Peserta
            </button>

        </form>

    </div>

</section>

</body>
</html>