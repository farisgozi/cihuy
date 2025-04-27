<?php
// Pastikan hanya administrator yang bisa mengakses halaman ini
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrator') {
    header("Location: index.php?page=dashboard");
    exit;
}

// Inisialisasi koneksi database dan objek menu
$database = new Database();
$db = $database->getConnection();
$menu = new Menu($db);

// Proses form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'create':
                    if (empty($_POST['nama_menu'])) {
                        throw new Exception("Nama menu tidak boleh kosong");
                    }
                    if (empty($_POST['harga']) || !is_numeric($_POST['harga']) || $_POST['harga'] < 0) {
                        throw new Exception("Harga harus berupa angka positif");
                    }
                    
                    $menu->nama_menu = htmlspecialchars(strip_tags($_POST['nama_menu']));
                    $menu->harga = (float)htmlspecialchars(strip_tags($_POST['harga']));
                    
                    if ($menu->create()) {
                        echo "<div class='alert alert-success'>
                                <i class='bi bi-check-circle me-2'></i>
                                Menu <strong>{$menu->nama_menu}</strong> berhasil ditambahkan.
                              </div>";
                    } else {
                        throw new Exception("Gagal menambahkan menu. Silakan coba lagi.");
                    }
                    break;
                    
                case 'update':
                    if (empty($_POST['id_menu'])) {
                        throw new Exception("ID menu tidak valid");
                    }
                    if (empty($_POST['nama_menu'])) {
                        throw new Exception("Nama menu tidak boleh kosong");
                    }
                    if (empty($_POST['harga']) || !is_numeric($_POST['harga']) || $_POST['harga'] < 0) {
                        throw new Exception("Harga harus berupa angka positif");
                    }
                    
                    $menu->id_menu = (int)$_POST['id_menu'];
                    $menu->nama_menu = htmlspecialchars(strip_tags($_POST['nama_menu']));
                    $menu->harga = (float)htmlspecialchars(strip_tags($_POST['harga']));
                    
                    if ($menu->update()) {
                        echo "<div class='alert alert-success'>
                                <i class='bi bi-check-circle me-2'></i>
                                Menu <strong>{$menu->nama_menu}</strong> berhasil diperbarui.
                              </div>";
                    } else {
                        throw new Exception("Gagal memperbarui menu. Silakan coba lagi.");
                    }
                    break;
                    
                case 'delete':
                    if (empty($_POST['id_menu'])) {
                        throw new Exception("ID menu tidak valid");
                    }
                    
                    $menu->id_menu = (int)$_POST['id_menu'];
                    if ($menu->delete()) {
                        echo "<div class='alert alert-success'>
                                <i class='bi bi-check-circle me-2'></i>
                                Menu berhasil dihapus.
                              </div>";
                    } else {
                        throw new Exception("Gagal menghapus menu. Silakan coba lagi.");
                    }
                    break;
                
                default:
                    throw new Exception("Operasi tidak valid");
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>
                    <i class='bi bi-exclamation-triangle me-2'></i>
                    {$e->getMessage()}
                  </div>";
        }
    }
}

// Ambil semua data menu
$stmt = $menu->read();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Menu</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMenuModal">
            <i class="bi bi-plus-circle"></i> Tambah Menu
        </button>
    </div>

    <!-- Tabel Menu -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Menu</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_menu']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_menu']); ?></td>
                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info text-white" 
                                        onclick="editMenu(<?php echo $row['id_menu']; ?>, '<?php echo addslashes($row['nama_menu']); ?>', <?php echo $row['harga']; ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="deleteMenu(<?php echo $row['id_menu']; ?>, '<?php echo addslashes($row['nama_menu']); ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Menu -->
<div class="modal fade" id="addMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Menu Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label for="nama_menu" class="form-label">Nama Menu</label>
                        <input type="text" class="form-control" id="nama_menu" name="nama_menu" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Menu -->
<div class="modal fade" id="editMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id_menu" id="edit_id_menu">
                    <div class="mb-3">
                        <label for="edit_nama_menu" class="form-label">Nama Menu</label>
                        <input type="text" class="form-control" id="edit_nama_menu" name="nama_menu" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="edit_harga" name="harga" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id_menu" id="delete_id_menu">
                    <p>Apakah Anda yakin ingin menghapus menu <span id="delete_menu_name"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editMenu(id, nama, harga) {
    document.getElementById('edit_id_menu').value = id;
    document.getElementById('edit_nama_menu').value = nama;
    document.getElementById('edit_harga').value = harga;
    new bootstrap.Modal(document.getElementById('editMenuModal')).show();
}

function deleteMenu(id, nama) {
    document.getElementById('delete_id_menu').value = id;
    document.getElementById('delete_menu_name').textContent = nama;
    new bootstrap.Modal(document.getElementById('deleteMenuModal')).show();
}
</script>