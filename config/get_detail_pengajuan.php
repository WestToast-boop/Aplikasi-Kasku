<?php
include "connect.php";
session_start();

header('Content-Type: application/json');

$pId = $_GET['pId'] ?? null;

if (!$pId) {
    echo json_encode(['success' => false, 'message' => 'pId wajib']);
    exit;
}

$sql = "
    SELECT
        p.*,
        u.username AS nama_pengaju
    FROM pengajuan p
    JOIN user u ON u.userId = p.pengaju_userid
    WHERE p.pId = ?
    LIMIT 1
";

$stmt = $koneksi->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => $koneksi->error]);
    exit;
}

$stmt->bind_param("i", $pId);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if ($row) {
    echo json_encode($row);
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
}

$stmt->close();
$koneksi->close();
