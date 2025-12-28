<?php
include 'connect.php';

$query = "
  SELECT pId, pKeterangan, pJumlah, pStatus, digunakan
  FROM pengajuan
  ORDER BY pTanggal DESC
";

$result = mysqli_query($koneksi, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
