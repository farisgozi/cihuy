<?php
// Inisialisasi koneksi database
$database = new Database();
$db = $database->getConnection();

// Mendapatkan statistik untuk dashboard
try {

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
    <br>

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
                        <?php if ($_SESSION['user_role'] === 'administrator'): ?>
                            <a href="index.php?page=menu" class="btn btn-info text-white">
                                <i class="bi bi-menu-button-wide"></i> Kelola Menu
                            </a>
                            <a href="index.php?page=meja" class="btn btn-primary">
                                <i class="bi bi-table"></i> Kelola Meja
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>