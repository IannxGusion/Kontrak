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
function rupiah($angka)
{
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}
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
            margin: 50px;
            background: #fff;
            color: #000;
        }

        h1 {
            text-align: center;
            font-weight: 600;
        }

        .nomor-kontrak {
            text-align: center;
            font-size: 14px;
            margin-bottom: 40px;
            font-weight: 600;
        }

        .info {
            margin-bottom: 30px;
        }

        .info strong {
            width: 80px;
            display: inline-block;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h3 {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .box {
            background: #fff;
            border: 1px solid #ccc;
            padding: 12px;
            border-radius: 4px;
        }

        .nilai-box {
            display: flex;
            gap: 40px;
            margin-top: 30px;
            margin-bottom: 50px;
        }

        .nilai-box div {
            flex: 1;
        }

        .ttd {
            display: flex;
            justify-content: space-between;
            margin-top: 70px;
        }

        .ttd .box {
            width: 40%;
            text-align: center;
        }

        .ttd .line {
            border-top: 1px solid #000;
            margin-top: 70px;
            padding-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 60px;
        }

        .btn-back {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .btn-back:hover {
            background-color: #2980b9;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 300px;
            height: auto;
        }
    </style>
</head>

<body>
    <img src="logolti.png" alt="Logo LTI" class="logo">

    <h1>KONTRAK KERJA</h1>
    <div class="nomor-kontrak"><?= htmlspecialchars($data['no_kontrak'] ?? '-') ?></div>

    <div class="info">
        <p><strong>Judul</strong>: <?= htmlspecialchars($data['judul'] ?? '-') ?></p>
        <p><strong>Tanggal</strong>: <?= isset($data['tanggal']) ? htmlspecialchars(date("d F Y", strtotime($data['tanggal']))) : '-' ?></p>
    </div>

    <div class="section">
        <h3>1. Pihak 1</h3>
        <div class="box">
            <?= htmlspecialchars(($data['pos1'] ?? '-') . ' - ' . ($data['nama_pos1'] ?? '-')) ?>
        </div>
    </div>

    <div class="section">
        <h3>2. Pihak 2</h3>
        <div class="box">
            <?= htmlspecialchars(($data['pos2'] ?? '-') . ' - ' . ($data['nama_pos2'] ?? '-')) ?>
        </div>
    </div>

    <div class="section">
        <h3>3. Pihak 3</h3>
        <div class="box">
            <?= htmlspecialchars(($data['pos3'] ?? '-') . ' - ' . ($data['nama_pos3'] ?? '-')) ?>
        </div>
    </div>

    <div class="section">
        <h3>Deskripsi Kontrak</h3>
        <div class="box">
            <?= nl2br(htmlspecialchars($data['deskripsi'] ?? '-')) ?>
        </div>
    </div>



    <div class="nilai-box">
        <div><strong>Nilai:</strong><br><?= rupiah($data['nilai'] ?? 0) ?></div>
        <div><strong>Durasi:</strong><br><?= htmlspecialchars($data['durasi'] ?? '1 tahun') ?></div>
        <div><strong>Status:</strong><br><?= htmlspecialchars($data['status'] ?? '-') ?></div>
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

    <div class="footer">
        <a class="btn-back" href="kontrak_cetak.php?judul=<?= urlencode($judulSebelumnya) ?>">&larr; Kembali</a>
    </div>

</body>

</html>