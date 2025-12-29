<?php
include "connect.php";
session_start();

/**
 * ============================
 * VALIDASI SESSION
 * ============================
 */
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'ketua') {
    die("Akses ditolak");
}

$verifikator_id = $_SESSION['userId'];

/**
 * ============================
 * AMBIL DATA POST
 * ============================
 */
$pId = $_POST['pId'] ?? null;
$aksi = $_POST['aksi'] ?? null;
$alasan = $_POST['pAlasan'] ?? '';

if (!$pId || !$aksi) {
    die("Data tidak lengkap");
}

/**
 * ============================
 * TENTUKAN STATUS
 * ============================
 */
if ($aksi === "Setuju") {
    $status = "Disetujui";
} elseif ($aksi === "Tolak") {
    $status = "Ditolak";
} else {
    die("Aksi tidak valid");
}

/**
 * ============================
 * UPDATE PENGAJUAN
 * ============================
 */
$stmt = $koneksi->prepare("
    UPDATE pengajuan
    SET 
        pStatus = ?,
        pAlasan = ?,
        verifikator_userid = ?
    WHERE pId = ?
");

$stmt->bind_param(
    "ssii",
    $status,
    $alasan,
    $verifikator_id,
    $pId
);

if ($stmt->execute()) {
    echo "<script>
        alert('Status pengajuan berhasil diperbarui');
        window.location='../ketua/ketua_pengajuan.php';
    </script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$koneksi->close();