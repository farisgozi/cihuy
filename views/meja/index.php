<?php
// Pastikan hanya administrator yang bisa mengakses halaman ini
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrator') {
    header("Location: index.php?page=dashboard");
    exit;
}

// Inisialisasi koneksi database dan objek meja
$database = new Database();
$db = $database->getConnection();
$meja = new Meja($db);

// Proses form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'create':
                    if (empty($_POST['nomor_meja'])) {
                        throw new Exception("Nomor meja tidak boleh kosong");
                    }
                    
                    $meja->nomor_meja = (int)htmlspecialchars(strip_tags($_POST['nomor_meja']));
                    
                    if ($meja->create()) {
                        echo "<div class='alert alert-success'>
                                <i class='bi bi-check-circle me-2'></i>
                                Meja <strong>{$meja->nomor_meja}</strong> berhasil ditambahkan.
                              </div>";
                    } else {
                        throw new Exception("Gagal menambahkan meja. Silakan coba lagi.");
                    }
                    break;
                    
                case 'update':
                    if (empty($_POST['id_meja'])) {
                        throw new Exception("ID meja tidak valid");
                    }
                    if (empty($_POST['nomor_meja']) || !is_numeric($_POST['nomor_meja']) || $_POST['nomor_meja'] < 0) {
                        throw new Exception("Nomor meja harus dimulai dari setidaknya 1");
                    }
                    
                    $meja->id_meja = (int)$_POST['id_meja'];
                    $meja->nomor_meja = (float)htmlspecialchars(strip_tags($_POST['nomor_meja']));
                    
                    if ($meja->update()) {
                        echo "<div class='alert alert-success'>
                                <i class='bi bi-check-circle me-2'></i>
                                Meja <strong>{$meja->nomor_meja}</strong> berhasil diperbarui.
                              </div>";
                    } else {
                        throw new Exception("Gagal memperbarui meja. Silakan coba lagi.");
                    }
                    break;
                    
                case 'delete':
                    if (empty($_POST['id_meja'])) {
                        throw new Exception("ID meja tidak valid");
                    }
                    
                    $meja->id_meja = (int)$_POST['id_meja'];
                    if ($meja->delete()) {
                        echo "<div class='alert alert-success'>
                                <i class='bi bi-check-circle me-2'></i>
                                Meja berhasil dihapus.
                              </div>";
                    } else {
                        throw new Exception("Gagal menghapus meja. Silakan coba lagi.");
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

// Ambil semua data meja
$stmt = $meja->read();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Meja</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMejaModal">
            <i class="bi bi-plus-circle"></i> Tambah Meja
        </button>
    </div>

    <!-- Tabel Meja -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nomor Meja</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nomor_meja']); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info text-white" 
                                        onclick="editMeja(<?php echo $row['id_meja']; ?>, '<?php echo addslashes($row['nomor_meja']); ?>')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="deleteMeja(<?php echo $row['id_meja']; ?>, '<?php echo addslashes($row['nomor_meja']); ?>')">
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

<!-- Modal Tambah Meja -->
<div class="modal fade" id="addMejaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Meja Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label for="nomor_meja" class="form-label">Nomor Meja</label>
                        <input type="number" class="form-control" id="nomor_meja" name="nomor_meja" required>
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

<!-- Modal Edit Meja -->
<div class="modal fade" id="editMejaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Meja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id_meja" id="edit_id_meja">
                    <div class="mb-3">
                        <label for="edit_nomor_meja" class="form-label">Nomor Meja</label>
                        <input type="number" class="form-control" id="edit_nomor_meja" name="nomor_meja" required>
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
<div class="modal fade" id="deleteMejaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id_meja" id="delete_id_meja">
                    <p>Apakah Anda yakin ingin menghapus meja <span id="delete_meja_number"></span>?</p>
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
function editMeja(id, nomor) {
    document.getElementById('edit_id_meja').value = id;
    document.getElementById('edit_nomor_meja').value = nomor;
    new bootstrap.Modal(document.getElementById('editMejaModal')).show();
}

function deleteMeja(id, nomor) {
    document.getElementById('delete_id_meja').value = id;
    document.getElementById('delete_meja_number').textContent = nomor;
    new bootstrap.Modal(document.getElementById('deleteMejaModal')).show();
}
</script>