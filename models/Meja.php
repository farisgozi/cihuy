<?php
class Meja {
    private $conn;
    private $table_name = "meja";

    public $id_meja;
    public $nomor_meja;

    public function __construct($db) {
        $this->conn = $db;
        
        // Buat stored procedure untuk operasi CRUD
        $this->createStoredProcedures();
    }

    private function createStoredProcedures() {
        // Stored Procedure untuk mendapatkan semua meja
        $query = "DROP PROCEDURE IF EXISTS sp_get_all_meja;
                  CREATE PROCEDURE sp_get_all_meja()
                  BEGIN
                      SELECT * FROM meja ORDER BY nomor_meja;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk mendapatkan meja berdasarkan ID
        $query = "DROP PROCEDURE IF EXISTS sp_get_meja_by_id;
                  CREATE PROCEDURE sp_get_meja_by_id(IN meja_id INT)
                  BEGIN
                      SELECT * FROM meja WHERE id_meja = meja_id;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk membuat meja baru
        $query = "DROP PROCEDURE IF EXISTS sp_create_meja;
                  CREATE PROCEDURE sp_create_meja(IN p_nomor_meja VARCHAR(20))
                  BEGIN
                      INSERT INTO meja(nomor_meja) VALUES(p_nomor_meja);
                      SELECT LAST_INSERT_ID() as id_meja;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk update meja
        $query = "DROP PROCEDURE IF EXISTS sp_update_meja;
                  CREATE PROCEDURE sp_update_meja(IN p_id_meja INT, IN p_nomor_meja VARCHAR(20))
                  BEGIN
                      UPDATE meja 
                      SET nomor_meja = p_nomor_meja 
                      WHERE id_meja = p_id_meja;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk menghapus meja
        $query = "DROP PROCEDURE IF EXISTS sp_delete_meja;
                  CREATE PROCEDURE sp_delete_meja(IN p_id_meja INT)
                  BEGIN
                      DELETE FROM meja WHERE id_meja = p_id_meja;
                  END";
        $this->conn->exec($query);
    }

    // Mendapatkan semua meja
    public function getAll() {
        $stmt = $this->conn->prepare("CALL sp_get_all_meja()");
        $stmt->execute();
        return $stmt;
    }

    // Mendapatkan meja berdasarkan ID
    public function getById($id) {
        $stmt = $this->conn->prepare("CALL sp_get_meja_by_id(?)");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    // Membuat meja baru
    public function create() {
        $stmt = $this->conn->prepare("CALL sp_create_meja(?)");
        $stmt->bindParam(1, $this->nomor_meja);
        
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_meja = $row['id_meja'];
            return true;
        }
        return false;
    }

    // Update meja
    public function update() {
        $stmt = $this->conn->prepare("CALL sp_update_meja(?, ?)");
        $stmt->bindParam(1, $this->id_meja);
        $stmt->bindParam(2, $this->nomor_meja);
        
        return $stmt->execute();
    }

    // Hapus meja
    public function delete() {
        $stmt = $this->conn->prepare("CALL sp_delete_meja(?)");
        $stmt->bindParam(1, $this->id_meja);
        
        return $stmt->execute();
    }
}
?>