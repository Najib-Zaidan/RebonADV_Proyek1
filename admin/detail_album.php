<?php
require 'konek.php';

$id = $_GET['id'];

$album = mysqli_fetch_assoc(mysqli_query(
    $konek,
    "SELECT * FROM album WHERE id_album='$id'"
));

/* EDIT NAMA ALBUM */
if(isset($_POST['edit_album'])){

    $nama_album = mysqli_real_escape_string(
        $konek,
        $_POST['nama_album']
    );

    mysqli_query(
        $konek,
        "UPDATE album
         SET nama='$nama_album'
         WHERE id_album='$id'"
    );

    echo "<script>
            alert('Nama album berhasil diubah');
            location.href='detail_album.php?id=$id';
          </script>";
    exit;
}

/* HAPUS FOTO TERPILIH */
if(isset($_POST['hapus_terpilih'])){

    if(!empty($_POST['foto_hapus'])){

        foreach($_POST['foto_hapus'] as $id_galeri){

            $q = mysqli_query(
                $konek,
                "SELECT * FROM galeri
                 WHERE id_galeri='$id_galeri'"
            );

            $foto = mysqli_fetch_assoc($q);

            if(file_exists("../gambar/galeri/".$foto['nama_file'])){
                unlink("../gambar/galeri/".$foto['nama_file']);
            }

            mysqli_query(
                $konek,
                "DELETE FROM galeri
                 WHERE id_galeri='$id_galeri'"
            );
        }

        echo "<script>
                alert('Foto berhasil dihapus');
                location.href='detail_album.php?id=$id';
              </script>";
        exit;
    }
}

/* UPLOAD FOTO */
if(isset($_POST['upload'])){

    foreach($_FILES['foto']['tmp_name'] as $key => $tmp){

        if(empty($tmp)){
            continue;
        }

        $nama = time().'_'.$key.'_'.$_FILES['foto']['name'][$key];

        move_uploaded_file(
            $tmp,
            "../gambar/galeri/".$nama
        );

        mysqli_query(
            $konek,
            "INSERT INTO galeri(id_album,nama_file)
             VALUES('$id','$nama')"
        );
    }

    echo "<script>
            alert('Foto berhasil diupload');
            location.href='detail_album.php?id=$id';
          </script>";
    exit;
}

$data = mysqli_query(
    $konek,
    "SELECT * FROM galeri
     WHERE id_album='$id'
     ORDER BY id_galeri DESC"
);
?>

<style>

.album-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:20px;
    margin-bottom:25px;
    padding:25px;
    background:#fff;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.album-header h2{
    margin:0;
    color:#222;
    font-size:28px;
}

.album-header p{
    color:#777;
    margin:8px 0 18px;
}

.edit-album-form{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.edit-album-form input{
    padding:12px 15px;
    border:1px solid #ddd;
    border-radius:10px;
    min-width:260px;
    outline:none;
}

.btn-save{
    padding:12px 18px;
    border:none;
    border-radius:10px;
    background:#6b3df5;
    color:white;
    cursor:pointer;
    font-weight:bold;
}

.btn-save:hover{
    opacity:.9;
}

.btn-kembali{
    background:#111;
    color:white;
    text-decoration:none;
    padding:12px 18px;
    border-radius:12px;
    font-weight:bold;
}

.upload-box{
    background:#fff;
    padding:20px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
    margin-bottom:25px;
}

.upload-title h3{
    margin-bottom:5px;
}

.upload-title p{
    color:#777;
    margin-bottom:15px;
}

.edit-album-form .edit_nama_album{
    color:black;
}

.form-upload{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.form-upload input[type=file]{
    flex:1;
    padding:12px;
    border:1px dashed #ccc;
    border-radius:10px;
}

.btn-upload{
    background:#6b3df5;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
}

.toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
    background:#fff;
    padding:15px 20px;
    border-radius:15px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.btn-hapus{
    background:#e53935;
    color:white;
    border:none;
    padding:12px 18px;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
}

.galeri-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
    gap:20px;
}

.foto-card{
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
    transition:.3s;
}

.foto-card:hover{
    transform:translateY(-4px);
}

.foto-wrapper{
    position:relative;
}

.foto-wrapper img{
    width:100%;
    height:220px;
    object-fit:cover;
    display:block;
}

.checkbox-foto{
    position:absolute;
    top:12px;
    left:12px;
    width:22px;
    height:22px;
    cursor:pointer;
}

.foto-footer{
    padding:15px;
}

.btn-hapus-satu{
    display:block;
    text-align:center;
    background:#e53935;
    color:white;
    padding:10px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
}

.btn-hapus-satu:hover{
    opacity:.9;
}

@media(max-width:768px){

    .album-header{
        flex-direction:column;
    }

    .toolbar{
        flex-direction:column;
        gap:15px;
        align-items:flex-start;
    }

}

</style>


<div class="album-header">

    <div>
        <h2>Album : <?php echo $album['nama']; ?></h2>
        <p>Kelola foto galeri album gunung</p>

        <form method="POST" class="edit-album-form">

                <p class="edit_nama_album">Edit Nama Album:</p>
            <input type="text"
                   name="nama_album"
                   value="<?php echo $album['nama']; ?>"
                   required>

            <button type="submit"
                    name="edit_album"
                    class="btn-save">
                Simpan Nama Album
            </button>

        </form>
    </div>

    <a href="index.php?menu=galeri"
       class="btn-kembali">
        Kembali
    </a>

</div>


<div class="upload-box">

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


<form method="POST">

<div class="toolbar">

    <label>
        <input type="checkbox" id="pilih_semua">
        Pilih Semua
    </label>

    <button type="submit"
            name="hapus_terpilih"
            onclick="return confirm('Hapus semua foto yang dipilih?')"
            class="btn-hapus">
        Hapus Foto Terpilih
    </button>

</div>

<div class="galeri-grid">

<?php while($f = mysqli_fetch_assoc($data)){ ?>

    <div class="foto-card">

        <div class="foto-wrapper">

            <input type="checkbox"
                   class="checkbox-foto"
                   name="foto_hapus[]"
                   value="<?php echo $f['id_galeri']; ?>">

            <img src="../gambar/galeri/<?php echo $f['nama_file']; ?>">

        </div>

        <div class="foto-footer">

            <a href="hapus_foto.php?id=<?php echo $f['id_galeri']; ?>&album=<?php echo $id; ?>"
               onclick="return confirm('Hapus foto ini?')"
               class="btn-hapus-satu">
                Hapus Foto
            </a>

        </div>

    </div>

<?php } ?>

</div>

</form>

<script>
document.getElementById('pilih_semua').addEventListener('change', function(){

    document.querySelectorAll('input[name="foto_hapus[]"]').forEach(function(item){
        item.checked = document.getElementById('pilih_semua').checked;
    });

});
</script>