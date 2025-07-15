<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$tahun = date("Y");
$dir = __DIR__ . "/kontrak";
$files = glob("$dir/kontrak_*.json");
usort($files, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});

// Hapus file jika ada request
$pesan = "";
if (isset($_GET['hapus'])) {
    $fileToDelete = $_GET['hapus'];
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        $pesan = "Kontrak berhasil dihapus.";
    } else {
        $pesan = "File tidak ditemukan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Kontrak</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #eef2f5, #dfe9f3);
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: #2c3e50;
            padding: 15px;
            color: white;
            text-align: center;
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
            transition: background-color 0.3s ease;
        }
        .navbar a:hover {
            background-color: #1abc9c;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #ecf0f1;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        .notif {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        a.action-link {
            text-decoration: none;
            color: #3498db;
            margin-right: 10px;
        }
        a.action-link:hover {
            color: #1abc9c;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(10px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>📂 Daftar Kontrak</div>
        <div>
            <a href="dashboard.php">🏠 Halaman Utama</a>
            <a href="kontrak.php">📝 Buat Kontrak</a>
            <a href="cari_kontak.php">🔎 Cari kontrak</a>
            <a href="daftar.php">📂 Daftar Kontrak</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>📑 Kontrak yang Telah Dibuat</h2>

        <?php if ($pesan): ?>
            <div class="notif">✅ <?= htmlspecialchars($pesan) ?></div>
        <?php endif; ?>

        <table>
            <tr>
                <th>No. Kontrak</th>
                <th>Judul</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
            <?php foreach ($files as $file):
                $data = json_decode(file_get_contents($file), true);
            ?>
                <tr>
                    <td><?= $data['no_kontrak'] ?></td>
                    <td><?= htmlspecialchars($data['judul']) ?></td>
                    <td><?= date("d-m-Y", strtotime($data['tanggal'])) ?></td>
                    <td>
                        <a class="action-link" href="edit_kontrak.php?edit=<?= urlencode($file) ?>">✏️ Edit</a>
                        <a class="action-link" href="hapus.php?file=<?= urlencode(basename($file)) ?>" onclick="return confirm('Yakin ingin menghapus?')">🗑️ Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
