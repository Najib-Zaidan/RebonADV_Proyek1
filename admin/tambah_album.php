<?php
require 'konek.php';

if(isset($_POST['submit'])){

    $nama = $_POST['nama'];

    mysqli_query($konek,"
    INSERT INTO album
    VALUES(NULL,'$nama')
    ");

    header("Location:index.php?menu=galeri");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Tambah Album</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#f4f0ff;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

.card{
    width:100%;
    max-width:450px;
    background:white;
    padding:35px;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

.judul{
    margin-bottom:25px;
}

.judul h2{
    color:#321180;
    margin-bottom:8px;
}

.judul p{
    color:#777;
    font-size:14px;
}

.input-group{
    margin-bottom:20px;
}

.input-group label{
    display:block;
    margin-bottom:8px;
    font-size:14px;
    font-weight:bold;
    color:#444;
}

.input-group input{
    width:100%;
    padding:13px;
    border:1px solid #ddd;
    border-radius:10px;
    outline:none;
    font-size:14px;
    transition:0.3s;
}

.input-group input:focus{
    border-color:#6b3df5;
    box-shadow:0 0 0 3px rgba(107,61,245,0.15);
}

.button-group{
    display:flex;
    gap:10px;
    margin-top:25px;
}

.btn{
    flex:1;
    padding:12px;
    border:none;
    border-radius:10px;
    text-decoration:none;
    text-align:center;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

.btn-simpan{
    background:#6b3df5;
    color:white;
}

.btn-simpan:hover{
    background:#5527dd;
}

.btn-kembali{
    background:#ece8ff;
    color:#321180;
}

.btn-kembali:hover{
    background:#ddd4ff;
}
</style>

</head>
<body>

<div class="card">

    <div class="judul">
        <h2>Tambah Album Gunung</h2>
        <p>Tambahkan album baru untuk galeri pendakian.</p>
    </div>

    <form method="POST">

        <div class="input-group">
            <label>Nama Gunung / Album</label>

            <input type="text"
                   name="nama"
                   placeholder="Contoh: Gunung Ciremai"
                   required>
        </div>

        <div class="button-group">

            <a href="index.php?menu=galeri" class="btn btn-kembali">
                Kembali
            </a>

            <button type="submit"
                    name="submit"
                    class="btn btn-simpan">
                Simpan
            </button>

        </div>

    </form>

</div>

</body>
</html>