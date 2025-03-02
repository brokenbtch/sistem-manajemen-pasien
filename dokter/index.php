<?php
require '../autoload.php';

use App\Config\Database;

$db = new Database();
$conn = $db->getConnection();

// Ambil antrian yang belum diperiksa
$query = "SELECT a.*, p.Nama, p.Keluhan_pasien, p.Tanggal_lahir, p.Jenis_kelamin, p.No_telepon, p.Alamat 
          FROM antrian_pasien a
          JOIN pasien p ON a.id_pasien = p.id_pasien
          WHERE a.status = 'menunggu'
          ORDER BY a.waktu_daftar ASC";
$result = mysqli_query($conn, $query);
$antrian = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Dokter - Sistem Manajemen Rumah Sakit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }
        .list-group-item {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }
        .list-group-item:hover {
            border-left-color: #3498db;
            background-color: #f8f9fa;
        }
        .list-group-item.active {
            border-left-color: #2ecc71;
            background-color: #f0fff4;
        }
        .patient-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .patient-info h6 {
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        .patient-info p {
            margin-bottom: 10px;
            padding-left: 10px;
            border-left: 3px solid #3498db;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-user-md me-2"></i>
                Panel Dokter
            </span>
            <span class="text-white">
                <i class="fas fa-clock me-2"></i>
                <?= date('l, d F Y') ?>
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <!-- Daftar Antrian -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list-ol me-2"></i>Daftar Antrian
                            <span class="badge bg-warning float-end"><?= count($antrian) ?> Pasien</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php if (empty($antrian)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard-check fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Tidak ada antrian saat ini</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($antrian as $item): ?>
                                    <a href="#" class="list-group-item list-group-item-action" 
                                       onclick="pilihPasien(<?= htmlspecialchars(json_encode($item)) ?>)">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <h6 class="mb-1">
                                                <i class="fas fa-user-circle me-2 text-primary"></i>
                                                <?= htmlspecialchars($item['Nama']) ?>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('H:i', strtotime($item['waktu_daftar'])) ?>
                                            </small>
                                        </div>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-notes-medical me-1"></i>
                                            <?= htmlspecialchars($item['Keluhan_pasien']) ?>
                                        </p>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Pemeriksaan -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-stethoscope me-2"></i>Form Pemeriksaan
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Info Pasien -->
                        <div id="patientInfo" class="patient-info d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-user me-2"></i>Nama Pasien</h6>
                                    <p id="namaPasien">-</p>
                                    
                                    <h6><i class="fas fa-venus-mars me-2"></i>Jenis Kelamin</h6>
                                    <p id="jenisKelamin">-</p>
                                    
                                    <h6><i class="fas fa-calendar me-2"></i>Tanggal Lahir</h6>
                                    <p id="tanggalLahir">-</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-phone me-2"></i>No. Telepon</h6>
                                    <p id="noTelepon">-</p>
                                    
                                    <h6><i class="fas fa-map-marker-alt me-2"></i>Alamat</h6>
                                    <p id="alamat">-</p>
                                    
                                    <h6><i class="fas fa-notes-medical me-2"></i>Keluhan</h6>
                                    <p id="keluhan">-</p>
                                </div>
                            </div>
                        </div>

                        <form id="formPemeriksaan" action="simpan_pemeriksaan.php" method="POST" class="d-none">
                            <input type="hidden" name="id_antrian" id="id_antrian">
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clipboard-list me-2"></i>Diagnosa
                                </label>
                                <textarea class="form-control" name="diagnosa" rows="3" required 
                                          placeholder="Tuliskan diagnosa pasien..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-prescription me-2"></i>Resep
                                </label>
                                <textarea class="form-control" name="resep" rows="3" required
                                          placeholder="Tuliskan resep obat..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-comment-medical me-2"></i>Catatan Tambahan
                                </label>
                                <textarea class="form-control" name="catatan_dokter" rows="2"
                                          placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Pemeriksaan
                                </button>
                            </div>
                        </form>

                        <!-- Pesan Default -->
                        <div id="defaultMessage" class="text-center py-5">
                            <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Pilih pasien dari daftar antrian</h5>
                            <p class="text-muted small">Klik pada salah satu pasien di daftar antrian untuk mulai pemeriksaan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function pilihPasien(data) {
            // Update info pasien
            document.getElementById('patientInfo').classList.remove('d-none');
            document.getElementById('formPemeriksaan').classList.remove('d-none');
            document.getElementById('defaultMessage').classList.add('d-none');
            
            document.getElementById('namaPasien').textContent = data.Nama;
            document.getElementById('jenisKelamin').textContent = data.Jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
            document.getElementById('tanggalLahir').textContent = new Date(data.Tanggal_lahir).toLocaleDateString('id-ID');
            document.getElementById('noTelepon').textContent = data.No_telepon;
            document.getElementById('alamat').textContent = data.Alamat;
            document.getElementById('keluhan').textContent = data.Keluhan_pasien;
            
            document.getElementById('id_antrian').value = data.id_antrian;
        }

        function resetForm() {
            document.getElementById('formPemeriksaan').reset();
        }
    </script>
</body>
</html> 