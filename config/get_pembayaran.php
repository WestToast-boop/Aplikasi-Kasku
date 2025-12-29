<?php
include "connect.php";

$query = "
    SELECT pembayaran.*, user.nama 
    FROM pembayaran
    JOIN user ON pembayaran.userId = user.userId
    ORDER BY bId DESC
";
$result = mysqli_query($koneksi, $query);