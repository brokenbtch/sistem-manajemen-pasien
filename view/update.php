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

// Ambil ID dari parameter URL
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Ambil data pasien berdasarkan ID
$query = "SELECT * FROM pasien WHERE id_pasien = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Ambil riwayat kunjungan pasien (contoh data)
$riwayat_kunjungan = [];
$query_riwayat = "SELECT * FROM kunjungan WHERE id_pasien = ? ORDER BY tanggal_kunjungan DESC LIMIT 5";
$stmt_riwayat = $conn->prepare($query_riwayat);

// Cek apakah query berhasil dipersiapkan
if ($stmt_riwayat) {
    $stmt_riwayat->bind_param("i", $id);
    $stmt_riwayat->execute();
    $result_riwayat = $stmt_riwayat->get_result();
    
    // Struktur pengulangan while untuk mengambil data riwayat
    while ($row = $result_riwayat->fetch_assoc()) {
        $riwayat_kunjungan[] = $row;
    }
} else {
    // Jika tabel kunjungan belum ada, buat data dummy untuk contoh
    $riwayat_kunjungan = [
        ['tanggal_kunjungan' => date('Y-m-d', strtotime('-7 days')), 'diagnosa' => 'Demam', 'tindakan' => 'Pemberian obat penurun panas'],
        ['tanggal_kunjungan' => date('Y-m-d', strtotime('-14 days')), 'diagnosa' => 'Batuk', 'tindakan' => 'Pemberian obat batuk'],
        ['tanggal_kunjungan' => date('Y-m-d', strtotime('-30 days')), 'diagnosa' => 'Flu', 'tindakan' => 'Istirahat dan minum banyak air']
    ];
}

// Proses form update jika ada POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    
    // Validasi Alamat (minimal 5 karakter)
    if (strlen($_POST['Alamat']) < 5) {
        $errors['Alamat'] = "Alamat harus diisi minimal 5 karakter";
    }
    
    // Validasi Keluhan (minimal 5 karakter)
    if (strlen($_POST['Keluhan_pasien']) < 5) {
        $errors['Keluhan_pasien'] = "Keluhan harus diisi minimal 5 karakter";
    }
    
    // Jika tidak ada error, update data
    if (empty($errors)) {
        $updateData = [
            'id_pasien' => $_POST['id_pasien'],
            'Nama' => $_POST['Nama'],
            'Jenis_kelamin' => $_POST['Jenis_kelamin'],
            'Tanggal_lahir' => $_POST['Tanggal_lahir'],
            'No_telepon' => $_POST['No_telepon'],
            'Alamat' => $_POST['Alamat'],
            'Keluhan_pasien' => $_POST['Keluhan_pasien']
        ];

        if ($pasien->updatePasien($id, $updateData)) {
            header('Location: index.php');
            exit;
        } else {
            $error = "Gagal mengupdate data";
        }
    }
}

// Fungsi untuk menghitung usia berdasarkan tanggal lahir
function hitungUsia($tanggal_lahir) {
    $birthDate = new DateTime($tanggal_lahir);
    $today = new DateTime();
    $age = $birthDate->diff($today);
    return $age->y;
}

// Struktur percabangan untuk menentukan kategori usia
function getKategoriUsia($usia) {
    if ($usia < 12) {
        return "Anak-anak";
    } else if ($usia >= 12 && $usia < 18) {
        return "Remaja";
    } else if ($usia >= 18 && $usia < 60) {
        return "Dewasa";
    } else {
        return "Lansia";
    }
}

// Hitung usia pasien
$usia = hitungUsia($data['Tanggal_lahir']);
$kategori_usia = getKategoriUsia($usia);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data Pasien - Sistem Manajemen Rumah Sakit</title>
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
        .badge-kategori {
            font-size: 0.9rem;
            padding: 0.5rem;
            border-radius: 4px;
        }
        .kategori-anak {
            background-color: #8BC34A;
            color: white;
        }
        .kategori-remaja {
            background-color: #03A9F4;
            color: white;
        }
        .kategori-dewasa {
            background-color: #673AB7;
            color: white;
        }
        .kategori-lansia {
            background-color: #FF5722;
            color: white;
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
                    <i class="fas fa-user-edit me-2"></i>
                    Update Data Pasien
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Update Pasien</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Informasi Pasien -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Pasien</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID Pasien:</strong> <?= htmlspecialchars($data['id_pasien']) ?></p>
                        <p><strong>Nama:</strong> <?= htmlspecialchars($data['Nama']) ?></p>
                        <p><strong>Jenis Kelamin:</strong> <?= $data['Jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Usia:</strong> <?= $usia ?> tahun</p>
                        <p>
                            <strong>Kategori Usia:</strong> 
                            <?php
                            // Struktur percabangan untuk menentukan class badge berdasarkan kategori usia
                            $badge_class = '';
                            if ($kategori_usia == 'Anak-anak') {
                                $badge_class = 'kategori-anak';
                            } else if ($kategori_usia == 'Remaja') {
                                $badge_class = 'kategori-remaja';
                            } else if ($kategori_usia == 'Dewasa') {
                                $badge_class = 'kategori-dewasa';
                            } else {
                                $badge_class = 'kategori-lansia';
                            }
                            ?>
                            <span class="badge-kategori <?= $badge_class ?>"><?= $kategori_usia ?></span>
                        </p>
                        <p><strong>No. Telepon:</strong> <?= htmlspecialchars($data['No_telepon']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Kunjungan -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Kunjungan</h5>
            </div>
            <div class="card-body">
                <?php if (empty($riwayat_kunjungan)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Belum ada riwayat kunjungan untuk pasien ini.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Kunjungan</th>
                                    <th>Diagnosa</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Struktur pengulangan foreach untuk menampilkan data riwayat kunjungan
                                $no = 1;
                                foreach ($riwayat_kunjungan as $riwayat): 
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d F Y', strtotime($riwayat['tanggal_kunjungan'])) ?></td>
                                        <td><?= htmlspecialchars($riwayat['diagnosa']) ?></td>
                                        <td><?= htmlspecialchars($riwayat['tindakan']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Form Update Data</h5>
            </div>
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
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control" name="id_pasien" value="<?= htmlspecialchars($data['id_pasien']) ?>" readonly>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Nama Pasien</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control <?= isset($errors['Nama']) ? 'is-invalid' : '' ?>" 
                                       name="Nama" required pattern="[a-zA-Z\s]+" 
                                       value="<?= isset($_POST['Nama']) ? htmlspecialchars($_POST['Nama']) : htmlspecialchars($data['Nama']) ?>">
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
                                    <option value="L" <?= (isset($_POST['Jenis_kelamin']) ? $_POST['Jenis_kelamin'] == 'L' : $data['Jenis_kelamin'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= (isset($_POST['Jenis_kelamin']) ? $_POST['Jenis_kelamin'] == 'P' : $data['Jenis_kelamin'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
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
                                       value="<?= isset($_POST['Tanggal_lahir']) ? $_POST['Tanggal_lahir'] : htmlspecialchars($data['Tanggal_lahir']) ?>">
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
                                       value="<?= isset($_POST['No_telepon']) ? htmlspecialchars($_POST['No_telepon']) : htmlspecialchars($data['No_telepon']) ?>">
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
                                          name="Alamat" rows="1" required minlength="5"><?= isset($_POST['Alamat']) ? htmlspecialchars($_POST['Alamat']) : htmlspecialchars($data['Alamat']) ?></textarea>
                                <div class="invalid-feedback">
                                    <?= isset($errors['Alamat']) ? $errors['Alamat'] : 'Alamat harus diisi minimal 5 karakter' ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label required">Keluhan</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fas fa-notes-medical"></i></span>
                                <textarea class="form-control <?= isset($errors['Keluhan_pasien']) ? 'is-invalid' : '' ?>" 
                                          name="Keluhan_pasien" rows="3" required minlength="5"><?= isset($_POST['Keluhan_pasien']) ? htmlspecialchars($_POST['Keluhan_pasien']) : htmlspecialchars($data['Keluhan_pasien']) ?></textarea>
                                <div class="invalid-feedback">
                                    <?= isset($errors['Keluhan_pasien']) ? $errors['Keluhan_pasien'] : 'Keluhan harus diisi minimal 5 karakter' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Data
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