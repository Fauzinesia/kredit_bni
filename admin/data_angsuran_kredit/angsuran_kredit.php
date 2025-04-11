<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';
require_once $root_path . 'config/koneksi.php';

// Note: tanggal_indo() and rupiah() functions are already defined in koneksi.php
// so we don't need to define them again here

$sql = "SELECT ak.*, k.nama_nasabah, k.nama_kredit FROM tb_angsuran_kredit ak
        LEFT JOIN tb_kredit k ON ak.kredit_id = k.kredit_id
        ORDER BY ak.tanggal_angsuran DESC";
$result = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Data Angsuran Kredit - Sistem Kredit BNI</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../../assets/img/logo.PNG" type="image/x-icon" />

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
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../../assets/css/kaiadmin.min.css" />
</head>
<body>
    <div class="wrapper">
        <?php include "../../admin/includes/sidebar.php"; ?>
        <div class="main-panel">
            <?php include "../../admin/includes/navbar.php"; ?>
            <?php include "../../admin/includes/header.php"; ?>

            <div class="container-fluid">
                <div class="page-inner">
                    <div class="page-header">
                        <h4 class="page-title">Data Angsuran Kredit</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Data Angsuran Kredit</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Daftar Data Angsuran Kredit</h4>
                                    <div class="d-flex gap-2">
                                        <a href="cetak.php" class="btn btn-secondary btn-round" target="_blank">
                                            <i class="fa fa-print mr-1"></i> Cetak Data
                                        </a>
                                        <a href="tambah.php" class="btn btn-primary btn-round">
                                            <i class="fa fa-plus mr-1"></i> Tambah Data
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php
                                    if (isset($_SESSION['success'])) {
                                        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                                        unset($_SESSION['success']);
                                    }
                                    if (isset($_SESSION['error'])) {
                                        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                                        unset($_SESSION['error']);
                                    }
                                    ?>
                                    <div class="table-responsive">
                                        <table id="tabel-kredit" class="table table-striped table-hover" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>No. Kredit</th>
                                                    <th>Nasabah</th>
                                                    <th>Jenis Kredit</th>
                                                    <th>Tanggal Angsuran</th>
                                                    <th>Total Angsuran</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($result && mysqli_num_rows($result) > 0) {
                                                    $no = 1;
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        $status = $row['status_pembayaran'];
                                                        $tanggal_indo = tanggal_indo($row['tanggal_angsuran']);
                                                        $total_angsuran = rupiah($row['total_angsuran']);

                                                        // Replace match expression with if-else for compatibility
                                                        if ($status == 'Lunas') {
                                                            $statusClass = 'badge-primary';
                                                        } elseif ($status == 'Belum Dibayar') {
                                                            $statusClass = 'badge-warning';
                                                        } elseif ($status == 'Terlambat') {
                                                            $statusClass = 'badge-danger';
                                                        } elseif ($status == 'Proses') {
                                                            $statusClass = 'badge-info';
                                                        } else {
                                                            $statusClass = 'badge-secondary';
                                                        }

                                                        echo "<tr>";
                                                        echo "<td>{$no}</td>";
                                                        echo "<td>{$row['no_kredit']}</td>";
                                                        echo "<td>{$row['nama_nasabah']}</td>";
                                                        echo "<td>{$row['nama_kredit']}</td>";
                                                        echo "<td>{$tanggal_indo}</td>";
                                                        echo "<td>{$total_angsuran}</td>";
                                                        echo "<td><span class='badge {$statusClass}'>{$status}</span></td>";
                                                        echo "<td>
                                                                <div class='form-button-action'>
                                                                    <a href='edit.php?id={$row['angsuran_id']}' class='btn btn-link btn-primary' data-toggle='tooltip' title='Edit'>
                                                                        <i class='fa fa-edit'></i>
                                                                    </a>
                                                                    <a href='hapus.php?id={$row['angsuran_id']}' class='btn btn-link btn-danger' data-toggle='tooltip' title='Hapus' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>
                                                                        <i class='fa fa-trash'></i>
                                                                    </a>
                                                                </div>
                                                            </td>";
                                                        echo "</tr>";
                                                        $no++;
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='8' class='text-center'>Tidak ada data angsuran</td></tr>";
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
                <?php include "../../admin/includes/footer.php"; ?>
            </div>
        </div>
    </div>

    <script src="../../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/core/popper.min.js"></script>
    <script src="../../assets/js/core/bootstrap.min.js"></script>
    <script src="../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../../assets/js/kaiadmin.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tabel-kredit').DataTable({
                "pageLength": 10,
                "responsive": true,
                "scrollX": true,
                "autoWidth": false
            });
        });
    </script>
</body>
</html>
