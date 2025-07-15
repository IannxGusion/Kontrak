<?php
$dir = __DIR__ . "/kontrak/";

if (!isset($_GET["file"])) {
    echo "File kontrak tidak ditemukan.";
    exit;
}

$filename = basename($_GET["file"]);
$filepath = $dir . $filename;

if (!file_exists($filepath)) {
    echo "File kontrak tidak ditemukan.";
    exit;
}

$data = json_decode(file_get_contents($filepath), true);

function rupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function tampilPihak($kode, $nama)
{
    return htmlspecialchars(trim($kode . ' - ' . $nama));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Kontrak</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 40px;
            font-size: 14px;
            line-height: 1.5;
        }

        h2,
        h3 {
            text-align: center;
            margin: 0;
        }

        h2 {
            font-size: 20px;
            margin-bottom: 4px;
        }

        h3 {
            font-size: 14px;
            margin-bottom: 20px;
            color: #555;
        }

        .info,
        .section,
        .footer {
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 110px;
        }

        .box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 6px;
            border-radius: 5px;
        }

        .nilai-box {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .nilai-box div {
            width: 30%;
        }

        .ttd {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .ttd div {
            width: 45%;
            text-align: center;
        }

        .ttd .nama {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 4px;
            display: inline-block;
            width: 80%;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 200px;
            height: auto;
        }
    </style>
</head>

<body onload="window.print()">
    <img src="logolti.png" alt="Logo LTI" class="logo">

    <h2>KONTRAK KERJA</h2>
    <h3><?= htmlspecialchars($data['no_kontrak']) ?></h3>

    <div class="info">
        <div><span class="label">Judul</span>: <?= htmlspecialchars($data['judul']) ?></div>
        <div><span class="label">Tanggal</span>: <?= date("d F Y", strtotime($data['tanggal'])) ?></div>
    </div>

    <!-- Pihak 1 -->
    <div class="section">
        <strong>1. Pihak 1</strong>
        <div class="box"><?= tampilPihak($data['pos1'] ?? '-', $data['nama_pos1'] ?? '-') ?></div>
    </div>

    <!-- Pihak 2 -->
    <?php if (!empty($data['pos2']) || !empty($data['nama_pos2'])): ?>
        <div class="section">
            <strong>2. Pihak 2</strong>
            <div class="box"><?= tampilPihak($data['pos2'] ?? '-', $data['nama_pos2'] ?? '-') ?></div>
        </div>
    <?php endif; ?>

    <!-- Pihak 3 -->
    <?php if (!empty($data['pos3']) || !empty($data['nama_pos3'])): ?>
        <div class="section">
            <strong>3. Pihak 3</strong>
            <div class="box"><?= tampilPihak($data['pos3'] ?? '-', $data['nama_pos3'] ?? '-') ?></div>
        </div>
    <?php endif; ?>

    <!-- Deskripsi -->
    <?php if (!empty($data['deskripsi'])): ?>
        <div class="section">
            <strong>Deskripsi Kontrak:</strong>
            <div class="box"><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></div>
        </div>
    <?php endif; ?>

    <!-- Nilai, Durasi, Status -->
    <div class="nilai-box">
        <div>
            <strong>Nilai:</strong><br>
            <?= rupiah($data['nilai'] ?? 0) ?>
        </div>
        <div>
            <strong>Durasi:</strong><br>
            <?= htmlspecialchars($data['durasi'] ?? '-') ?>
        </div>
        <div>
            <strong>Status:</strong><br>
            <?= htmlspecialchars($data['status'] ?? '-') ?>
        </div>
    </div>

    <!-- Tanda Tangan -->
    <div class="ttd">
        <div>
            Pihak Pertama<br><br><br>
            <div class="nama">( Tanda Tangan )</div>
        </div>
        <div>
            Pihak Kedua<br><br><br>
            <div class="nama">( Tanda Tangan )</div>
        </div>
    </div>
</body>

</html>