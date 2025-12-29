<?php
include "connect.php";
session_start();

header('Content-Type: application/json');

// Ambil userId dari session
$pengaju_userid = $_SESSION['userId'] ?? null;
$role = $_SESSION['role'] ?? null;

if (!$pengaju_userid) {
    echo json_encode(['success' => false, 'message' => 'User belum login']);
    exit;
}

// (Opsional tapi bagus) batasi siapa yang boleh mengajukan
// kalau bendahara saja yang boleh, ubah kondisinya.
if ($role && !in_array($role, ['bendahara', 'warga'])) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$pDetail = $_POST['pDetail'] ?? ''; // sekarang dianggap "detail" bukan "nama"
$pTanggal = $_POST['pTanggal'] ?? '';
$pKeterangan = $_POST['pKeterangan'] ?? '';
$pJumlah = $_POST['pJumlah'] ?? 0;
$jenis_pengajuan = $_POST['jenis_pengajuan'] ?? 'Pemasukan';

// Status jangan dari POST (biar aman)
$pStatus = 'Diproses';

$sql = "
    INSERT INTO pengajuan (
        pengaju_userid,
        pDetail,
        pTanggal,
        pKeterangan,
        pJumlah,
        jenis_pengajuan,
        pStatus,
        digunakan
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Tidak')
";

$stmt = $koneksi->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => $koneksi->error]);
    exit;
}

$stmt->bind_param(
    "isssdss",
    $pengaju_userid,
    $pDetail,
    $pTanggal,
    $pKeterangan,
    $pJumlah,
    $jenis_pengajuan,
    $pStatus
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$koneksi->close();
