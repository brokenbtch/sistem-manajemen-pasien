<?php
namespace src\Model;

class Pasien {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll() {
        $query = "SELECT * FROM pasien";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function tambahPasien($data) {
        $stmt = $this->conn->prepare("INSERT INTO pasien (id_pasien, Nama, Jenis_kelamin, Tanggal_lahir, No_telepon, Alamat, Keluhan_pasien) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", 
            $data['id_pasien'], 
            $data['Nama'], 
            $data['Jenis_kelamin'], 
            $data['Tanggal_lahir'], 
            $data['No_telepon'], 
            $data['Alamat'], 
            $data['Keluhan_pasien']
        );
        return $stmt->execute();
    }

    public function updatePasien($id, $data) {
        $stmt = $this->conn->prepare("UPDATE pasien SET 
            Nama = ?, 
            Jenis_kelamin = ?, 
            Tanggal_lahir = ?, 
            No_telepon = ?, 
            Alamat = ?, 
            Keluhan_pasien = ? 
            WHERE id_pasien = ?");
        
        $stmt->bind_param("ssssssi", 
            $data['Nama'],
            $data['Jenis_kelamin'],
            $data['Tanggal_lahir'],
            $data['No_telepon'],
            $data['Alamat'],
            $data['Keluhan_pasien'],
            $id
        );
        
        return $stmt->execute();
    }

    public function deletePasien($id) {
        $stmt = $this->conn->prepare("DELETE FROM pasien WHERE id_pasien = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
