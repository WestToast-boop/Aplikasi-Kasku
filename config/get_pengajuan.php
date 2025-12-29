<?php
include "connect.php";
session_start();

header('Content-Type: application/json');

$sql = "
    SELECT
        p.pId,
        p.pTanggal,
        p.pKeterangan,
        p.pJumlah,
        p.jenis_pengajuan,
        p.pStatus,
        u.username AS nama_pengaju
    FROM pengajuan p
    JOIN user u ON u.userId = p.pengaju_userid
    ORDER BY p.pId DESC
";

$result = $koneksi->query($sql);

$data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
$koneksi->close();
