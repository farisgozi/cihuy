<?php
class Pesanan {
    private $conn;
    private $table_name = "pesanan";

    public $id_pesanan;
    public $id_menu;
    public $id_pelanggan;
    public $jumlah;
    public $id_user;

    public function __construct($db) {
        $this->conn = $db;
        
        // Buat stored procedure untuk operasi CRUD
        $this->createStoredProcedures();
    }

    private function createStoredProcedures() {
        // Stored Procedure untuk mendapatkan semua pesanan dengan detail
        $query = "DROP PROCEDURE IF EXISTS sp_get_all_pesanan;
                  CREATE PROCEDURE sp_get_all_pesanan()
                  BEGIN
                      SELECT p.*, m.nama_menu, m.harga, pl.nama_pelanggan, u.nama_user
                      FROM pesanan p
                      LEFT JOIN menu m ON p.id_menu = m.id_menu
                      LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
                      LEFT JOIN users u ON p.id_user = u.id_user
                      ORDER BY p.id_pesanan DESC;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk mendapatkan pesanan berdasarkan ID
        $query = "DROP PROCEDURE IF EXISTS sp_get_pesanan_by_id;
                  CREATE PROCEDURE sp_get_pesanan_by_id(IN pesanan_id INT)
                  BEGIN
                      SELECT p.*, m.nama_menu, m.harga, pl.nama_pelanggan, u.nama_user
                      FROM pesanan p
                      LEFT JOIN menu m ON p.id_menu = m.id_menu
                      LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
                      LEFT JOIN users u ON p.id_user = u.id_user
                      WHERE p.id_pesanan = pesanan_id;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk membuat pesanan baru
        $query = "DROP PROCEDURE IF EXISTS sp_create_pesanan;
                  CREATE PROCEDURE sp_create_pesanan(
                      IN p_id_menu INT,
                      IN p_id_pelanggan INT,
                      IN p_jumlah INT,
                      IN p_id_user INT
                  )
                  BEGIN
                      INSERT INTO pesanan(id_menu, id_pelanggan, jumlah, id_user)
                      VALUES(p_id_menu, p_id_pelanggan, p_jumlah, p_id_user);
                      SELECT LAST_INSERT_ID() as id_pesanan;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk update pesanan
        $query = "DROP PROCEDURE IF EXISTS sp_update_pesanan;
                  CREATE PROCEDURE sp_update_pesanan(
                      IN p_id_pesanan INT,
                      IN p_id_menu INT,
                      IN p_id_pelanggan INT,
                      IN p_jumlah INT,
                      IN p_id_user INT
                  )
                  BEGIN
                      UPDATE pesanan 
                      SET id_menu = p_id_menu,
                          id_pelanggan = p_id_pelanggan,
                          jumlah = p_jumlah,
                          id_user = p_id_user
                      WHERE id_pesanan = p_id_pesanan;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk menghapus pesanan
        $query = "DROP PROCEDURE IF EXISTS sp_delete_pesanan;
                  CREATE PROCEDURE sp_delete_pesanan(IN p_id_pesanan INT)
                  BEGIN
                      DELETE FROM pesanan WHERE id_pesanan = p_id_pesanan;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk mendapatkan pesanan berdasarkan pelanggan
        $query = "DROP PROCEDURE IF EXISTS sp_get_pesanan_by_pelanggan;
                  CREATE PROCEDURE sp_get_pesanan_by_pelanggan(IN p_id_pelanggan INT)
                  BEGIN
                      SELECT p.*, m.nama_menu, m.harga, pl.nama_pelanggan, u.nama_user
                      FROM pesanan p
                      LEFT JOIN menu m ON p.id_menu = m.id_menu
                      LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
                      LEFT JOIN users u ON p.id_user = u.id_user
                      WHERE p.id_pelanggan = p_id_pelanggan
                      ORDER BY p.id_pesanan DESC;
                  END";
        $this->conn->exec($query);
    }

    // Mendapatkan semua pesanan
    public function getAll() {
        $stmt = $this->conn->prepare("CALL sp_get_all_pesanan()");
        $stmt->execute();
        return $stmt;
    }

    // Mendapatkan pesanan berdasarkan ID
    public function getById($id) {
        $stmt = $this->conn->prepare("CALL sp_get_pesanan_by_id(?)");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    // Membuat pesanan baru
    public function create() {
        $stmt = $this->conn->prepare("CALL sp_create_pesanan(?, ?, ?, ?)");
        $stmt->bindParam(1, $this->id_menu);
        $stmt->bindParam(2, $this->id_pelanggan);
        $stmt->bindParam(3, $this->jumlah);
        $stmt->bindParam(4, $this->id_user);
        
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_pesanan = $row['id_pesanan'];
            return true;
        }
        return false;
    }

    // Update pesanan
    public function update() {
        $stmt = $this->conn->prepare("CALL sp_update_pesanan(?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $this->id_pesanan);
        $stmt->bindParam(2, $this->id_menu);
        $stmt->bindParam(3, $this->id_pelanggan);
        $stmt->bindParam(4, $this->jumlah);
        $stmt->bindParam(5, $this->id_user);
        
        return $stmt->execute();
    }

    // Hapus pesanan
    public function delete() {
        $stmt = $this->conn->prepare("CALL sp_delete_pesanan(?)");
        $stmt->bindParam(1, $this->id_pesanan);
        
        return $stmt->execute();
    }

    // Mendapatkan pesanan berdasarkan pelanggan
    public function getByPelanggan($id_pelanggan) {
        $stmt = $this->conn->prepare("CALL sp_get_pesanan_by_pelanggan(?)");
        $stmt->bindParam(1, $id_pelanggan);
        $stmt->execute();
        return $stmt;
    }
}
?>