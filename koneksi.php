<?php
namespace App\Config;

class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "db_pasien";
    public $conn;

    public function __construct() {
        // Coba koneksi ke MySQL tanpa database
        $this->conn = new \mysqli($this->host, $this->user, $this->pass);
        
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }

        // Cek apakah database sudah ada
        if (!$this->databaseExists()) {
            $this->createDatabase();
            $this->importSQL();
        }

        // Koneksi ke database yang sudah ada
        $this->conn->select_db($this->db);
    }

    private function databaseExists() {
        $result = $this->conn->query("SHOW DATABASES LIKE '{$this->db}'");
        return $result->num_rows > 0;
    }

    private function createDatabase() {
        if (!$this->conn->query("CREATE DATABASE {$this->db}")) {
            die("Error creating database: " . $this->conn->error);
        }
        $this->conn->select_db($this->db);
    }

    private function importSQL() {
        // Baca file SQL
        $sql_file = _DIR_ . '/../../pasien.sql';
        
        if (!file_exists($sql_file)) {
            die("File SQL tidak ditemukan di: " . $sql_file);
        }

        $sql = file_get_contents($sql_file);
        
        // Split SQL berdasarkan delimiter
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        // Eksekusi setiap query
        foreach ($queries as $query) {
            if (!empty($query)) {
                if (!$this->conn->query($query)) {
                    die("Error importing SQL: " . $this->conn->error . "\nQuery: " . $query);
                }
            }
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}