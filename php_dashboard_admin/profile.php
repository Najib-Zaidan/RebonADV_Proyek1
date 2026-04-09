<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("Location: ../login_user.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        /* Global Styles */
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

nav .active6 {
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

        /* Hero & Content Section */
        .hero {
            background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
            min-height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px 20px;
        }

        .card-container {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(5px);
            width: 100%;
            max-width: 700px;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* Profile View */
        .profile-view h2 { text-transform: uppercase; margin-bottom: 5px; font-size: 1.2rem; }
        .profile-view h1 { font-size: 2.5rem; margin-bottom: 30px; }
        
        .btn {
            display: block;
            width: 200px;
            margin: 10px auto;
            padding: 12px;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-edit { background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%); }
        .btn-logout { background: #ff4b2b; }
        .btn-delete { background: #ff416c; }
        .btn-save { background: #2ecc71; width: 150px; }

        /* Form Edit Styles */
        .form-group {
            text-align: left;
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #e0e0e0;
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
        <a href="profile.php" class="active6" ><?php if (isset($_SESSION['username'])): ?>
            <!-- JIKA SUDAH LOGIN -->
            <span style="color:blue; margin-right:10px;">
              👤 <?php echo $_SESSION['username']; ?>
            </span>
        </a>
            <a href="logout_user.php">
              <button class="active5">Logout</button>
            </a>

        <?php else: ?>
            <!-- JIKA BELUM LOGIN -->
            <a href="../login_user.html">
              <button class="active5">Masuk</button>
            </a>
        <?php endif; ?>
      </nav>
    </header>

    <main class="hero">
        <div class="card-container">
            
            <div class="profile-view">
                <h2>Profile</h2>
                <h1>Welkombek, <br>
                
                <?php if (isset($_SESSION['username'])): ?>
            <!-- JIKA SUDAH LOGIN -->
            <span style="color:blue; margin-right:10px;">
              👤 <?php echo $_SESSION['username']; ?>
            </span>
                <?php endif; ?>

                <a href="#" class="btn btn-edit">Ubah Profil</a>
                <a href="logout_user.php" class="btn btn-logout">Log Out</a>
                <a href="delete_user.php" class="btn btn-delete" onclick="return confirm('Yakin hapus akun?')">Hapus Akun</a>
            </div>

            <!-- <div class="profile-edit" style="display:none;">
                <h2>EDIT PROFILE</h2>
                <form action="update_profile.php" method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="Najib Jeruk">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password lama</label>
                        <input type="password" name="old_pass">
                    </div>
                    <div class="form-group">
                        <label>Password baru</label>
                        <input type="password" name="new_pass">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password baru</label>
                        <input type="password" name="confirm_new_pass">
                    </div>
                    <button type="submit" class="btn btn-save">Simpan</button>
                </form>
            </div> -->
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
              <img src="../gambar/ig-icon.png" alt="IG" />
              <img src="../gambar/tt-icon.png" alt="TK" />
            </div>
          </div>
        </div>
      </div>

      <div class="copyright">© 2026 REBON ADVENTURE. ALL RIGHTS RESERVED.</div>
    </footer>

</body>
</html>