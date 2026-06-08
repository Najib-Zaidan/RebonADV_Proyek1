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
    min-height:100vh;
    background:
        radial-gradient(circle at top left,#8a6be8 0%,transparent 35%),
        radial-gradient(circle at bottom right,#5a2fcf 0%,transparent 35%),
        linear-gradient(135deg,#4b1fa3,#6b3df5);
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

.form{
    width:100%;
    max-width:450px;
}

form{
    background:rgba(234,228,204,.98);
    border-radius:24px;
    padding:28px;
    box-shadow:
        0 20px 40px rgba(0,0,0,.15),
        0 5px 15px rgba(0,0,0,.08);
    border:1px solid rgba(255,255,255,.4);
}

.btn-kembali{
    display:inline-flex;
    align-items:center;
    text-decoration:none;
    color:#fff;
    background:#6b3df5;
    padding:10px 16px;
    border-radius:12px;
    font-size:13px;
    font-weight:600;
    margin-bottom:20px;
    transition:.3s;
}

.btn-kembali:hover{
    background:#4b1fa3;
}

.judul{
    text-align:center;
    margin-bottom:20px;
}

.judul h2{
    color:#2c2f7a;
    font-size:24px;
    margin-bottom:5px;
}

.judul p{
    color:#666;
    font-size:13px;
}

/* CARD REKENING */
.info-rekening{
    background:linear-gradient(135deg,#e8deff,#f4efff);
    border-radius:18px;
    padding:16px;
    margin-bottom:20px;
    border:1px solid #d9c9ff;
}

.info-rekening h3{
    text-align:center;
    color:#4b1fa3;
    margin-bottom:12px;
    font-size:16px;
}

.rekening-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:10px 0;
    border-bottom:1px solid rgba(0,0,0,.08);
}

.rekening-item:last-child{
    border-bottom:none;
}

.rekening-item span{
    color:#666;
    font-size:13px;
}

.rekening-item strong{
    color:#222;
    font-size:14px;
}

label{
    display:block;
    margin-bottom:8px;
    color:#333;
    font-size:14px;
    font-weight:600;
}

input[type="number"],
input[type="file"],
textarea{
    width:100%;
    border:none;
    outline:none;
    background:#d8cfee;
    border-radius:14px;
    padding:13px 15px;
    font-size:14px;
    transition:.3s;
}

input[type="number"]:focus,
input[type="file"]:focus,
textarea:focus{
    background:#cfc3ef;
    box-shadow:0 0 0 4px rgba(107,61,245,.15);
}

textarea{
    min-height:100px;
    resize:vertical;
}

.info-upload{
    display:block;
    margin-top:8px;
    color:#777;
    font-size:12px;
}

hr{
    border:none;
    height:1px;
    background:#d4cab7;
    margin:20px 0;
}

button{
    width:100%;
    border:none;
    padding:14px;
    border-radius:14px;
    background:linear-gradient(135deg,#4b1fa3,#2c2f7a);
    color:#fff;
    font-size:15px;
    font-weight:700;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(75,31,163,.25);
}

select{
    width:100%;
    padding:12px;
    border:none;
    border-radius:12px;
    background:#d8cfee;
    margin-bottom:15px;
    outline:none;
}

.payment-card{
    background:#f4efff;
    border-radius:16px;
    padding:15px;
    margin-bottom:20px;
    border:1px solid #d9c9ff;
}

.payment-card h4{
    margin-bottom:10px;
    color:#4b1fa3;
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

/* RESPONSIVE */
@media(max-width:768px){

    body{
        padding:15px;
        align-items:flex-start;
    }

    .form{
        max-width:100%;
        margin-top:20px;
    }

    form{
        padding:22px;
    }
}

@media(max-width:480px){

    form{
        padding:18px;
    }

    .btn-kembali{
        width:100%;
        justify-content:center;
    }

    .rekening-item{
        flex-direction:column;
        align-items:flex-start;
        gap:4px;
    }

    .judul h2{
        font-size:20px;
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
            <h2>Pembayaran Trip</h2>
            <p>Upload bukti pembayaran untuk proses verifikasi oleh admin.</p>
        </div>

        <input type="hidden" name="id_booking" value="<?php echo $_GET['id_booking']; ?>">

        <label>Metode Pembayaran</label>

            <select id="metodePembayaran" onchange="showPaymentInfo()">
                <option value="">-- Pilih Metode Pembayaran --</option>

                <option value="bca">BCA</option>
                <option value="bri">BRI</option>
                <option value="bsi">BSI</option>
                <option value="dana">DANA</option>
                <option value="gopay">GoPay</option>
            </select>

<div id="paymentInfo"></div>

<hr>

        <label>Nominal Pembayaran</label>
        <input
            type="number"
            name="nominal"
            placeholder="Masukkan nominal pembayaran"
            required>

        <hr>

        <label>Bukti Pembayaran</label>
        <input
            type="file"
            name="bukti_bayar"
            accept="image/*"
            required>

        <small class="info-upload">
            * Maksimal ukuran file 5MB (JPG, JPEG, PNG)
        </small>

        <hr>

        <label>Catatan</label>
        <textarea
            name="catatan"
            placeholder="Tambahkan catatan pembayaran (opsional)..."></textarea>

        <hr>

        <button type="submit">
            Kirim Pembayaran
        </button>

    </form>

</div>

<script>
function showPaymentInfo(){

    var metode = document.getElementById("metodePembayaran").value;
    var box = document.getElementById("paymentInfo");

    if(metode == "bca"){

        box.innerHTML = `
        <div class="payment-card">
            <h4>BCA</h4>

            <div class="payment-item">
                <span>No Rekening</span>
                <strong>123456789</strong>
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
            <h4>BRI</h4>

            <div class="payment-item">
                <span>No Rekening</span>
                <strong>123456789</strong>
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
            <h4>BSI</h4>

            <div class="payment-item">
                <span>No Rekening</span>
                <strong>123456789</strong>
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
            <h4>DANA</h4>

            <div class="payment-item">
                <span>Nomor DANA</span>
                <strong>08123456789</strong>
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
            <h4>GoPay</h4>

            <div class="payment-item">
                <span>Nomor GoPay</span>
                <strong>08123456789</strong>
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