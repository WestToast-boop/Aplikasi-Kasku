<?php
include "connect.php";

$result = mysqli_query($koneksi, "SELECT userId, username, role FROM user ORDER BY username ASC");

$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

echo json_encode($rows);
?>