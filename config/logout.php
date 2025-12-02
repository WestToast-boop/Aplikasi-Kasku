<?php
session_start();

// Hapus semua session
session_unset();

// Hapus session dari server
session_destroy();

// Redirect kembali ke halaman login
header("Location: ../index.php");
exit();
