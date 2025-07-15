<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$dir = __DIR__ . "/kontrak";
$mingguan = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$rentangMinggu = [];

$bulanIni = date('m');
$tahunIni = date('Y');
$hariDalamBulan = cal_days_in_month(CAL_GREGORIAN, $bulanIni, $tahunIni);

// Hitung rentang tanggal tiap minggu
for ($i = 1; $i <= 5; $i++) {
    $start = ($i - 1) * 7 + 1;
    $end = min($i * 7, $hariDalamBulan);
    $rentangMinggu[$i] = "$start - $end " . date('M');
}

if (is_dir($dir)) {
    $files = array_filter(scandir($dir), fn($f) => str_ends_with($f, '.json'));
    foreach ($files as $file) {
        $data = json_decode(file_get_contents($dir . '/' . $file), true);
        $tanggal = $data['tanggal'] ?? null;
        if ($tanggal) {
            $dateObj = date_create($tanggal);
            if ($dateObj && date_format($dateObj, 'Y') == $tahunIni && date_format($dateObj, 'm') == $bulanIni) {
                $day = (int)date_format($dateObj, 'j');
                $week = ceil($day / 7);
                $mingguan[$week] = ($mingguan[$week] ?? 0) + 1;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistem Kontrak</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #eef2f3, #d9e2ec);
            margin: 0;
            padding: 0;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar {
            background: #2c3e50;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar img {
            height: 50px;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            margin-left: 10px;
            background-color: #3498db;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar a:hover {
            background-color: #1abc9c;
        }

        .hero {
            background: linear-gradient(120deg, #2980b9, #6dd5fa);
            color: white;
            text-align: center;
            padding: 70px 30px 50px;
        }

        .hero img {
            width: 140px;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: 38px;
            margin-bottom: 15px;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 25px;
        }

        .hero a {
            background: white;
            color: #2980b9;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .hero a:hover {
            background: #f2faff;
            transform: scale(1.05);
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin: 50px auto 80px;
            max-width: 1100px;
            padding: 0 20px;
        }

        .feature-box {
            background: white;
            padding: 30px 20px;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            flex: 1 1 280px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-box:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }

        .feature-box img {
            width: 70px;
            margin-bottom: 18px;
        }

        .feature-box h3 {
            margin-bottom: 10px;
            color: #2c3e50;
            font-size: 20px;
        }

        .feature-box p {
            color: #555;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 28px;
            }

            .navbar {
                flex-direction: column;
                text-align: center;
            }

            .navbar a {
                display: inline-block;
                margin: 8px 5px;
            }
        }

        footer {
            background: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 20px 10px;
            font-size: 14px;
        }

        footer a {
            color: #1abc9c;
            text-decoration: none;
        }

        .chart-container {
            max-width: 800px;
            margin: 50px auto 80px;
            padding: 0 20px;
        }

        .chart-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <img src="logolti.png" alt="logo">
        <div>
            <a href="dashboard.php">🏠 Halaman Utama</a>
            <a href="kontrak.php">📝 Buat Kontrak</a>
            <a href="kontrak_cetak.php">🖨️ Cetak Kontrak</a>
            <a href="daftar.php">📂 Daftar Kontrak</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="hero">
        <img src="https://cdn-icons-png.flaticon.com/512/2869/2869816.png" alt="Document Icon">
        <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <p>Kelola kontrak Anda secara efisien dan profesional langsung dari browser Anda.</p>
        <a href="kontrak.php">🚀 Mulai Buat Kontrak</a>
    </div>

    <div class="features">
        <div class="feature-box">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135679.png" alt="Create">
            <h3>📝 Pembuatan Kontrak</h3>
            <p>Formulir lengkap untuk membuat kontrak dengan mudah dan cepat.</p>
        </div>
        <div class="feature-box">
            <img src="https://cdn-icons-png.flaticon.com/512/3050/3050525.png" alt="Archive">
            <h3>📂 Manajemen Arsip</h3>
            <p>Lihat dan kelola kontrak yang telah Anda buat sebelumnya.</p>
        </div>
        <div class="feature-box">
            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828911.png" alt="Edit">
            <h3>✏️ Edit dan Cetak</h3>
            <p>Edit kembali kontrak yang sudah dibuat dan simpan sebagai PDF.</p>
        </div>
    </div>

    <div class="chart-container">
        <h2>📊 Statistik Mingguan - Bulan <?= date('F Y') ?></h2>
        <canvas id="mingguChart"></canvas>
    </div>

    <footer>
        &copy; <?= date('Y') ?> Sistem Kontrak | Dibuat oleh <a href="#">Rian Ardiansyah</a>
    </footer>

    <script>
        const mingguData = <?= json_encode(array_values($mingguan)); ?>;
        const rentangLabel = <?= json_encode(array_values($rentangMinggu)); ?>;

        const ctx = document.getElementById('mingguChart').getContext('2d');
        const mingguChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: rentangLabel,
                datasets: [{
                    label: 'Jumlah Kontrak',
                    data: mingguData,
                    backgroundColor: '#3498db',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    </script>

</body>

</html>
