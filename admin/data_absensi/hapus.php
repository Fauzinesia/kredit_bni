<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Cek apakah user adalah admin
if ($_SESSION['role'] != 'Admin') {
    $_SESSION['error'] = "Anda tidak memiliki akses ke halaman ini";
    header("Location: ../index.php");
    exit;
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';
require_once $root_path . 'config/koneksi.php';

// Cek apakah ada parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID absensi tidak valid";
    header("Location: absensi.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Get file path before deleting record
$query_file = "SELECT foto_absensi FROM tb_absensi_karyawan WHERE absensi_id = ?";
$stmt_file = mysqli_prepare($koneksi, $query_file);
mysqli_stmt_bind_param($stmt_file, "i", $id);
mysqli_stmt_execute($stmt_file);
$result_file = mysqli_stmt_get_result($stmt_file);
$file_data = mysqli_fetch_assoc($result_file);

// Delete the record
$query = "DELETE FROM tb_absensi_karyawan WHERE absensi_id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    // Delete the file if it exists
    if (!empty($file_data['foto_absensi']) && file_exists($root_path . $file_data['foto_absensi'])) {
        unlink($root_path . $file_data['foto_absensi']);
    }
    $_SESSION['success'] = "Data absensi berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($koneksi);
}

header("Location: absensi.php");
exit;
?>
