<?php
require '../autoload.php';

use App\Config\Database;

if (isset($_POST['id_antrian'])) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $id_antrian = $_POST['id_antrian'];
    
    $query = "DELETE FROM antrian_pasien WHERE id_antrian = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_antrian);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} 