<?php
session_start();
include "connect.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $koneksi->prepare("SELECT userId, username, role, password FROM user WHERE username=? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Username atau password salah!";
    header("Location: ../index.php");
    exit();
}

$data = $result->fetch_assoc();
$hash = $data['password'];

// support untuk akun lama (md5) + akun baru (password_hash)
$ok = false;

// kalau hash bcrypt (password_hash)
if (str_starts_with($hash, '$2y$') || str_starts_with($hash, '$2a$') || str_starts_with($hash, '$2b$')) {
    $ok = password_verify($password, $hash);
} else {
    // fallback md5 untuk akun lama
    $ok = (md5($password) === $hash);
}

if ($ok) {
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
