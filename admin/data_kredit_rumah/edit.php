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

// Ambil ID kredit yang akan diedit
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mengambil data berdasarkan ID
    $sql = "SELECT * FROM tb_kredit_rumah WHERE kredit_id = '$id'";
    $result = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($result) == 0) {
        $_SESSION['error'] = "Data tidak ditemukan!";
        header('Location: kredit_rumah.php');
        exit;
    }

    // Ambil data kredit dari query
    $row = mysqli_fetch_assoc($result);
    $no_kredit = $row['no_kredit'];
    $nama_nasabah = $row['nama_nasabah'];
    $nama_kredit = $row['nama_kredit'];
    $jumlah_kredit = $row['jumlah_kredit'];
    $angsuran_per_bulan = $row['angsuran_per_bulan'];
    $tenor = $row['tenor'];
    $tanggal_pengajuan = $row['tanggal_pengajuan'];
    $status_kredit = $row['status_kredit'];
} else {
    $_SESSION['error'] = "ID tidak ditemukan!";
    header('Location: kredit_rumah.php');
    exit;
}

// Proses update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_nasabah = $_POST['nama_nasabah'];
    $nama_kredit = $_POST['nama_kredit'];
    $jumlah_kredit = $_POST['jumlah_kredit'];
    $angsuran_per_bulan = $_POST['angsuran_per_bulan'];
    $tenor = $_POST['tenor'];
    $tanggal_pengajuan = $_POST['tanggal_pengajuan'];
    $status_kredit = $_POST['status_kredit'];

    // Query untuk update data kredit rumah
    $sql_update = "UPDATE tb_kredit_rumah 
                   SET nama_nasabah = '$nama_nasabah', 
                       nama_kredit = '$nama_kredit', 
                       jumlah_kredit = '$jumlah_kredit', 
                       angsuran_per_bulan = '$angsuran_per_bulan', 
                       tenor = '$tenor', 
                       tanggal_pengajuan = '$tanggal_pengajuan', 
                       status_kredit = '$status_kredit'
                   WHERE kredit_id = '$id'";

    if (mysqli_query($koneksi, $sql_update)) {
        $_SESSION['success'] = 'Data kredit rumah berhasil diperbarui!';
        header('Location: kredit_rumah.php');
        exit;
    } else {
        $_SESSION['error'] = 'Gagal memperbarui data kredit rumah: ' . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Edit Data Kredit Rumah - Sistem Kredit BNI</title>
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
                        <h4 class="page-title">Edit Data Kredit Rumah</h4>
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
                                <a href="#">Edit Data Kredit Rumah</a>
                            </li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Form Edit Data Kredit Rumah</h4>
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

                                    <form method="POST" action="edit.php?id=<?php echo $id; ?>">
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

                                        <button type="submit" class="btn btn-primary">Perbarui Data</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>