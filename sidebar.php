<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Jika tidak login, kembalikan ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'] ?? $_SESSION['username'];
$current_page = basename($_SERVER['PHP_SELF']);

// Logika tagihan berdasarkan role
if ($role == 'warga') {
    // tampilkan tagihan milik user itu saja
}

if ($role == 'ketua') {
    // tampilkan semua tagihan seluruh warga
}

if ($role == 'bendahara') {
    // tampilkan semua tagihan + fitur konfirmasi pembayaran
}

// Sidebar menu per role
$menu = [
    'ketua' => [
        '/kasku/dashboard.php' => ['Dashboard', 'bi-house'],
        '/kasku/tagihan.php' => ['Tagihan', 'bi-journal-text'],
        '/kasku/ketua/ketua_pengajuan.php' => ['Pengajuan', 'bi-pencil'],
        '/kasku/ketua/data_warga.php' => ['Data Warga', 'bi-people']
    ],

    'bendahara' => [
        '/kasku/dashboard.php' => ['Dashboard', 'bi-house'],
        '/kasku/tagihan.php' => ['Tagihan', 'bi-wallet'],
        '/kasku/bendahara/bendahara_pengajuan.php' => ['Pengajuan', 'bi-envelope'],
        '/kasku/bendahara/pembayaran_warga.php' => ['Kelola Iuran', 'bi-envelope'],
    ],

    'warga' => [
        '/kasku/dashboard' => ['Dashboard', 'bi-house'],
        '/kasku/tagihan.php' => ['Tagihan Saya', 'bi-receipt'],
    ]
];
?>

<div class="header">
    <div class="d-flex align-items-center header-left">
        <img src="../img/kasku.jpg" alt="Kasku Text" height="40">
    </div>

    <div class="center-title">
        <h2 class="page-title mb-0">
            <?= isset($page_title) ? $page_title : 'KASKU' ?>
        </h2>
    </div>

    <div class="d-flex align-items-center">
        <span class="me-2 text-secondary">Welcome, <?= ucfirst($nama) ?></span>
        <i class="bi bi-person-circle fs-4 text-secondary"></i>
    </div>
</div>


<div class="sidebar">
    <img src="../img/kas.jpg" alt="Logo KASKU">
    <h4>KASKU</h4>

    <nav class="nav flex-column w-100 px-2">
        <?php foreach ($menu[$role] as $file => $item): ?>
            <a href="<?= $file ?>" class="nav-link text-white d-flex align-items-center rounded px-3
               <?= $current_page == $file ? 'active' : '' ?>">
                <i class="bi <?= $item[1] ?> me-2"></i>
                <span><?= $item[0] ?></span>
            </a>
        <?php endforeach; ?>

        <div class="mt-auto w-100">
            <a href="../config/logout.php"
                class="nav-link logout-btn d-flex align-items-center justify-content-center rounded">
                <i class="bi bi-box-arrow-right me-2"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</div>