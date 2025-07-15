<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$tahun = date("Y");
$dir = __DIR__ . "/kontrak";

$pesan = "";
if (isset($_GET['success'])) $pesan = "✅ Kontrak berhasil disimpan atau diperbarui.";

if (isset($_GET['hapus'])) {
    $fileToDelete = $dir . "/" . basename($_GET['hapus']);
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        $pesan = "🗑️ Kontrak berhasil dihapus.";
    } else {
        $pesan = "❌ File tidak ditemukan.";
    }
}

$files = glob("$dir/kontrak_*.json");
usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

$filterStatus = $_GET['status'] ?? '';
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

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        .notif {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            text-align: center;
        }

        .filter-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .filter-bar select, .filter-bar button {
            padding: 10px 14px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-family: 'Poppins', sans-serif;
        }

        .filter-bar select:focus {
            border-color: #3498db;
            outline: none;
        }

        .filter-bar button {
            background-color: #3498db;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filter-bar button:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #ecf0f1;
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
            <a href="kontrak_cetak.php">🖨️ Cetak Kontrak</a>
            <a href="daftar.php">📂 Daftar Kontrak</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>📑 Kontrak yang Telah Dibuat</h2>

        <?php if ($pesan): ?>
            <div class="notif"><?= htmlspecialchars($pesan) ?></div>
        <?php endif; ?>

        <form method="get" class="filter-bar">
            <label for="status"><strong>Filter Status:</strong></label>
            <select name="status" id="status">
                <option value="">-- Semua Status --</option>
                <?php
                $statusList = ['Menunggu', 'Diproses', 'Dipanding', 'Disetujui', 'Tidak Disetujui'];
                foreach ($statusList as $s) {
                    $selected = ($filterStatus === $s) ? 'selected' : '';
                    echo "<option value=\"$s\" $selected>$s</option>";
                }
                ?>
            </select>
            <button type="submit">Terapkan</button>
        </form>

        <table>
            <tr>
                <th>No. Kontrak</th>
                <th>Judul</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php
            foreach ($files as $file):
                $data = json_decode(file_get_contents($file), true);
                if (!$data) continue;
                if ($filterStatus && (!isset($data['status']) || $data['status'] !== $filterStatus)) continue;
                $filename = basename($file);
            ?>
                <tr>
                    <td><?= htmlspecialchars($data['no_kontrak']) ?></td>
                    <td><?= htmlspecialchars($data['judul']) ?></td>
                    <td><?= date("d-m-Y", strtotime($data['tanggal'])) ?></td>
                    <td><?= htmlspecialchars($data['status'] ?? 'Tidak Diketahui') ?></td>
                    <td>
                       <a class="action-link" href="kontrak.php?edit=<?= urlencode($dir . '/' . $filename) ?>">✏️ Edit</a>
                        <a class="action-link" href="daftar.php?hapus=<?= urlencode($filename) ?>" onclick="return confirm('Yakin ingin menghapus kontrak ini?')">🗑️ Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
