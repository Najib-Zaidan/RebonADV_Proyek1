<?php 
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

require 'konek.php';
require 'fungsi.php';

// Mengambil data tujuan beserta nilai default untuk auto-fill
$result_tujuan = mysqli_query($konek, "SELECT id_tujuan, tujuan, kota, harga_def, harga_dp_def, rute_def FROM tujuan ORDER BY tujuan ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Trip</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<style>
body {
    background: #f4f2f7;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 20px 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #333;
}

form {
    width: 100%;
    max-width: 900px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    box-sizing: border-box;
}

h3 {
    margin: 0 0 20px 0;
    color: #6f42c1;
    font-size: 1.3rem;
    border-bottom: 2px solid #e1d8f5;
    padding-bottom: 10px;
}

form > div[id^="section_"], 
form > h3:first-of-type,
form > div {
    background: #ffffff;
    border: 1px solid #e1d8f5;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(111, 66, 193, 0.08);
    box-sizing: border-box;
}

.item-row {
    background: #fdfbff;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #e1d8f5;
    box-sizing: border-box;
}

label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #495057;
    font-size: 0.85rem;
}

input[type="text"],
input[type="number"],
input[type="date"],
input[type="time"],
textarea,
select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ced4da;
    border-radius: 8px;
    background: #ffffff;
    font-size: 1rem;
    box-sizing: border-box;
    transition: all 0.3s ease;
}

input:focus, textarea:focus, select:focus {
    outline: none;
    border-color: #6f42c1;
    box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.15);
}

input:disabled, select:disabled, textarea:disabled {
    background: #e9ecef;
    border-color: #ced4da;
    cursor: not-allowed;
    color: #6c757d;
}

button {
    padding: 12px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    width: auto;
}

button[type="submit"] {
    background: #6f42c1;
    color: #fff;
    padding: 16px;
    font-size: 1.1rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(111, 66, 193, 0.3);
    margin-top: 10px;
    width: 100%;
}

button[type="submit"]:hover {
    background: #5a32a3;
}

button[onclick^="add"] {
    background: #e1d8f5;
    color: #6f42c1;
    font-size: 0.9rem;
    border: 1px solid rgba(111, 66, 193, 0.2);
    width: 100%;
    margin-bottom: 10px;
}

button[onclick^="add"]:hover {
    background: #d1c2f0;
}

button[onclick="removeRow(this)"] {
    background: #dc3545;
    color: #fff;
    padding: 8px 15px;
    margin-top: 5px;
    font-size: 0.85rem;
    width: 100%;
}

.preview-img {
    width: 100%;
    max-width: 200px;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
    border: 2px solid #e1d8f5;
    display: block;
}

hr {
    display: none;
}

.header-container {
    width: 100%;
    max-width: 900px;
    margin: 0 auto 20px auto;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    padding: 15px 20px;
    border-radius: 16px;
    border: 1px solid #e1d8f5;
    box-shadow: 0 4px 12px rgba(111, 66, 193, 0.08);
    box-sizing: border-box;
}

.header-container h1 {
    margin: 0;
    color: #6f42c1;
    font-size: 1.4rem;
}

.btn-kembali {
    background: #fff;
    color: #6f42c1;
    text-decoration: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    transition: 0.2s;
    border: 1px solid #6f42c1;
    text-align: center;
}

.btn-kembali:hover {
    background: #f4f2f7;
}

/* Kustomisasi Responsif Khusus Mobile */
@media (max-width: 576px) {
    body {
        padding: 15px 10px;
    }
    .header-container {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
        text-align: center;
    }
    form > div[id^="section_"], form > div {
        padding: 15px;
    }
    input[type="text"], input[type="number"], input[type="date"], input[type="time"], textarea, select {
        font-size: 16px; /* Mencegah auto-zoom safari browser di iOS */
    }
}
</style>
<body>
<div class="header-container">
    <h1>Tambah Trip Baru</h1>
    <a href="index.php" class="btn-kembali">Kembali ke Daftar Trip</a>
</div>

<form action="proses_tambah_trip.php" method="POST" enctype="multipart/form-data">
    <div id="section_trip">
    <h3>Informasi Trip</h3>
    <div>
        <label>Tujuan:</label>
        <select name="id_tujuan" id="id_tujuan" required>
            <option value="">-- Pilih Tujuan --</option>
            <?php while($row_tujuan = mysqli_fetch_assoc($result_tujuan)) : ?>
                <option value="<?= $row_tujuan['id_tujuan']; ?>" 
                        data-harga="<?= $row_tujuan['harga_def']; ?>" 
                        data-dp="<?= $row_tujuan['harga_dp_def']; ?>" 
                        data-rute="<?= htmlspecialchars($row_tujuan['rute_def']); ?>">
                    <?= htmlspecialchars($row_tujuan['tujuan'] . " (" . $row_tujuan['kota'] . ")"); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div>
        <label>Tanggal Berangkat:</label>
        <input type="date" name="tgl_berangkat" required>
    </div>
    <div>
        <label>Tanggal Pulang:</label>
        <input type="date" name="tgl_pulang" required>
    </div>
    <div>
        <label>Harga:</label>
        <input type="number" name="harga" id="harga" required disabled>
    </div>
    <div>
        <label>Harga DP:</label>
        <input type="number" name="harga_dp" id="harga_dp" required disabled>
    </div>
    <div>
        <label>Kuota:</label>
        <input type="number" name="kuota" required>
    </div>
    <div>
        <label>Rute Pendakian / Perjalanan:</label>
        <input type="text" name="rute" id="rute" placeholder="Contoh: Jalur Palutungan" required disabled>
    </div>
    <div>
        <label>Privasi Trip:</label>
        <select name="publik" required>
            <option value="0">Private (Sembunyikan)</option>
            <option value="1">Publik (Tampilkan di Katalog)</option>
        </select>
    </div>
    <div>
        <label>Catatan:</label>
        <textarea name="catatan"></textarea>
    </div>
    <div>
        <label>Deskripsi Katalog:</label>
        <textarea name="deskripsi" required></textarea>
    </div>
    </div>

    <hr>

    <div id="section_itinerary">
        <h3>Itinerary</h3>
        <div class="row">
            <label>Waktu Mulai : </label>
            <input type="time" name="mulai[]" required> <br>
            <label>Waktu Selesai : </label>
            <input type="time" name="selesai[]" required> <br>
            <label>Kegiatan : </label>
            <input type="text" name="kegiatan[]" placeholder="Kegiatan" required>
        </div>
    </div>
    <button type="button" onclick="addItinerary()">Tambah Itinerary</button>

    <hr>

    <div id="section_meetpoint">
        <h3>Meetpoint</h3>
        <div class="row">
            <label>Waktu Penjemputan : </label>
            <input type="time" name="waktu_mp[]" required> <br>
            <label>Kota : </label>
            <input type="text" name="kota_mp[]" placeholder="Kota" required> <br>
            <label>Daerah : </label>
            <input type="text" name="daerah_mp[]" placeholder="Daerah" required>
        </div>
    </div>
    <button type="button" onclick="addMeetpoint()">Tambah Meetpoint</button>

    <hr>

    <div id="section_fasilitas">
        <h3>Fasilitas</h3>
        <div class="row">
            <label>Fasilitas : </label>
            <input type="text" name="fasilitas[]" placeholder="Nama Fasilitas" required>
            <label>Jenis : </label>
            <select name="jenis_fasilitas[]">
                <option value="include">Include</option>
                <option value="exclude">Exclude</option>
            </select>
        </div>
    </div>
    <button type="button" onclick="addFasilitas()">Tambah Fasilitas</button>

    <hr>

    <div id="section_gambar">
        <h3>Gambar</h3>
        <div class="row">
            <label>Upload Gambar : </label>
            <input type="file" name="files[]" accept="image/*" required>
        </div>
    </div>
    <button type="button" onclick="addGambar()">Tambah Gambar</button>

    <hr>

    <button type="submit">Buat Trip</button>
</form>
</body>

<script>
function removeRow(btn) {
    btn.parentElement.remove();
}

function addItinerary() {
    let div = document.createElement('div');
    div.classList.add('item-row');
    div.innerHTML = '<label>Waktu Mulai : </label><input type="time" name="mulai[]" required> <br><label>Waktu Selesai : </label><input type="time" name="selesai[]" required> <br><label>Kegiatan : </label><input type="text" name="kegiatan[]" placeholder="Kegiatan" required> <button type="button" onclick="removeRow(this)">Hapus</button>';
    document.getElementById('section_itinerary').appendChild(div);
}

function addMeetpoint() {
    let div = document.createElement('div');
    div.classList.add('item-row');
    div.innerHTML = '<label>Waktu Penjemputan : </label><input type="time" name="waktu_mp[]" required> <br><label>Kota : </label><input type="text" name="kota_mp[]" placeholder="Kota" required> <br><label>Daerah : </label><input type="text" name="daerah_mp[]" placeholder="Daerah" required> <button type="button" onclick="removeRow(this)">Hapus</button>';
    document.getElementById('section_meetpoint').appendChild(div);
}

function addFasilitas() {
    let div = document.createElement('div');
    div.classList.add('item-row');
    div.innerHTML = '<label>Fasilitas : </label><input type="text" name="fasilitas[]" placeholder="Nama Fasilitas" required><label>Jenis : </label> <select name="jenis_fasilitas[]"><option value="include">Include</option><option value="exclude">Exclude</option></select> <button type="button" onclick="removeRow(this)">Hapus</button>';
    document.getElementById('section_fasilitas').appendChild(div);
}

function addGambar() {
    let div = document.createElement('div');
    div.classList.add('item-row');
    div.innerHTML = '<label>Upload Gambar : </label><input type="file" name="files[]" required> <button type="button" onclick="removeRow(this)">Hapus</button>';
    document.getElementById('section_gambar').appendChild(div);
}

document.querySelectorAll('#section_trip > div, #section_itinerary > div, #section_meetpoint > div, #section_fasilitas > div, #section_gambar > div').forEach(el => {
    el.classList.add('item-row');
});

// Logika interaksi Dropdown Tujuan, Penguncian, dan Auto-Fill Data Default
const selectTujuan = document.getElementById('id_tujuan');
const inputHarga = document.getElementById('harga');
const inputHargaDp = document.getElementById('harga_dp');
const inputRute = document.getElementById('rute');
const formNode = selectTujuan.closest('form');

selectTujuan.addEventListener('change', function() {
    if (this.value !== "") {
        // Aktifkan input jika tujuan dipilih
        inputHarga.disabled = false;
        inputHargaDp.disabled = false;
        inputRute.disabled = false;

        // Ambil data default dari attribute option yang dipilih
        const selectedOption = this.options[this.selectedIndex];
        const hargaDef = selectedOption.getAttribute('data-harga');
        const dpDef = selectedOption.getAttribute('data-dp');
        const ruteDef = selectedOption.getAttribute('data-rute');

        // Isi kolom otomatis (jika null di database, kosongkan)
        inputHarga.value = hargaDef !== null ? hargaDef : "";
        inputHargaDp.value = dpDef !== null ? dpDef : "";
        inputRute.value = ruteDef !== null ? ruteDef : "";
    } else {
        // Kunci kembali dan kosongkan isi field jika dropdown direset
        inputHarga.disabled = true;
        inputHargaDp.disabled = true;
        inputRute.disabled = true;
        inputHarga.value = "";
        inputHargaDp.value = "";
        inputRute.value = "";
    }
});

formNode.addEventListener('submit', function(e) {
    if (inputHarga.value <= 0) {
        e.preventDefault();
        alert('Harga harus lebih besar dari 0!');
        inputHarga.focus();
        return;
    }
    if (inputHargaDp.value < 0) {
        e.preventDefault();
        alert('Harga DP tidak boleh minus!');
        inputHargaDp.focus();
        return;
    }
});
</script>
</html>
