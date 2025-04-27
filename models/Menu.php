<?php
class Menu {
    private $conn;
    private $table_name = "menu";

    public $id_menu;
    public $nama_menu;
    public $harga;

    public function __construct($db) {
        $this->conn = $db;
        
        // Buat stored procedure untuk operasi CRUD
        $this->createStoredProcedures();
    }

    private function createStoredProcedures() {
        // Stored Procedure untuk mendapatkan semua menu
        $query = "DROP PROCEDURE IF EXISTS sp_get_all_menu;
                  CREATE PROCEDURE sp_get_all_menu()
                  BEGIN
                      SELECT * FROM menu ORDER BY nama_menu;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk mendapatkan menu berdasarkan ID
        $query = "DROP PROCEDURE IF EXISTS sp_get_menu_by_id;
                  CREATE PROCEDURE sp_get_menu_by_id(IN menu_id INT)
                  BEGIN
                      SELECT * FROM menu WHERE id_menu = menu_id;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk membuat menu baru
        $query = "DROP PROCEDURE IF EXISTS sp_create_menu;
                  CREATE PROCEDURE sp_create_menu(IN p_nama_menu VARCHAR(100), IN p_harga DECIMAL(10,2))
                  BEGIN
                      INSERT INTO menu(nama_menu, harga) VALUES(p_nama_menu, p_harga);
                      SELECT LAST_INSERT_ID() as id_menu;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk update menu
        $query = "DROP PROCEDURE IF EXISTS sp_update_menu;
                  CREATE PROCEDURE sp_update_menu(IN p_id_menu INT, IN p_nama_menu VARCHAR(100), IN p_harga DECIMAL(10,2))
                  BEGIN
                      UPDATE menu 
                      SET nama_menu = p_nama_menu, 
                          harga = p_harga 
                      WHERE id_menu = p_id_menu;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk menghapus menu
        $query = "DROP PROCEDURE IF EXISTS sp_delete_menu;
                  CREATE PROCEDURE sp_delete_menu(IN p_id_menu INT)
                  BEGIN
                      DELETE FROM menu WHERE id_menu = p_id_menu;
                  END";
        $this->conn->exec($query);
    }

    // Mendapatkan semua menu
    public function getAll() {
        $stmt = $this->conn->prepare("CALL sp_get_all_menu()");
        $stmt->execute();
        return $stmt;
    }

    // Mendapatkan menu berdasarkan ID
    public function getById($id) {
        $stmt = $this->conn->prepare("CALL sp_get_menu_by_id(?)");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    // Membuat menu baru
    public function create() {
        $stmt = $this->conn->prepare("CALL sp_create_menu(?, ?)");
        $stmt->bindParam(1, $this->nama_menu);
        $stmt->bindParam(2, $this->harga);
        
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_menu = $row['id_menu'];
            return true;
        }
        return false;
    }

    // Update menu
    public function update() {
        $stmt = $this->conn->prepare("CALL sp_update_menu(?, ?, ?)");
        $stmt->bindParam(1, $this->id_menu);
        $stmt->bindParam(2, $this->nama_menu);
        $stmt->bindParam(3, $this->harga);
        
        return $stmt->execute();
    }

    // Hapus menu
    public function delete() {
        $stmt = $this->conn->prepare("CALL sp_delete_menu(?)");
        $stmt->bindParam(1, $this->id_menu);
        
        return $stmt->execute();
    }

    // Alias untuk getAll()
    public function read() {
        return $this->getAll();
    }
}
?>