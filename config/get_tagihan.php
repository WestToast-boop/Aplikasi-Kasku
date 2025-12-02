<?php
include "connect.php";

$id = $_GET['id'];
$q = mysqli_query($conn, "SELECT * FROM tagihan WHERE tId = '$id'");
echo json_encode(mysqli_fetch_assoc($q));
