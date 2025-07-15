<?php
$dir = __DIR__ . "/kontrak/";

// Validasi file
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

// Ambil data JSON
$data = json_decode(file_get_contents($filepath), true);

// Ambil nilai kontrak dengan validasi angka
$nilai = isset($data['nilai']) && is_numeric($data['nilai']) ? (float)$data['nilai'] : 0;
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
        .poin,
        .footer {
            margin-bottom: 12px;
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
        }

        .deskripsi {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }

        .footer td {
            padding: 5px 10px;
            vertical-align: top;
        }

        .ttd {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }

        .ttd div {
            width: 45%;
            text-align: center;
        }

        .ttd .nama {
            margin-top: 60px;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 4px;
            width: 80%;
        }
    </style>
</head>

<body onload="window.print()">
    <h2>KONTRAK KERJA</h2>
    <h3><?= htmlspecialchars($data['no_kontrak']) ?></h3>

    <div class="info">
        <div><span class="label">Judul</span>: <?= htmlspecialchars($data['judul']) ?></div>
        <div><span class="label">Tanggal</span>: <?= date("d F Y", strtotime($data['tanggal'])) ?></div>
    </div>

    <div class="poin"><strong>1. Pihak 1</strong>
        <div class="box"><?= nl2br(htmlspecialchars($data['pos1'])) ?></div>
    </div>

    <?php if (!empty($data['pos2'])): ?>
        <div class="poin"><strong>2. Pihak 2</strong>
            <div class="box"><?= nl2br(htmlspecialchars($data['pos2'])) ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($data['pos3'])): ?>
        <div class="poin"><strong>3. Pihak 3</strong>
            <div class="box"><?= nl2br(htmlspecialchars($data['pos3'])) ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($data['deskripsi'])): ?>
        <div class="deskripsi"><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></div>
    <?php endif; ?>

    <table class="footer" style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="text-align: left;">
                <strong>Nilai</strong>:<br>Rp <?= number_format($nilai, 0, ',', '.') ?>
            </td>
            <td style="text-align: 50px;">
                <strong>Durasi</strong>:<br><?= htmlspecialchars($data['durasi'] ?? '-') ?>
            </td>
            <td style="text-align: right;">
                <strong>Status</strong>:<br><?= htmlspecialchars($data['status'] ?? '-') ?>
            </td>
        </tr>
    </table>


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