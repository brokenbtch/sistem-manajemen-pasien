<?php
require '../autoload.php';

use App\Config\Database;
use src\Model\Pasien;

$db = new Database();
$conn = $db->getConnection();
$pasien = new Pasien($conn);

// Ambil antrian hari ini
$query = "SELECT a.*, p.Nama, p.Keluhan_pasien 
          FROM antrian_pasien a 
          JOIN pasien p ON a.id_pasien = p.id_pasien 
          WHERE DATE(a.tanggal_antrian) = CURDATE()
          ORDER BY a.waktu_daftar ASC";
$result = mysqli_query($conn, $query);
$antrian = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Ambil daftar pasien yang belum masuk antrian hari ini
$query_pasien = "SELECT p.* FROM pasien p 
                 WHERE p.id_pasien NOT IN (
                     SELECT a.id_pasien 
                     FROM antrian_pasien a 
                     WHERE DATE(a.tanggal_antrian) = CURDATE()
                 )";
$result_pasien = mysqli_query($conn, $query_pasien);
$pasien_list = mysqli_fetch_all($result_pasien, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Pasien - Sistem Manajemen Rumah Sakit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Style sama seperti sebelumnya -->
</head>
<body class="bg-light">
    <!-- Navbar sama seperti sebelumnya -->

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2><i class="fas fa-list-ol me-2"></i>Antrian Pasien</h2>
            </div>
            <div class="col-md-6 text-md-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahAntrianModal">
                    <i class="fas fa-plus me-2"></i>Tambah ke Antrian
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No. Antrian</th>
                                <th>Nama Pasien</th>
                                <th>Keluhan</th>
                                <th>Status</th>
                                <th>Waktu Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($antrian as $item): ?>
                                <tr>
                                    <td><?= $item['id_antrian'] ?></td>
                                    <td><?= htmlspecialchars($item['Nama']) ?></td>
                                    <td><?= htmlspecialchars($item['Keluhan_pasien']) ?></td>
                                    <td>
                                        <?php
                                        $badge_class = match($item['status']) {
                                            'menunggu' => 'bg-warning',
                                            'diperiksa' => 'bg-info',
                                            'selesai' => 'bg-success',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= ucfirst($item['status']) ?></span>
                                    </td>
                                    <td><?= date('H:i', strtotime($item['waktu_daftar'])) ?></td>
                                    <td>
                                        <?php if($item['status'] === 'menunggu'): ?>
                                            <button class="btn btn-sm btn-danger" onclick="batalkanAntrian(<?= $item['id_antrian'] ?>)">
                                                <i class="fas fa-times"></i> Batalkan
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Antrian -->
    <div class="modal fade" id="tambahAntrianModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah ke Antrian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formTambahAntrian" action="tambah_antrian.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Pilih Pasien</label>
                            <select class="form-select" name="id_pasien" required>
                                <option value="">Pilih Pasien...</option>
                                <?php if (empty($pasien_list)): ?>
                                    <option disabled>Semua pasien sudah dalam antrian</option>
                                <?php else: ?>
                                    <?php foreach ($pasien_list as $p): ?>
                                        <option value="<?= $p['id_pasien'] ?>">
                                            <?= htmlspecialchars($p['Nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" form="formTambahAntrian" class="btn btn-primary" 
                            <?= empty($pasien_list) ? 'disabled' : '' ?>>
                        Tambah ke Antrian
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer sama seperti sebelumnya -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function batalkanAntrian(idAntrian) {
        if (confirm('Yakin ingin membatalkan antrian ini?')) {
            fetch('batalkan_antrian.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_antrian=' + idAntrian
            })
            .then(response => response.text())
            .then(result => {
                if (result === 'success') {
                    // Reload halaman setelah berhasil
                    window.location.reload();
                } else {
                    alert('Gagal membatalkan antrian');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }
    }
    </script>
</body>
</html> 