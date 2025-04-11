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

// Cek apakah ID ada di URL dan pastikan ID valid
if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
} else {
    // Redirect ke halaman kredit dengan pesan error jika ID tidak valid
    $_SESSION['error'] = "ID kredit tidak valid atau tidak ditemukan!";
    header("Location: kredit.php");
    exit;
}

// Ambil data kredit berdasarkan ID
$sql = "SELECT * FROM tb_kredit WHERE kredit_id = '$id'";
$result = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    // Redirect ke halaman kredit dengan pesan error jika data tidak ditemukan
    $_SESSION['error'] = "Data kredit tidak ditemukan!";
    header("Location: kredit.php");
    exit;
}

// Proses update data ketika form disubmit
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

    // Query untuk update data
    $sql_update = "UPDATE tb_kredit SET
                    nama_nasabah = '$nama_nasabah',
                    nama_kredit = '$nama_kredit',
                    tanggal_pengajuan = '$tanggal_pengajuan',
                    jumlah_kredit = '$jumlah_kredit',
                    angsuran_per_bulan = '$angsuran_per_bulan',
                    tenor = '$tenor',
                    suku_bunga = '$suku_bunga',
                    status_kredit = '$status_kredit'
                   WHERE kredit_id = '$id'";

    if (mysqli_query($koneksi, $sql_update)) {
        $_SESSION['success'] = "Data kredit berhasil diperbarui!";
        header("Location: kredit.php"); // Redirect setelah update
        exit;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Edit Data Kredit - Sistem Kredit BNI</title>
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
                        <h4 class="page-title">Edit Data Kredit</h4>
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
                                <a href="kredit.php">Data Kredit</a>
                            </li>
                            <li class="separator">
                                <i class="fas fa-angle-right"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Edit Data Kredit</a>
                            </li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Form Edit Data Kredit</h4>
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
                                    <!-- Form edit data kredit -->
                                    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
                                        <div class="form-group">
                                            <label for="nama_nasabah">Nama Nasabah</label>
                                            <input type="text" class="form-control" id="nama_nasabah" name="nama_nasabah" value="<?php echo htmlspecialchars($data['nama_nasabah']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama_kredit">Jenis Kredit</label>
                                            <select class="form-control" id="nama_kredit" name="nama_kredit" required>
                                                <option value="Kredit Tanpa Agunan" <?php echo ($data['nama_kredit'] == 'Kredit Tanpa Agunan') ? 'selected' : ''; ?>>Kredit Tanpa Agunan</option>
                                                <option value="Kredit Kendaraan Bermotor" <?php echo ($data['nama_kredit'] == 'Kredit Kendaraan Bermotor') ? 'selected' : ''; ?>>Kredit Kendaraan Bermotor</option>
                                                <option value="Kredit Pemilikan Rumah" <?php echo ($data['nama_kredit'] == 'Kredit Pemilikan Rumah') ? 'selected' : ''; ?>>Kredit Pemilikan Rumah</option>
                                                <option value="Kredit Pensiun" <?php echo ($data['nama_kredit'] == 'Kredit Pensiun') ? 'selected' : ''; ?>>Kredit Pensiun</option>
                                                <option value="Kredit Modal Kerja" <?php echo ($data['nama_kredit'] == 'Kredit Modal Kerja') ? 'selected' : ''; ?>>Kredit Modal Kerja</option>
                                                <option value="Kredit Investasi" <?php echo ($data['nama_kredit'] == 'Kredit Investasi') ? 'selected' : ''; ?>>Kredit Investasi</option>
                                                <option value="Kredit Usaha Mikro" <?php echo ($data['nama_kredit'] == 'Kredit Usaha Mikro') ? 'selected' : ''; ?>>Kredit Usaha Mikro</option>
                                                <option value="Kredit Pemilikan Rumah Subsidi" <?php echo ($data['nama_kredit'] == 'Kredit Pemilikan Rumah Subsidi') ? 'selected' : ''; ?>>Kredit Pemilikan Rumah Subsidi</option>
                                                <option value="Kredit Usaha Rakyat" <?php echo ($data['nama_kredit'] == 'Kredit Usaha Rakyat') ? 'selected' : ''; ?>>Kredit Usaha Rakyat</option>
                                                <option value="Kredit Korporasi" <?php echo ($data['nama_kredit'] == 'Kredit Korporasi') ? 'selected' : ''; ?>>Kredit Korporasi</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="tanggal_pengajuan">Tanggal Pengajuan</label>
                                            <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" value="<?php echo htmlspecialchars($data['tanggal_pengajuan']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="jumlah_kredit">Jumlah Kredit</label>
                                            <input type="number" class="form-control" id="jumlah_kredit" name="jumlah_kredit" value="<?php echo htmlspecialchars($data['jumlah_kredit']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="angsuran_per_bulan">Angsuran Per Bulan</label>
                                            <input type="number" class="form-control" id="angsuran_per_bulan" name="angsuran_per_bulan" value="<?php echo htmlspecialchars($data['angsuran_per_bulan']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="tenor">Tenor (bulan)</label>
                                            <input type="number" class="form-control" id="tenor" name="tenor" value="<?php echo htmlspecialchars($data['tenor']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="suku_bunga">Suku Bunga (%)</label>
                                            <input type="number" step="0.01" class="form-control" id="suku_bunga" name="suku_bunga" value="<?php echo htmlspecialchars($data['suku_bunga']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="status_kredit">Status Kredit</label>
                                            <select class="form-control" id="status_kredit" name="status_kredit" required>
                                                <option value="Diajukan" <?php echo ($data['status_kredit'] == 'Diajukan') ? 'selected' : ''; ?>>Diajukan</option>
                                                <option value="Dalam Proses" <?php echo ($data['status_kredit'] == 'Dalam Proses') ? 'selected' : ''; ?>>Dalam Proses</option>
                                                <option value="Disetujui" <?php echo ($data['status_kredit'] == 'Disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                                                <option value="Ditolak" <?php echo ($data['status_kredit'] == 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save"></i> Perbarui Data
                                            </button>
                                            <a href="kredit.php" class="btn btn-danger">
                                                <i class="fa fa-times"></i> Batal
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <?php include "../../admin/includes/footer.php"; ?>
    </div>
    <!-- JS Files -->
    <script src="../../assets/js/core/jquery.3.2.1.min.js"></script>
    <script src="../../assets/js/core/popper.min.js"></script>
    <script src="../../assets/js/core/bootstrap.min.js"></script>
    <script src="../../assets/js/plugins/bootstrap-switch.js"></script>
    <script src="../../assets/js/plugins/moment.js"></script>
    <script src="../../assets/js/kaiadmin.min.js"></script>
</body>
</html>
