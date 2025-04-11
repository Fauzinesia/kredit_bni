<?php
// Mulai session
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    // Untuk sementara kita nonaktifkan redirect ke login
    // header("Location: ../../login.php");
    // exit;
}

// Tentukan path ke root project
$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';

// Include file konfigurasi database menggunakan path absolut
require_once $root_path . 'config/koneksi.php';

// Query untuk mengambil data kredit
$sql = "SELECT * FROM tb_kredit ORDER BY tanggal_pengajuan DESC";
$result = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Data Kredit - Sistem Kredit BNI</title>
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
                        <h4 class="page-title">Data Kredit</h4>
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
                                <a href="#">Data Kredit</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Daftar Data Kredit</h4>
                                    <div class="d-flex gap-2">
                                        <a href="cetak.php" class="btn btn-secondary btn-round" target="_blank">
                                            <i class="fa fa-print mr-1"></i>
                                            Cetak Data Kredit
                                        </a>
                                        <a href="tambah.php" class="btn btn-primary btn-round">
                                            <i class="fa fa-plus mr-1"></i>
                                            Tambah Data Kredit
                                        </a>
                                    </div>
                                </div>
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
                                   
                                    <div class="table-responsive" style="width: 100%; overflow-x: auto;">
                                        <table id="tabel-kredit" class="table table-striped table-hover" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>No. Kredit</th>
                                                    <th>Nasabah</th>
                                                    <th>Jenis Kredit</th>
                                                    <th>Jumlah</th>
                                                    <th>Jangka Waktu</th>
                                                    <th>Angsuran/Bulan</th>
                                                    <th>Tanggal Pengajuan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($result && mysqli_num_rows($result) > 0) {
                                                    $no = 1;
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        // Pastikan kolom 'status', 'jumlah_kredit', 'angsuran_per_bulan', 'no_kredit', 'jenis_kredit', 'jangka_waktu', dan 'id' ada
                                                        $status = isset($row['status_kredit']) ? $row['status_kredit'] : 'Tidak Diketahui';
                                                        $jumlah_kredit = isset($row['jumlah_kredit']) ? $row['jumlah_kredit'] : 0;
                                                        $angsuran_per_bulan = isset($row['angsuran_per_bulan']) ? $row['angsuran_per_bulan'] : 0;
                                                        $no_kredit = isset($row['no_kredit']) ? $row['no_kredit'] : 'Tidak Ada';
                                                        $nama_kredit = isset($row['nama_kredit']) ? $row['nama_kredit'] : 'Tidak Ada';
                                                        $tenor = isset($row['tenor']) ? $row['tenor'] : 'Tidak Diketahui';
                                                        $id = isset($row['id']) ? $row['id'] : 0;

                                                        // Tentukan class badge berdasarkan status
                                                        $statusClass = '';
                                                        switch ($status) {
                                                            case 'Disetujui':
                                                                $statusClass = 'badge-success';
                                                                break;
                                                            case 'Ditolak':
                                                                $statusClass = 'badge-danger';
                                                                break;
                                                            case 'Dalam Proses':
                                                                $statusClass = 'badge-warning';
                                                                break;
                                                            case 'Diajukan':
                                                                $statusClass = 'badge-info';
                                                                break;
                                                            case 'Lunas':
                                                                $statusClass = 'badge-primary';
                                                                break;
                                                            default:
                                                                $statusClass = 'badge-secondary';
                                                                break;
                                                        }

                                                        // Format tanggal pengajuan
                                                        $tanggal_pengajuan = date('Y-m-d', strtotime($row['tanggal_pengajuan']));
                                                        $tanggal_indo = tanggal_indo($tanggal_pengajuan);

                                                        // Format jumlah kredit dan angsuran menggunakan fungsi rupiah()
                                                        $jumlah_kredit = rupiah($jumlah_kredit);
                                                        $angsuran_rupiah = rupiah($angsuran_per_bulan);

                                                        echo "<tr>";
                                                        echo "<td>" . $no++ . "</td>";
                                                        echo "<td>" . $no_kredit . "</td>";
                                                        echo "<td>" . $row['nama_nasabah'] . "</td>";
                                                        echo "<td>" . $nama_kredit . "</td>";
                                                        echo "<td>" . $jumlah_kredit . "</td>";
                                                        echo "<td>" . $tenor . " bulan</td>";
                                                        echo "<td>" . $angsuran_rupiah . "</td>";
                                                        echo "<td>" . $tanggal_indo . "</td>";
                                                        echo "<td><span class='badge " . $statusClass . "'>" . $status . "</span></td>";
                                                        echo "<td>
                                                                <div class='form-button-action'>
                                                                    <a href='edit.php?id=" . $row['kredit_id'] . "' class='btn btn-link btn-primary btn-lg' data-toggle='tooltip' title='Edit Data'>
                                                                        <i class='fa fa-edit'></i>
                                                                    </a>
                                                                    <a href='hapus.php?id=" . $row['kredit_id'] . "' class='btn btn-link btn-danger btn-lg' data-toggle='tooltip' title='Edit Data'>
                                                                        <i class='fa fa-trash'></i>
                                                                    </a>
                                                                </div>
                                                            </td>";
                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='10' class='text-center'>Tidak ada data kredit</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
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
   
    <!--   Core JS Files   -->
    <script src="../../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/core/popper.min.js"></script>
    <script src="../../assets/js/core/bootstrap.min.js"></script>
   
    <!-- jQuery Scrollbar -->
    <script src="../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
   
    <!-- Datatables -->
    <script src="../../assets/js/plugin/datatables/datatables.min.js"></script>
   
    <!-- Sweet Alert -->
    <script src="../../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
   
    <!-- Kaiadmin JS -->
    <script src="../../assets/js/kaiadmin.min.js"></script>
   
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables dengan responsivitas
            $('#tabel-kredit').DataTable({
                "pageLength": 10,
                "responsive": true,
                "scrollX": true,
                "autoWidth": false,
                "columnDefs": [
                    { "width": "5%", "targets": 0 },
                    { "width": "10%", "targets": 1 },
                    { "width": "15%", "targets": 2 },
                    { "width": "10%", "targets": 3 },
                    { "width": "12%", "targets": 4 },
                    { "width": "8%", "targets": 5 },
                    { "width": "12%", "targets": 6 },
                    { "width": "10%", "targets": 7 },
                    { "width": "8%", "targets": 8 },
                    { "width": "10%", "targets": 9 }
                ]
            });
        });
    </script>
</body>
</html>
