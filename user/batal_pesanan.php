<?php
session_start();
require 'konek.php';
require 'fungsi.php';

// cek login
if (!isset($_SESSION['id_akun'])) {
    header("Location: login_user.php");
    exit;
}

$id_akun = $_SESSION['id_akun'];
$id_booking = $_GET['id_booking'] ?? '';

// validasi booking
if (empty($id_booking)) {
    echo "<script>
        alert('Booking tidak ditemukan!');
        window.location='profiluser.php';
    </script>";
    exit;
}

// cek kepemilikan booking
$cek = kueri("
    SELECT * FROM booking 
    WHERE id_booking = '$id_booking' 
    AND id_akun = '$id_akun'
");

if (mysqli_num_rows($cek) == 0) {
    echo "<script>
        alert('Tidak punya akses ke booking ini!');
        window.location='profiluser.php';
    </script>";
    exit;
}

// kalau form disubmit
if (isset($_POST['kirim_batal'])) {

    $alasan = $_POST['alasan'];

    $update = kueri("
        INSERT INTO batal_open (id_booking, alasan)
        VALUES ('$id_booking', '$alasan')
    ");

    if ($update) {
        echo "<script>
            alert('Batal Pesanan berhasil diajukan');
            window.location='profiluser.php';
        </script>";
        exit;
    } else {
        echo "Gagal: " . mysqli_error($konek);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Batal Pesanan</title>

<style>
body{
    font-family: Arial;
    background: linear-gradient(135deg,#4b1fa3,#8a6be8);
    margin:0;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card{
    background:#eae4cc;
    padding:30px;
    border-radius:12px;
    width:400px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

h2{
    margin-bottom:15px;
}

textarea{
    width:100%;
    height:120px;
    padding:10px;
    border:none;
    border-radius:8px;
    background:#cfc6e8;
    resize:none;
    margin-bottom:15px;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#6b3df5;
    color:white;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    background:#5027d6;
}

.back{
    display:block;
    margin-top:10px;
    text-align:center;
    text-decoration:none;
    color:#333;
}
</style>

</head>
<body>

<div class="card">

    <h2>Batalkan Pesanan</h2>
    <p>Berikan alasan pembatalan:</p>

    <form method="POST">
        <textarea name="alasan" placeholder="Tulis alasan pembatalan..." required></textarea>

        <button type="submit" name="kirim_batal">Kirim Pembatalan</button>
    </form>

    <a href="profiluser.php" class="back">Kembali</a>

</div>

</body>
</html>