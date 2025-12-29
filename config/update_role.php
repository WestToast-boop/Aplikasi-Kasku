<?php
include "connect.php";
session_start();

// (Opsional) proteksi: hanya ketua boleh ubah role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ketua') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$userId = $_POST['userId'] ?? null;
$role = $_POST['role'] ?? null;

$allowed = ['warga', 'bendahara', 'ketua'];

if (!$userId || !$role || !in_array($role, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

$stmt = $koneksi->prepare("UPDATE user SET role=? WHERE userId=?");
$stmt->bind_param("si", $role, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}
$stmt->close();
$koneksi->close();
