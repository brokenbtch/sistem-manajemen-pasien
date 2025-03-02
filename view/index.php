<?php
// require '../koneksi.php'; // hapus atau comment karena sudah tidak dipakai
require '../autoload.php';

use App\Config\Database;
use src\Model\Pasien;

// Koneksi ke database
$db = new Database();
$conn = $db->getConnection(); // Ambil koneksi database
$pasien = new Pasien($conn); // Pass koneksi ke constructor Pasien

// Ambil data pasien
$dataPasien = $pasien->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pasien Rumah Sakit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #2c3e50;
            color: white;
        }
        .btn-action {
            width: 40px;
            height: 40px;
            padding: 0;
            line-height: 40px;
            text-align: center;
            margin: 2px;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        .nav-tabs .nav-link {
            color: #2c3e50;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            color: #2980b9;
            font-weight: 600;
        }
        .tab-pane {
            padding: 20px 0;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-hospital-alt me-2"></i>
                Sistem Manajemen Pasien
            </span>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#resepsionis">
                    <i class="fas fa-user-nurse me-2"></i>Panel Resepsionis
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#antrian">
                    <i class="fas fa-list-ol me-2"></i>Antrian
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#dokter">
                    <i class="fas fa-user-md me-2"></i>Panel Dokter
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#arsip">
                    <i class="fas fa-archive me-2"></i>Arsip
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Panel Resepsionis -->
            <div class="tab-pane fade show active" id="resepsionis">
                <!-- Header Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h2><i class="fas fa-users me-2"></i>Daftar Pasien</h2>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="add.php" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Tambah Pasien Baru
                        </a>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Pasien</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tanggal Lahir</th>
                                        <th>No. Telepon</th>
                                        <th>Alamat</th>
                                        <th>Keluhan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dataPasien as $data): ?>
                                        <tr>
                                            <td><?= $data['id_pasien']; ?></td>
                                            <td>
                                                <i class="fas fa-user-circle me-2 text-primary"></i>
                                                <?= $data['Nama']; ?>
                                            </td>
                                            <td>
                                                <?php if($data['Jenis_kelamin'] == 'L'): ?>
                                                    <span class="badge bg-info">Laki-laki</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Perempuan</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($data['Tanggal_lahir'])); ?></td>
                                            <td>
                                                <i class="fas fa-phone me-1 text-success"></i>
                                                <?= $data['No_telepon']; ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                                <?= $data['Alamat']; ?>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    <i class="fas fa-notes-medical me-1"></i>
                                                    <?= $data['Keluhan_pasien']; ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="update.php?id=<?= $data['id_pasien']; ?>" 
                                                   class="btn btn-warning btn-action" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete.php?id=<?= $data['id_pasien']; ?>" 
                                                   class="btn btn-danger btn-action" 
                                                   onclick="return confirm('Yakin ingin menghapus data ini?')"
                                                   title="Hapus">
                                                    <i class="fas fa-trash"></i>
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

            <!-- Panel Antrian -->
            <div class="tab-pane fade" id="antrian">
                <iframe src="antrian.php" style="width: 100%; height: 800px; border: none;"></iframe>
            </div>

            <!-- Panel Dokter -->
            <div class="tab-pane fade" id="dokter">
                <iframe src="../dokter/index.php" style="width: 100%; height: 800px; border: none;"></iframe>
            </div>

            <!-- Panel Arsip -->
            <div class="tab-pane fade" id="arsip">
                <iframe src="arsip.php" style="width: 100%; height: 800px; border: none;"></iframe>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p class="mb-0">© <?= date('Y'); ?> Sistem Manajemen Rumah Sakit. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
