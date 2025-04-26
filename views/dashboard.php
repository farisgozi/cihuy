<?php
// Inisialisasi koneksi database
$database = new Database();
$db = $database->getConnection();

// Mendapatkan statistik untuk dashboard
try {
    // Total pesanan hari ini
    $query = "SELECT COUNT(*) as total_pesanan FROM pesanan WHERE DATE(created_at) = CURDATE()";
    $stmt = $db->query($query);
    $total_pesanan = $stmt->fetch(PDO::FETCH_ASSOC)['total_pesanan'] ?? 0;

    // Total pendapatan hari ini
    $query = "SELECT SUM(total) as total_pendapatan FROM transaksi 
             WHERE DATE(created_at) = CURDATE()";
    $stmt = $db->query($query);
    $total_pendapatan = $stmt->fetch(PDO::FETCH_ASSOC)['total_pendapatan'] ?? 0;

    // Menu terlaris
    $query = "SELECT m.nama_menu, COUNT(p.id_menu) as jumlah_pesanan 
             FROM pesanan p 
             JOIN menu m ON p.id_menu = m.id_menu 
             GROUP BY p.id_menu 
             ORDER BY jumlah_pesanan DESC 
             LIMIT 5";
    $stmt = $db->query($query);
    $menu_terlaris = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Dashboard</h2>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Pesanan Hari Ini</h5>
                    <h2 class="card-text"><?php echo $total_pesanan; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Pendapatan Hari Ini</h5>
                    <h2 class="card-text">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Menu Terlaris</h5>
                    <p class="card-text">
                        <?php if (!empty($menu_terlaris)): ?>
                            <?php echo htmlspecialchars($menu_terlaris[0]['nama_menu']); ?>
                            <span class="badge bg-white text-info">
                                <?php echo $menu_terlaris[0]['jumlah_pesanan']; ?> pesanan
                            </span>
                        <?php else: ?>
                            Belum ada data
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Menu Terlaris -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">5 Menu Terlaris</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Jumlah Pesanan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menu_terlaris as $menu): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($menu['nama_menu']); ?></td>
                                    <td><?php echo $menu['jumlah_pesanan']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($menu_terlaris)): ?>
                                <tr>
                                    <td colspan="2" class="text-center">Belum ada data</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="index.php?page=pesanan&action=create" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Buat Pesanan Baru
                        </a>
                        <a href="index.php?page=transaksi" class="btn btn-success">
                            <i class="bi bi-cash"></i> Lihat Transaksi
                        </a>
                        <a href="index.php?page=menu" class="btn btn-info text-white">
                            <i class="bi bi-menu-button-wide"></i> Kelola Menu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>