<?php
session_start();
require_once 'config/config.php';
require_once 'autoload.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Tampilkan halaman berdasarkan role user
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$role = $_SESSION['user_role'];

// Definisi akses halaman berdasarkan role
$allowed_pages = [
    'administrator' => ['dashboard', 'menu', 'meja'],
    'owner' => ['dashboard', 'transaksi', 'pelanggan'],
    'kasir' => ['dashboard', 'transaksi'],
    'waiter' => ['dashboard', 'pesanan', 'meja']
];

// Cek apakah user memiliki akses ke halaman yang diminta
if (!in_array($page, $allowed_pages[$role])) {
    $page = 'dashboard'; // Redirect ke dashboard jika tidak memiliki akses
}

// Header
include 'views/templates/header.php';

// Content
switch($page) {
    case 'dashboard':
        include 'views/dashboard.php';
        break;
    case 'menu':
        include 'views/menu/index.php';
        break;
    case 'pesanan':
        include 'views/pesanan/index.php';
        break;
    case 'transaksi':
        include 'views/transaksi/index.php';
        break;
    case 'pelanggan':
        include 'views/pelanggan/index.php';
        break;
    case 'meja':
        include 'views/meja/index.php';
        break;
    default:
        include 'views/dashboard.php';
}

// Footer
include 'views/templates/footer.php';
?>