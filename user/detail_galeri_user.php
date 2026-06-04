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

if(count($fotos) == 0){
    $fotos[] = 'default.jpg';
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $album['nama']; ?></title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#0f0f0f;
    min-height:100vh;
    padding:30px;
}

.wrapper{
    max-width:1200px;
    margin:auto;
}

/* HEADER */
.topbar{
    text-align:center;
    margin-bottom:25px;
}

.judul{
    color:white;
}

.judul h1{
    font-size:32px;
    margin-bottom:8px;
}

.judul p{
    color:#aaa;
    font-size:14px;
}

/* SLIDER */
.slider{
    position:relative;
    display:flex;
    justify-content:center;
    align-items:center;
    width:100%;
}

/* FOTO */
.slider img{
    width:100%;
    max-width:900px;
    height:65vh;
    object-fit:contain;
    background:#000;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.5);
    display:block;
}

/* KEMBALI */
.kembali{
    position:absolute;
    top:-50px;
    left:calc(50% - 450px);
    z-index:10;

    padding:10px 18px;
    background:rgba(49, 9, 179, 0.56);
    color:white;
    text-decoration:none;
    border-radius:10px;
    font-size:14px;
    font-weight:bold;
    backdrop-filter:blur(6px);
    transition:.3s;
}

.kembali:hover{
    background:rgba(49, 9, 179, 0.8);
}

/* PREV NEXT */
.btn{
    position:absolute;
    top:50%;
    transform:translateY(-50%);

    width:55px;
    height:55px;

    border:none;
    border-radius:50%;

    background:#6b3df5;
    color:white;

    font-size:28px;
    cursor:pointer;
    transition:.3s;
}

.btn:hover{
    background:#4d22c7;
}

.prev{
    left:calc(50% - 520px);
}

.next{
    right:calc(50% - 520px);
}

/* COUNTER */
.counter{
    margin-top:18px;
    text-align:center;
    color:white;
    font-size:15px;
}

/* RESPONSIVE */
@media(max-width:1000px){

    .prev{
        left:10px;
    }

    .next{
        right:10px;
    }

    .kembali{
        left:15px;
    }

}

@media(max-width:768px){

    body{
        padding:15px;
    }

    .judul h1{
        font-size:24px;
    }

    .slider img{
        height:50vh;
    }

    .btn{
        width:45px;
        height:45px;
        font-size:22px;
    }

    .kembali{
        top:10px;
        left:10px;
        padding:8px 14px;
        font-size:13px;
    }

}

</style>
</head>
<body>

<div class="wrapper">

    <div class="topbar">

        <div class="judul">
            <h1>ALBUM <?php echo strtoupper($album['nama']); ?></h1>
            <p>Gunakan tombol kiri dan kanan untuk melihat foto</p>
        </div>

    </div>

    <div class="slider">

        <a href="home1.php?menu=galeri" class="kembali">
            Home
        </a>

        <button class="btn prev" onclick="prev()">
            ❮
        </button>

        <img
            id="slide"
            src="../gambar/galeri/<?php echo $fotos[0]; ?>"
            alt=""
        >

        <button class="btn next" onclick="next()">
            ❯
        </button>

    </div>

    <div class="counter" id="counter">
        1 / <?php echo count($fotos); ?>
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

document.addEventListener("keydown", function(e){

    if(e.key === "ArrowRight"){
        next();
    }

    if(e.key === "ArrowLeft"){
        prev();
    }

});

</script>

</body>
</html>