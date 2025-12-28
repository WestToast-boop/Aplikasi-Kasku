<?php
session_start();
include "connect.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$username = $_POST['username'] ?? '';
$password = md5($_POST['password'] ?? '');

$query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    $_SESSION['error'] = "Terjadi kesalahan query: " . mysqli_error($koneksi);
    header("Location: ../index.php");
    exit();
}

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);

    $_SESSION['userId'] = $data['userId'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];

    header("Location: ../dashboard.php");
    exit();
} else {
    $_SESSION['error'] = "Username atau password salah!";
    header("Location: ../index.php");
    exit();
}
