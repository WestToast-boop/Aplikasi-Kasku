<?php
include "connect.php";
session_start();
header('Content-Type: application/json');

// (Opsional) hanya ketua boleh tambah user
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ketua') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'warga';

$allowed = ['warga', 'bendahara', 'ketua'];

if ($username === '' || $alamat === '' || $password === '' || !in_array($role, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap / role tidak valid']);
    exit;
}

// cek username unik
$cek = $koneksi->prepare("SELECT userId FROM user WHERE username=? LIMIT 1");
$cek->bind_param("s", $username);
$cek->execute();
$cekRes = $cek->get_result();
if ($cekRes->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username sudah dipakai']);
    exit;
}

// simpan password (lebih aman pakai hash)
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $koneksi->prepare("
  INSERT INTO user (username, role, alamat, password, created_at)
  VALUES (?, ?, ?, ?, NOW())
");
$stmt->bind_param("ssss", $username, $role, $alamat, $hash);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$koneksi->close();
