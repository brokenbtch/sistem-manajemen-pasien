<?php
require '../autoload.php';

use App\Config\Database;
use src\Model\Pasien;

// Koneksi ke database
$db = new Database();
$conn = $db->getConnection();
$pasien = new Pasien($conn);

// Inisialisasi variabel error
$errors = [];

// Proses form jika ada POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi ID Pasien (hanya angka)
    if (!preg_match('/^[0-9]+$/', $_POST['id_pasien'])) {
        $errors['id_pasien'] = "ID Pasien hanya boleh berisi angka";
    }
    
    // Validasi Nama (hanya huruf dan spasi)
    if (!preg_match('/^[a-zA-Z\s]+$/', $_POST['Nama'])) {
        $errors['Nama'] = "Nama hanya boleh berisi huruf dan spasi";
    }
    
    // Validasi No Telepon (hanya angka, minimal 10 digit)
    if (!preg_match('/^[0-9]{10,15}$/', $_POST['No_telepon'])) {
        $errors['No_telepon'] = "Nomor telepon hanya boleh berisi angka (10-15 digit)";
    }
    
    // Validasi Tanggal Lahir (tidak boleh di masa depan)
    $tanggal_lahir = new DateTime($_POST['Tanggal_lahir']);
    $today = new DateTime();
    if ($tanggal_lahir > $today) {
        $errors['Tanggal_lahir'] = "Tanggal lahir tidak boleh di masa depan";
    }
    
    // Jika tidak ada error, simpan data
    if (empty($errors)) {
        $data = [
            'id_pasien' => $_POST['id_pasien'],
            'Nama' => $_POST['Nama'],
            'Jenis_kelamin' => $_POST['Jenis_kelamin'],
            'Tanggal_lahir' => $_POST['Tanggal_lahir'],
            'No_telepon' => $_POST['No_telepon'],
            'Alamat' => $_POST['Alamat'],
            'Keluhan_pasien' => $_POST['Keluhan_pasien']
        ];

        if ($pasien->tambahPasien($data)) {
            header('Location: index.php');
            exit;
        } else {
            $error = "Gagal menambahkan data";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pasien Baru - Sistem Manajemen Rumah Sakit</title>
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
        .form-label {
            font-weight: 500;
        }
        .required::after {
            content: " *";
            color: red;
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
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h2>
                    <i class="fas fa-user-plus me-2"></i>
                    Tambah Pasien Baru
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tambah Pasien</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card">
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">ID Pasien</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control <?= isset($errors['id_pasien']) ? 'is-invalid' : '' ?>" 
                                       name="id_pasien" required pattern="[0-9]+" 
                                       value="<?= isset($_POST['id_pasien']) ? htmlspecialchars($_POST['id_pasien']) : '' ?>">
                                <div class="invalid-feedback">
                                    <?= isset($errors['id_pasien']) ? $errors['id_pasien'] : 'ID Pasien hanya boleh berisi angka' ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Nama Pasien</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control <?= isset($errors['Nama']) ? 'is-invalid' : '' ?>" 
                                       name="Nama" required pattern="[a-zA-Z\s]+" 
                                       value="<?= isset($_POST['Nama']) ? htmlspecialchars($_POST['Nama']) : '' ?>">
                                <div class="invalid-feedback">
                                    <?= isset($errors['Nama']) ? $errors['Nama'] : 'Nama hanya boleh berisi huruf dan spasi' ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Jenis Kelamin</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                <select class="form-select <?= isset($errors['Jenis_kelamin']) ? 'is-invalid' : '' ?>" 
                                        name="Jenis_kelamin" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" <?= (isset($_POST['Jenis_kelamin']) && $_POST['Jenis_kelamin'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= (isset($_POST['Jenis_kelamin']) && $_POST['Jenis_kelamin'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih jenis kelamin
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tanggal Lahir</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control <?= isset($errors['Tanggal_lahir']) ? 'is-invalid' : '' ?>" 
                                       name="Tanggal_lahir" required max="<?= date('Y-m-d'); ?>" 
                                       value="<?= isset($_POST['Tanggal_lahir']) ? $_POST['Tanggal_lahir'] : '' ?>">
                                <div class="invalid-feedback">
                                    <?= isset($errors['Tanggal_lahir']) ? $errors['Tanggal_lahir'] : 'Tanggal lahir tidak boleh di masa depan' ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">No. Telepon</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control <?= isset($errors['No_telepon']) ? 'is-invalid' : '' ?>" 
                                       name="No_telepon" required pattern="[0-9]{10,15}" 
                                       value="<?= isset($_POST['No_telepon']) ? htmlspecialchars($_POST['No_telepon']) : '' ?>">
                                <div class="invalid-feedback">
                                    <?= isset($errors['No_telepon']) ? $errors['No_telepon'] : 'Nomor telepon hanya boleh berisi angka (10-15 digit)' ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Alamat</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea class="form-control <?= isset($errors['Alamat']) ? 'is-invalid' : '' ?>" 
                                          name="Alamat" rows="1" required minlength="5"><?= isset($_POST['Alamat']) ? htmlspecialchars($_POST['Alamat']) : '' ?></textarea>
                                <div class="invalid-feedback">
                                    Alamat harus diisi minimal 5 karakter
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label required">Keluhan</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fas fa-notes-medical"></i></span>
                                <textarea class="form-control <?= isset($errors['Keluhan_pasien']) ? 'is-invalid' : '' ?>" 
                                          name="Keluhan_pasien" rows="3" required minlength="5"><?= isset($_POST['Keluhan_pasien']) ? htmlspecialchars($_POST['Keluhan_pasien']) : '' ?></textarea>
                                <div class="invalid-feedback">
                                    Keluhan harus diisi minimal 5 karakter
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p class="mb-0">© <?= date('Y'); ?> Sistem Manajemen Rumah Sakit. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
        
        // Tambahan validasi khusus
        document.addEventListener('DOMContentLoaded', function() {
            // Validasi ID Pasien (hanya angka)
            const idPasienInput = document.querySelector('input[name="id_pasien"]');
            idPasienInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            
            // Validasi Nama (hanya huruf dan spasi)
            const namaInput = document.querySelector('input[name="Nama"]');
            namaInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            });
            
            // Validasi No Telepon (hanya angka)
            const teleponInput = document.querySelector('input[name="No_telepon"]');
            teleponInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>
</html>
