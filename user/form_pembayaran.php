<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pembayaran Trip</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(135deg,#4b1fa3,#8a6be8);
    min-height:100vh;
}

.form{
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px 15px;
}

form{
    width:100%;
    max-width:500px;
    background:#eae4cc;
    padding:30px;
    border-radius:20px;
    box-shadow:0 15px 40px rgba(0,0,0,.2);
}

.btn-kembali{
    display:inline-block;
    text-decoration:none;
    background:#6b3df5;
    color:#fff;
    padding:10px 18px;
    border-radius:10px;
    font-weight:600;
    margin-bottom:20px;
    transition:.3s;
}

.btn-kembali:hover{
    background:#4b1fa3;
}

.judul{
    text-align:center;
    margin-bottom:25px;
}

.judul h2{
    color:#2c2f7a;
    margin-bottom:5px;
}

.judul p{
    color:#666;
    font-size:13px;
}

label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
    color:#333;
}

select,
input[type="number"],
input[type="file"]{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#cfc6e8;
    outline:none;
    font-size:14px;
}

textarea{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#cfc6e8;
    resize:none;
    outline:none;
}

.payment-card{
    background:#f4efff;
    border:1px solid #ddd;
    border-radius:15px;
    padding:15px;
    margin-top:15px;
    margin-bottom:20px;
}

.payment-card h4{
    color:#4b1fa3;
    margin-bottom:12px;
}

.payment-item{
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid rgba(0,0,0,.08);
}

.payment-item:last-child{
    border-bottom:none;
}

hr{
    border:none;
    height:1px;
    background:#bbb;
    margin:20px 0;
}

button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#4b1fa3,#2c2f7a);
    color:white;
    font-size:15px;
    font-weight:bold;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    transform:translateY(-2px);
}

.info-upload{
    display:block;
    margin-top:8px;
    color:#444;
    font-size:12px;
}

@media(max-width:600px){

    .form{
        padding:20px 10px;
    }

    form{
        padding:20px;
    }

    .payment-item{
        flex-direction:column;
        gap:4px;
    }

    .btn-kembali{
        width:100%;
        text-align:center;
    }
}

</style>
</head>

<body>

<div class="form">

<form action="proses_pembayaran.php" method="POST" enctype="multipart/form-data">

    <a href="profiluser.php" class="btn-kembali">
         Kembali
    </a>

    <div class="judul">
        <h2> Pembayaran Trip</h2>
        <p>Pilih metode pembayaran kemudian upload bukti transfer.</p>
    </div>

    <input type="hidden" name="id_booking" value="<?php echo $_GET['id_booking']; ?>">

    <label>Metode Pembayaran</label>

    <select id="metodePembayaran" onchange="showPaymentInfo()">
        <option value="">-- Pilih Metode Pembayaran --</option>

        <option value="bca"> BCA</option>
        <option value="bri"> BRI</option>
        <option value="bsi"> BSI</option>
        <option value="dana"> DANA</option>
        <option value="gopay"> GoPay</option>
    </select>

    <div id="paymentInfo"></div>

    <hr>

    <label>Nominal Pembayaran</label>
    <input type="number" name="nominal" required>

    <hr>

    <label>Bukti Pembayaran</label>
    <input type="file" name="bukti_bayar" accept="image/*" required>

    <small class="info-upload">
        * Maksimal ukuran file 5MB (JPG, JPEG, PNG)
    </small>

    <hr>

    <label>Catatan</label>

    <textarea
        name="catatan"
        rows="4"
        placeholder="Tulis catatan pembayaran (opsional)..."></textarea>

    <hr>

    <button type="submit">
        Kirim Pembayaran
    </button>

</form>

</div>

<script>

function showPaymentInfo(){

    let metode = document.getElementById("metodePembayaran").value;
    let box = document.getElementById("paymentInfo");

    if(metode == "bca"){

        box.innerHTML = `
        <div class="payment-card">
            <h4> BCA</h4>

            <div class="payment-item">
                <span>No Rekening</span>
                <strong>1234567890</strong>
            </div>

            <div class="payment-item">
                <span>Atas Nama</span>
                <strong>Rebon Adventure</strong>
            </div>
        </div>`;
    }

    else if(metode == "bri"){

        box.innerHTML = `
        <div class="payment-card">
            <h4> BRI</h4>

            <div class="payment-item">
                <span>No Rekening</span>
                <strong>9876543210</strong>
            </div>

            <div class="payment-item">
                <span>Atas Nama</span>
                <strong>Rebon Adventure</strong>
            </div>
        </div>`;
    }

    else if(metode == "bsi"){

        box.innerHTML = `
        <div class="payment-card">
            <h4> BSI</h4>

            <div class="payment-item">
                <span>No Rekening</span>
                <strong>1122334455</strong>
            </div>

            <div class="payment-item">
                <span>Atas Nama</span>
                <strong>Rebon Adventure</strong>
            </div>
        </div>`;
    }

    else if(metode == "dana"){

        box.innerHTML = `
        <div class="payment-card">
            <h4> DANA</h4>

            <div class="payment-item">
                <span>Nomor DANA</span>
                <strong>081234567890</strong>
            </div>

            <div class="payment-item">
                <span>Atas Nama</span>
                <strong>Rebon Adventure</strong>
            </div>
        </div>`;
    }

    else if(metode == "gopay"){

        box.innerHTML = `
        <div class="payment-card">
            <h4> GoPay</h4>

            <div class="payment-item">
                <span>Nomor GoPay</span>
                <strong>081234567890</strong>
            </div>

            <div class="payment-item">
                <span>Atas Nama</span>
                <strong>Rebon Adventure</strong>
            </div>
        </div>`;
    }

    else{
        box.innerHTML = "";
    }
}

</script>

</body>
</html>