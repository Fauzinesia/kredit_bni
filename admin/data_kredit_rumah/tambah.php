<?php
// Mulai session
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Tentukan path ke root project
$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';

// Include file konfigurasi database
require_once $root_path . 'config/koneksi.php';

// Inisialisasi variabel
$no_kredit = $nama_nasabah = $nama_kredit = $jumlah_kredit = $angsuran_per_bulan = $tenor = $tanggal_pengajuan = '';
$status_kredit = 'Diajukan';

// Query untuk mendapatkan No. Kredit otomatis
$sql_no_kredit = "SELECT MAX(CAST(SUBSTRING(no_kredit, 4) AS UNSIGNED)) AS max_kredit FROM tb_kredit_rumah";
$result_no_kredit = mysqli_query($koneksi, $sql_no_kredit);
$row_no_kredit = mysqli_fetch_assoc($result_no_kredit);
$max_no_kredit = $row_no_kredit['max_kredit'];

// Membuat No. Kredit berikutnya (misalnya KR-0001, KR-0002, ...)
if ($max_no_kredit) {
    $no_kredit = 'KR-' . str_pad($max_no_kredit + 1, 4, '0', STR_PAD_LEFT);
} else {
    $no_kredit = 'KR-0001'; // Jika data pertama, mulai dari KR-0001
}

// Proses data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_nasabah = $_POST['nama_nasabah'];
    $nama_kredit = $_POST['nama_kredit'];
    $jumlah_kredit = $_POST['jumlah_kredit'];
    $angsuran_per_bulan = $_POST['angsuran_per_bulan'];
    $tenor = $_POST['tenor'];
    $tanggal_pengajuan = $_POST['tanggal_pengajuan'];
    $status_kredit = $_POST['status_kredit'];

    // Query untuk insert data kredit rumah
    $sql = "INSERT INTO tb_kredit_rumah (no_kredit, nama_nasabah, nama_kredit, jumlah_kredit, angsuran_per_bulan, tenor, tanggal_pengajuan, status_kredit)
            VALUES ('$no_kredit', '$nama_nasabah', '$nama_kredit', '$jumlah_kredit', '$angsuran_per_bulan', '$tenor', '$tanggal_pengajuan', '$status_kredit')";

    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['success'] = 'Data kredit rumah berhasil ditambahkan!';
        header('Location: kredit_rumah.php');
        exit;
    } else {
        $_SESSION['error'] = 'Gagal menambahkan data kredit rumah: ' . mysqli_error($koneksi);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Tambah Data Kredit Rumah - Sistem Kredit BNI</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../../assets/img/logo.PNG" type="image/x-icon" />

    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../../assets/css/kaiadmin.min.css" />
</head>
<body>
    <div class="wrapper">
        <!-- Include Sidebar -->
        <?php include "../../admin/includes/sidebar.php"; ?>

        <div class="main-panel">
            <!-- Include Navbar -->
            <?php include "../../admin/includes/navbar.php"; ?>

            <!-- Include Header -->
            <?php include "../../admin/includes/header.php"; ?>

            <div class="container-fluid">
                <div class="page-inner">
                    <div class="page-header">
                        <h4 class="page-title">Tambah Data Kredit Rumah</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home">
                                <a href="../index.php">
                                    <i class="fas fa-home"></i>
                                </a>
                            </li>
                            <li class="separator">
                                <i class="fas fa-angle-right"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Tambah Data Kredit Rumah</a>
                            </li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Form Tambah Data Kredit Rumah</h4>
                                </div>

                                <div class="card-body">
                                    <?php
                                    // Tampilkan pesan sukses jika ada
                                    if (isset($_SESSION['success'])) {
                                        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                                        unset($_SESSION['success']);
                                    }
                                    // Tampilkan pesan error jika ada
                                    if (isset($_SESSION['error'])) {
                                        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                                        unset($_SESSION['error']);
                                    }
                                    ?>

                                    <form method="POST" action="tambah.php">
                                        <div class="form-group">
                                            <label for="no_kredit">No. Kredit</label>
                                            <input type="text" class="form-control" id="no_kredit" name="no_kredit" value="<?php echo $no_kredit; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama_nasabah">Nama Nasabah</label>
                                            <input type="text" class="form-control" id="nama_nasabah" name="nama_nasabah" value="<?php echo $nama_nasabah; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama_kredit">Jenis Kredit</label>
                                            <select class="form-control" id="nama_kredit" name="nama_kredit" required>
                                                <option value="BNI Griya" <?php echo ($nama_kredit == 'BNI Griya') ? 'selected' : ''; ?>>BNI Griya</option>
                                                <option value="BNI Griya Subsidi" <?php echo ($nama_kredit == 'BNI Griya Subsidi') ? 'selected' : ''; ?>>BNI Griya Subsidi</option>
                                                <option value="BNI Griya Refinancing" <?php echo ($nama_kredit == 'BNI Griya Refinancing') ? 'selected' : ''; ?>>BNI Griya Refinancing</option>
                                                <option value="BNI Griya Multiguna" <?php echo ($nama_kredit == 'BNI Griya Multiguna') ? 'selected' : ''; ?>>BNI Griya Multiguna</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="jumlah_kredit">Jumlah Kredit</label>
                                            <input type="number" class="form-control" id="jumlah_kredit" name="jumlah_kredit" value="<?php echo $jumlah_kredit; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="angsuran_per_bulan">Angsuran/Bulan</label>
                                            <input type="number" class="form-control" id="angsuran_per_bulan" name="angsuran_per_bulan" value="<?php echo $angsuran_per_bulan; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="tenor">Tenor (Bulan)</label>
                                            <input type="number" class="form-control" id="tenor" name="tenor" value="<?php echo $tenor; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="tanggal_pengajuan">Tanggal Pengajuan</label>
                                            <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" value="<?php echo $tanggal_pengajuan; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="status_kredit">Status Kredit</label>
                                            <select class="form-control" id="status_kredit" name="status_kredit" required>
                                                <option value="Diajukan" <?php echo ($status_kredit == 'Diajukan') ? 'selected' : ''; ?>>Diajukan</option>
                                                <option value="Dalam Proses" <?php echo ($status_kredit == 'Dalam Proses') ? 'selected' : ''; ?>>Dalam Proses</option>
                                                <option value="Disetujui" <?php echo ($status_kredit == 'Disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                                                <option value="Ditolak" <?php echo ($status_kredit == 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </
