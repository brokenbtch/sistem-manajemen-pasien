<?php
require '../autoload.php';

use App\Config\Database;
use src\Model\Pasien;

// Koneksi ke database
$db = new Database();
$conn = $db->getConnection();
$pasien = new Pasien($conn);

// Ambil ID dari parameter URL
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Tambahkan method deletePasien di class Pasien
if ($pasien->deletePasien($id)) {
    header('Location: index.php');
} else {
    echo "Gagal menghapus data";
}
?>
