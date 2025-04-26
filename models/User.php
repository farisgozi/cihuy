<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id_user;
    public $nama_user;
    public $username;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
        
        // Buat stored procedure untuk operasi CRUD jika belum ada
        $this->createStoredProcedures();
    }

    private function createStoredProcedures() {
        // Stored Procedure untuk mendapatkan semua user
        $query = "DROP PROCEDURE IF EXISTS sp_get_all_users;
                  CREATE PROCEDURE sp_get_all_users()
                  BEGIN
                      SELECT * FROM users;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk mendapatkan user berdasarkan ID
        $query = "DROP PROCEDURE IF EXISTS sp_get_user_by_id;
                  CREATE PROCEDURE sp_get_user_by_id(IN user_id INT)
                  BEGIN
                      SELECT * FROM users WHERE id_user = user_id;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk membuat user baru
        $query = "DROP PROCEDURE IF EXISTS sp_create_user;
                  CREATE PROCEDURE sp_create_user(IN p_nama_user VARCHAR(100))
                  BEGIN
                      INSERT INTO users(nama_user) VALUES(p_nama_user);
                      SELECT LAST_INSERT_ID() as id_user;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk update user
        $query = "DROP PROCEDURE IF EXISTS sp_update_user;
                  CREATE PROCEDURE sp_update_user(IN p_id_user INT, IN p_nama_user VARCHAR(100))
                  BEGIN
                      UPDATE users SET nama_user = p_nama_user WHERE id_user = p_id_user;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk menghapus user
        $query = "DROP PROCEDURE IF EXISTS sp_delete_user;
                  CREATE PROCEDURE sp_delete_user(IN p_id_user INT)
                  BEGIN
                      DELETE FROM users WHERE id_user = p_id_user;
                  END";
        $this->conn->exec($query);
    }

    // Mendapatkan semua user
    public function getAll() {
        $stmt = $this->conn->prepare("CALL sp_get_all_users()");
        $stmt->execute();
        return $stmt;
    }

    // Mendapatkan user berdasarkan ID
    public function getById($id) {
        $stmt = $this->conn->prepare("CALL sp_get_user_by_id(?)");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    // Membuat user baru
    public function create() {
        $stmt = $this->conn->prepare("CALL sp_create_user(?)");
        $stmt->bindParam(1, $this->nama_user);
        
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_user = $row['id_user'];
            return true;
        }
        return false;
    }

    // Update user
    public function update() {
        $stmt = $this->conn->prepare("CALL sp_update_user(?, ?)");
        $stmt->bindParam(1, $this->id_user);
        $stmt->bindParam(2, $this->nama_user);
        
        return $stmt->execute();
    }

    // Hapus user
    public function delete() {
        $stmt = $this->conn->prepare("CALL sp_delete_user(?)");
        $stmt->bindParam(1, $this->id_user);
        
        return $stmt->execute();
    }
}
?>