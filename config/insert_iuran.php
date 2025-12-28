<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once __DIR__ . '/connect.php';
$koneksi->set_charset('utf8mb4');

/**
 * ===============================
 * AMBIL DATA FORM
 * ===============================
 */
$pId        = $_POST['pId'] ?? null;
$tanggal    = $_POST['t_tanggal'] ?? null;
$jumlah     = $_POST['t_jumlah'] ?? null;
$keterangan = $_POST['t_keterangan'] ?? null;
$noRek      = $_POST['no_rek'] ?? null;
$atasNama   = $_POST['atas_nama'] ?? null;

/**
 * ===============================
 * VALIDASI DATA WAJIB
 * ===============================
 */
if (!$pId || !$tanggal || !$jumlah || !$keterangan || !$noRek || !$atasNama) {
    die('Data form tidak lengkap (pId/tanggal/jumlah/keterangan/no_rek/atas_nama)');
}

/**
 * ===============================
 * VALIDASI FILE QR
 * ===============================
 */
if (!isset($_FILES['foto_qr']) || $_FILES['foto_qr']['error'] !== UPLOAD_ERR_OK) {
    die('File QR (foto_qr) tidak valid / belum dipilih');
}

/**
 * ===============================
 * UPLOAD FILE QR KE /uploads
 * (sesuai struktur kamu: langsung di uploads)
 * ===============================
 */
$uploadDir = dirname(__DIR__) . '/uploads/'; // .../KasKu/uploads/
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
$ext = strtolower(pathinfo($_FILES['foto_qr']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt, true)) {
    die('Format foto QR harus jpg/jpeg/png/webp');
}

$fotoQR = 'qr_' . time() . '_' . random_int(100, 999) . '.' . $ext;
$target = $uploadDir . $fotoQR;

if (!move_uploaded_file($_FILES['foto_qr']['tmp_name'], $target)) {
    die('Gagal menyimpan file QR ke folder uploads');
}

/**
 * ===============================
 * TRANSAKSI
 * ===============================
 */
$koneksi->begin_transaction();

try {
    /**
     * 1) CEK PENGAJUAN VALID
     */
    $stmtCek = $koneksi->prepare("
        SELECT pId
        FROM pengajuan
        WHERE pId = ?
          AND pStatus = 'Disetujui'
          AND digunakan = 'Tidak'
        FOR UPDATE
    ");
    $stmtCek->bind_param("i", $pId);
    $stmtCek->execute();
    $res = $stmtCek->get_result();

    if ($res->num_rows === 0) {
        throw new Exception('Pengajuan tidak valid / belum disetujui / sudah digunakan');
    }

    /**
     * 2) INSERT TAGIHAN
     */
    $stmtTagihan = $koneksi->prepare("
        INSERT INTO tagihan
            (pId, t_tanggal, t_jumlah, no_rek, photo, atas_nama, t_keterangan)
        VALUES
            (?, ?, ?, ?, ?, ?, ?)
    ");

    $jumlahFloat = (float)$jumlah;

    $stmtTagihan->bind_param(
        "isdssss",
        $pId,
        $tanggal,
        $jumlahFloat,
        $noRek,
        $fotoQR,
        $atasNama,
        $keterangan
    );
    $stmtTagihan->execute();

    $tId = $koneksi->insert_id;

    /**
     * 3) INSERT PEMBAYARAN UNTUK SEMUA USER
     * bStatus = NULL (BELUM BAYAR)
     */
    $users = $koneksi->query("SELECT userId FROM user");

    $stmtBayar = $koneksi->prepare("
        INSERT INTO pembayaran (userId, tId, bStatus)
        VALUES (?, ?, NULL)
    ");

    while ($u = $users->fetch_assoc()) {
        $uid = (int)$u['userId'];
        $stmtBayar->bind_param("ii", $uid, $tId);
        $stmtBayar->execute();
    }

    /**
     * 4) KUNCI PENGAJUAN
     */
    $stmtUpd = $koneksi->prepare("
        UPDATE pengajuan
        SET digunakan = 'Ya'
        WHERE pId = ?
    ");
    $stmtUpd->bind_param("i", $pId);
    $stmtUpd->execute();

    /**
     * COMMIT
     */
    $koneksi->commit();

    header("Location: ../bendahara/pembayaran_warga.php?status=sukses");
    exit;

} catch (Throwable $e) {
    $koneksi->rollback();

    // kalau transaksi gagal, hapus file QR yang terlanjur ke-upload biar tidak numpuk
    if (!empty($fotoQR) && file_exists($target)) {
        @unlink($target);
    }

    die("Gagal: " . $e->getMessage());
}
