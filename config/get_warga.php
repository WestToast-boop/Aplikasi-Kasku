<?php
include "connect.php";
header('Content-Type: application/json');

$res = $koneksi->query("SELECT userId, username, role FROM user ORDER BY username ASC");
$data = [];
while ($row = $res->fetch_assoc())
    $data[] = $row;
echo json_encode($data);
