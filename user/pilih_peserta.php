<?php
session_start();

$id = $_SESSION['id_akun'];
$id_trip = $_GET['id'];
require 'fungsi.php';
$query = "SELECT id_peserta, nama, no_hp, tgl_lahir, alamat, riwayat FROM peserta WHERE id_akun = '$id'";
$result = kueri($query);
if(mysqli_num_rows($result)):
?>
<h1>Silakan pilih peserta</h1>
<form action="proses_booking.php" method="POST">
    <hr>
    <table>
        <thead>
            <tr>
                <th>Pilih</th>
                <th>Nama</th>
                <th>No HP</th>
                <th>Tanggal Lahir</th>
                <th>Alamat</th>
                <th>Riwayat</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = ambil($result)): ?>
            <tr>
                <td>
                    <input type="radio" name="id_peserta" value="<?php echo $row['id_peserta']; ?>" required>
                    <input type="hidden" name="id_trip" value="<?= $id_trip ?>" required>
                </td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['no_hp']; ?></td>
                <td><?php echo $row['tgl_lahir']; ?></td>
                <td><?php echo $row['alamat']; ?></td>
                <td><?php echo $row['riwayat']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <button type="submit">Submit Pilihan</button>
    <hr>
</form>

<h1>Atau <a href="form.php">Tambah Peserta</a></h1>

<?php else:
  ?>
  <h1>O oww Kamu belum memiliki data peserta</h1>
  <h3>Silakan <a href="form.php?loc=pilih_peserta">Tambah peserta disini</a></h3>
<?php endif; ?>