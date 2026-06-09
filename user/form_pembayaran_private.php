<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pembayaran Private Trip</title>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #4b1fa3, #8a6be8);
    min-height: 100vh;
}

.wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 30px;
}

.form-box {
    background: #eae4cc;
    width: 450px;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.25);
}

h2 {
    margin: 0 0 20px;
    font-size: 20px;
    text-align: center;
    color: #2c2c2c;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

input[type="number"],
input[type="file"],
select,
textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 10px;
    border: none;
    background: #cfc6e8;
    font-size: 14px;
    outline: none;
}

textarea {
    resize: none;
}

button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #4b1fa3, #2c2f7a);
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    transform: scale(1.02);
    opacity: 0.95;
}

.back-btn {
    display: inline-block;
    margin-bottom: 15px;
    text-decoration: none;
    font-size: 14px;
    color: #4b1fa3;
    font-weight: 600;
}

.back-btn:hover {
    text-decoration: underline;
}

hr {
    border: none;
    height: 1px;
    background: #b9b2d6;
    margin: 15px 0;
}

/* BOX REKENING */
.rekening-box {
    background: #fff;
    border-radius: 12px;
    padding: 12px;
    margin-bottom: 15px;
    font-size: 13px;
    border-left: 5px solid #4b1fa3;
    display: none;
}

.rekening-box span {
    display: block;
    margin-top: 5px;
    font-weight: bold;
    color: #333;
}
</style>
</head>

<body>

<div class="wrapper">

    <div class="form-box">

        <a href="profiluser.php" class="back-btn"> Kembali ke Profil</a>

        <h2>Pembayaran Private Trip</h2>

        <form action="proses_pembayaran_private.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id_private"
                value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">

            <label>Metode Pembayaran:</label>
            <select name="metode_pembayaran" id="metode" required>
                <option value="">-- Pilih Metode --</option>
                <option value="BCA">BCA</option>
                <option value="BRI">BRI</option>
                <option value="BSI">BSI</option>
                <option value="DANA">DANA</option>
                <option value="GOPAY">GOPAY</option>
            </select>

            <!-- BOX REKENING -->
            <div class="rekening-box" id="rekeningBox">
                <div id="rekeningText"></div>
            </div>

            <label>Nominal Pembayaran:</label>
            <input type="number" name="nominal" required>

            <label>Bukti Pembayaran:</label>
            <input type="file" name="bukti_bayar" accept="image/*" required>

            <label>Catatan:</label>
            <textarea name="catatan" rows="4" placeholder="Tulis catatan pembayaran (opsional)..."></textarea>

            <button type="submit">Kirim Pembayaran</button>

        </form>
    </div>

</div>

<script>
const metode = document.getElementById("metode");
const box = document.getElementById("rekeningBox");
const text = document.getElementById("rekeningText");

const dataRekening = {
    BCA: "Bank BCA\nNo Rekening: 123456789\nA/N: Rebon Adventure",
    BRI: "Bank BRI\nNo Rekening: 123456789\nA/N: Rebon Adventure",
    BSI: "Bank BSI\nNo Rekening: 123456789\nA/N: Rebon Adventure",
    DANA: "E-Wallet DANA\nNomor: 08123456789\nA/N: Rebon Adventure",
    GOPAY: "E-Wallet GOPAY\nNomor: 08987654321\nA/N: Rebon Adventure"
};

metode.addEventListener("change", function () {
    const val = this.value;

    if (dataRekening[val]) {
        box.style.display = "block";
        text.innerHTML = dataRekening[val].replace(/\n/g, "<br>");
    } else {
        box.style.display = "none";
        text.innerHTML = "";
    }
});
</script>

</body>
</html>