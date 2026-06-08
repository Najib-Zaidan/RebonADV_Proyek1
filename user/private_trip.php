<?php
session_start();
require "fungsi.php";

if (isset($_POST['submit'])) {
    // 1. Pastikan user sudah login
    if (!isset($_SESSION['username'])) {
        echo "<script>showLoginModal();</script>";
        exit;
    }

    $username = $_SESSION['username'];

    // 2. Cari id_akun dari tabel akun untuk username yang sedang login & role = 'user'
    $query_akun = "SELECT id_akun FROM akun WHERE username = '$username' AND role = 'user' LIMIT 1";
    $result_akun = kueri($query_akun);

    if (mysqli_num_rows($result_akun) > 0) {
        $row = mysqli_fetch_assoc($result_akun);
        $id_akun = $row['id_akun'];

        // 3. Tangkap data dari form dan sanitasi
        $nama = mysqli_real_escape_string($konek, $_POST['nama']);
        $no_hp = mysqli_real_escape_string($konek, $_POST['nohp']);
        $tujuan = mysqli_real_escape_string($konek, $_POST['destinasi']);
        $tgl_berangkat = mysqli_real_escape_string($konek, $_POST['tgl_berangkat']);
        $tgl_pulang = mysqli_real_escape_string($konek, $_POST['tgl_pulang']);
        $catatan = mysqli_real_escape_string($konek, $_POST['catatan']);
        $jumlah_peserta = mysqli_real_escape_string($konek, $_POST['jumlah']);
        
        // Atur timezone dan set tanggal booking ke waktu saat ini
        date_default_timezone_set('Asia/Jakarta');
        $tgl_booking = date('Y-m-d H:i:s');

        
        $query_insert = "INSERT INTO private_trip (id_akun, nama, no_hp, tujuan, tgl_berangkat, tgl_pulang, catatan, jumlah_peserta) 
                         VALUES ('$id_akun', '$nama', '$no_hp', '$tujuan', '$tgl_berangkat', '$tgl_pulang', '$catatan', '$jumlah_peserta')";

        if (kueri($query_insert)) {
        $id_private_baru = mysqli_insert_id($konek);    
        echo "<script>
                    alert('Berhasil! Private Trip Anda telah terpesan.');
                    window.location.href = 'form_member_private.php?jumlah=$jumlah_peserta&id_private=$id_private_baru'; // Refresh halaman agar form kosong lagi
                  </script>";
        } else {
            echo "<script>alert('Gagal menyimpan data: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan: Akun tidak ditemukan atau bukan sebagai User.');</script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Private Trip</title>

  <style>
    :root {
  --purple-dark: #4b00ff;
  --purple-mid: #7a2cff;
  --purple-light: #763ef9;
  --cream: #ffff;
  --lavender: #eee3ff;
  --input-border: rgba(0, 0, 0, 0.08);
  --text-dark: #111;
  --radius: 12px;
  --max-width: 1150px;
  --page-padding: 40px;
  --shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
}

/* reset-ish */
* {
  box-sizing: border-box;
}
html,
body {
  height: 100%;
  margin: 0;
  font-family:
    "Poppins",
    system-ui,
    -apple-system,
    "Segoe UI",
    Roboto,
    "Helvetica Neue",
    Arial;
  color: var(--text-dark);

  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
}

body {
  background: #e7e2c8;
  background: linear-gradient(180deg, #8652ff 0%, #7a2cff 100%);
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

nav .active3 {
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
.hero-area {
      padding: 48px 20px;
    }

    .hero-inner {
      max-width: var(--max-width);
      margin: 0 auto;
      background: var(--cream);
      border-radius: 12px;
      padding: 28px;
      display: flex;
      gap: 28px;
      box-shadow: var(--shadow);
      align-items: flex-start;
    }

    /* --- Komponen Gambar (Kiri) --- */
    .card-image {
      flex: 0 0 48%;
    }

    .image-wrap {
      position: relative;
      border-radius: 14px;
      overflow: hidden;
    }

    .image-wrap img {
      width: 100%;
      height: 100%;
      display: block;
      object-fit: cover;
      aspect-ratio: 1/1;
      min-height: 420px;
    }

    .hero-title {
      position: absolute;
      left: 20px;
      bottom: 28px;
      margin: 0;
      font-size: 56px;
      line-height: 0.95;
      color: white;
      font-weight: 800;
      text-shadow: 0 6px 18px rgba(0, 0, 0, 0.45);
    }

    /* --- Komponen Form (Kanan) --- */
    .card-form {
      flex: 0 0 46%;
    }

    .trip-form {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    /* Penyelarasan tampilan huruf dan desain elemen Input/Textarea */
    .trip-form input[type="text"],
    .trip-form input[type="tel"],
    .trip-form input[type="date"],
    .trip-form input[type="number"],
    .trip-form textarea {
      background: var(--lavender);
      border-radius: 10px;
      border: 1px solid var(--input-border);
      padding: 12px 14px;
      outline: none;
      box-shadow: 0 10px 25px rgba(97, 88, 88, 0.1);
      width: 100%;
      
      /* Pengaturan huruf input disamakan */
      font-family: inherit;
      font-size: 15px;
      font-weight: 500;
      color: #333333;
    }

    /* Penyelarasan placeholder bawaan */
    .trip-form input::placeholder,
    .trip-form textarea::placeholder {
      color: #888888;
      font-family: inherit;
      font-size: 15px;
    }

    .trip-form input:focus,
    .trip-form textarea:focus {
      border: 2px solid #6b3df5;
      background: #e6e0ff;
      color: #000000;
    }

    .trip-form textarea {
      resize: vertical;
    }

    /* Input Grup Tanggal */
    .date-group {
      display: flex;
      gap: 10px;
      width: 100%;
    }

    .date-group input[type="date"] {
      flex: 1;
    }

    /* Placeholder palsu khusus untuk input date */
    .date-group input[type="date"]:invalid::before {
      content: attr(data-placeholder);
      color: #888888;
      font-family: inherit;
      font-size: 15px;
    }

    .date-group input[type="date"]:focus::before,
    .date-group input[type="date"]:valid::before {
      content: "";
    }

    /* Menyembunyikan teks bawaan browser sebelum tanggal dipilih */
    .date-group input[type="date"]:invalid::-webkit-datetime-edit {
      color: transparent;
    }

    /* Penyelarasan teks tanggal pilihan user */
    .date-group input[type="date"]::-webkit-datetime-edit {
      color: #333333;
      font-family: inherit;
      font-size: 15px;
    }

    /* Tombol Submit / WhatsApp */
    .cta-row {
      display: flex;
      justify-content: flex-end;
      margin-top: 10px;
    }

    .btn-whatsapp {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: linear-gradient(180deg, #4b00ff 0%, #7a2cff 100%);
      color: white;
      border: none;
      padding: 14px 22px;
      border-radius: 10px;
      cursor: pointer;
      text-decoration: none;
      
      /* Pengaturan Huruf Tombol */
      font-family: inherit;
      font-size: 16px;
      font-weight: 700;
      transition: all 0.2s ease;
    }

    .btn-whatsapp:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    /* ==========================================================================
       3. LAYOUT FOOTER YANG SUDAH DIRAPIKAN
       ========================================================================== */
    footer {
      background-color: #fdfae6;
      padding: 50px 10% 25px 10%;
      color: #333333;
      font-family: inherit;
      margin-top: 40px;
    }

    .footer-content {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      flex-wrap: wrap;
      gap: 40px;
      border-bottom: 1px solid #e2dfcc;
      padding-bottom: 40px;
    }

    .footer-column {
      flex: 1;
      min-width: 200px;
    }

    /* Kolom logo proporsional */
    .footer-column.logo-col {
      flex: 1.2;
      min-width: 220px;
    }

    .footer-logo-img {
      width: 100%;
      max-width: 220px;
      height: auto;
      display: block;
    }

    .footer-column h4 {
      font-size: 15px;
      margin-bottom: 20px;
      font-weight: 800;
      letter-spacing: 0.5px;
      color: #111111;
    }

    /* Navigasi List Link */
    .footer-column ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer-column ul li {
      margin-bottom: 12px;
    }

    .footer-column ul li a {
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      color: #555555;
      transition: color 0.2s ease;
    }

    .footer-column ul li a:hover {
      color: #6b3df5; /* Menggunakan aksen warna utama form */
    }

    /* Item Informasi Kontak */
    .contact-item {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin-bottom: 14px;
      font-size: 14px;
      font-weight: 600;
      color: #444444;
    }

    .contact-item .icon {
      font-size: 16px;
      color: #6b3df5;
    }

    .contact-item p {
      line-height: 1.4;
      margin: 0;
    }

    /* Bagian Sosial Media */
    .social-section {
      margin-top: 30px;
    }

    .social-section h4 {
      font-size: 13px;
      margin-bottom: 10px;
    }

    .social-icons {
      display: flex;
      gap: 12px;
      align-items: center;
    }

    .social-icons a {
      display: inline-block;
      text-decoration: none;
      transition: transform 0.2s ease;
    }

    .social-icons a:hover {
      transform: translateY(-3px);
    }

    .social-icons img {
      width: 28px;
      height: 28px;
      object-fit: contain;
      display: block;
    }

    /* Hak Cipta */
    .copyright {
      text-align: center;
      font-size: 12px;
      margin-top: 25px;
      font-weight: 700;
      color: #777777;
      letter-spacing: 0.5px;
    }
/* ================= FOOTER ================= */

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

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 10px;
  font-size: 14px;
  font-weight: 600;
}

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
  width: 220px; 
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

    
/* page background gradient (big purple area) */

  </style>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <link rel="stylesheet" href="private.css" />

  <style>
    /* ===== LOGIN MODAL POPUP ===== */
    #loginModal {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 9999;
      align-items: center;
      justify-content: center;
    }

    #loginModal.active {
      display: flex;
    }

    .modal-backdrop {
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.50);
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      animation: fadeIn 0.25s ease;
    }

    .modal-box {
      position: relative;
      z-index: 1;
      background: #ffffff;
      border-radius: 20px;
      padding: 40px 36px 32px;
      width: 100%;
      max-width: 400px;
      text-align: center;
      box-shadow: 0 20px 60px rgba(75, 0, 255, 0.25);
      animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .modal-icon {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background: linear-gradient(135deg, #eee3ff, #d4b8ff);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 32px;
    }

    .modal-box h2 {
      font-size: 22px;
      font-weight: 800;
      color: #111;
      margin: 0 0 10px;
    }

    .modal-box p {
      font-size: 14px;
      color: #666;
      line-height: 1.6;
      margin: 0 0 28px;
    }

    .modal-box p span {
      color: #6b3df5;
      font-weight: 700;
    }

    .modal-btn-group {
      display: flex;
      gap: 12px;
    }

    .modal-btn-login {
      flex: 1;
      padding: 13px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      font-family: inherit;
      font-size: 15px;
      font-weight: 700;
      background: linear-gradient(180deg, #4b00ff 0%, #7a2cff 100%);
      color: #fff;
      text-decoration: none;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .modal-btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(107, 61, 245, 0.4);
    }

    .modal-btn-close {
      flex: 1;
      padding: 13px;
      border-radius: 10px;
      border: 2px solid #e0d6ff;
      cursor: pointer;
      font-family: inherit;
      font-size: 15px;
      font-weight: 700;
      background: transparent;
      color: #6b3df5;
      transition: background 0.2s ease, border-color 0.2s ease;
    }

    .modal-btn-close:hover {
      background: #f3eeff;
      border-color: #6b3df5;
    }

    .modal-close-x {
      position: absolute;
      top: 14px;
      right: 18px;
      background: none;
      border: none;
      font-size: 20px;
      color: #aaa;
      cursor: pointer;
      line-height: 1;
      padding: 4px;
      transition: color 0.2s;
    }

    .modal-close-x:hover {
      color: #333;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to   { opacity: 1; }
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(30px) scale(0.95); }
      to   { opacity: 1; transform: translateY(0)    scale(1);    }
    }
    /* ===== END MODAL ===== */
  </style>
</head>

<body>

<header class="navbar">
  <div class="logo">
    <img src="../gambar/REBON LOGO GRADIENT presisi.png" alt="Rebon Adventure" />
  </div>

  <nav>
        <a href="home1.php" class="active1">Home</a>
        <a href="open_trip.php" class="active2">Open</a>
        <a href="private_trip.php" class="active3">Private</a>
        <a href="tentang_kami.php" class="active4">Tentang Kami</a>
        <a href="profiluser.php"><?php if (isset($_SESSION['username'])): ?>
            <span style="color:blue; margin-right:10px;">
              👤 <?php echo $_SESSION['username']; ?>
            </span>
        </a>
            <a href="logout_user.php">
              <button class="active5" onclick="return confirm('Yakin ingin logout?')">Logout</button>
            </a>

        <?php else: ?>
            <a href="login_user.php">
              <button class="active5">Masuk</button>
            </a>
        <?php endif; ?>
      </nav>
</header>

<main class="hero-area">
  <div class="hero-inner">

    <div class="card-image">
      <div class="image-wrap">
        <img src="../gambar/123.jpg" alt="Destinasi"/>
        <h1 class="hero-title">Rencanakan<br>Trip Anda</h1>
      </div>
    </div>

    <aside class="card-form">
      <form action="" method="post" class="trip-form">

        <input type="text" name="nama" placeholder="Nama Lengkap" autocomplete="off" required />

        <input type="text" name="nohp" placeholder="Nomor Telepon" autocomplete="off" required />

        <input type="text" name="destinasi" placeholder="Lokasi Destinasi" autocomplete="off" required />

        <!-- INPUT TANGGAL RAPI -->
        <div class="date-group">

          <input 
            type="date"
            name="tgl_berangkat"
            data-placeholder="Tanggal Berangkat"
            required
          >

          <input 
            type="date"
            name="tgl_pulang"
            data-placeholder="Tanggal Pulang"
            required
          >

        </div>

        <textarea 
          name="catatan" 
          placeholder="Catatan Tambahan" 
          rows="5"></textarea>

        <input 
          type="number" 
          name="jumlah" 
          placeholder="Jumlah Peserta" 
          min="1" 
          required 
        />

        <div class="cta-row">
          <button type="submit" name="submit" class="btn-whatsapp">
            Pesan Sekarang
          </button>
        </div>

      </form>
    </aside>

  </div>
</main>

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
              <a href="https://www.instagram.com/rebon_adv?igsh=MW4xcDc1YTJhMzRpMw==" target="_blank" rel="noopener noreferrer">
                <img src="../gambar/ig-icon.png" alt="Instagram" />
              </a>
              <a href="https://www.tiktok.com/@rebon.adventure?is_from_webapp=1&sender_device=pc" target="_blank" rel="noopener noreferrer">
                <img src="../gambar/tt-icon.png" alt="Tiktok" />
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="copyright">© 2026 REBON ADVENTURE. ALL RIGHTS RESERVED.</div>
    </footer>

<!-- ===== LOGIN MODAL ===== -->
<div id="loginModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-backdrop" onclick="closeLoginModal()"></div>
  <div class="modal-box">
    <button class="modal-close-x" onclick="closeLoginModal()" aria-label="Tutup">&#x2715;</button>
    <div class="modal-icon">🔒</div>
    <h2 id="modalTitle">Login Diperlukan</h2>
    <p>Kamu harus <span>login</span> terlebih dahulu sebelum memesan <span>Private Trip</span>. Yuk masuk ke akunmu!</p>
    <div class="modal-btn-group">
      <a href="login_user.php" class="modal-btn-login">Masuk Sekarang</a>
      <button class="modal-btn-close" onclick="closeLoginModal()">Nanti Saja</button>
    </div>
  </div>
</div>
<!-- ===== END MODAL ===== -->

<script>
  // Cek status login dari PHP
  const isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;

  // Fungsi tampilkan modal
  function showLoginModal() {
    const modal = document.getElementById('loginModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  // Fungsi tutup modal
  function closeLoginModal() {
    const modal = document.getElementById('loginModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }

  // Tutup modal dengan tombol Escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLoginModal();
  });

  // Intercept submit form — cek login di sisi klien dulu (lebih cepat)
  document.querySelector('.trip-form').addEventListener('submit', function(e) {
    if (!isLoggedIn) {
      e.preventDefault();
      showLoginModal();
    }
  });
</script>

</body>
</html>