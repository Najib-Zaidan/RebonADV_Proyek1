<?php 
session_start();
if(!isset($_SESSION['beres'])){
    header("location: ../admin/destroy.php");
}
session_unset();
$_SESSION = array();
session_destroy();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Konfigurasi - Processing</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6b3df5;
            --secondary: #00d2ff;
            --accent: #00ff88;
            --bg-dark: #0a081a;
            --glass: rgba(255, 255, 255, 0.04);
            --border: rgba(255, 255, 255, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Ambient Glow di Background */
        .bg-glow {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: 
                radial-gradient(circle at 20% 30%, rgba(107, 61, 245, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(0, 210, 255, 0.15) 0%, transparent 40%);
        }

        /* Kotak Panel Utama - Glassmorphism */
        .container {
            width: 90%;
            max-width: 600px;
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            text-align: center;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 30px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
        }

        /* Styling Loading Section */
        .loading-section {
            margin-bottom: 25px;
            text-align: left;
        }

        .label-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.8);
        }

        .percent-text {
            font-weight: 600;
            color: var(--secondary);
            transition: color 0.3s;
        }

        .progress-container {
            height: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            box-shadow: 0 0 15px var(--primary);
            border-radius: 10px;
        }

        /* Styling Bagian Redirect & Countdown */
        #redirect-section {
            display: none;
            margin-top: 35px;
            padding-top: 30px;
            border-top: 1px solid var(--border);
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .countdown-circle {
            font-size: 4rem;
            font-weight: 800;
            color: var(--secondary);
            text-shadow: 0 0 25px rgba(0, 210, 255, 0.6);
            margin: 10px 0;
            line-height: 1;
        }

        .status-text {
            font-size: 0.9rem;
            color: var(--accent);
            margin-bottom: 25px;
        }

        /* Tombol Pindah Manual */
        .redirect-btn {
            padding: 16px 40px;
            background: linear-gradient(135deg, var(--primary), #481fd1);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 25px rgba(107, 61, 245, 0.4);
        }

        .redirect-btn:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 35px rgba(107, 61, 245, 0.6);
            background: linear-gradient(135deg, #7b52f6, var(--primary));
        }

        /* Responsif untuk Mobile */
        @media (max-width: 480px) {
            .container { padding: 30px 20px; }
            h1 { font-size: 1.5rem; }
            .countdown-circle { font-size: 3rem; }
        }
    </style>
</head>
<body>

    <div class="bg-glow"></div>

    <div class="container">
        <h1>Database Setup</h1>
        <h1>Rebon Adventure Project</h1>

        <div class="loading-section">
            <div class="label-group">
                <span>Deleting the database...</span>
                <span class="percent-text" id="percent-1">0%</span>
            </div>
            <div class="progress-container">
                <div class="progress-bar" id="bar-1"></div>
            </div>
        </div>

        <div class="loading-section">
            <div class="label-group">
                <span>Importing new database...</span>
                <span class="percent-text" id="percent-2">0%</span>
            </div>
            <div class="progress-container">
                <div class="progress-bar" id="bar-2"></div>
            </div>
        </div>

        <div class="loading-section">
            <div class="label-group">
                <span>Preparing all for you...</span>
                <span class="percent-text" id="percent-3">0%</span>
            </div>
            <div class="progress-container">
                <div class="progress-bar" id="bar-3"></div>
            </div>
        </div>

        <div id="redirect-section">
            <p style="color: rgba(255,255,255,0.7); font-size: 0.95rem;">Anda akan Dialihkan dalam</p>
            <div class="countdown-circle" id="timer">7</div>
            <p class="status-text">Semua database berhasil di impor dengan josjiss (no error) ✓</p>
            
            <a href="../admin/index.php" class="redirect-btn">
                Pindah Sekarang 
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>

    <script>
        // HALAMAN TUJUAN (Ganti sesuai kebutuhan, misal 'dashboard.php')
        const REDIRECT_URL = '../admin/index.php'; 

        // Fungsi utama yang menjalankan urutan proses
        async function startLoading() {
            // Animasi bar urut 1 per 1 (masing-masing 2000 ms = 2 detik)
            await animateProgress('bar-1', 'percent-1', 2000);
            await animateProgress('bar-2', 'percent-2', 2000);
            await animateProgress('bar-3', 'percent-3', 2000);

            // Munculkan bagian countdown
            document.getElementById('redirect-section').style.display = 'block';
            
            // Mulai countdown 7 detik
            startCountdown(7);
        }

        // Fungsi animasi Bar (menggunakan Promise agar bisa di-await)
        function animateProgress(barId, percentId, duration) {
            return new Promise(resolve => {
                let bar = document.getElementById(barId);
                let percentText = document.getElementById(percentId);
                let start = null;

                function step(timestamp) {
                    if (!start) start = timestamp;
                    let progress = timestamp - start;
                    
                    // Kalkulasi presentase 0 ke 100
                    let percentage = Math.min((progress / duration) * 100, 100);
                    
                    bar.style.width = percentage + '%';
                    percentText.innerText = Math.floor(percentage) + '%';

                    if (progress < duration) {
                        window.requestAnimationFrame(step);
                    } else {
                        // Jika selesai
                        percentText.innerText = 'Completed ✓';
                        percentText.style.color = 'var(--accent)';
                        resolve();
                    }
                }
                window.requestAnimationFrame(step);
            });
        }

        // Fungsi Countdown Timer
        function startCountdown(seconds) {
            let counter = seconds;
            let timerDisplay = document.getElementById('timer');
            
            let interval = setInterval(() => {
                counter--;
                timerDisplay.innerText = counter;

                if (counter <= 0) {
                    clearInterval(interval);
                    // Pindah halaman otomatis saat angka menyentuh 0
                    window.location.href = REDIRECT_URL;
                }
            }, 1000);
        }

        // Jalankan script saat body selesai di-load
        window.onload = startLoading;
    </script>
</body>
</html>
