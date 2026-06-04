<?php
require 'konek.php';

$id = $_GET['id'];

$album = mysqli_fetch_assoc(mysqli_query($konek,"
SELECT * FROM album
WHERE id_album='$id'
"));

if(isset($_POST['upload'])){

    $jumlah = count($_FILES['foto']['name']);

    for($i=0; $i<$jumlah; $i++){

        $namaFile = $_FILES['foto']['name'][$i];
        $tmp      = $_FILES['foto']['tmp_name'][$i];

        if($namaFile != ''){

            $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

            $namaBaru = time().'_'.$i.'_'.uniqid().'.'.$ext;

            if(move_uploaded_file(
                $tmp,
                "../gambar/galeri/".$namaBaru
            )){

                mysqli_query($konek,"
                    INSERT INTO galeri
                    VALUES(
                        NULL,
                        '$namaBaru',
                        '$id'
                    )
                ");
            }
        }
    }

    header("Location: detail_album.php?id=$id");
    exit;
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
    font-family:Arial, sans-serif;
}

body{
    background:#f4f0ff;
    padding:30px;
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    flex-wrap:wrap;
    gap:15px;
}

.header h2{
    color:#321180;
    margin-bottom:5px;
}

.header p{
    color:#666;
    font-size:14px;
}

/* BUTTON */
.btn{
    padding:10px 18px;
    border-radius:10px;
    text-decoration:none;
    font-size:14px;
    font-weight:bold;
    transition:0.3s;
}

.btn-kembali{
    background:#ece8ff;
    color:#321180;
}

.btn-kembali:hover{
    background:#ddd4ff;
}

/* CARD UPLOAD */
.upload-card{
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
    margin-bottom:30px;
}

.upload-title{
    margin-bottom:18px;
}

.upload-title h3{
    color:#321180;
    margin-bottom:5px;
}

.upload-title p{
    color:#777;
    font-size:14px;
}

.form-upload{
    display:flex;
    gap:15px;
    align-items:center;
    flex-wrap:wrap;
}

.form-upload input[type=file]{
    flex:1;
    background:#fafafa;
    border:1px solid #ddd;
    padding:12px;
    border-radius:10px;
}

.btn-upload{
    padding:12px 20px;
    background:#6b3df5;
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
    transition:0.3s;
}

.btn-upload:hover{
    background:#5527dd;
}

/* GALERI */
.galeri-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:20px;
}

.foto-card{
    background:white;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    transition:0.3s;
}

.foto-card:hover{
    transform:translateY(-4px);
}

.foto-card img{
    width:100%;
    height:220px;
    object-fit:cover;
    display:block;
}

/* EMPTY */
.kosong{
    background:white;
    padding:40px;
    border-radius:16px;
    text-align:center;
    color:#777;
    box-shadow:0 4px 10px rgba(0,0,0,0.05);
}
</style>

</head>
<body>

<!-- HEADER -->
<div class="header">

    <div>
        <h2>Album : <?php echo $album['nama']; ?></h2>
        <p>Kelola foto galeri album gunung.</p>
    </div>

    <a href="index.php?menu=galeri" class="btn btn-kembali">
        Kembali
    </a>

</div>

<!-- CARD UPLOAD -->
<div class="upload-card">

    <div class="upload-title">
        <h3>Upload Foto</h3>
        <p>Bisa upload banyak foto sekaligus.</p>
    </div>

    <form method="POST"
          enctype="multipart/form-data"
          class="form-upload">

        <input type="file"
               name="foto[]"
               multiple
               accept="image/*"
               required>

        <button name="upload"
                class="btn-upload">
            Upload
        </button>

    </form>

</div>

<!-- GALERI FOTO -->
<div class="galeri-grid">

<?php
$fotos = mysqli_query($konek,"
SELECT * FROM galeri
WHERE id_album='$id'
ORDER BY id_galeri DESC
");

if(mysqli_num_rows($fotos) > 0):

while($f = mysqli_fetch_assoc($fotos)):
?>

<div class="foto-card">

    <img src="../gambar/galeri/<?php echo $f['nama_file']; ?>">

    <div style="padding:12px;">

        <a href="hapus_foto.php?id=<?php echo $f['id_galeri']; ?>&album=<?php echo $id; ?>"
           onclick="return confirm('Hapus foto ini?')"
           style="
           display:block;
           text-align:center;
           background:red;
           color:white;
           padding:10px;
           border-radius:10px;
           text-decoration:none;
           font-size:14px;
           font-weight:bold;
           ">
           Hapus Foto
        </a>

    </div>

</div>

<?php endwhile; else: ?>

<div class="kosong" style="grid-column:1/-1;">
    Belum ada foto di album ini.
</div>

<?php endif; ?>

</div>

</body>
</html>