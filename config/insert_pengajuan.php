<?php
header("Content-Type: application/json");
include 'connect.php';

$pKeterangan = $_POST['pKeterangan'] ?? '';
$pTanggal = $_POST['pTanggal'] ?? '';
$pJumlah = $_POST['pJumlah'] ?? '';
$pDetail = $_POST['pDetail'] ?? '';
$pStatus = $_POST['pStatus'] ?? '';

$sql = "INSERT INTO pengajuan (pKeterangan, pTanggal, pJumlah, pDetail, pStatus)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ssiss", $pKeterangan, $pTanggal, $pJumlah, $pDetail, $pStatus);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}
exit;
