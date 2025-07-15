<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$dir = __DIR__ . "/kontrak";
$hasil = [];

$files = array_filter(scandir($dir), fn($f) => str_ends_with($f, '.json'));

foreach ($files as $file) {
    $path = "$dir/$file";
    $data = json_decode(file_get_contents($path), true);
    $data['__filename'] = $file;
    $hasil[] = $data;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Cetak Kontrak</title>
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
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
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

        .result {
            background: #f7f9fb;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .result p {
            margin: 6px 0;
        }

        .result hr {
            margin: 15px 0;
            border: none;
            border-top: 1px solid #ccc;
        }

        .tombol-aksi {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .lihat-detail,
        .lihat-cetak {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            text-align: center;
            color: white;
        }

        .lihat-detail {
            background: #16a085;
        }

        .lihat-detail:hover {
            background: #138d75;
        }

        .lihat-cetak {
            background: #3498db;
        }

        .lihat-cetak:hover {
            background: #2980b9;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div>📋 Daftar Kontrak Siap Cetak</div>
        <div>
            <a href="dashboard.php">🏠 Halaman Utama</a>
            <a href="kontrak.php">📝 Buat Kontrak</a>
            <a href="kontrak_cetak.php">🖨️ cetak kontrak</a>
            <a href="daftar.php">📂 Daftar Kontrak</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="container">
        <h2 style="text-align:center;">Urutan Daftar Kontrak</h2>
        <?php if (empty($hasil)): ?>
            <p style="text-align:center; color:red;">❌ Tidak ada kontrak ditemukan.</p>
        <?php else: ?>
            <?php foreach ($hasil as $kontrak): ?>
                <div class="result">
                    <div class="tombol-aksi">
                        <a class="lihat-detail" href="detail.php?file=<?= urlencode($kontrak['__filename']) ?>">👁️ Detail</a>
                        <a class="lihat-cetak" href="cetak.php?file=<?= urlencode($kontrak['__filename']) ?>" target="_blank">🖨️ Cetak</a>
                    </div>
                    <p><strong>No. Kontrak:</strong> <?= htmlspecialchars($kontrak['no_kontrak']) ?></p>
                    <p><strong>Judul:</strong> <?= htmlspecialchars($kontrak['judul']) ?></p>
                    <p><strong>Tanggal:</strong> <?= htmlspecialchars($kontrak['tanggal']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($kontrak['status']) ?></p>
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