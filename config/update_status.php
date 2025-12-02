<?php
include "connect.php";
session_start();

$id     = $_POST['pId'];
$aksi   = $_POST['aksi'];
$alasan = mysqli_real_escape_string($koneksi, $_POST['pAlasan']);

if ($aksi == "Setuju") {
    $status = "Disetujui";
} else {
    $status = "Ditolak";
}

$query = "
    UPDATE pengajuan 
    SET pStatus='$status', pAlasan='$alasan'
    WHERE pId='$id'
";

if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Status berhasil diperbarui'); window.location='../ketua/ketua_pengajuan.php';</script>";
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>
