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

function rupiah($angka)
{
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

function terbilang($angka)
{
    $angka = (int)$angka;
    $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

    if ($angka < 12) {
        return $baca[$angka];
    } elseif ($angka < 20) {
        return $baca[$angka - 10] . " Belas";
    } elseif ($angka < 100) {
        return terbilang(intval($angka / 10)) . " Puluh " . terbilang($angka % 10);
    } elseif ($angka < 200) {
        return "Seratus " . terbilang($angka - 100);
    } elseif ($angka < 1000) {
        return terbilang(intval($angka / 100)) . " Ratus " . terbilang($angka % 100);
    } elseif ($angka < 2000) {
        return "Seribu " . terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        return terbilang(intval($angka / 1000)) . " Ribu " . terbilang($angka % 1000);
    } elseif ($angka < 1000000000) {
        return terbilang(intval($angka / 1000000)) . " Juta " . terbilang($angka % 1000000);
    } elseif ($angka < 1000000000000) {
        return terbilang(intval($angka / 1000000000)) . " Miliar " . terbilang($angka % 1000000000);
    } else {
        return "Angka terlalu besar";
    }
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

    <!-- Vertikal: Nilai, Durasi, Status -->
    <div class="section">
        <h3>Nilai</h3>
        <div class="box">
            <?php
            $nilai = (int) preg_replace('/[^\d]/', '', $data['nilai'] ?? 0);
            ?>
            <?= rupiah($nilai) ?><br>
            <small><em>(<?= ucwords(trim(terbilang($nilai))) ?> Rupiah)</em></small>
        </div>
    </div>

    <div class="section">
        <h3>Durasi</h3>
        <div class="box"><?= htmlspecialchars($data['durasi'] ?? '1 tahun') ?></div>
    </div>

    <div class="section">
        <h3>Status</h3>
        <div class="box"><?= htmlspecialchars($data['status'] ?? '-') ?></div>
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
