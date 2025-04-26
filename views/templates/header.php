<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir Restoran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar .active {
            background-color: #495057;
        }
        .content {
            padding: 20px;
        }
        .navbar {
            background-color: #212529;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-white text-center mb-4">Kasir Restoran</h4>
                <nav>
                    <a href="index.php" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="index.php?page=menu" class="<?php echo $page == 'menu' ? 'active' : ''; ?>">
                        <i class="bi bi-menu-button-wide"></i> Menu
                    </a>
                    <a href="index.php?page=meja" class="<?php echo $page == 'meja' ? 'active' : ''; ?>">
                        <i class="bi bi-table"></i> Meja
                    </a>
                    <a href="index.php?page=pesanan" class="<?php echo $page == 'pesanan' ? 'active' : ''; ?>">
                        <i class="bi bi-cart"></i> Pesanan
                    </a>
                    <a href="index.php?page=transaksi" class="<?php echo $page == 'transaksi' ? 'active' : ''; ?>">
                        <i class="bi bi-cash"></i> Transaksi
                    </a>
                    <a href="index.php?page=pelanggan" class="<?php echo $page == 'pelanggan' ? 'active' : ''; ?>">
                        <i class="bi bi-people"></i> Pelanggan
                    </a>
                </nav>
            </div>

            <!-- Content -->
            <div class="col-md-10">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-dark mb-4">
                    <div class="container-fluid">
                        <span class="navbar-text">
                            Selamat datang, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </span>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a href="logout.php" class="nav-link">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Main Content -->
                <div class="content">