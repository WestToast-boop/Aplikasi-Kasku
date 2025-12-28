<?php
session_start();
require_once __DIR__ . '/connect.php';

if (!isset($_SESSION['userId'])) {
    die('Session user tidak valid');
}

$userId = $_SESSION['userId'];
$tId    = $_POST['tId'] ?? null;

if (!$tId) {
    die('Data tidak lengkap: tId kosong.');
}

if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== 0) {
    die('File bukti pembayaran tidak valid');
}

/* ===============================
   UPLOAD FILE
================================ */
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
$namaFile = 'bukti_' . time() . '_' . rand(100,999) . '.' . $ext;
$target   = $uploadDir . $namaFile;

if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $target)) {
    die('Gagal menyimpan file bukti');
}

/* ===============================
   CEK APAKAH ROW PEMBAYARAN ADA
================================ */
$cek = $koneksi->prepare("
    SELECT bId FROM pembayaran 
    WHERE userId = ? AND tId = ?
");
$cek->bind_param("ii", $userId, $tId);
$cek->execute();
$res = $cek->get_result();

if ($res->num_rows > 0) {

    // ✅ ROW SUDAH ADA → UPDATE
    $stmt = $koneksi->prepare("
        UPDATE pembayaran
        SET bFoto = ?, bStatus = 'Verifikasi'
        WHERE userId = ? AND tId = ?
    ");
    $stmt->bind_param("sii", $namaFile, $userId, $tId);

} else {

    // ✅ ROW BELUM ADA → INSERT
    $stmt = $koneksi->prepare("
        INSERT INTO pembayaran (userId, tId, bFoto, bStatus)
        VALUES (?, ?, ?, 'Verifikasi')
    ");
    $stmt->bind_param("iis", $userId, $tId, $namaFile);
}

if (!$stmt->execute()) {
    die('Gagal menyimpan pembayaran: ' . $stmt->error);
}

/* ===============================
   REDIRECT
================================ */
header("Location: ../tagihan.php?status=berhasil");
exit;
