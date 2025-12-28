<?php
include "connect.php";

$id = $_GET['id'] ?? 0;
$query = mysqli_query($koneksi, "SELECT * FROM tagihan WHERE tId = '$id' LIMIT 1");
$data = mysqli_fetch_assoc($query);

header('Content-Type: application/json');
echo json_encode($data);
