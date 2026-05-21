<?php
session_start();
require 'konek.php';
require 'fungsi.php';

if (!isset($_SESSION['id_akun'])) {
    header("Location: login_user.php");
    exit;
}

$id_akun = $_SESSION['id_akun'];
$id_private = $_GET['id_private'] ?? '';

if (empty($id_private)) {
    echo "<script>
        alert('Data Private Trip tidak ditemukan!');
        window.location='profiluser.php';
    </script>";
    exit;
}

$cek = kueri("
    SELECT * FROM private_trip 
    WHERE id_private = '$id_private' 
    AND id_akun = '$id_akun'
");

if (mysqli_num_rows($cek) == 0) {
    echo "<script>
        alert('Anda tidak memiliki akses ke data ini!');
        window.location='profiluser.php';
    </script>";
    exit;
}

if (isset($_POST['kirim_batal_private'])) {

    $alasan = $_POST['alasan'];

    $update = kueri("
        INSERT INTO batal_private (id_private, alasan, status, tgl_pembatalan)
        VALUES ('$id_private', '$alasan', 0, NOW())
    ");

    if ($update) {
        echo "<script>
            alert('Pembatalan Private Trip berhasil diajukan');
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
<title>Batal Private Trip</title>

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
    color: #333;
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
    box-sizing: border-box;
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
    font-size: 14px;
}
</style>

</head>
<body>

<div class="card">

    <h2>Batalkan Private Trip</h2>
    <p>Berikan alasan pembatalan private trip anda:</p>

    <form method="POST">
        <textarea name="alasan" placeholder="Tulis alasan pembatalan private trip..." required></textarea>

        <button type="submit" name="kirim_batal_private">Kirim Pengajuan Pembatalan</button>
    </form>

    <a href="profiluser.php" class="back">← Kembali</a>

</div>

</body>
</html>