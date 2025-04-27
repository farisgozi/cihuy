<?php
session_start();
require_once 'config/config.php';

// Jika sudah login, redirect ke halaman utama
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_user = $_POST['nama_user'] ?? '';
    $id_user = $_POST['id_user'] ?? '';

    // Koneksi database
    $database = new Database();
    $db = $database->getConnection();

    // Cek kredensial user berdasarkan nama_user dan id_user
    $stmt = $db->prepare("SELECT * FROM users WHERE nama_user = ? AND id_user = ?");
    $stmt->execute([$nama_user, $id_user]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Untuk implementasi sederhana, kita gunakan nama_user sebagai username
    if ($user) {
        // Simpan data user ke session
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_name'] = $user['nama_user'];
        
        // Set role berdasarkan id_user
        $role = '';
        switch($user['id_user']) {
            case 1:
                $role = 'administrator';
                break;
            case 2:
                $role = 'owner';
                break;
            case 3:
                $role = 'kasir';
                break;
            case 4:
                $role = 'waiter';
                break;
            default:
                $role = 'unknown';
        }
        $_SESSION['user_role'] = $role;
        
        header('Location: index.php');
        exit();
    } else {
        $error = 'ID User atau Nama User tidak valid!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Kasir Restoran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-title {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2 class="login-title">Login Kasir Restoran</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="id_user" class="form-label">ID User</label>
                    <input type="number" class="form-control" id="id_user" name="id_user" required>
                </div>
                <div class="mb-3">
                    <label for="nama_user" class="form-label">Nama User</label>
                    <input type="text" class="form-control" id="nama_user" name="nama_user" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>