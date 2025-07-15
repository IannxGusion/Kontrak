<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['file'])) {
    echo "File tidak ditemukan.";
    exit;
}

$dir = __DIR__ . "/kontrak/";
$filename = basename($_GET['file']);
$filepath = $dir . $filename;

if (!file_exists($filepath)) {
    echo "File tidak ditemukan.";
    exit;
}

$data = json_decode(file_get_contents($filepath), true);
$judulSebelumnya = $_GET['judul'] ?? '';

// Fungsi format rupiah
function rupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}

// Coba deteksi apakah pos3 adalah angka numerik
$nilaiFormatted = is_numeric($data['pos3']) ? rupiah($data['pos3']) : nl2br(htmlspecialchars($data['pos3']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Kontrak</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 60px;
            color: #000;
            background: #f8f9fa;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .nomor-kontrak {
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .info {
            margin-bottom: 30px;
        }
        .info strong {
            display: inline-block;
            width: 100px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            margin-bottom: 8px;
            color: #34495e;
        }
        .nilai {
            display: flex;
            gap: 30px;
            margin: 40px 0;
        }
        .nilai div {
            flex: 1;
            background: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
        }
        .ttd {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        .ttd .box {
            width: 40%;
            text-align: center;
        }
        .ttd .box .line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
        .kembali {
            display: inline-block;
            margin-top: 50px;
            background: #3498db;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .kembali:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <h1>KONTRAK KERJA</h1>
    <div class="nomor-kontrak"><?= htmlspecialchars($data['no_kontrak']) ?></div>

    <div class="info">
        <p><strong>Judul</strong>: <?= htmlspecialchars($data['judul']) ?></p>
        <p><strong>Tanggal</strong>: <?= htmlspecialchars($data['tanggal']) ?></p>
    </div>

    <div class="section">
        <h3>1. Para Pihak</h3>
        <p><?= nl2br(htmlspecialchars($data['pos1'])) ?></p>
    </div>

    <div class="section">
        <h3>2. Kerja Sama</h3>
        <p><?= nl2br(htmlspecialchars($data['pos2'])) ?></p>
    </div>

    <div class="nilai">
        <div><strong>Nilai:</strong><br><?= $nilaiFormatted ?></div>
        <div><strong>Durasi:</strong><br>1 tahun</div>
        <div><strong>Status:</strong><br>Aktif</div>
    </div>

    <div class="ttd">
        <div class="box">
            <div>Pihak Pertama</div>
            <div class="line">( Tanda Tangan )</div>
        </div>
        <div class="box">
            <div>Pihak Kedua</div>
            <div class="line">( Tanda Tangan )</div>
        </div>
    </div>

    <a class="kembali" href="cari_kontak.php?judul=<?= urlencode($judulSebelumnya) ?>">&larr; Kembali ke Hasil Pencarian</a>
</body>
</html>
