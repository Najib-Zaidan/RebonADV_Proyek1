<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rebon Adventure - Profile</title>
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #fdfdf0;
        }

        /* Navbar */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 8%;
            background: rgba(255, 255, 255, 0.9);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo img { height: 45px; }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 14px;
        }

        .active {
            color: #6a5acd;
            border-bottom: 2px solid #6a5acd;
            padding-bottom: 5px;
        }

        .profile-icon {
            background-color: #483d8b;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }

        /* Hero Section */
        .hero {
            background: url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
            min-height: 85vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            width: 100%;
            max-width: 650px;
            border-radius: 25px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        /* View Profile State */
        .view-mode h2 { font-size: 1.2rem; margin-bottom: 10px; }
        .view-mode h1 { font-size: 2.2rem; margin-bottom: 25px; text-transform: lowercase; }

        /* Edit Form State */
        .edit-mode h2 { margin-bottom: 20px; font-weight: bold; }
        .form-group { text-align: left; margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600; }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #d9d9d9;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            width: 200px;
            padding: 12px;
            margin: 8px 0;
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-blue { background: linear-gradient(to right, #4facfe, #00f2fe); }
        .btn-red { background: #ff4b2b; }
        .btn-soft-red { background: #ff6b6b; }
        .btn-green { background: #00ce4f; width: 140px; margin-top: 15px; }
        .btn:hover { opacity: 0.8; }

        /* Footer */
        footer {
            background-color: #fdfdf0;
            padding: 60px 8% 20px;
            border-top: 1px solid #eee;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-logo img { width: 220px; }
        .footer-col h4 { margin-bottom: 20px; font-size: 16px; font-weight: bold; }
        .footer-col p, .footer-col ul { font-size: 14px; line-height: 1.8; list-style: none; }
        .social-icons { display: flex; gap: 10px; margin-top: 15px; }

        .bottom-bar {
            background-color: #2a1b5d;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <img src="logo.png" alt="Rebon Adventure">
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="open.php" class="active">Open</a></li>
                <li><a href="private.php">Private</a></li>
                <li><a href="about.php">Tentang Kami</a></li>
            </ul>
        </nav>
        <div class="profile-icon">👤</div>
    </header>

    <main class="hero">
        <div class="card">
            
            <?php if(!isset($_GET['page']) || $_GET['page'] != 'edit'): ?>
            <div class="view-mode">
                <h2>PROFILE</h2>
                <h1>WELKOMBEK,<br><strong>najib jerug</strong></h1>
                <a href="?page=edit" class="btn btn-blue">Ubah Profil</a><br>
                <a href="logout.php" class="btn btn-red">Log Out</a><br>
                <a href="hapus.php" class="btn btn-soft-red">Hapus Akun</a>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['page']) && $_GET['page'] == 'edit'): ?>
            <div class="edit-mode">
                <h2>EDIT PROFILE</h2>
                <form action="update_process.php" method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="Najib Jeruk">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password lama</label>
                        <input type="password" name="old_password">
                    </div>
                    <div class="form-group">
                        <label>Password baru</label>
                        <input type="password" name="new_password">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password baru</label>
                        <input type="password" name="confirm_password">
                    </div>
                    <button type="submit" class="btn btn-green">Simpan</button>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </main>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <img src="footer-logo.png" alt="Rebon Adventure" style="width: 100%;">
            </div>
            <div class="footer-col">
                <h4>KONTAK KAMI</h4>
                <p>📧 rebonadventure@gmail.com</p>
                <p>📞 +62 812-3456-7890</p>
                <p>📍 Jl. sukawera No. 15, Cirebon, Indonesia</p>
            </div>
            <div class="footer-col">
                <h4>LAYANAN KAMI</h4>
                <ul>
                    <li>Open Trip</li>
                    <li>Private Trip</li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>INFORMASI</h4>
                <ul>
                    <li>Tentang Kami</li>
                    <li>Trip Tersedia</li>
                    <li>FAQ</li>
                </ul>
                <div class="social-icons">
                    <span>FB</span> <span>IG</span> <span>TK</span>
                </div>
            </div>
        </div>
    </footer>

    <div class="bottom-bar">
        © 2026 REBON ADVENTURE. ALL RIGHTS RESERVED.
    </div>

</body>
</html>