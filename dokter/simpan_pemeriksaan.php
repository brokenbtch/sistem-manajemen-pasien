<?php
require '../autoload.php';

use App\Config\Database;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $id_antrian = $_POST['id_antrian'];
    $diagnosa = $_POST['diagnosa'];
    $resep = $_POST['resep'];
    $catatan = $_POST['catatan_dokter'];
    
    // Update status antrian
    $query1 = "UPDATE antrian_pasien SET status = 'selesai' WHERE id_antrian = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("i", $id_antrian);
    
    // Simpan hasil pemeriksaan
    $query2 = "INSERT INTO pemeriksaan (id_antrian, diagnosa, resep, catatan_dokter) 
               VALUES (?, ?, ?, ?)";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("isss", $id_antrian, $diagnosa, $resep, $catatan);
    
    $conn->begin_transaction();
    try {
        $stmt1->execute();
        $stmt2->execute();
        $id_pemeriksaan = $conn->insert_id;
        $conn->commit();
        
        // Redirect ke halaman generate PDF
        header("Location: generate_pdf.php?id=" . $id_pemeriksaan);
    } catch (Exception $e) {
        $conn->rollback();
        echo "Gagal menyimpan pemeriksaan";
    }
} 