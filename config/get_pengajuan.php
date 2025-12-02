<?php
header("Content-Type: application/json");
include 'connect.php';

$result = $koneksi->query("SELECT * FROM pengajuan ORDER BY pId DESC");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
