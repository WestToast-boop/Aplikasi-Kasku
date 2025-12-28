<?php
session_start();
header('Content-Type: application/json');
include "connect.php";

if (empty($_SESSION['userId'])) {
    echo json_encode(['status' => 'error', 'message' => 'User belum login']);
    exit;
}

$tanggal = $_POST['tanggal'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';
$jumlah = $_POST['jumlah'] ?? '';
$no_rek = $_POST['no_rek'] ?? '';
$atas_nama = $_POST['atas_nama'] ?? '';

if (!$tanggal || !$keterangan || !$jumlah || !$no_rek || !$atas_nama) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi!']);
    exit;
}

// upload qr
$qrFotoName = null;

if (!empty($_FILES['foto_qr']['name'])) {
    $folder = "../uploads/";
    if (!is_dir($folder))
        mkdir($folder, 0777, true);

    $ext = pathinfo($_FILES['foto_qr']['name'], PATHINFO_EXTENSION);
    $qrFotoName = "QR_" . time() . "." . $ext;
    $path = $folder . $qrFotoName;

    if (!move_uploaded_file($_FILES['foto_qr']['tmp_name'], $path)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal upload foto QR']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Foto QR wajib diupload']);
    exit;
}

$stmt = $koneksi->prepare("
    INSERT INTO tagihan (t_tanggal, t_jumlah, t_keterangan, no_rek, atas_nama, photo, t_status)
    VALUES (?, ?, ?, ?, ?, ?, 'Belum Bayar')
");

$stmt->bind_param(
    "sdssss",
    $tanggal,
    $jumlah,
    $keterangan,
    $no_rek,
    $atas_nama,
    $qrFotoName
);

$response = $stmt->execute()
    ? ['status' => 'success', 'message' => 'Tagihan berhasil ditambahkan!']
    : ['status' => 'error', 'message' => 'Gagal menambahkan tagihan: ' . $stmt->error];

$stmt->close();
$koneksi->close();

echo json_encode($response);
