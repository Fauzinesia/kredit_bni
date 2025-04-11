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

// Cek apakah ID ada di URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Cek apakah data dengan ID tersebut ada
    $check_query = "SELECT ak.*, k.nama_nasabah, k.nama_kredit 
                    FROM tb_angsuran_kredit ak
                    LEFT JOIN tb_kredit k ON ak.kredit_id = k.kredit_id
                    WHERE ak.angsuran_id = '$id'";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Data ditemukan, lanjutkan dengan proses hapus
        $data = mysqli_fetch_assoc($check_result);
        
        // Proses hapus data
        if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
            // Hapus file bukti pembayaran jika ada
            if (!empty($data['bukti_pembayaran'])) {
                $file_path = "../../uploads/bukti_pembayaran/" . $data['bukti_pembayaran'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            // Hapus data
            $delete_query = "DELETE FROM tb_angsuran_kredit WHERE angsuran_id = '$id'";
            if (mysqli_query($koneksi, $delete_query)) {
                $_SESSION['success'] = "Data angsuran kredit berhasil dihapus!";
                header("Location: angsuran_kredit.php");
                exit;
            } else {
                $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($koneksi);
                header("Location: angsuran_kredit.php");
                exit;
            }
        }
        
        // Tampilkan konfirmasi hapus
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <title>Hapus Data Angsuran Kredit - Sistem Kredit BNI</title>
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
                    <?php include "../../admin/includes/header.php"; ?>
                    <div class="container-fluid">
                        <div class="page-inner">
                            <div class="page-header">
                                <h4 class="page-title">Konfirmasi Hapus Data</h4>
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
                                        <a href="angsuran_kredit.php">Data Angsuran Kredit</a>
                                    </li>
                                    <li class="separator">
                                        <i class="fas fa-angle-right"></i>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#">Hapus Data</a>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Konfirmasi Penghapusan</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-warning">
                                                <h5><i class="fas fa-exclamation-triangle"></i> Peringatan!</h5>
                                                <p>Anda akan menghapus data angsuran kredit berikut:</p>
                                            </div>
                                            
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="30%">ID Angsuran</th>
                                                    <td><?php echo $data['angsuran_id']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>No. Kredit</th>
                                                    <td><?php echo $data['no_kredit']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Nama Nasabah</th>
                                                    <td><?php echo $data['nama_nasabah']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Jenis Kredit</th>
                                                    <td><?php echo $data['nama_kredit']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Tanggal Angsuran</th>
                                                    <td><?php echo date('d-m-Y', strtotime($data['tanggal_angsuran'])); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Total Angsuran</th>
                                                    <td>Rp <?php echo number_format($data['total_angsuran'], 0, ',', '.'); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Status Pembayaran</th>
                                                    <td><?php echo $data['status_pembayaran']; ?></td>
                                                </tr>
                                            </table>
                                            
                                            <div class="mt-4">
                                                <p class="text-danger">Data yang sudah dihapus tidak dapat dikembalikan!</p>
                                                <a href="hapus.php?id=<?php echo $id; ?>&confirm=yes" class="btn btn-danger">
                                                    <i class="fa fa-trash"></i> Ya, Hapus Data
                                                </a>
                                                <a href="angsuran_kredit.php" class="btn btn-secondary">
                                                    <i class="fa fa-times"></i> Batal
                                                </a>
                                            </div>
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
            <script src="../../assets/js/core/jquery-3.7.1.min.js"></script>
            <script src="../../assets/js/core/popper.min.js"></script>
            <script src="../../assets/js/core/bootstrap.min.js"></script>
            <script src="../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
            <script src="../../assets/js/kaiadmin.min.js"></script>
        </body>
        </html>
        <?php
        exit;
    } else {
        // Data tidak ditemukan
        echo "<div style='color:red; padding:20px;'>";
        echo "<h3>Error: Data tidak ditemukan!</h3>";
        echo "<p>Tidak ada data angsuran kredit dengan ID: $id</p>";
        echo "<p><a href='angsuran_kredit.php' class='btn btn-primary'>Kembali ke daftar angsuran kredit</a></p>";
        echo "</div>";
        exit;
    }
} else {
    // ID tidak valid
    echo "<div style='color:red; padding:20px;'>";
    echo "<h3>Error: ID angsuran kredit tidak valid!</h3>";
    echo "<p>Parameter ID tidak ditemukan atau tidak valid.</p>";
    echo "<p><a href='angsuran_kredit.php' class='btn btn-primary'>Kembali ke daftar angsuran kredit</a></p>";
    echo "</div>";
    exit;
}
?>
