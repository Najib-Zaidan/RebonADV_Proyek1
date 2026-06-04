<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ</title>
    <style>
      * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
}

body {
  background: linear-gradient(180deg, #1f1f1f 10% , #6b1dfc) ;
   min-height: 100svh;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 80px;
  background: #f4f0e5;
}

.logo img {
  height: 50px;
}

nav {
  display: flex;
  gap: 30px;
  align-items: center;
}

nav a {
  text-decoration: none;
  color: black;
  font-weight: 500;
}

nav .active4 {
  color: #6b3df5;
}

.active5 {
  background: #6b3df5;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
}
.container {
  max-width: 900px;
  min-height: 100vh;
  margin: auto;

}

h1 {
  text-align: center;
  margin-bottom: 30px;
  color: #ffffff;
}

.faq-filter{
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 30px;
    margin-bottom: 35px;
    
}

.filter-card{
    cursor: pointer;
}

.filter-card input{
    display: none;
}

.filter-content{
    background: rgba(255,255,255,0.08);
    padding: 14px 22px;
    border-radius: 30px;
    transition: 0.3s ease;
    border: 2px solid transparent;
    backdrop-filter: blur(5px);
}

.filter-content h4{
    color: white;
    font-size: 15px;
    font-weight: 600;
}

/* ACTIVE */
.filter-card input:checked + .filter-content{
    background: white;
    border-color: #6b3df5;
}

.filter-card input:checked + .filter-content h4{
    color: #6b3df5;
}

/* HOVER */
.filter-card:hover .filter-content{
    transform: translateY(-3px);
}

/* ================= FAQ ================= */

.faq{
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 18px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: 0.3s ease;
}

.faq:hover{
    transform: translateY(-4px);
}

.faq-question{
     font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    color: #222;
}

.faq-answer{
    margin-top: 15px;
    color: #555;
    line-height: 1.6;
    display: none;
    font-size: 15px;
}

footer {
  background-color: #fdfae6;
  padding: 40px 10% 20px 10%;
  color: #333;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 30px;
  border-bottom: 1px solid #ccc;
  padding-bottom: 30px;
}

.footer-column h4 {
  font-size: 16px;
  margin-bottom: 15px;
  font-weight: 800;
}

.footer-column ul {
  list-style: none;
}

.footer-column ul li {
  margin-bottom: 8px;
  font-size: 14px;
  font-weight: 600;
}
h5 {
  text-align: center;
  color: #fff;
}

.container2 {
    display: flex;
    justify-content: center;   /* tengah horizontal */
    align-items: center;       /* tengah vertikal */

}

.wa-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background-color: #25D366;
    color: white;
    padding: 12px 20px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    display: flex;
    justify-content: center;
    align-items: center; 
    width: 500px;
  }

  .wa-btn img {
    width: 20px;
    height: 20px;
  }

  .wa-btn:hover {
    background-color: #1ebe5d;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
  }

/* Styling Kontak dengan Ikon */
.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 10px;
  font-size: 14px;
  font-weight: 600;
}

/* Bagian Media Sosial */
.social-section {
  margin-top: 25px;
}

.social-icons {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.social-icons img {
  width: 24px;
  height: 24px;
  cursor: pointer;
}

.footer-logo-img {
  width: 220px; /* Ukuran logo dikecilkan agar proporsional */
  height: auto;
  display: block;
}

.copyright {
  text-align: center;
  font-size: 12px;
  margin-top: 20px;
  font-weight: bold;
  color: #333;
}
    </style>
</head>
<body>
    <header class="navbar">
      <div class="logo">
        <img
          src="../gambar/REBON LOGO GRADIENT presisi.png"
          alt="Rebon Adventure"
        />
      </div>

      <nav>
        <a href="home1.php" class="active1">Home</a>
        <a href="open_trip.php" class="active2">Open</a>
        <a href="private_trip.php" class="active3">Private</a>
        <a href="tentang_kami.php" class="active4">Tentang Kami</a>
        <a href="profiluser.php"><?php if (isset($_SESSION['username'])): ?>
            <!-- JIKA SUDAH LOGIN -->
            <span style="color:blue; margin-right:10px;">
              👤 <?php echo $_SESSION['username']; ?>
            </span>
        </a>
            <a href="logout_user.php">
              <button class="active5" onclick="return confirm('Yakin ingin logout?')">Logout</button>
            </a>

        <?php else: ?>
            <!-- JIKA BELUM LOGIN -->
            <a href="login_user.php">
              <button class="active5">Masuk</button>
            </a>
        <?php endif; ?>
      </nav>
    </header>
        <div class="container">
          <br>
          <br>
      <h1>Pertanyaan Yang Sering Di Ajukan (F.A.Q)</h1>
      <br>
      <br>

      <div class="faq-filter">


    <!-- UMUM -->
    <label class="filter-card">

        <input type="radio" name="faq-filter" id="umum">

        <div class="filter-content">
            <h4>Pertanyaan Umum</h4>
        </div>

    </label>


    <!-- PERLENGKAPAN -->
    <label class="filter-card">

        <input type="radio" name="faq-filter" id="perlengkapan">

        <div class="filter-content">
            <h4>Perlengkapan</h4>
        </div>

    </label>


    <!-- PEMBAYARAN -->
    <label class="filter-card">

        <input type="radio" name="faq-filter" id="pembayaran">

        <div class="filter-content">
            <h4>Pembayaran</h4>
        </div>

    </label>


    <!-- KEBERANGKATAN -->
    <label class="filter-card">

        <input type="radio" name="faq-filter" id="keberangkatan">

        <div class="filter-content">
            <h4>Keberangkatan</h4>
        </div>

    </label>

</div>



<!-- ================= PERTANYAAN UMUM ================= -->

<div class="faq umum">

    <div class="faq-question">
        Apa itu open trip?
    </div>

    <div class="faq-answer">
        Open trip adalah perjalanan yang dibuka untuk umum, sehingga siapa saja bisa ikut dan akan digabung dengan peserta lain dalam satu grup sesuai jadwal yang sudah ditentukan oleh Rebon Adventure.
    </div>

</div>

<div class="faq umum">

    <div class="faq-question">
        Apa itu private trip?
    </div>

    <div class="faq-answer">
        Private trip adalah perjalanan khusus yang pesertanya hanya dari satu kelompok atau komunitas sendiri.
    </div>

</div>

<div class="faq umum">

    <div class="faq-question">
        Bagaimana cara memesan Open trip?
    </div>

    <div class="faq-answer">
        Private trip adalah perjalanan khusus yang pesertanya hanya dari satu kelompok atau komunitas sendiri.
    </div>

</div>

<div class="faq umum">

    <div class="faq-question">
        Bagaimana cara memesan Private trip?
    </div>

    <div class="faq-answer">
        Private trip adalah perjalanan khusus yang pesertanya hanya dari satu kelompok atau komunitas sendiri.
    </div>

</div>



<!-- ================= PERLENGKAPAN ================= -->

<div class="faq perlengkapan">

    <div class="faq-question">
        Barang apa saja yang harus dibawa?
    </div>

    <div class="faq-answer">
        Peserta wajib membawa perlengkapan pribadi seperti jaket, sleeping bag, pakaian ganti, dan obat pribadi.
    </div>

</div>

<div class="faq perlengkapan">

    <div class="faq-question">
        Apakah carrier disediakan?
    </div>

    <div class="faq-answer">
        Carrier dapat disewa sesuai ketersediaan dari pihak Rebon Adventure.
    </div>

</div>



<!-- ================= PEMBAYARAN ================= -->

<div class="faq pembayaran">

    <div class="faq-question">
        Bagaimana cara pembayaran?
    </div>

    <div class="faq-answer">
        Pembayaran dapat dilakukan melalui transfer bank ataupun e-wallet yang tersedia.
    </div>

</div>

<div class="faq pembayaran">

    <div class="faq-question">
        Apakah bisa DP terlebih dahulu?
    </div>

    <div class="faq-answer">
        Ya, peserta dapat melakukan pembayaran DP terlebih dahulu sesuai ketentuan yang berlaku.
    </div>

</div>

<div class="faq pembayaran">

    <div class="faq-question">
        Kapan dimulainya pembayaran dp?
    </div>

    <div class="faq-answer">
        Ya, peserta dapat melakukan pembayaran DP terlebih dahulu sesuai ketentuan yang berlaku.
    </div>

</div>

<div class="faq pembayaran">

    <div class="faq-question">
        Kapan batasan pembayaran dp?
    </div>

    <div class="faq-answer">
        Ya, peserta dapat melakukan pembayaran DP terlebih dahulu sesuai ketentuan yang berlaku.
    </div>

</div>



<!-- ================= KEBERANGKATAN ================= -->

<div class="faq keberangkatan">

    <div class="faq-question">
        Jam keberangkatan kapan untuk open trip?
    </div>

    <div class="faq-answer">
        Jadwal keberangkatan akan diinformasikan oleh admin sebelum hari pelaksanaan trip.
    </div>

</div>

<div class="faq keberangkatan">

    <div class="faq-question">
        Titik kumpul berada di mana?
    </div>

    <div class="faq-answer">
        Titik kumpul akan diinformasikan sesuai destinasi dan jadwal trip yang dipilih.
    </div>

</div>


    </div>
    <h5>informasi lebih lanjut hubungi kami:</h5>
    <br>
    <div class="container2">
    <a href="https://wa.me/6281234567890" target="_blank" class="wa-btn">
          <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp">
          <span>Chat WhatsApp</span>
        </a>
        </div>
          <br>
          <br>
          
    
<script>

/* ================= FILTER FAQ ================= */

const filters = document.querySelectorAll('.faq-filter input');
const faqs = document.querySelectorAll('.faq');

filters.forEach(filter => {

    filter.addEventListener('change', () => {

        const id = filter.id;

        faqs.forEach(faq => {

            if(faq.classList.contains(id)){
                faq.style.display = 'block';
            }

            else{
                faq.style.display = 'none';
            }

        });

    });

});
window.addEventListener('load', () => {

    faqs.forEach(faq => {

        if(faq.classList.contains('umum')){
            faq.style.display = 'block';
        }

        else{
            faq.style.display = 'none';
        }

    });

});
/* ================= FAQ OPEN CLOSE ================= */

const questions = document.querySelectorAll(".faq-question");

questions.forEach(q => {

    q.addEventListener("click", () => {

        const answer = q.nextElementSibling;

        if(answer.style.display === "block"){
            answer.style.display = "none";
        }

        else{
            answer.style.display = "block";
        }

    });

});

</script>
    <br>
    <br>
    <footer>
      <div class="footer-content">
        <div class="footer-column logo-col">
          <img
            src="../gambar/logo-rebon.png"
            alt="Rebon Adventure Logo"
            class="footer-logo-img"
          />
        </div>

        <div class="footer-column">
          <h4>KONTAK KAMI</h4>
          <div class="contact-item">
            <span class="icon">✉</span>
            <p>rebonadventure@gmail.com</p>
          </div>
          <div class="contact-item">
            <span class="icon">📞</span>
            <p>+62 812-3456-7890</p>
          </div>
          <div class="contact-item">
            <span class="icon">📍</span>
            <p>Jl. sukawera No. 15,<br />Cirebon, Indonesia</p>
          </div>
        </div>

        <div class="footer-column">
          <h4>LAYANAN KAMI</h4>
          <ul>
            <li>OPEN TRIP</li>
            <li>PRIVATE TRIP</li>
          </ul>
        </div>

        <div class="footer-column">
          <h4>INFORMASI</h4>
          <ul>
            <li>TENTANG KAMI</li>
            <li>TRIP TERSEDIA</li>
            <li>FAQ</li>
          </ul>

          <div class="social-section">
            <h4>FOLLOW US ON</h4>
            <div class="social-icons">
              <img src="../gambar/fb-icon.png" alt="FB" />
              <img src="../gambar/ig-icon.png" alt="IG" />
              <img src="../gambar/tt-icon.png" alt="TK" />
            </div>
          </div>
        </div>
      </div>

      <div class="copyright">© 2026 REBON ADVENTURE. ALL RIGHTS RESERVED.</div>
    </footer>
   </body>
<html>
  