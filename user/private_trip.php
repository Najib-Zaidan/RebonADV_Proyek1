<?php
session_start();
require "fungsi.php";

if (isset($_POST['submit'])) {
    // 1. Pastikan user sudah login
    if (!isset($_SESSION['username'])) {
        echo "<script>
                alert('Silakan login terlebih dahulu untuk memesan Private Trip!');
                window.location.href = 'login_user.php';
              </script>";
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

        // 4. Insert data ke tabel private
        // (Berdasarkan screenshot, nama tabelnya adalah 'private')
        $query_insert = "INSERT INTO private_trip (id_akun, nama, no_hp, tujuan, tgl_berangkat, tgl_pulang, catatan, jumlah_peserta) 
                         VALUES ('$id_akun', '$nama', '$no_hp', '$tujuan', '$tgl_berangkat', '$tgl_pulang', '$catatan', '$jumlah_peserta')";

        if (kueri($query_insert)) {
            echo "<script>
                    alert('Berhasil! Private Trip Anda telah terpesan.');
                    window.location.href = 'form_member_private.php?jumlah=$jumlah_peserta'; // Refresh halaman agar form kosong lagi
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
  --purple-light: #4b00ff;
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
  background: linear-gradient(180deg, #4b00ff 0%, #7a2cff 100%);
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

    /* Image */
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

    /* Form */
    .card-form {
      flex: 0 0 46%;
    }

    .trip-form {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .trip-form input[type="text"],
    .trip-form input[type="tel"],
    .trip-form input[type="date"],
    .trip-form input[type="number"],
    .trip-form textarea {
      background: var(--lavender);
      border-radius: 10px;
      border: 1px solid var(--input-border);
      padding: 12px 14px;
      font-size: 15px;
      outline: none;
      box-shadow: 0 10px 25px rgba(97, 88, 88, 0.1);
      width: 100%;
    }

    .trip-form input:focus,
    .trip-form textarea:focus {
      border: 2px solid #6b3df5;
      background: #e6e0ff;
    }

    .trip-form textarea {
      resize: vertical;
    }

    /* Tanggal */
    .date-group {
      display: flex;
      gap: 10px;
      width: 100%;
    }

    .date-group input[type="date"] {
      flex: 1;
    }

    /* Placeholder palsu */
    .date-group input[type="date"]:invalid::before {
      content: attr(data-placeholder);
      color: #888;
    }

    .date-group input[type="date"]:focus::before,
    .date-group input[type="date"]:valid::before {
      content: "";
    }

    /* Hilangkan text default */
    .date-group input[type="date"]:invalid::-webkit-datetime-edit {
      color: transparent;
    }

    .date-group input[type="date"]::-webkit-datetime-edit {
      color: black;
    }

    /* Button */
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
      font-weight: 700;
      cursor: pointer;
      font-size: 16px;
      text-decoration: none;
    }

    .btn-whatsapp:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    /* Footer */
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
      padding: 0;
    }

    .footer-column ul li {
      margin-bottom: 8px;
      font-size: 14px;
      font-weight: 600;
    }

    .contact-item {
      margin-bottom: 10px;
      font-size: 14px;
      font-weight: 600;
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
          rows="5">
        </textarea>

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
      <img src="../gambar/logo-rebon.png" class="footer-logo-img" />
    </div>

    <div class="footer-column">
      <h4>KONTAK KAMI</h4>

      <div class="contact-item">
        ✉ rebonadventure@gmail.com
      </div>

      <div class="contact-item">
        📞 +62 812-3456-7890
      </div>

      <div class="contact-item">
        📍 Cirebon, Indonesia
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
    </div>

  </div>

  <div class="copyright">
    © 2026 REBON ADVENTURE
  </div>

</footer>

</body>
</html>