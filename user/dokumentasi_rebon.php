<?php
require 'konek.php';

/*
    Ambil semua foto beserta nama album
*/
$query = mysqli_query($konek, "
    SELECT g.*, a.nama AS nama_album
    FROM galeri g
    JOIN album a ON g.id_album = a.id_album
    ORDER BY g.id_galeri ASC
");

$foto = [];

while($row = mysqli_fetch_assoc($query)){
    $foto[] = $row;
}

$total = count($foto);

if($total == 0){
    die("Belum ada foto dokumentasi.");
}

$index = isset($_GET['foto']) ? (int)$_GET['foto'] : 0;

if($index < 0){
    $index = $total - 1;
}

if($index >= $total){
    $index = 0;
}

$data = $foto[$index];

$prev = $index - 1;
if($prev < 0){
    $prev = $total - 1;
}

$next = $index + 1;
if($next >= $total){
    $next = 0;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Dokumentasi Rebon Adventure</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#f4f4f4;
}

.header{
    background:#6b3df5;
    color:white;
    text-align:center;
    padding:25px;
}

.header h1{
    margin-bottom:8px;
}

.container{
    width:90%;
    max-width:1000px;
    margin:30px auto;
}

.kembali{
    display:inline-block;
    text-decoration:none;
    background:#444;
    color:white;
    padding:10px 20px;
    border-radius:8px;
    margin-bottom:20px;
}

.kembali:hover{
    background:#222;
}

.card{
    background:white;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 5px 15px rgba(0,0,0,0.15);
}

.foto-area{
    position:relative;
}

.foto-area img{
    width:100%;
    height:400px;
    object-fit:cover;
    display:block;
}

.nav{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
    text-decoration:none;
    background:rgba(0,0,0,0.6);
    color:white;
    font-size:35px;
    width:60px;
    height:60px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
}

.nav:hover{
    background:#6b3df5;
}

.prev{
    left:20px;
}

.next{
    right:20px;
}

.info{
    padding:20px;
    text-align:center;
}

.info h2{
    color:#6b3df5;
    margin-bottom:10px;
}

.counter{
    color:#666;
    font-size:14px;
}

@media(max-width:768px){

    .foto-area img{
        height:350px;
    }

    .nav{
        width:50px;
        height:50px;
        font-size:28px;
    }
}

</style>
</head>

<body>

<div class="header">
    <h1>DOKUMENTASI REBON ADVENTURE</h1>
    <p>Gunakan tombol kiri dan kanan untuk melihat dokumentasi</p>
</div>

<div class="container">

    <a href="tentang_kami.php" class="kembali">
         Kembali
    </a>

    <div class="card">

        <div class="foto-area">

            <a href="?foto=<?php echo $prev; ?>" class="nav prev">
                &#10094;
            </a>

            <img src="../gambar/galeri/<?php echo $data['nama_file']; ?>" alt="Dokumentasi">

            <a href="?foto=<?php echo $next; ?>" class="nav next">
                &#10095;
            </a>

        </div>

        <div class="info">

            <h2>
                <?php echo $data['nama_album']; ?>
            </h2>

            <div class="counter">
                Foto <?php echo ($index + 1); ?> dari <?php echo $total; ?>
            </div>

        </div>

    </div>

</div>

</body>
</html>