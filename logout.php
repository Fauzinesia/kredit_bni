<?php
session_start();
session_destroy(); // Hapus semua session
header("location: login.php"); // Redirect ke halaman login
exit;
?>
