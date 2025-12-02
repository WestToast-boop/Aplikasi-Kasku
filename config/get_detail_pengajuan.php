<?php
include 'connect.php';

$id = $_GET['pId'];

$stmt = $koneksi->prepare("SELECT * FROM pengajuan WHERE pId = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
echo json_encode($result->fetch_assoc());
