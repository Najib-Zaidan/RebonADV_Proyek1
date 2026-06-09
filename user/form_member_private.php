<?php
session_start();
require 'fungsi.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_GET['id_private'])) {
        die("ID Private Trip tidak ditemukan.");
    }

    $id_private = $_GET['id_private'];

    $daftar_nama   = $_POST['nama'];
    $daftar_usia   = $_POST['usia'];
    $daftar_alamat = $_POST['alamat'];
    $daftar_detail = $_POST['detail']; 

    $sukses = true;

    mysqli_begin_transaction($konek);

    try {
        foreach ($daftar_nama as $index => $nama) {

            $nama_clean   = mysqli_real_escape_string($konek, $nama);
            $usia_clean   = mysqli_real_escape_string($konek, $daftar_usia[$index]);
            $alamat_clean = mysqli_real_escape_string($konek, $daftar_alamat[$index]);
            $riwayat_clean = mysqli_real_escape_string($konek, $daftar_detail[$index]);

            $sql = "INSERT INTO peserta_private 
                    (id_private, nama, usia, alamat, riwayat) 
                    VALUES 
                    ('$id_private', '$nama_clean', '$usia_clean', '$alamat_clean', '$riwayat_clean')";

            if (!kueri($sql)) {
                $sukses = false;
                break;
            }
        }

        if ($sukses) {
            mysqli_commit($konek);
            echo "<script>
                    alert('Berhasil mendaftarkan semua peserta!');
                    window.location.href = 'profiluser.php'; 
                  </script>";
        } else {
            mysqli_rollback($konek);
            echo "Terjadi kesalahan saat menyimpan data.";
        }

    } catch (Exception $e) {
        mysqli_rollback($konek);
        echo "Error: " . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Formulir Private Trip</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:#e7e2c8;
}

/* SECTION */
.form-section{
    min-height:100vh;
    background:linear-gradient(135deg,#4e2bbf,#8b6cf6);
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px 15px;
}

/* CARD */
.form-container{
    width:100%;
    max-width:800px;
    background:#fff;
    padding:35px;
    border-radius:20px;
    box-shadow:0 15px 35px rgba(0,0,0,.2);
}

.form-container h2{
    text-align:center;
    color:#4e2bbf;
    margin-bottom:25px;
    font-size:26px;
}

/* PESERTA */
.peserta-card{
    background:#f7f7f7;
    border:1px solid #e5e5e5;
    padding:20px;
    border-radius:15px;
    margin-bottom:18px;
}

.peserta-card h4{
    color:#4e2bbf;
    margin-bottom:12px;
}

/* INPUT */
.form-container input{
    width:100%;
    padding:12px 14px;
    margin-bottom:10px;
    border:1px solid #ddd;
    border-radius:10px;
    outline:none;
    transition:.3s;
}

.form-container input:focus{
    border-color:#6b3df5;
    box-shadow:0 0 0 4px rgba(107,61,245,.15);
}

/* BUTTON */
.button-group{
    display:flex;
    gap:12px;
    margin-top:20px;
}

.btn{
    flex:1;
    padding:14px;
    border-radius:10px;
    text-align:center;
    font-weight:bold;
    text-decoration:none;
    border:none;
    cursor:pointer;
    transition:.3s;
}

/* KEMBALI */
.btn-back{
    background:#e0e0e0;
    color:#333;
}

.btn-back:hover{
    background:#c9c9c9;
}

/* SUBMIT */
.btn-submit{
    background:#4e2bbf;
    color:#fff;
}

.btn-submit:hover{
    background:#3d1ea5;
}

/* RESPONSIVE */
@media(max-width:768px){
    .form-container{
        padding:25px;
    }

    .button-group{
        flex-direction:column;
    }
}
</style>
</head>

<body>

<section class="form-section">

<?php
$jumlah_peserta = isset($_GET['jumlah']) ? (int)$_GET['jumlah'] : 1;
?>

<div class="form-container">

    <h2>TAMBAH PESERTA PRIVATE TRIP</h2>

    <form method="POST">

        <?php $i = 1; while ($i <= $jumlah_peserta): ?>

        <div class="peserta-card">
            <h4>Peserta Ke-<?php echo $i; ?></h4>

            <input type="text" name="nama[]" placeholder="Nama Lengkap Peserta <?php echo $i; ?>" required>

            <input type="number" name="usia[]" placeholder="Usia Peserta <?php echo $i; ?>" required>

            <input type="text" name="alamat[]" placeholder="Alamat Lengkap Peserta <?php echo $i; ?>" required>

            <input type="text" name="detail[]" placeholder="Riwayat Penyakit (Opsional)">
        </div>

        <?php $i++; endwhile; ?>

        <div class="button-group">

            <!-- <a href="private_trip.php" class="btn btn-back">
                Kembali
            </a> -->

            <button type="submit" class="btn btn-submit">
                Pesan Sekarang
            </button>

        </div>

    </form>

</div>

</section>

</body>
</html>