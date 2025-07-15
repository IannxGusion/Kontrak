<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistem Kontrak</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f3f8;
            margin: 0;
            padding: 0;
            animation: fadeIn 1s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .navbar {
            background: #2c3e50;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            margin-left: 12px;
            background-color: #2980b9;
            padding: 8px 14px;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .navbar a:hover {
            background-color: #1c6ca1;
        }
        .hero {
            background: linear-gradient(to right, #3498db, #2980b9);
            color: white;
            text-align: center;
            padding: 60px 30px;
        }
        .hero img {
            width: 150px;
            margin-bottom: 20px;
        }
        .hero h1 {
            font-size: 42px;
            margin-bottom: 10px;
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
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }
        .hero a:hover {
            background: #dfeeff;
        }
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin: 60px auto;
            max-width: 1100px;
            padding: 0 20px;
        }
        .feature-box {
            background: white;
            padding: 30px 20px;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.05);
            flex: 1 1 280px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .feature-box img {
            width: 80px;
            margin-bottom: 15px;
        }
        .feature-box h3 {
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .feature-box p {
            color: #555;
        }
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 30px;
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

        .navbar a:hover {
            background-color: #1abc9c;
        }
    </style>
</head>
<body>

<div class="navbar">
    <img src="logolti.png" width="200px" height="auto" alt="logo">
    <div>
        <a href="dashboard.php">🏠 Halaman Utama</a>
        <a href="kontrak.php">📝 Buat Kontrak</a>
        <a href="cari_kontak.php">🔎 Cari kontrak</a>
        <a href="daftar.php">📂 Daftar Kontrak</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

<div class="hero">
    <img src="https://cdn-icons-png.flaticon.com/512/2869/2869816.png" alt="Document Icon">
    <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <p>Kelola kontrak Anda secara efisien dan profesional langsung dari browser Anda.</p>
    <a href="kontrak.php">Mulai Buat Kontrak</a>
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

</body>
</html>
