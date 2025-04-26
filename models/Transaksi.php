<?php
class Transaksi {
    private $conn;
    private $table_name = "transaksi";

    public $id_transaksi;
    public $id_pesanan;
    public $total;
    public $bayar;

    public function __construct($db) {
        $this->conn = $db;
        
        // Buat stored procedure untuk operasi CRUD
        $this->createStoredProcedures();
    }

    private function createStoredProcedures() {
        // Stored Procedure untuk mendapatkan semua transaksi dengan detail
        $query = "DROP PROCEDURE IF EXISTS sp_get_all_transaksi;
                  CREATE PROCEDURE sp_get_all_transaksi()
                  BEGIN
                      SELECT t.*, p.jumlah, m.nama_menu, m.harga, pl.nama_pelanggan
                      FROM transaksi t
                      LEFT JOIN pesanan p ON t.id_pesanan = p.id_pesanan
                      LEFT JOIN menu m ON p.id_menu = m.id_menu
                      LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
                      ORDER BY t.id_transaksi DESC;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk mendapatkan transaksi berdasarkan ID
        $query = "DROP PROCEDURE IF EXISTS sp_get_transaksi_by_id;
                  CREATE PROCEDURE sp_get_transaksi_by_id(IN transaksi_id INT)
                  BEGIN
                      SELECT t.*, p.jumlah, m.nama_menu, m.harga, pl.nama_pelanggan
                      FROM transaksi t
                      LEFT JOIN pesanan p ON t.id_pesanan = p.id_pesanan
                      LEFT JOIN menu m ON p.id_menu = m.id_menu
                      LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
                      WHERE t.id_transaksi = transaksi_id;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk membuat transaksi baru
        $query = "DROP PROCEDURE IF EXISTS sp_create_transaksi;
                  CREATE PROCEDURE sp_create_transaksi(
                      IN p_id_pesanan INT,
                      IN p_total DECIMAL(12,2),
                      IN p_bayar DECIMAL(12,2)
                  )
                  BEGIN
                      INSERT INTO transaksi(id_pesanan, total, bayar)
                      VALUES(p_id_pesanan, p_total, p_bayar);
                      SELECT LAST_INSERT_ID() as id_transaksi;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk update transaksi
        $query = "DROP PROCEDURE IF EXISTS sp_update_transaksi;
                  CREATE PROCEDURE sp_update_transaksi(
                      IN p_id_transaksi INT,
                      IN p_total DECIMAL(12,2),
                      IN p_bayar DECIMAL(12,2)
                  )
                  BEGIN
                      UPDATE transaksi 
                      SET total = p_total,
                          bayar = p_bayar
                      WHERE id_transaksi = p_id_transaksi;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk menghapus transaksi
        $query = "DROP PROCEDURE IF EXISTS sp_delete_transaksi;
                  CREATE PROCEDURE sp_delete_transaksi(IN p_id_transaksi INT)
                  BEGIN
                      DELETE FROM transaksi WHERE id_transaksi = p_id_transaksi;
                  END";
        $this->conn->exec($query);

        // Stored Procedure untuk menghitung kembalian
        $query = "DROP PROCEDURE IF EXISTS sp_hitung_kembalian;
                  CREATE PROCEDURE sp_hitung_kembalian(IN p_id_transaksi INT)
                  BEGIN
                      SELECT (bayar - total) as kembalian
                      FROM transaksi
                      WHERE id_transaksi = p_id_transaksi;
                  END";
        $this->conn->exec($query);
    }

    // Mendapatkan semua transaksi
    public function getAll() {
        $stmt = $this->conn->prepare("CALL sp_get_all_transaksi()");
        $stmt->execute();
        return $stmt;
    }

    // Mendapatkan transaksi berdasarkan ID
    public function getById($id) {
        $stmt = $this->conn->prepare("CALL sp_get_transaksi_by_id(?)");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    // Membuat transaksi baru
    public function create() {
        $stmt = $this->conn->prepare("CALL sp_create_transaksi(?, ?, ?)");
        $stmt->bindParam(1, $this->id_pesanan);
        $stmt->bindParam(2, $this->total);
        $stmt->bindParam(3, $this->bayar);
        
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_transaksi = $row['id_transaksi'];
            return true;
        }
        return false;
    }

    // Update transaksi
    public function update() {
        $stmt = $this->conn->prepare("CALL sp_update_transaksi(?, ?, ?)");
        $stmt->bindParam(1, $this->id_transaksi);
        $stmt->bindParam(2, $this->total);
        $stmt->bindParam(3, $this->bayar);
        
        return $stmt->execute();
    }

    // Hapus transaksi
    public function delete() {
        $stmt = $this->conn->prepare("CALL sp_delete_transaksi(?)");
        $stmt->bindParam(1, $this->id_transaksi);
        
        return $stmt->execute();
    }

    // Hitung kembalian
    public function hitungKembalian() {
        $stmt = $this->conn->prepare("CALL sp_hitung_kembalian(?)");
        $stmt->bindParam(1, $this->id_transaksi);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['kembalian'];
    }
}
?>