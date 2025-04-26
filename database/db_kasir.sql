-- Database: db_kasir_resto_pdm_strict

-- Tabel untuk Pengguna Sistem (Sesuai PDM Asli)
-- PERINGATAN: Tidak ada kolom username, password, atau role.
-- Ini akan menyebabkan masalah besar untuk implementasi Login dan Hak Akses.
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_user VARCHAR(100) -- Disesuaikan dari CHARACTER(0) di PDM menjadi VARCHAR
    -- Tidak ada username
    -- Tidak ada password
    -- Tidak ada role
);

-- Tabel untuk Meja Restoran (Tambahan Wajib untuk Fitur "Entri Meja")
CREATE TABLE meja (
    id_meja INT AUTO_INCREMENT PRIMARY KEY,
    nomor_meja VARCHAR(20) NOT NULL UNIQUE -- Kolom utama untuk identifikasi meja
);

-- Tabel untuk Menu Makanan/Minuman (Sesuai PDM Asli)
CREATE TABLE menu (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    nama_menu VARCHAR(100), -- Disesuaikan dari CHARACTER(0) di PDM menjadi VARCHAR
    harga DECIMAL(10, 2) -- Disesuaikan dari INTEGER di PDM menjadi DECIMAL untuk uang
);

-- Tabel untuk Pelanggan (Sesuai PDM Asli)
-- Catatan: Tidak terhubung langsung ke alur pesanan meja dalam struktur ini.
CREATE TABLE pelanggan (
    id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100), -- Disesuaikan dari CHARACTER(0)
    jenis_kelamin BOOLEAN, -- Sesuai PDM (0 atau 1, perlu didefinisikan artinya di aplikasi)
    no_hp VARCHAR(15), -- Disesuaikan dari CHARACTER(13)
    alamat VARCHAR(255) -- Disesuaikan dari CHARACTER(95)
);

-- Tabel untuk Pesanan (Sesuai PDM Asli)
-- PERINGATAN: Menghubungkan ke idpelanggan, bukan id_meja.
-- Ini akan menyulitkan implementasi alur table service.
CREATE TABLE pesanan (
    id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
    id_menu INT,
    id_pelanggan INT, -- Sesuai PDM, BUKAN id_meja
    jumlah INT,
    id_user INT, -- Asumsi ini adalah id_user waiter yang mencatat
    FOREIGN KEY (id_menu) REFERENCES menu(id_menu) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Tabel untuk Transaksi (Sesuai PDM Asli)
-- PERINGATAN: Menghubungkan ke satu idpesanan.
-- Ini akan menyulitkan proses pembayaran untuk seluruh item pesanan di satu meja.
CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_pesanan INT UNIQUE, -- Hanya bisa terhubung ke satu baris/item pesanan
    total DECIMAL(12, 2), -- Disesuaikan dari INTEGER ke DECIMAL
    bayar DECIMAL(12, 2), -- Disesuaikan dari INTEGER ke DECIMAL
    -- Tidak ada id_user_kasir
    -- Tidak ada kembalian (bisa dihitung di aplikasi)
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE ON UPDATE CASCADE -- Jika pesanan dihapus, transaksi terkait dihapus
);

-- Contoh data awal untuk users (jika diperlukan untuk pengujian awal, tapi tanpa role)
-- INSERT INTO users (nama_user) VALUES ('Admin Utama'), ('Waiter Budi'), ('Kasir Siti'), ('Pemilik Resto');
-- INGAT: Aplikasi tidak akan tahu peran mereka dari data ini saja.

