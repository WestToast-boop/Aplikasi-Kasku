<?php
// PENTING: JANGAN ADA SPASI / HTML DI ATAS ATAU BAWAH FILE INI

require_once __DIR__ . '/connect.php';

header('Content-Type: application/json');

$bId = $_POST['bId'] ?? null;
$status = $_POST['status'] ?? null;

if (!$bId || !$status) {
    echo json_encode([
        'success' => false,
        'msg' => 'Data tidak lengkap'
    ]);
    exit;
}

// HANYA STATUS FINAL
$allowed = ['Disetujui', 'Ditolak'];
if (!in_array($status, $allowed)) {
    echo json_encode([
        'success' => false,
        'msg' => 'Status tidak valid'
    ]);
    exit;
}

/**
 * Ambil pId dari relasi pembayaran → tagihan → pengajuan
 */
$stmt = $koneksi->prepare("
    SELECT tagihan.pId
    FROM pembayaran
    JOIN tagihan ON pembayaran.tId = tagihan.tId
    WHERE pembayaran.bId = ?
");
$stmt->bind_param("i", $bId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'msg' => 'Data pembayaran tidak ditemukan'
    ]);
    exit;
}

$row = $result->fetch_assoc();
$pId = $row['pId'];

/**
 * Update pembayaran
 */
$stmt = $koneksi->prepare("
    UPDATE pembayaran
    SET bStatus = ?
    WHERE bId = ?
");
$stmt->bind_param("si", $status, $bId);
$stmt->execute();

/**
 * Update pengajuan
 */
$stmt = $koneksi->prepare("
    UPDATE pengajuan
    SET pStatus = ?
    WHERE pId = ?
");
$stmt->bind_param("si", $status, $pId);
$stmt->execute();

echo json_encode([
    'success' => true
]);
