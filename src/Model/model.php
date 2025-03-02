<?php
namespace App\Models;

class Model {
    protected $db;
    protected $table;

    public function __construct($database, $table) {
        $this->db = $database->conn;
        $this->table = $table;
    }

    public function getAll() {
        $query = $this->db->query("SELECT * FROM {$this->table}");
        return $query->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_pasien = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function deleteById($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id_pasien = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }
}
