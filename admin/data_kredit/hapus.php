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

// Debugging - Tampilkan semua parameter GET
echo "<pre>Debug GET Parameters: ";
print_r($_GET);
echo "</pre>";

// Cek apakah ID ada di URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Cek apakah data dengan ID tersebut ada
    $check_query = "SELECT * FROM tb_kredit WHERE kredit_id = '$id'";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Data ditemukan, lanjutkan dengan proses hapus
        $data = mysqli_fetch_assoc($check_result);
        
        // Proses hapus data
        if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
            // Hapus data
            $delete_query = "DELETE FROM tb_kredit WHERE kredit_id = '$id'";
            if (mysqli_query($koneksi, $delete_query)) {
                $_SESSION['success'] = "Data kredit berhasil dihapus!";
                header("Location: kredit.php");
                exit;
            } else {
                $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($koneksi);
                header("Location: kredit.php");
                exit;
            }
        }
        
        // Tampilkan konfirmasi hapus
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <title>Hapus Data Kredit - Sistem Kredit BNI</title>
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
                                        <a href="kredit.php">Data Kredit</a>
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
                                                <p>Anda akan menghapus data kredit berikut:</p>
                                            </div>
                                            
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="30%">ID Kredit</th>
                                                    <td><?php echo $data['kredit_id']; ?></td>
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
                                                    <th>Jumlah Kredit</th>
                                                    <td>Rp <?php echo number_format($data['jumlah_kredit'], 0, ',', '.'); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Status Kredit</th>
                                                    <td><?php echo $data['status_kredit']; ?></td>
                                                </tr>
                                            </table>
                                            
                                            <div class="mt-4">
                                                <p class="text-danger">Data yang sudah dihapus tidak dapat dikembalikan!</p>
                                                <a href="hapus.php?id=<?php echo $id; ?>&confirm=yes" class="btn btn-danger">
                                                    <i class="fa fa-trash"></i> Ya, Hapus Data
                                                </a>
                                                <a href="kredit.php" class="btn btn-secondary">
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
            <script src="../../assets/js/core/jquery.3.2.1.min.js"></script>
            <script src="../../assets/js/core/popper.min.js"></script>
            <script src="../../assets/js/core/bootstrap.min.js"></script>
            <script src="../../assets/js/plugins/bootstrap-switch.js"></script>
            <script src="../../assets/js/plugins/moment.js"></script>
            <script src="../../assets/js/kaiadmin.min.js"></script>
        </body>
        </html>
        <?php
        exit;
    } else {
        // Data tidak ditemukan
        echo "<div style='color:red; padding:20px;'>";
        echo "<h3>Error: Data tidak ditemukan!</h3>";
        echo "<p>Tidak ada data kredit dengan ID: $id</p>";
        echo "<p><a href='kredit.php' class='btn btn-primary'>Kembali ke daftar kredit</a></p>";
        echo "</div>";
        exit;
    }
} else {
    // ID tidak valid
    echo "<div style='color:red; padding:20px;'>";
    echo "<h3>Error: ID kredit tidak valid!</h3>";
    echo "<p>Parameter ID tidak ditemukan atau tidak valid.</p>";
    echo "<p><a href='kredit.php' class='btn btn-primary'>Kembali ke daftar kredit</a></p>";
    echo "</div>";
    exit;
}
?>
