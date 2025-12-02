<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "keuangan_rtw";
// melakukan koneksi ke db
$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Gagal konek: " . mysqli_connect_error());
}
