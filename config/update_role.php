<?php
include "connect.php";

if (!isset($_POST['userId']) || !isset($_POST['role'])) {
    echo "INVALID";
    exit;
}

$userId = $_POST['userId'];
$role = $_POST['role'];

$query = mysqli_query($koneksi, "UPDATE user SET role='$role' WHERE userId='$userId'");

echo ($query) ? "OK" : "ERR";
?>