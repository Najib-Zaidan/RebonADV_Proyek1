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

.faq {
  background: #fff;
  border-radius: 10px;
  margin-bottom: 10px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.15);
  width: auto;
  margin: 15px;
  padding: 3px;
  transition: all 0.3s ease;
}

.faq-question {
  padding: 15px;
  cursor: pointer;
  font-weight: bold;
}

.faq-answer {
  padding: 0 15px 15px;
  display: none;
  color: #555;
}
.faq:hover{
    background-color: #fff;
    transform: translateY(-20px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
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

      <div class="faq">
        <div class="faq-question">Apa itu website ini?</div>
        <div class="faq-answer">Website ini digunakan untuk belajar membuat FAQ sederhana.</div>
      </div>

      <div class="faq">
        <div class="faq-question">Bagaimana cara mendaftar?</div>
        <div class="faq-answer">Klik tombol daftar lalu isi data yang diminta.</div>
      </div>

      <div class="faq">
        <div class="faq-question">Apakah gratis?</div>
        <div class="faq-answer">Ya, layanan ini bisa digunakan secara gratis.</div>
      </div>

      <div class="faq">
        <div class="faq-question">Cara boking private?</div>
        <div class="faq-answer">Website ini digunakan untuk belajar membuat FAQ sederhana.</div>
      </div>

      <div class="faq">
        <div class="faq-question">Cara membuat user baru?</div>
        <div class="faq-answer">Website ini digunakan untuk belajar membuat FAQ sederhana.</div>
      </div>

      <div class="faq">
        <div class="faq-question">cara logout?</div>
        <div class="faq-answer">Website ini digunakan untuk belajar membuat FAQ sederhana.</div>
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
    const questions = document.querySelectorAll(".faq-question");

    questions.forEach(q => {
      q.addEventListener("click", () => {
        const answer = q.nextElementSibling;

        answer.style.display =
          answer.style.display === "block" ? "none" : "block";
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
  