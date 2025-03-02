<?php
require '../autoload.php';

use App\Config\Database;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $id_pasien = $_POST['id_pasien'];
    
    $query = "INSERT INTO antrian_pasien (id_pasien, tanggal_antrian) VALUES (?, CURDATE())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_pasien);
    
    if ($stmt->execute()) {
        header('Location: antrian.php');
    } else {
        echo "Gagal menambahkan ke antrian";
    }
} 