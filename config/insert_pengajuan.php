<?php
require_once __DIR__.'/connect.php';
header('Content-Type: application/json');

$pDetail = $_POST['pDetail'] ?? '';
$pTanggal = $_POST['pTanggal'] ?? '';
$pKeterangan = $_POST['pKeterangan'] ?? '';
$pJumlah = $_POST['pJumlah'] ?? '';
$pStatus = $_POST['pStatus'] ?? 'Diproses';

if(!$pDetail || !$pTanggal || !$pKeterangan || !$pJumlah){
echo json_encode([
'success'=>false,
'message'=>'Data tidak lengkap'
]);
exit;
}

$stmt = $koneksi->prepare("
INSERT INTO pengajuan
(pDetail,pTanggal,pKeterangan,pJumlah,pStatus,digunakan)
VALUES (?,?,?,?,?, 'Tidak')
");

$stmt->bind_param(
"sssds",
$pDetail,
$pTanggal,
$pKeterangan,
$pJumlah,
$pStatus
);

if($stmt->execute()){
echo json_encode(['success'=>true]);
}else{
echo json_encode([
'success'=>false,
'message'=>$stmt->error
]);
}
