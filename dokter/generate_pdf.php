<?php
require '../autoload.php';
require_once('../tcpdf/tcpdf.php');

use App\Config\Database;

if (isset($_GET['id'])) {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Ambil data pemeriksaan
    $query = "SELECT p.*, a.tanggal_antrian, a.waktu_daftar,
              ps.Nama, ps.Jenis_kelamin, ps.Tanggal_lahir, ps.No_telepon, ps.Alamat
              FROM pemeriksaan p
              JOIN antrian_pasien a ON p.id_antrian = a.id_antrian
              JOIN pasien ps ON a.id_pasien = ps.id_pasien
              WHERE p.id_pemeriksaan = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        // Buat PDF
        class MYPDF extends TCPDF {
            public function Header() {
                $this->SetFont('helvetica', 'B', 16);
                $this->Cell(0, 15, 'HASIL PEMERIKSAAN PASIEN', 0, true, 'C');
                $this->SetFont('helvetica', '', 10);
                $this->Cell(0, 10, 'Klinik Dokter Umum', 0, true, 'C');
                $this->Ln(10);
            }
        }

        $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Sistem Manajemen Rumah Sakit');
        $pdf->SetAuthor('Dokter');
        $pdf->SetTitle('Hasil Pemeriksaan - ' . $data['Nama']);

        // Set margins
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetHeaderMargin(10);

        $pdf->AddPage();

        // Info Pasien
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'DATA PASIEN', 0, true, 'L');
        $pdf->SetFont('helvetica', '', 10);
        
        // Data pasien dalam format tabel
        $w1 = 40; // width label
        $w2 = 5;  // width separator
        $w3 = 0;  // width value (sisanya)
        
        $pdf->Cell($w1, 7, 'Nama', 0, 0);
        $pdf->Cell($w2, 7, ':', 0, 0);
        $pdf->Cell($w3, 7, $data['Nama'], 0, 1);
        
        $pdf->Cell($w1, 7, 'Jenis Kelamin', 0, 0);
        $pdf->Cell($w2, 7, ':', 0, 0);
        $pdf->Cell($w3, 7, ($data['Jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'), 0, 1);
        
        $pdf->Cell($w1, 7, 'Tanggal Lahir', 0, 0);
        $pdf->Cell($w2, 7, ':', 0, 0);
        $pdf->Cell($w3, 7, date('d/m/Y', strtotime($data['Tanggal_lahir'])), 0, 1);
        
        $pdf->Cell($w1, 7, 'No. Telepon', 0, 0);
        $pdf->Cell($w2, 7, ':', 0, 0);
        $pdf->Cell($w3, 7, $data['No_telepon'], 0, 1);
        
        $pdf->Cell($w1, 7, 'Alamat', 0, 0);
        $pdf->Cell($w2, 7, ':', 0, 0);
        $pdf->Cell($w3, 7, $data['Alamat'], 0, 1);
        
        $pdf->Ln(10);

        // Hasil Pemeriksaan
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'HASIL PEMERIKSAAN', 0, true, 'L');
        $pdf->SetFont('helvetica', '', 10);
        
        $pdf->Cell($w1, 7, 'Tanggal', 0, 0);
        $pdf->Cell($w2, 7, ':', 0, 0);
        $pdf->Cell($w3, 7, date('d/m/Y', strtotime($data['tanggal_antrian'])), 0, 1);
        
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, 'Diagnosa:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 7, $data['diagnosa'], 0, 'L');
        
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, 'Resep:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 7, $data['resep'], 0, 'L');
        
        if (!empty($data['catatan_dokter'])) {
            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 7, 'Catatan Tambahan:', 0, 1);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 7, $data['catatan_dokter'], 0, 'L');
        }
        
        // Tanda tangan
        $pdf->Ln(20);
        $pdf->Cell(0, 7, 'Dokter Pemeriksa,', 0, 1, 'R');
        $pdf->Ln(15);
        $pdf->Cell(0, 7, 'dr. ________________', 0, 1, 'R');

        // Output PDF
        $pdf->Output('Hasil_Pemeriksaan_' . $data['Nama'] . '.pdf', 'I');
    } else {
        echo "Data pemeriksaan tidak ditemukan";
    }
} else {
    echo "ID Pemeriksaan tidak ditemukan";
} 