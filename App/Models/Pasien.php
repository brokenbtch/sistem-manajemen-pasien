class Pasien {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll() {
        // query untuk ambil data
        $query = "SELECT * FROM pasien";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    
    // method lainnya...
} 