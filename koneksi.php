<?php
$conn = new mysqli("localhost", "root", "", "kontrak");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
