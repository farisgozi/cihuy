<?php
class Pelanggan {
    private $conn;
    private $table_name = "pelanggan";

    public $id_pelanggan;
    public $nama_pelanggan;
    public $jenis_kelamin;
    public $no_hp;
    public $alamat;

    public function __construct($db) {
        $this->conn = $db;
        
        // Buat stored procedure untuk operasi CRUD
        $this->createStoredProcedures();
    }

    private function createStoredProcedures() {
        // Stored Procedure untuk mendapatkan semua pelanggan
        $query = "DROP PROCEDURE IF EXISTS sp_get_all_pelanggan;
                  CREATE PROCEDURE sp_get_all_pelanggan()
                  BEGIN
                      SELECT * FROM pelanggan ORDER BY nama_pelanggan;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk mendapatkan pelanggan berdasarkan ID
        $query = "DROP PROCEDURE IF EXISTS sp_get_pelanggan_by_id;
                  CREATE PROCEDURE sp_get_pelanggan_by_id(IN pelanggan_id INT)
                  BEGIN
                      SELECT * FROM pelanggan WHERE id_pelanggan = pelanggan_id;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk membuat pelanggan baru
        $query = "DROP PROCEDURE IF EXISTS sp_create_pelanggan;
                  CREATE PROCEDURE sp_create_pelanggan(
                      IN p_nama_pelanggan VARCHAR(100),
                      IN p_jenis_kelamin BOOLEAN,
                      IN p_no_hp VARCHAR(15),
                      IN p_alamat VARCHAR(255)
                  )
                  BEGIN
                      INSERT INTO pelanggan(nama_pelanggan, jenis_kelamin, no_hp, alamat)
                      VALUES(p_nama_pelanggan, p_jenis_kelamin, p_no_hp, p_alamat);
                      SELECT LAST_INSERT_ID() as id_pelanggan;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk update pelanggan
        $query = "DROP PROCEDURE IF EXISTS sp_update_pelanggan;
                  CREATE PROCEDURE sp_update_pelanggan(
                      IN p_id_pelanggan INT,
                      IN p_nama_pelanggan VARCHAR(100),
                      IN p_jenis_kelamin BOOLEAN,
                      IN p_no_hp VARCHAR(15),
                      IN p_alamat VARCHAR(255)
                  )
                  BEGIN
                      UPDATE pelanggan 
                      SET nama_pelanggan = p_nama_pelanggan,
                          jenis_kelamin = p_jenis_kelamin,
                          no_hp = p_no_hp,
                          alamat = p_alamat
                      WHERE id_pelanggan = p_id_pelanggan;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk menghapus pelanggan
        $query = "DROP PROCEDURE IF EXISTS sp_delete_pelanggan;
                  CREATE PROCEDURE sp_delete_pelanggan(IN p_id_pelanggan INT)
                  BEGIN
                      DELETE FROM pelanggan WHERE id_pelanggan = p_id_pelanggan;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk mencari pelanggan berdasarkan nama
        $query = "DROP PROCEDURE IF EXISTS sp_search_pelanggan;
                  CREATE PROCEDURE sp_search_pelanggan(IN p_nama VARCHAR(100))
                  BEGIN
                      SELECT * FROM pelanggan 
                      WHERE nama_pelanggan LIKE CONCAT('%', p_nama, '%')
                      ORDER BY nama_pelanggan;
                  END";
        $this->conn->exec($query);
    }

    // Mendapatkan semua pelanggan
    public function getAll() {
        $stmt = $this->conn->prepare("CALL sp_get_all_pelanggan()");
        $stmt->execute();
        return $stmt;
    }

    // Mendapatkan pelanggan berdasarkan ID
    public function getById($id) {
        $stmt = $this->conn->prepare("CALL sp_get_pelanggan_by_id(?)");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    // Membuat pelanggan baru
    public function create() {
        $stmt = $this->conn->prepare("CALL sp_create_pelanggan(?, ?, ?, ?)");
        $stmt->bindParam(1, $this->nama_pelanggan);
        $stmt->bindParam(2, $this->jenis_kelamin);
        $stmt->bindParam(3, $this->no_hp);
        $stmt->bindParam(4, $this->alamat);
        
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_pelanggan = $row['id_pelanggan'];
            return true;
        }
        return false;
    }

    // Update pelanggan
    public function update() {
        $stmt = $this->conn->prepare("CALL sp_update_pelanggan(?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $this->id_pelanggan);
        $stmt->bindParam(2, $this->nama_pelanggan);
        $stmt->bindParam(3, $this->jenis_kelamin);
        $stmt->bindParam(4, $this->no_hp);
        $stmt->bindParam(5, $this->alamat);
        
        return $stmt->execute();
    }

    // Hapus pelanggan
    public function delete() {
        $stmt = $this->conn->prepare("CALL sp_delete_pelanggan(?)");
        $stmt->bindParam(1, $this->id_pelanggan);
        
        return $stmt->execute();
    }

    // Mencari pelanggan berdasarkan nama
    public function search($nama) {
        $stmt = $this->conn->prepare("CALL sp_search_pelanggan(?)");
        $stmt->bindParam(1, $nama);
        $stmt->execute();
        return $stmt;
    }
}
?>