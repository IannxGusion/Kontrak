<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$dir = __DIR__ . "/kontrak";
$hasil = [];
$judulDicari = $_GET['judul'] ?? '';

if ($judulDicari !== '') {
    $files = array_filter(scandir($dir), fn($f) => str_ends_with($f, '.json'));

    foreach ($files as $file) {
        $path = "$dir/$file";
        $data = json_decode(file_get_contents($path), true);
        if (stripos($data['judul'], $judulDicari) !== false) {
            $data['__filename'] = $file;
            $hasil[] = $data;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cari Kontrak</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #ecf0f1, #d0e3f3);
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #2c3e50;
            padding: 15px;
            color: white;
            font-size: 20px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            margin: 0 10px;
            background-color: #2980b9;
            padding: 6px 12px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .navbar a:hover {
            background-color: #1abc9c;
        }
        .container {
            max-width: 850px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        input[type="text"] {
            width: 80%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: 0.3s;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px #3498db;
        }
        button {
            padding: 12px 18px;
            border: none;
            background: #3498db;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .result {
            margin-top: 30px;
            background: #eaf2f8;
            padding: 25px;
            border-radius: 10px;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.05);
            animation: fadeIn 0.6s ease-in-out;
            position: relative;
        }
        .result p {
            margin: 10px 0;
        }
        .result hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #ccc;
        }
        .judul-hasil {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
            color: #2c3e50;
            font-weight: 600;
        }
        .tombol-aksi {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .lihat-detail,
        .lihat-cetak {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            text-align: center;
            color: white;
            transition: background-color 0.3s;
        }
        .lihat-detail {
            background: #16a085;
        }
        .lihat-detail:hover {
            background-color: #138d75;
        }
        .lihat-cetak {
            background: #3498db;
        }
        .lihat-cetak:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>🔎 Cari Kontrak</div>
        <div>
            <a href="dashboard.php">🏠 Halaman Utama</a>
            <a href="kontrak.php">📝 Buat Kontrak</a>
            <a href="cari_kontak.php">🔎 Cari kontrak</a>
            <a href="daftar.php">📂 Daftar Kontrak</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>
    <div class="container">
        <h2 style="text-align:center;">Cari Kontrak Berdasarkan Judul</h2>
        <form method="get" style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="judul" placeholder="Masukkan judul kontrak" value="<?= htmlspecialchars($judulDicari) ?>" required>
            <button type="submit">🔍 Cari</button>
        </form>

        <?php if ($judulDicari && empty($hasil)): ?>
            <p style="color:red; margin-top:20px; text-align:center;">❌ Kontrak dengan judul "<strong><?= htmlspecialchars($judulDicari) ?></strong>" tidak ditemukan.</p>
        <?php elseif (!empty($hasil)): ?>
            <p class="judul-hasil">🔎 Ditemukan <?= count($hasil) ?> hasil untuk "<strong><?= htmlspecialchars($judulDicari) ?></strong>"</p>
            <?php foreach ($hasil as $kontrak): ?>
                <div class="result">
                    <div class="tombol-aksi">
                       <a class="lihat-detail" href="detail.php?file=<?= urlencode($kontrak['__filename']) ?>&judul=<?= urlencode($judulDicari) ?>">👁️ Lihat Detail</a>
                        <a class="lihat-cetak" href="cetak.php?file=<?= urlencode($kontrak['__filename']) ?>" target="_blank">🖨️ Cetak</a>
                    </div>
                    <p><strong>Nomor Kontrak:</strong> <?= htmlspecialchars($kontrak['no_kontrak']) ?></p>
                    <p><strong>Judul:</strong> <?= htmlspecialchars($kontrak['judul']) ?></p>
                    <p><strong>Tanggal:</strong> <?= htmlspecialchars($kontrak['tanggal']) ?></p>
                    <hr>
                    <p><strong>Pos 1:</strong><br><?= nl2br(htmlspecialchars($kontrak['pos1'])) ?></p>
                    <p><strong>Pos 2:</strong><br><?= nl2br(htmlspecialchars($kontrak['pos2'])) ?></p>
                    <p><strong>Pos 3:</strong><br><?= nl2br(htmlspecialchars($kontrak['pos3'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
