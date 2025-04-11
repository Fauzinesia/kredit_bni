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

// Proses hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Cek apakah user yang akan dihapus adalah user yang sedang login
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error'] = "Anda tidak dapat menghapus akun yang sedang digunakan";
        header("Location: pengguna.php");
        exit;
    }
    
    // Hapus data dari database
    $query_delete = "DELETE FROM tb_user WHERE id = ?";
    $stmt_delete = mysqli_prepare($koneksi, $query_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $id);
    
    if (mysqli_stmt_execute($stmt_delete)) {
        $_SESSION['success'] = "Data pengguna berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($koneksi);
    }
    
    header("Location: pengguna.php");
    exit;
}

// Ambil data pengguna
$query = "SELECT * FROM tb_user ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Data Pengguna - Sistem Kredit BNI</title>
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
                        <h4 class="page-title">Data Pengguna</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Data Pengguna</a></li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success">
                                    <?= $_SESSION['success']; ?>
                                    <?php unset($_SESSION['success']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger">
                                    <?= $_SESSION['error']; ?>
                                    <?php unset($_SESSION['error']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Daftar Pengguna</h4>
                                        <div class="ml-auto">
                                            <a href="cetak.php" class="btn btn-info btn-round mr-2" target="_blank">
                                                <i class="fa fa-print mr-1"></i> Cetak
                                            </a>
                                            <a href="tambah.php" class="btn btn-primary btn-round">
                                                <i class="fa fa-plus mr-1"></i> Tambah Pengguna
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="pengguna-table" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Username</th>
                                                    <th>Role</th>
                                                    <th>Terdaftar</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                if (mysqli_num_rows($result) > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo "<tr>";
                                                        echo "<td>" . $no++ . "</td>";
                                                        echo "<td>" . $row['nama'] . "</td>";
                                                        echo "<td>" . $row['username'] . "</td>";
                                                        
                                                        // Role dengan badge
                                                        echo "<td>";
                                                        if ($row['role'] == 'Admin') {
                                                            echo "<span class='badge badge-primary'>Admin</span>";
                                                        } else {
                                                            echo "<span class='badge badge-secondary'>Operator</span>";
                                                        }
                                                        echo "</td>";
                                                        
                                                        echo "<td>" . date('d-m-Y H:i', strtotime($row['created_at'])) . "</td>";
                                                        
                                                        // Tombol aksi
                                                        echo "<td>";
                                                        echo "<div class='btn-group'>";
                                                        
                                                        // Hanya tampilkan tombol edit dan hapus jika bukan user yang sedang login
                                                        if ($row['id'] != $_SESSION['user_id']) {
                                                            echo "<a href='edit.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm' title='Edit'><i class='fa fa-edit'></i></a>";
                                                            echo "<a href='#' onclick='confirmDelete(" . $row['id'] . ")' class='btn btn-danger btn-sm' title='Hapus'><i class='fa fa-trash'></i></a>";
                                                        } else {
                                                            echo "<a href='edit.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm' title='Edit'><i class='fa fa-edit'></i></a>";
                                                            echo "<button class='btn btn-danger btn-sm' disabled title='Tidak dapat menghapus akun yang sedang digunakan'><i class='fa fa-trash'></i></button>";
                                                        }
                                                        
                                                        echo "</div>";
                                                        echo "</td>";
                                                        echo "</tr>";
                                                    }
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
    
    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data pengguna ini? Data yang dihapus tidak dapat dikembalikan.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a href="#" id="btn-delete" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/core/popper.min.js"></script>
    <script src="../../assets/js/core/bootstrap.min.js"></script>
    <script src="../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../../assets/js/kaiadmin.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#pengguna-table').DataTable({
                "pageLength": 10,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data yang tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
        });
        
        function confirmDelete(id) {
            $('#btn-delete').attr('href', 'pengguna.php?action=delete&id=' + id);
            $('#deleteModal').modal('show');
        }
    </script>
</body>
</html>
