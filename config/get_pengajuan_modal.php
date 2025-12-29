<?php
include 'connect.php';
session_start();

$query = "
  SELECT
    p.pId,
    u.username AS nama_pengaju,
    p.pTanggal,
    p.pKeterangan,
    p.pJumlah,
    p.jenis_pengajuan,
    p.pStatus,
    p.digunakan
  FROM pengajuan p
  JOIN user u ON u.userId = p.pengaju_userid
  ORDER BY p.pTanggal DESC
";

$result = mysqli_query($koneksi, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
