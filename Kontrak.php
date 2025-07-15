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

// Reset counter jika diminta
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reset_counter"])) {
    file_put_contents("$dir/counter_$tahun.txt", 0);
    header("Location: kontrak.php?reset=1");
    exit;
}

// Cek jumlah kontrak hari ini
$kontrakHariIni = array_filter(scandir($dir), function ($file) use ($tanggalHariIni) {
    return strpos($file, $tanggalHariIni) !== false && str_ends_with($file, '.json');
});

// Simpan kontrak baru atau edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["reset_counter"])) {
    $isEdit = isset($_POST["edit_file"]) && file_exists($_POST["edit_file"]);
    $tanggal = $_POST["tanggal"] ?? date("Y-m-d");

    if ($isEdit) {
        $filejson = $_POST["edit_file"];
        $nomor = (int)substr(json_decode(file_get_contents($filejson), true)['no_kontrak'], 0, 4);
    } else {
        $counter_file = "$dir/counter_$tahun.txt";
        $nomor = 1;
        if (file_exists($counter_file)) {
            $nomor = (int)file_get_contents($counter_file) + 1;
        }
        file_put_contents($counter_file, $nomor);
        $filejson = "$dir/kontrak_" . $tanggal . "_" . str_pad($nomor, 4, "0", STR_PAD_LEFT) . ".json";
    }

    $inputPihak = array_filter([$_POST['pos1'], $_POST['pos2'], $_POST['pos3']]);
    $bagian = [];
    foreach ($inputPihak as $kode) {
        if (!in_array($kode, $bagian)) {
            $bagian[] = $kode;
        }
    }

    $kodePihak = implode("/", $bagian);
    $no_kontrak = str_pad($nomor, 4, "0", STR_PAD_LEFT) . "/" . $kodePihak . "/" . $tahun;

    $data = [
        "no_kontrak" => $no_kontrak,
        "judul" => $_POST["judul"],
        "tanggal" => $tanggal,
        "pos1" => $_POST["pos1"],
        "nama_pos1" => $_POST["nama_pos1"],
        "pos2" => $_POST["pos2"] ?? '',
        "nama_pos2" => $_POST["nama_pos2"] ?? '',
        "pos3" => $_POST["pos3"] ?? '',
        "nama_pos3" => $_POST["nama_pos3"] ?? '',
        "deskripsi" => $_POST["deskripsi"] ?? '',
        "nilai" => $_POST["nilai"] ?? '',
        "durasi" => $_POST["durasi"] ?? '',
        "status" => $_POST["status"] ?? ''
    ];

    file_put_contents($filejson, json_encode($data, JSON_PRETTY_PRINT));
    header("Location: daftar.php?success=1");
    exit;
}

$editData = null;
if (isset($_GET["edit"])) {
    $filepath = $_GET["edit"];
    if (file_exists($filepath)) {
        $editData = json_decode(file_get_contents($filepath), true);
    }
}

$previewNo = "";
if ($editData) {
    $previewNo = $editData['no_kontrak'];
} else {
    if (count($kontrakHariIni) >= 2) {
        $previewNo = "Maksimal 2 kontrak per hari telah tercapai";
    } else {
        $counter_file = "$dir/counter_$tahun.txt";
        $next_nomor = 1;
        if (file_exists($counter_file)) {
            $next_nomor = (int)file_get_contents($counter_file) + 1;
        }

        $p1 = $_POST["pos1"] ?? '';
        $p2 = $_POST["pos2"] ?? '';
        $p3 = $_POST["pos3"] ?? '';

        $kode1 = trim(strtok($p1, ' '));
        $kode2 = trim(strtok($p2, ' '));
        $kode3 = trim(strtok($p3, ' '));
        $pihakStr = implode("/", array_filter([$kode1, $kode2, $kode3]));

        $previewNo = str_pad($next_nomor, 4, "0", STR_PAD_LEFT) . '/' . $pihakStr . '/' . $tahun;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Kontrak</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #eef2f5, #dbe9f4);
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
        }

        .navbar a:hover {
            background-color: #1abc9c;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 10px;
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
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background: #2980b9;
            transform: scale(1.02);
        }

        .note {
            font-size: 13px;
            color: #555;
            margin: 5px 0 15px 0;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
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
    </style>
</head>

<body>
    <div class="navbar">
        <img src="logolti.png" width="200px" height="auto" alt="logo">
        <div>
            <a href="dashboard.php">🏠 Halaman Utama</a>
            <a href="kontrak.php">📝 Buat Kontrak</a>
            <a href="kontrak_cetak.php">🖨️ cetak kontrak</a>
            <a href="daftar.php">📂 Daftar Kontrak</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert-success">✅ Kontrak berhasil disimpan.</div>
        <?php endif; ?>
        <?php if (isset($_GET['reset'])): ?>
            <div class="alert-success" style="background:#fff3cd; color:#856404; border-color:#ffeeba;">🔁 Nomor kontrak berhasil di-reset ke 0001.</div>
        <?php endif; ?>

        <form method="post" style="text-align:right;">
            <input type="hidden" name="reset_counter" value="1">
            <button type="submit" onclick="return confirm('Yakin ingin mereset nomor kontrak ke 0001?')">🔁 Reset ke 0001</button>
        </form>

        <?php if ($previewNo !== "Maksimal 2 kontrak per hari telah tercapai"): ?>
            <form method="post">
                <?php if ($editData): ?>
                    <input type="hidden" name="edit_file" value="<?= $_GET['edit'] ?>">
                <?php endif; ?>

                <label>Judul Kontrak:</label>
                <input type="text" name="judul" required value="<?= $editData['judul'] ?? '' ?>">

                <label>Tanggal Kontrak:</label>
                <input type="date" name="tanggal" required value="<?= $editData['tanggal'] ?? date('Y-m-d') ?>">

                <!-- Pihak 1 -->
                <label>Pihak 1:</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <select name="pos1" required>
                        <option value="">-- Pilih Kode --</option>
                        <option value="LTI" <?= ($editData['pos1'] ?? '') == 'LTI' ? 'selected' : '' ?>>LTI</option>
                        <option value="DU" <?= ($editData['pos1'] ?? '') == 'DU' ? 'selected' : '' ?>>DU</option>
                        <option value="DK" <?= ($editData['pos1'] ?? '') == 'DK' ? 'selected' : '' ?>>DK</option>
                        <option value="DO" <?= ($editData['pos1'] ?? '') == 'DO' ? 'selected' : '' ?>>DO</option>
                    </select>

                    <select name="nama_pos1" required>
                        <option value="">-- Pilih Nama --</option>
                        <option value="Pihak LTI" <?= ($editData['nama_pos1'] ?? '') == 'Pihak LTI' ? 'selected' : '' ?>>Pihak LTI</option>
                        <option value="Pihak DU" <?= ($editData['nama_pos1'] ?? '') == 'Pihak DU' ? 'selected' : '' ?>>Pihak DU</option>
                        <option value="Pihak DK" <?= ($editData['nama_pos1'] ?? '') == 'Pihak DK' ? 'selected' : '' ?>>Pihak DK</option>
                        <option value="Pihak DO" <?= ($editData['nama_pos1'] ?? '') == 'Pihak DO' ? 'selected' : '' ?>>Pihak DO</option>
                    </select>
                </div>

                <!-- Pihak 2 -->
                <label>Pihak 2:</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <select name="pos2">
                        <option value="">-- Pilih Kode --</option>
                        <option value="LTI" <?= ($editData['pos2'] ?? '') == 'LTI' ? 'selected' : '' ?>>LTI</option>
                        <option value="DU" <?= ($editData['pos2'] ?? '') == 'DU' ? 'selected' : '' ?>>DU</option>
                        <option value="DK" <?= ($editData['pos2'] ?? '') == 'DK' ? 'selected' : '' ?>>DK</option>
                        <option value="DO" <?= ($editData['pos2'] ?? '') == 'DO' ? 'selected' : '' ?>>DO</option>
                    </select>

                    <select name="nama_pos2">
                        <option value="">-- Pilih Nama --</option>
                        <option value="Pihak LTI" <?= ($editData['nama_pos2'] ?? '') == 'Pihak LTI' ? 'selected' : '' ?>>Pihak LTI</option>
                        <option value="Pihak DU" <?= ($editData['nama_pos2'] ?? '') == 'Pihak DU' ? 'selected' : '' ?>>Pihak DU</option>
                        <option value="Pihak DK" <?= ($editData['nama_pos2'] ?? '') == 'Pihak DK' ? 'selected' : '' ?>>Pihak DK</option>
                        <option value="Pihak DO" <?= ($editData['nama_pos2'] ?? '') == 'Pihak DO' ? 'selected' : '' ?>>Pihak DO</option>
                    </select>
                </div>

                <!-- Pihak 3 -->
                <label>Pihak 3:</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <select name="pos3">
                        <option value="">-- Pilih Kode --</option>
                        <option value="LTI" <?= ($editData['pos3'] ?? '') == 'LTI' ? 'selected' : '' ?>>LTI</option>
                        <option value="DU" <?= ($editData['pos3'] ?? '') == 'DU' ? 'selected' : '' ?>>DU</option>
                        <option value="DK" <?= ($editData['pos3'] ?? '') == 'DK' ? 'selected' : '' ?>>DK</option>
                        <option value="DO" <?= ($editData['pos3'] ?? '') == 'DO' ? 'selected' : '' ?>>DO</option>
                    </select>

                    <select name="nama_pos3">
                        <option value="">-- Pilih Nama --</option>
                        <option value="Pihak LTI" <?= ($editData['nama_pos3'] ?? '') == 'Pihak LTI' ? 'selected' : '' ?>>Pihak LTI</option>
                        <option value="Pihak DU" <?= ($editData['nama_pos3'] ?? '') == 'Pihak DU' ? 'selected' : '' ?>>Pihak DU</option>
                        <option value="Pihak DK" <?= ($editData['nama_pos3'] ?? '') == 'Pihak DK' ? 'selected' : '' ?>>Pihak DK</option>
                        <option value="Pihak DO" <?= ($editData['nama_pos3'] ?? '') == 'Pihak DO' ? 'selected' : '' ?>>Pihak DO</option>
                    </select>
                </div>



                <label>Deskripsi Kontrak:</label>
                <textarea name="deskripsi"><?= $editData['deskripsi'] ?? '' ?></textarea>

                <label>Nilai Kontrak (Rp):</label>
                <input type="text" name="nilai" placeholder="Contoh: 30000" value="<?= $editData['nilai'] ?? '' ?>">

                <label>Durasi Kontrak:</label>
                <input type="text" name="durasi" value="<?= $editData['durasi'] ?? '' ?>">

                <label>Status Kontrak:</label>
                <select name="status">
                    <?php
                    $statusOptions = ["Menunggu", "Diproses", "Dipanding", "Disetujui", "Tidak Disetujui"];
                    foreach ($statusOptions as $status):
                    ?>
                        <option value="<?= $status ?>" <?= ($editData['status'] ?? '') == $status ? 'selected' : '' ?>><?= $status ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">📝 Simpan Kontrak</button>
            </form>
            <div class="kontrak-preview-no"><?= $previewNo ?></div>
        <?php else: ?>
            <div style="text-align:center; padding:20px; color:red; font-weight:bold;">
                ⚠️ Kontrak mencapai batas 2 Kontrak. Silakan buat lagi besok.
            </div>
        <?php endif; ?>
    </div>

    <script>
        const pihak1 = document.querySelector('select[name="pos1"]');
        const pihak2 = document.querySelector('select[name="pos2"]');
        const pihak3 = document.querySelector('select[name="pos3"]');
        const previewBox = document.querySelector('.kontrak-preview-no');

        const nextNomor = "<?= str_pad($next_nomor ?? 1, 4, "0", STR_PAD_LEFT) ?>";
        const tahun = "<?= $tahun ?>";

        function updatePreview() {
            const kode1 = pihak1.value.trim();
            const kode2 = pihak2.value.trim();
            const kode3 = pihak3.value.trim();

            // Filter hanya pihak unik yang dipilih
            const unikPihak = [];
            [kode1, kode2, kode3].forEach(kode => {
                if (kode && !unikPihak.includes(kode)) {
                    unikPihak.push(kode);
                }
            });

            if (unikPihak.length === 0) {
                previewBox.textContent = 'Harap pilih minimal 1 pihak untuk melihat nomor kontrak';
                return;
            }

            const hasil = `${nextNomor}/${unikPihak.join('/')}/${tahun}`;
            previewBox.textContent = hasil;
        }

        [pihak1, pihak2, pihak3].forEach(el => el.addEventListener('change', updatePreview));

        // Jalankan saat halaman dibuka
        updatePreview();
    </script>

</body>

</html>