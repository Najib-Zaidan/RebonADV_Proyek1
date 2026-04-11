<?php
require 'fungsi.php';
$id_trip = $_GET['id'];

$trip = ambil(kueri("SELECT * FROM trip WHERE id_trip = $id_trip"));
$katalog = ambil(kueri("SELECT * FROM katalog WHERE id_trip = $id_trip"));
$res_itinerary = kueri("SELECT * FROM itenerary WHERE id_trip = $id_trip");
$res_meetpoint = kueri("SELECT * FROM meetpoint WHERE id_trip = $id_trip");
$res_fasilitas = kueri("SELECT * FROM fasilitas WHERE id_trip = $id_trip");
$res_gambar = kueri("SELECT * FROM gambar WHERE id_trip = $id_trip");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Trip - <?php echo $trip['tujuan']; ?></title>
</head>
<style>
  body {
    background: url('bg1.jpeg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 50px 20px;
    display: flex;
    justify-content: center;
    color: #333;
}

form {
    width: 100%;
    max-width: 900px;
    display: flex;
    flex-direction: column;
    gap: 25px;
}

h3 {
    margin: 0 0 20px 0;
    color: #fff;
    font-size: 1.4rem;
    border-bottom: 2px solid rgba(157, 2, 8, 0.1);
    padding-bottom: 10px;
}

form > div[id^="section_"], 
form > h3:first-of-type,
form > div {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}

.item-row {
    background: rgba(255, 255, 255, 0.4);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 15px;
    border: 1px solid rgba(255, 255, 255, 0.5);
}

label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #4a0004;
    font-size: 0.85rem;
}

input[type="text"],
input[type="number"],
input[type="date"],
input[type="time"],
textarea,
select {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 15px;
    border: 1px solid rgba(157, 2, 8, 0.2);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
    box-sizing: border-box;
    transition: 0.3s;
}

input:focus, textarea:focus {
    outline: none;
    border-color: #9d0208;
    box-shadow: 0 0 0 3px rgba(157, 2, 8, 0.1);
}

button {
    padding: 10px 20px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

button[type="submit"] {
    background: #9d0208;
    color: #fff;
    padding: 18px;
    font-size: 1.1rem;
    border-radius: 50px;
    box-shadow: 0 10px 20px rgba(157, 2, 8, 0.4);
    margin-top: 20px;
}

button[type="submit"]:hover {
    background: #dc0000;
    transform: translateY(-3px);
}

button[onclick^="add"] {
    background: #4a0004;
    color: #fff;
    font-size: 0.9rem;
}

button[onclick="removeRow(this)"] {
    background: #ff4d4d;
    color: #fff;
    padding: 5px 15px;
    margin-top: 10px;
}

.preview-img {
    width: 200px;
    height: 120px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 15px;
    border: 3px solid #fff;
    display: block;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

hr {
    display: none;
}

</style>
<body>
<form action="proses_ubah_trip.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_trip" value="<?php echo $id_trip; ?>">
    <div id="section_trip">
    <h3>Informasi Trip</h3>
    <div>
        <label>Tujuan:</label>
        <input type="text" name="tujuan" value="<?php echo $trip['tujuan']; ?>" required>
    </div>
    <div>
        <label>Tanggal Berangkat:</label>
        <input type="date" name="tgl_berangkat" value="<?php echo $trip['tgl_berangkat']; ?>" required>
    </div>
    <div>
        <label>Tanggal Pulang:</label>
        <input type="date" name="tgl_pulang" value="<?php echo $trip['tgl_pulang']; ?>" required>
    </div>
    <div>
        <label>Harga:</label>
        <input type="number" name="harga" value="<?php echo $trip['harga']; ?>" required>
    </div>
    <div>
        <label>Kuota:</label>
        <input type="number" name="kuota" value="<?php echo $trip['kuota']; ?>" required>
    </div>
    <div>
        <label>Catatan:</label>
        <textarea name="catatan"><?php echo $trip['catatan']; ?></textarea>
    </div>
    <div>
        <label>Deskripsi Katalog:</label>
        <textarea name="deskripsi" required><?php echo $katalog['deskripsi']; ?></textarea>
    </div>
    </div>

    <hr>

    <div id="section_itinerary">
        <h3>Itinerary</h3>
        <?php 
        $i = 0;
        while($row = ambil($res_itinerary)): 
        ?>
        <div>
            <?php if($i > 0) echo "<hr>"; ?>
            <label>Waktu Mulai : </label>
            <input type="time" name="mulai[]" value="<?php echo $row['mulai']; ?>" required> <br>
            <label>Waktu Selesai : </label>
            <input type="time" name="selesai[]" value="<?php echo $row['selesai']; ?>" required> <br>
            <label>Kegiatan : </label>
            <input type="text" name="kegiatan[]" placeholder="Kegiatan" value="<?php echo $row['kegiatan']; ?>" required>
            <?php if($i > 0): ?>
                <button type="button" onclick="removeRow(this)">Hapus</button>
            <?php endif; ?>
        </div>
        <?php $i++; endwhile; ?>
    </div>
    <button type="button" onclick="addItinerary()">Tambah Itinerary</button>

    <hr>

    <div id="section_meetpoint">
        <h3>Meetpoint</h3>
        <?php 
        $i = 0;
        while($row = ambil($res_meetpoint)): 
        ?>
        <div>
            <?php if($i > 0) echo "<hr>"; ?>
            <label>Waktu Penjemputan : </label>
            <input type="time" name="waktu_mp[]" value="<?php echo $row['waktu']; ?>" required> <br>
            <label>Kota : </label>
            <input type="text" name="kota_mp[]" placeholder="Kota" value="<?php echo $row['kota']; ?>" required> <br>
            <label>Daerah : </label>
            <input type="text" name="daerah_mp[]" placeholder="Daerah" value="<?php echo $row['daerah']; ?>" required>
            <?php if($i > 0): ?>
                <button type="button" onclick="removeRow(this)">Hapus</button>
            <?php endif; ?>
        </div>
        <?php $i++; endwhile; ?>
    </div>
    <button type="button" onclick="addMeetpoint()">Tambah Meetpoint</button>

    <hr>

    <div id="section_fasilitas">
        <h3>Fasilitas</h3>
        <?php 
        $i = 0;
        while($row = ambil($res_fasilitas)): 
        ?>
        <div>
            <label>Fasilitas : </label>
            <input type="text" name="fasilitas[]" placeholder="Nama Fasilitas" value="<?php echo $row['fasilitas']; ?>" required>
            <label>Jenis : </label>
            <select name="jenis_fasilitas[]">
                <option value="include" <?php if($row['jenis'] == 'include') echo 'selected'; ?>>Include</option>
                <option value="exclude" <?php if($row['jenis'] == 'exclude') echo 'selected'; ?>>Exclude</option>
            </select>
            <?php if($i > 0): ?>
                <button type="button" onclick="removeRow(this)">Hapus</button>
            <?php endif; ?>
        </div>
        <?php $i++; endwhile; ?>
    </div>
    <button type="button" onclick="addFasilitas()">Tambah Fasilitas</button>

    <hr>

    <div id="section_gambar">
        <h3>Gambar</h3>
        <?php 
        $i = 0;
        while($row = ambil($res_gambar)): 
        ?>
        <div>
            <p>File lama: <?php echo $row['nama_file']; ?></p>
            <input type="hidden" name="gambar_lama[]" value="<?php echo $row['nama_file']; ?>">
            <input type="file" name="files[]">
            <?php if($i > 0): ?>
                <button type="button" onclick="removeRow(this)">Hapus</button>
            <?php endif; ?>
        </div>
        <?php $i++; endwhile; ?>
    </div>
    <button type="button" onclick="addGambar()">Tambah Gambar</button>

    <hr>

    <button type="submit">Update Trip</button>
</form>
</body>
<script>
function removeRow(btn) {
    btn.parentElement.remove();
}

function addItinerary() {
    let div = document.createElement('div');
    div.innerHTML = '<hr><label>Waktu Mulai : </label><input type="time" name="mulai[]" required> <br><label>Waktu Selesai : </label><input type="time" name="selesai[]" required> <br><input type="text" name="kegiatan[]" placeholder="Kegiatan" required> <button type="button" onclick="removeRow(this)">Hapus</button>';
    document.getElementById('section_itinerary').appendChild(div);
}

function addMeetpoint() {
    let div = document.createElement('div');
    div.innerHTML = '<hr><label>Waktu Penjemputan : </label><input type="time" name="waktu_mp[]" required> <br><input type="text" name="kota_mp[]" placeholder="Kota" required> <br><input type="text" name="daerah_mp[]" placeholder="Daerah" required> <button type="button" onclick="removeRow(this)">Hapus</button>';
    document.getElementById('section_meetpoint').appendChild(div);
}

function addFasilitas() {
    let div = document.createElement('div');
    div.innerHTML = '<input type="text" name="fasilitas[]" placeholder="Nama Fasilitas" required> <select name="jenis_fasilitas[]"><option value="include">Include</option><option value="exclude">Exclude</option></select> <button type="button" onclick="removeRow(this)">Hapus</button>';
    document.getElementById('section_fasilitas').appendChild(div);
}

function addGambar() {
    let div = document.createElement('div');
    div.innerHTML = '<input type="file" name="files[]" required> <button type="button" onclick="removeRow(this)">Hapus</button>';
    document.getElementById('section_gambar').appendChild(div);
}

document.querySelectorAll('#section_trip > div, #section_itinerary > div, #section_meetpoint > div, #section_fasilitas > div, #section_gambar > div').forEach(el => {
    el.classList.add('item-row');
});
</script>
</html>