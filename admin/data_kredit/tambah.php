<?php
// Mulai session
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: ../../login.php");
    exit;
}

// Tentukan path ke root project
$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';

// Include file konfigurasi database
require_once $root_path . 'config/koneksi.php';

// Fungsi untuk membuat no_kredit otomatis
function generateNoKredit($koneksi) {
    // Ambil tahun sekarang
    $tahun = date('Y');
    
    // Query untuk mendapatkan urutan terakhir dari no_kredit dengan tahun yang sama
    $sql = "SELECT no_kredit FROM tb_kredit WHERE no_kredit LIKE 'KRED-$tahun%' ORDER BY no_kredit DESC LIMIT 1";
    $result = mysqli_query($koneksi, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        // Ambil no_kredit terakhir
        $row = mysqli_fetch_assoc($result);
        // Ambil urutan terakhir dan tambah 1
        $lastNoKredit = $row['no_kredit'];
        $lastNumber = (int)substr($lastNoKredit, -3); // Ambil 3 digit terakhir
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // Tambah 1 dan pad dengan 0
    } else {
        // Jika belum ada data, mulai dari 001
        $newNumber = '001';
    }
    
    // Gabungkan dengan format no_kredit (misalnya KRED-2025-001)
    return 'KRED-' . $tahun . '-' . $newNumber;
}

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama_nasabah = mysqli_real_escape_string($koneksi, $_POST['nama_nasabah']);
    $nama_kredit = mysqli_real_escape_string($koneksi, $_POST['nama_kredit']);
    $tanggal_pengajuan = mysqli_real_escape_string($koneksi, $_POST['tanggal_pengajuan']);
    $jumlah_kredit = mysqli_real_escape_string($koneksi, $_POST['jumlah_kredit']);
    $angsuran_per_bulan = mysqli_real_escape_string($koneksi, $_POST['angsuran_per_bulan']);
    $tenor = mysqli_real_escape_string($koneksi, $_POST['tenor']);
    $suku_bunga = mysqli_real_escape_string($koneksi, $_POST['suku_bunga']);
    $status_kredit = mysqli_real_escape_string($koneksi, $_POST['status_kredit']);

    // Generate no_kredit otomatis
    $no_kredit = generateNoKredit($koneksi);

    // Query untuk menambahkan data
    $sql = "INSERT INTO tb_kredit (no_kredit, nama_nasabah, nama_kredit, tanggal_pengajuan, jumlah_kredit, angsuran_per_bulan, tenor, suku_bunga, status_kredit)
            VALUES ('$no_kredit', '$nama_nasabah', '$nama_kredit', '$tanggal_pengajuan', '$jumlah_kredit', '$angsuran_per_bulan', '$tenor', '$suku_bunga', '$status_kredit')";

    if (mysqli_query($koneksi, $sql)) {
        // Set session untuk pesan sukses
        $_SESSION['success'] = "Data kredit berhasil ditambahkan!";
        header("Location: kredit.php"); // Redirect ke halaman data kredit
        exit;
    } else {
        // Set session untuk pesan error
        $_SESSION['error'] = "Terjadi kesalahan: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Tambah Data Kredit - Sistem Kredit BNI</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../../assets/img/logo.PNG" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["../../assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

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
                        <h4 class="page-title">Tambah Data Kredit</h4>
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
                                <a href="#">Tambah Data Kredit</a>
                            </li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Form Tambah Data Kredit</h4>
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

                                    <!-- Form tambah data kredit -->
                                    <form action="tambah.php" method="POST">
                                        <div class="form-group">
                                            <label for="nama_nasabah">Nama Nasabah</label>
                                            <input type="text" class="form-control" id="nama_nasabah" name="nama_nasabah" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama_kredit">Jenis Kredit</label>
                                            <select class="form-control" id="nama_kredit" name="nama_kredit" required>
                                                <option value="Kredit Tanpa Agunan">Kredit Tanpa Agunan</option>
                                                <option value="Kredit Kendaraan Bermotor">Kredit Kendaraan Bermotor</option>
                                                <option value="Kredit Pemilikan Rumah">Kredit Pemilikan Rumah</option>
                                                <option value="Kredit Pensiun">Kredit Pensiun</option>
                                                <option value="Kredit Modal Kerja">Kredit Modal Kerja</option>
                                                <option value="Kredit Investasi">Kredit Investasi</option>
                                                <option value="Kredit Usaha Mikro">Kredit Usaha Mikro</option>
                                                <option value="Kredit Pemilikan Rumah Subsidi">Kredit Pemilikan Rumah Subsidi</option>
                                                <option value="Kredit Usaha Rakyat">Kredit Usaha Rakyat</option>
                                                <option value="Kredit Korporasi">Kredit Korporasi</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="tanggal_pengajuan">Tanggal Pengajuan</label>
                                            <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="jumlah_kredit">Jumlah Kredit</label>
                                            <input type="number" class="form-control" id="jumlah_kredit" name="jumlah_kredit" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="angsuran_per_bulan">Angsuran Per Bulan</label>
                                            <input type="number" class="form-control" id="angsuran_per_bulan" name="angsuran_per_bulan" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="tenor">Tenor (bulan)</label>
                                            <input type="number" class="form-control" id="tenor" name="tenor" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="suku_bunga">Suku Bunga (%)</label>
                                            <input type="number" step="0.01" class="form-control" id="suku_bunga" name="suku_bunga" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="status_kredit">Status Kredit</label>
                                            <select class="form-control" id="status_kredit" name="status_kredit" required>
                                                <option value="Diajukan">Diajukan</option>
                                                <option value="Dalam Proses">Dalam Proses</option>
                                                <option value="Disetujui">Disetujui</option>
                                                <option value="Ditolak">Ditolak</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Tambah Data</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Include Footer -->
            <?php include "../../admin/includes/footer.php"; ?>
        </div>
    </div>

    <!-- JS Files -->
    <script src="../../assets/js/core/jquery-3.7.1
