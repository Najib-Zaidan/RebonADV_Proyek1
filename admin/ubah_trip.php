<?php
require 'fungsi.php';
// 1. Tangkap ID Trip dari URL
$id_trip = $_GET['id'];

// 2. Proses Update Data (Jika tombol simpan ditekan)
if (isset($_POST['update'])) {
    $tujuan = $_POST['tujuan'];
    $tgl_berangkat = $_POST['tgl_berangkat'];
    $tgl_pulang = $_POST['tgl_pulang'];
    $harga = $_POST['harga'];
    $kuota = $_POST['kuota'];
    $catatan = $_POST['catatan'];

    $sql_update = "UPDATE trip SET 
                    tujuan = '$tujuan', 
                    tgl_berangkat = '$tgl_berangkat', 
                    tgl_pulang = '$tgl_pulang', 
                    harga = '$harga', 
                    kuota = '$kuota', 
                    catatan = '$catatan' 
                   WHERE id_trip = $id_trip";
    
    kueri($sql_update);
    
    // Redirect kembali ke halaman utama atau detail setelah sukses
    header("Location: index.php");
    exit;
}

// 3. Ambil data saat ini untuk nilai default formulir
$data_lama = ambil(kueri("SELECT * FROM trip WHERE id_trip = $id_trip"));
$data_katalog   = ambil(kueri("SELECT * FROM katalog WHERE id_trip = $id_trip"));
$data_gambar    = ambil(kueri("SELECT * FROM gambar WHERE id_trip = $id_trip"));
$data_itinerary = ambil(kueri("SELECT * FROM itenerary WHERE id_trip = $id_trip ORDER BY mulai ASC"));
$data_meetpoint = ambil(kueri("SELECT * FROM meetpoint WHERE id_trip = $id_trip"));
$data_fasilitas = ambil(kueri("SELECT * FROM fasilitas WHERE id_trip = $id_trip"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ubah Data Trip</title>
</head>
<body>

    <h2>Ubah Data Trip: <?php echo $data_lama['tujuan']; ?></h2>
    <a href="index.php">Batal dan Kembali</a>
    <hr>

    <form action="" method="POST">
        <table border="0" cellpadding="10">
            <tr>
                <td>Tujuan Trip</td>
                <td>:</td>
                <td>
                    <input type="text" name="tujuan" value="<?php echo $data_lama['tujuan']; ?>" required size="50">
                </td>
            </tr>
            <tr>
                <td>Tanggal Berangkat</td>
                <td>:</td>
                <td>
                    <input type="date" name="tgl_berangkat" value="<?php echo $data_lama['tgl_berangkat']; ?>" required>
                </td>
            </tr>
            <tr>
                <td>Tanggal Pulang</td>
                <td>:</td>
                <td>
                    <input type="date" name="tgl_pulang" value="<?php echo $data_lama['tgl_pulang']; ?>" required>
                </td>
            </tr>
            <tr>
                <td>Harga (Rp)</td>
                <td>:</td>
                <td>
                    <input type="number" name="harga" value="<?php echo $data_lama['harga']; ?>" required>
                </td>
            </tr>
            <tr>
                <td>Kuota Peserta</td>
                <td>:</td>
                <td>
                    <input type="number" name="kuota" value="<?php echo $data_lama['kuota']; ?>" required>
                </td>
            </tr>
            <tr>
                <td>Catatan Tambahan</td>
                <td>:</td>
                <td>
                    <textarea name="catatan" cols="50" rows="5"><?php echo $data_lama['catatan']; ?></textarea>
                </td>
            </tr>
            <tr>
                <td>Kuota Peserta</td>
                <td>:</td>
                <td>
                    <input type="number" name="kuota" value="<?php echo $data_lama['kuota']; ?>" required>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>
                    <button type="submit" name="update">Simpan Perubahan</button>
                    <button type="reset">Reset Form</button>
                </td>
            </tr>
        </table>
    </form>

</body>
</html>