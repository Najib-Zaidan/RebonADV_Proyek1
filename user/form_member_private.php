<?php
session_start();
// Menggunakan file koneksi sesuai permintaanmu
require 'fungsi.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil id_private dari URL (GET)
    if (!isset($_GET['id_private'])) {
        die("ID Private Trip tidak ditemukan.");
    }
    
    $id_private = $_GET['id_private'];
    
    // Ambil data array dari form
    $daftar_nama   = $_POST['nama'];
    $daftar_usia   = $_POST['usia'];
    $daftar_alamat = $_POST['alamat'];
    $daftar_detail = $_POST['detail']; 

    $sukses = true;

    // Mulai transaksi menggunakan variabel $konek dari konek.php
    mysqli_begin_transaction($konek);

    try {
        foreach ($daftar_nama as $index => $nama) {
            // Sanitasi menggunakan variabel $konek
            $nama_clean   = mysqli_real_escape_string($konek, $nama);
            $usia_clean   = mysqli_real_escape_string($konek, $daftar_usia[$index]);
            $alamat_clean = mysqli_real_escape_string($konek, $daftar_alamat[$index]);
            $riwayat_clean = mysqli_real_escape_string($konek, $daftar_detail[$index]);

            // Menyiapkan string query
            $sql = "INSERT INTO peserta_private (id_private, nama, usia, alamat, riwayat) 
                    VALUES ('$id_private', '$nama_clean', '$usia_clean', '$alamat_clean', '$riwayat_clean')";
            
            // Menjalankan perintah melalui fungsi kueri() yang kamu buat
            if (!kueri($sql)) {
                $sukses = false;
                break;
            }
        }

        if ($sukses) {
            mysqli_commit($konek);
            echo "<script>
                    alert('Berhasil mendaftarkan semua peserta!');
                    window.location.href = 'profiluser.php'; 
                  </script>";
        } else {
            mysqli_rollback($konek);
            echo "Terjadi kesalahan saat menyimpan data.";
        }

    } catch (Exception $e) {
        mysqli_rollback($konek);
        echo "Error: " . $e->getMessage();
    }
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Formulir Pendaftaran Private Trip</title>

  <style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
}

body {
  background: #e7e2c8;
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

/* SECTION BACKGROUND */
.form-section {
  background: linear-gradient(135deg, #4e2bbf, #8b6cf6);
  padding: 80px 0;
  display: flex;
  justify-content: center;
}

/* CARD FORM */
.form-container {
  background: #e9e4c7;
  padding: 40px;
  border-radius: 15px;
  width: 700px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

/* JUDUL */
.form-container h2 {
  text-align: center;
  margin-bottom: 25px;
  font-size: 22px;
  font-weight: bold;
}
h4 {
  text-align: center;
}

/* FORM */
.form-container form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

/* INPUT & SELECT */
.form-container input,
.form-container select {
  padding: 12px;
  border-radius: 8px;
  border: none;
  background: #cfc7ea;
  font-size: 14px;
  outline: none;
}

/* FOCUS EFFECT */
.form-container input:focus,
.form-container select:focus {
  border: 2px solid #6b3df5;
  background: #e6e0ff;
}

/* TANGGAL LAHIR */

.date-wrapper {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.date-wrapper label {
  font-size: 14px;
  font-weight: 600;
}

.date-group {
  display: flex;
  gap: 10px;
}

.date-group p {
  font-size: 14px;
  min-width: 110px;
}

.date-group input {
  width: 100%;
}

/* BUTTON */
.form-container button {
  margin-top: 10px;
  padding: 12px;
  border: none;
  border-radius: 10px;
  background: #4e2bbf;
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}

/* HOVER BUTTON */
.form-container button:hover {
  background: #4000ff;
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

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="form_user_op.css" />
    <title>formulir registrasi user</title>
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
        <a href="profile.php"><?php if (isset($_SESSION['username'])): ?>
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

    <section class="form-section">
    <?php
// Tangkap jumlah peserta dari URL, default ke 1 jika tidak ada
$jumlah_peserta = isset($_GET['jumlah']) ? (int)$_GET['jumlah'] : 1;
?>

<div class="form-container">
    <h2>TAMBAH PESERTA PRIVATE TRIP</h2>
<br>
    <form id="formPendaftaran" action="" method="POST">
        <?php 
        $i = 1;
        while ($i <= $jumlah_peserta) : 
        ?>
            
                <h4>Peserta Ke-<?php echo $i; ?></h4>
                
                <input
                    type="text"
                    name="nama[]"
                    placeholder="Nama Lengkap Peserta <?php echo $i; ?> "
                    required
                />

                <div class="date-wrapper">
                    <div class="date-group">
                        <input type="number" name="usia[]" placeholder="Usia peserta  <?php echo $i; ?> " required/>
                    </div>
                </div>

                <input
                    type="text"
                    name="alamat[]"
                    placeholder="Alamat Lengkap Peserta <?php echo $i; ?> "
                    required
                />
                
                <input
                    type="text"
                    name="detail[]"
                    placeholder="Detail Penyakit Peserta <?php echo $i; ?> "
                />
            <br>
        <?php 
            $i++;
        endwhile; 
        ?>

        <button type="submit">Pesan sekarang</button>
    </form>
</div>
    </section>

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
  </body>
</html>

