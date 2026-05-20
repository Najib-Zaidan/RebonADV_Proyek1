<?php
require 'konek.php';

$id = $_GET['id'];

$album = mysqli_fetch_assoc(mysqli_query($konek,"
SELECT * FROM album
WHERE id_album='$id'
"));

$data = mysqli_query($konek,"
SELECT * FROM galeri
WHERE id_album='$id'
");

$fotos = [];

while($d = mysqli_fetch_assoc($data)){
    $fotos[] = $d['nama_file'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo $album['nama']; ?></title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial;
}

body{
    background:#0f0f0f;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:30px;
}

/* WRAPPER */
.wrapper{
    width:100%;
    max-width:1100px;
}

/* HEADER */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.judul{
    color:white;
}

.judul h1{
    font-size:32px;
    margin-bottom:5px;
}

.judul p{
    color:#aaa;
    font-size:14px;
}

/* BUTTON */
.kembali{
    padding:12px 18px;
    background:#6b3df5;
    color:white;
    border-radius:10px;
    text-decoration:none;
    font-size:14px;
    font-weight:bold;
    transition:.3s;
}

.kembali:hover{
    background:#4d22c7;
}

/* SLIDER */
.slider{
    position:relative;
    background:#1b1b1b;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,0.4);
}

/* IMAGE */
.slider img{
    width:100%;
    height:75vh;
    object-fit:contain;
    display:block;
    background:black;
    cursor:pointer;
}

/* BUTTON NAV */
.btn{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
    width:55px;
    height:55px;
    border:none;
    border-radius:50%;
    background:rgba(255,255,255,0.15);
    color:white;
    font-size:28px;
    cursor:pointer;
    transition:.3s;
    backdrop-filter:blur(5px);
}

.btn:hover{
    background:rgba(255,255,255,0.3);
}

.prev{
    left:20px;
}

.next{
    right:20px;
}

/* COUNTER */
.counter{
    position:absolute;
    bottom:20px;
    left:50%;
    transform:translateX(-50%);
    background:rgba(0,0,0,0.5);
    color:white;
    padding:8px 15px;
    border-radius:20px;
    font-size:14px;
}

/* RESPONSIVE */
@media(max-width:768px){

    .judul h1{
        font-size:24px;
    }

    .slider img{
        height:60vh;
    }

    .btn{
        width:45px;
        height:45px;
        font-size:22px;
    }

}

</style>

</head>
<body>

<div class="wrapper">

    <div class="topbar">

        <div class="judul">
            <h1><?php echo $album['nama']; ?></h1>
            <p>Klik gambar untuk kembali ke galeri</p>
        </div>

        <a href="home1.php?menu=galeri" class="kembali">
            ← Kembali
        </a>

    </div>

    <div class="slider">

        <img id="slide"
             src="../gambar/galeri/<?php echo $fotos[0]; ?>"
             onclick="window.location='home1.php'">

        <button class="btn prev" onclick="prev()">
            ❮
        </button>

        <button class="btn next" onclick="next()">
            ❯
        </button>

        <div class="counter" id="counter">
            1 / <?php echo count($fotos); ?>
        </div>

    </div>

</div>

<script>

let foto = <?php echo json_encode($fotos); ?>;
let index = 0;

function tampil(){

    document.getElementById("slide").src =
    "../gambar/galeri/" + foto[index];

    document.getElementById("counter").innerHTML =
    (index + 1) + " / " + foto.length;
}

function next(){

    index++;

    if(index >= foto.length){
        index = 0;
    }

    tampil();
}

function prev(){

    index--;

    if(index < 0){
        index = foto.length - 1;
    }

    tampil();
}

</script>

</body>
</html>