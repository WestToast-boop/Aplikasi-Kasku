<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$nama = $_SESSION['nama'] ?? $_SESSION['username'];
$role = $_SESSION['role'] ?? 'warga';

// path halaman sekarang (biar active pas)
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// MENU DIGABUNG
$menu = [
    'umum' => [
        'title' => 'Menu Umum',
        'id' => 'menuUmum',
        'items' => [
            '/kasku/dashboard.php' => ['Dashboard', 'bi-house'],
            '/kasku/tagihan.php' => ['Tagihan', 'bi-journal-text'],
        ]
    ],
    'ketua' => [
        'title' => 'Menu Ketua',
        'id' => 'menuKetua',
        'items' => [
            '/kasku/ketua/ketua_pengajuan.php' => ['Pengajuan', 'bi-pencil'],
            '/kasku/ketua/data_warga.php' => ['Data Warga', 'bi-people'],
        ]
    ],
    'bendahara' => [
        'title' => 'Menu Bendahara',
        'id' => 'menuBendahara',
        'items' => [
            '/kasku/bendahara/bendahara_pengajuan.php' => ['Pengajuan', 'bi-envelope'],
            '/kasku/bendahara/pembayaran_warga.php' => ['Kelola Iuran', 'bi-wallet'],
        ]
    ],
];

// daftar section yang tampil per role
$sections_to_show = ['umum']; // selalu tampil
if ($role === 'ketua')
    $sections_to_show[] = 'ketua';
if ($role === 'bendahara')
    $sections_to_show[] = 'bendahara';
?>

<!-- ========== HEADER ========== -->
<div class="header d-flex align-items-center justify-content-between px-4">

    <!-- Kiri: Logo -->
    <div class="d-flex align-items-center">
        <img src="../img/kasku.jpg" alt="KASKU" height="40">
    </div>

    <!-- Tengah: Judul Halaman -->
    <div class="text-center flex-grow-1">
        <h4 class="mb-0 fw-semibold">
            <?= isset($page_title) ? $page_title : 'KASKU' ?>
        </h4>
    </div>

    <!-- Kanan: Welcome User -->
    <div class="d-flex align-items-center">
        <span class="me-2 text-secondary">
            Welcome, <strong><?= ucfirst($nama) ?></strong>
        </span>
        <i class="bi bi-person-circle fs-4 text-secondary"></i>
    </div>

</div>

<!-- ========== SIDEBAR ========== -->
<div class="sidebar">
    <img src="/kasku/img/kas.jpg" alt="Logo KASKU">
    <h4>KASKU</h4>

    <nav class="nav flex-column w-100 px-2">

        <?php foreach ($sections_to_show as $key): ?>
            <?php
            $section = $menu[$key];

            // auto-open dropdown kalau current page ada di dalam section tsb
            $is_section_active = array_key_exists($current_path, $section['items']);
            $collapse_class = $is_section_active ? 'show' : '';
            $btn_class = $is_section_active ? '' : 'collapsed';
            ?>

            <button
                class="btn w-100 text-start text-white d-flex align-items-center justify-content-between px-3 py-2 mt-2 <?= $btn_class ?>"
                type="button" data-bs-toggle="collapse" data-bs-target="#<?= $section['id'] ?>"
                aria-expanded="<?= $is_section_active ? 'true' : 'false' ?>" aria-controls="<?= $section['id'] ?>"
                style="background: rgba(255,255,255,0.08); border-radius: 10px;">
                <span class="fw-semibold"><?= $section['title'] ?></span>
                <i class="bi bi-chevron-down"></i>
            </button>

            <div class="collapse <?= $collapse_class ?>" id="<?= $section['id'] ?>">
                <div class="mt-1">
                    <?php foreach ($section['items'] as $href => $item): ?>
                        <?php $is_active = ($current_path === $href); ?>
                        <a href="<?= $href ?>"
                            class="nav-link text-white d-flex align-items-center rounded px-3 py-2 ms-2 me-2 <?= $is_active ? 'active' : '' ?>">
                            <i class="bi <?= $item[1] ?> me-2"></i>
                            <span><?= $item[0] ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php endforeach; ?>

        <div class="mt-auto w-100">
            <a href="/kasku/config/logout.php"
                class="nav-link logout-btn d-flex align-items-center justify-content-center rounded mt-3">
                <i class="bi bi-box-arrow-right me-2"></i>
                <span>Logout</span>
            </a>
        </div>

    </nav>
</div>