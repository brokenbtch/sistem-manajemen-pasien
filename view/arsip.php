<?php
require '../autoload.php';

use App\Config\Database;

$db = new Database();
$conn = $db->getConnection();

// Ambil semua data pemeriksaan
$query = "SELECT p.*, a.tanggal_antrian, a.waktu_daftar,
          ps.Nama, ps.No_telepon
          FROM pemeriksaan p 
          JOIN antrian_pasien a ON p.id_antrian = a.id_antrian
          JOIN pasien ps ON a.id_pasien = ps.id_pasien
          ORDER BY p.waktu_pemeriksaan DESC";
$result = mysqli_query($conn, $query);
$arsip = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Pemeriksaan - Sistem Manajemen Rumah Sakit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-archive me-2"></i>Arsip Pemeriksaan</h2>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Pasien</th>
                                <th>No. Telepon</th>
                                <th>Diagnosa</th>
                                <th>Resep</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($arsip as $item): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($item['tanggal_antrian'])) ?></td>
                                    <td><?= htmlspecialchars($item['Nama']) ?></td>
                                    <td><?= htmlspecialchars($item['No_telepon']) ?></td>
                                    <td>
                                        <?= nl2br(htmlspecialchars(
                                            strlen($item['diagnosa']) > 50 ? 
                                            substr($item['diagnosa'], 0, 50) . '...' : 
                                            $item['diagnosa']
                                        )) ?>
                                    </td>
                                    <td>
                                        <?= nl2br(htmlspecialchars(
                                            strlen($item['resep']) > 50 ? 
                                            substr($item['resep'], 0, 50) . '...' : 
                                            $item['resep']
                                        )) ?>
                                    </td>
                                    <td>
                                        <a href="../dokter/generate_pdf.php?id=<?= $item['id_pemeriksaan'] ?>" 
                                           class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-print"></i> Cetak
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 