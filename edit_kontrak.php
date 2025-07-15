<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$tahun = date("Y");
$tanggalHariIni = date("Y-m-d");
$dir = __DIR__ . "/kontrak";
if (!is_dir($dir)) mkdir($dir);

// Hitung kontrak hari ini (tidak digunakan lagi untuk batasan)
$kontrakHariIni = array_filter(scandir($dir), function ($file) use ($tanggalHariIni) {
    return strpos($file, $tanggalHariIni) !== false && str_ends_with($file, '.json');
});

// Jika submit form baru atau edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomor = $_POST["nomor"] ?? null;
    if (!$nomor) {
        $counter_file = "$dir/counter_$tahun.txt";
        $nomor = 1;
        if (file_exists($counter_file)) {
            $nomor = (int)file_get_contents($counter_file) + 1;
        }
        file_put_contents($counter_file, $nomor);
    }

    $no_kontrak = str_pad($nomor, 4, "0", STR_PAD_LEFT) . "/LIU/OUP/LI/" . $tahun;
    $filename = "kontrak_" . $tanggalHariIni . "_" . str_pad($nomor, 4, "0", STR_PAD_LEFT);
    $filejson = "$dir/$filename.json";

    $data = [
        "no_kontrak" => $no_kontrak,
        "judul" => $_POST["judul"],
        "tanggal" => $_POST["tanggal"],
        "pos1" => $_POST["pos1"],
        "pos2" => $_POST["pos2"] ?? '',
        "pos3" => $_POST["pos3"] ?? '',
        "deskripsi" => $_POST["deskripsi"] ?? '',
        "nilai" => $_POST["nilai"] ?? '',
        "durasi" => $_POST["durasi"] ?? '',
        "status" => $_POST["status"] ?? ''
    ];

    file_put_contents($filejson, json_encode($data, JSON_PRETTY_PRINT));
    header("Location: daftar.php?success=1");
    exit;
}

// Mode edit
$editData = null;
if (isset($_GET["edit"])) {
    $filepath = $_GET["edit"];
    if (file_exists($filepath)) {
        $editData = json_decode(file_get_contents($filepath), true);
    }
}

// Nomor preview kontrak
$previewNo = "";
if ($editData) {
    $previewNo = $editData['no_kontrak'];
} else {
    $counter_file = "$dir/counter_$tahun.txt";
    $next_nomor = 1;
    if (file_exists($counter_file)) {
        $next_nomor = (int)file_get_contents($counter_file) + 1;
    }
    $previewNo = str_pad($next_nomor, 4, "0", STR_PAD_LEFT) . "/LTI/DU/DK/DO" . $tahun;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Kontrak</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #eef2f5, #dbe9f4);
            margin: 0;
            padding: 0;
        }

        button {
            background: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background: #2980b9;
            transform: scale(1.02);
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
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        label {
            font-weight: bold;
        }

        button {
            background: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }

        .kontrak-preview-no {
            background: #ddd;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
        }

        .note {
            font-size: 13px;
            color: #888;
            margin-top: -15px;
            margin-bottom: 15px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .alert-success::before {
            content: "✔ ";
            font-weight: bold;
            margin-right: 5px;
        }


        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar a:hover {
            background-color: #1abc9c;
        }
        
        .container,
        .alert-success {
            animation: fadeIn 0.6s ease;
        }

        .kontrak-preview-no {
            background: #d0f0ff;
            color: #0077b6;
            padding: 12px 20px;
            margin-top: 25px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>

<div class="navbar">
    <div>✍️ Sistem Kontrak</div>
    <div>
        <a href="dashboard.php">🏠 Halaman Utama</a>
        <a href="kontrak.php">📝 Buat Kontrak</a>
        <a href="cari_kontak.php">🔎 Cari kontak</a>
        <a href="daftar.php">📂 Daftar Kontrak</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

<div class="container">
    <form method="post">
        <?php if ($editData): ?>
            <input type="hidden" name="nomor" value="<?= (int)substr($editData['no_kontrak'], 0, 4) ?>">
        <?php endif; ?>

        <label>Judul Kontrak:</label>
        <input type="text" name="judul" required value="<?= $editData['judul'] ?? '' ?>">

        <label>Tanggal Kontrak:</label>
        <input type="date" name="tanggal" required value="<?= $editData['tanggal'] ?? date('Y-m-d') ?>">

        <label>Pos 1 - Pihak 1:</label>
        <textarea name="pos1" required><?= $editData['pos1'] ?? '' ?></textarea>

        <label>Pos 2 - Pihak 2: <span class="note">(Opsional)</span></label>
        <textarea name="pos2"><?= $editData['pos2'] ?? '' ?></textarea>

        <label>Pos 3 - Pihak 3: <span class="note">(Opsional)</span></label>
        <textarea name="pos3"><?= $editData['pos3'] ?? '' ?></textarea>

        <label>Deskripsi Kontrak:</label>
        <textarea name="deskripsi"><?= $editData['deskripsi'] ?? '' ?></textarea>

        <label>Nilai Kontrak (Rp):</label>
        <input type="text" name="nilai" value="<?= $editData['nilai'] ?? '' ?>">

        <label>Durasi Kontrak:</label>
        <input type="text" name="durasi" value="<?= $editData['durasi'] ?? '' ?>">

        <label>Status Kontrak:</label>
        <select name="status">
            <option value="Aktif" <?= ($editData['status'] ?? '') == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
            <option value="Tidak Aktif" <?= ($editData['status'] ?? '') == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
        </select>

        <button type="submit">💾 Simpan Kontrak</button>
    </form>

    <div class="kontrak-preview-no">
        <?= $previewNo ?>
    </div>
</div>

</body>
</html>
