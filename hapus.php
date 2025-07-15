<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$dir = __DIR__ . "/kontrak";

if (isset($_GET['file'])) {
    $filename = basename($_GET['file']);
    $filepath = "$dir/$filename";

    if (file_exists($filepath) && unlink($filepath)) {
        header("Location: daftar.php?pesan=hapus-sukses");
        exit;
    } else {
        header("Location: daftar.php?pesan=hapus-gagal");
        exit;
    }
} else {
    header("Location: daftar.php?pesan=hapus-tidak-valid");
    exit;
}
?>
