<?php
session_start();
include "connect.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$username = $_POST['username'];
$password = md5($_POST['password']);

$query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);

    $_SESSION['userId'] = $data['userId'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];

    if ($data['role'] == 'ketua') {
        header("Location: ../dashboard.php");
        exit;
    } elseif ($data['role'] == 'warga') {
        header("Location: ../dashboard.php");
        exit;
    } elseif ($data['role'] == 'bendahara') {
        header("Location: ../dashboard.php");
        exit;
    }

    echo "Role tidak dikenali.";
} else {
    echo "<script>
        alert('Username atau password salah!');
        window.location.href = '../index.php';
    </script>";
}
