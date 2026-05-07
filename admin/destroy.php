<?php
$konek = mysqli_connect("127.0.0.1", "root", "");
$drop_db = "DROP DATABASE IF EXISTS rebon_adventure";
mysqli_query($konek, $drop_db);
session_start();
$_SESSION['beres'] = "Okeh";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Mode Import - Premium UI</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6b3df5;
            --primary-glow: rgba(107, 61, 245, 0.5);
            --secondary: #00d2ff;
            --secondary-glow: rgba(0, 210, 255, 0.5);
            --bg-dark: #0f0c29;
            --card-bg: rgba(255, 255, 255, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Latar Belakang Gelap Keren */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top left, #302b63, #24243e, #0f0c29);
            overflow: hidden;
            position: relative;
        }

        /* Ornamen Cahaya Berjalan di Latar Belakang */
        body::before, body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            filter: blur(100px);
            z-index: -1;
        }

        body::before {
            background: var(--primary);
            top: 10%;
            left: 10%;
            animation: move 10s infinite alternate;
        }

        body::after {
            background: var(--secondary);
            bottom: 10%;
            right: 10%;
            animation: move 12s infinite alternate-reverse;
        }

        @keyframes move {
            from { transform: translate(0, 0); }
            to { transform: translate(50px, 100px); }
        }

        .container {
            width: 100%;
            max-width: 800px;
            padding: 40px;
            text-align: center;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -1px;
            text-transform: uppercase;
        }

        p {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 50px;
            font-size: 1.1rem;
        }

        .button-wrapper {
            display: flex;
            gap: 25px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Styling Dasar Tombol - Glassmorphism */
        .import-btn {
            position: relative;
            padding: 20px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            color: white;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            min-width: 280px;
            background: var(--card-bg);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .btn-icon {
            font-size: 2rem;
            margin-bottom: 5px;
        }

        /* Tombol 1: Dengan Dummy (Tema Ungu) */
        .btn-dummy {
            background: linear-gradient(135deg, rgba(107, 61, 245, 0.2), rgba(107, 61, 245, 0.05));
        }

        .btn-dummy:hover {
            background: var(--primary);
            box-shadow: 0 0 30px var(--primary-glow);
            transform: scale(1.05) translateY(-5px);
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Tombol 2: Tanpa Dummy (Tema Biru Cyan) */
        .btn-real {
            background: linear-gradient(135deg, rgba(0, 210, 255, 0.2), rgba(0, 210, 255, 0.05));
        }

        .btn-real:hover {
            background: var(--secondary);
            box-shadow: 0 0 30px var(--secondary-glow);
            transform: scale(1.05) translateY(-5px);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .import-btn span:last-child {
            font-size: 0.85rem;
            opacity: 0.7;
            font-weight: 400;
        }

        /* Efek Kilap saat dihover */
        .import-btn::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .import-btn:hover::after {
            opacity: 1;
        }

        /* Responsif untuk Mobile / HP */
        @media (max-width: 600px) {
            h1 { font-size: 1.8rem; }
            .container { padding: 20px; }
            .import-btn { width: 100%; padding: 25px; }
            .button-wrapper { gap: 15px; }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Opsi Import Data</h1>
        <p>Silakan pilih metode konfigurasi database Anda</p>
        
        <div class="button-wrapper">
            <a href="import.php" class="import-btn btn-dummy">
                <div class="btn-icon">📁</div>
                <div>Import dengan Dummy</div>
                <span>Sertakan semua isi database (Direkomendasikan)</span>
            </a>

            <a href="no_dummy.php" class="import-btn btn-real">
                <div class="btn-icon">🚀</div>
                <div>Import tanpa Dummy</div>
                <span>Tabel dan Database saja, harap buka phpMyAdmin (tidak bisa login)</span>
            </a>
        </div>
    </div>

</body>
</html>
