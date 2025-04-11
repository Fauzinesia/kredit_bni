<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';
require_once $root_path . 'config/koneksi.php';

// Proses perubahan status verifikasi
if (isset($_GET['action']) && $_GET['action'] == 'verify' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);
    
    $query_update = "UPDATE tb_merchant SET status_verifikasi = ? WHERE merchant_id = ?";
    $stmt_update = mysqli_prepare($koneksi, $query_update);
    mysqli_stmt_bind_param($stmt_update, "si", $status, $id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['success'] = "Status verifikasi merchant berhasil diubah menjadi " . $status;
    } else {
        $_SESSION['error'] = "Gagal mengubah status verifikasi: " . mysqli_error($koneksi);
    }
    
    header("Location: merchant.php");
    exit;
}

// Proses perubahan status merchant
if (isset($_GET['action']) && $_GET['action'] == 'status' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);
    
    $query_update = "UPDATE tb_merchant SET status_merchant = ? WHERE merchant_id = ?";
    $stmt_update = mysqli_prepare($koneksi, $query_update);
    mysqli_stmt_bind_param($stmt_update, "si", $status, $id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['success'] = "Status merchant berhasil diubah menjadi " . $status;
    } else {
        $_SESSION['error'] = "Gagal mengubah status merchant: " . mysqli_error($koneksi);
    }
    
    header("Location: merchant.php");
    exit;
}

// Proses hapus data jika ada
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Ambil data merchant untuk mendapatkan file yang akan dihapus
    $query_get = "SELECT foto_merchant, dokumen_pendukung FROM tb_merchant WHERE merchant_id = ?";
    $stmt_get = mysqli_prepare($koneksi, $query_get);
    mysqli_stmt_bind_param($stmt_get, "i", $id);
    mysqli_stmt_execute($stmt_get);
    $result_get = mysqli_stmt_get_result($stmt_get);
    $merchant = mysqli_fetch_assoc($result_get);
    
    // Hapus file foto jika ada
    if (!empty($merchant['foto_merchant']) && file_exists($root_path . $merchant['foto_merchant'])) {
        unlink($root_path . $merchant['foto_merchant']);
    }
    
    // Hapus file dokumen jika ada
    if (!empty($merchant['dokumen_pendukung']) && file_exists($root_path . $merchant['dokumen_pendukung'])) {
        unlink($root_path . $merchant['dokumen_pendukung']);
    }
    
    // Hapus data dari database
    $query_delete = "DELETE FROM tb_merchant WHERE merchant_id = ?";
    $stmt_delete = mysqli_prepare($koneksi, $query_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $id);
    
    if (mysqli_stmt_execute($stmt_delete)) {
        $_SESSION['success'] = "Data merchant berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($koneksi);
    }
    
    header("Location: merchant.php");
    exit;
}

// Ambil data merchant untuk ditampilkan
$query = "SELECT * FROM tb_merchant ORDER BY tanggal_terdaftar DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Data Merchant - Sistem Kredit BNI</title>
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
    <link rel="stylesheet" href="../../assets/css/datatables.min.css" />
    <style>
        .merchant-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .doc-icon {
            font-size: 24px;
            color: #3498db;
        }
        .dropdown-menu {
            min-width: 180px;
        }
        .dropdown-item {
            padding: 0.5rem 1rem;
        }
        .dropdown-divider {
            margin: 0.25rem 0;
        }
    </style>
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
                        <h4 class="page-title">Data Merchant</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Data Merchant</a></li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Daftar Merchant </h4>
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
                                    
                                    <div class="table-responsive">
                                        <table id="merchant-table" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Foto</th>
                                                    <th>Kode</th>
                                                    <th>Nama Merchant</th>
                                                    <th>Pemilik</th>
                                                    <th>Kontak</th>
                                                    <th>Jenis Usaha</th>
                                                    <th>Dokumen</th>
                                                    <th>Status Verifikasi</th>
                                                    <th>Status Merchant</th>
                                                    <th>Tanggal Terdaftar</th>
                                                    <th style="width: 10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>";
                                                    echo "<td>" . $no++ . "</td>";
                                                    
                                                    // Foto Merchant
                                                    echo "<td>";
                                                    if (!empty($row['foto_merchant']) && file_exists($root_path . $row['foto_merchant'])) {
                                                        echo "<img src='../../" . $row['foto_merchant'] . "' class='merchant-img' alt='Foto Merchant'>";
                                                    } else {
                                                        echo "<i class='fas fa-store fa-2x text-muted'></i>";
                                                    }
                                                    echo "</td>";
                                                    
                                                    echo "<td>" . $row['kode_merchant'] . "</td>";
                                                    echo "<td>" . $row['nama_merchant'] . "</td>";
                                                    echo "<td>" . $row['nama_pemilik'] . "</td>";
                                                    echo "<td>" . $row['kontak'] . "</td>";
                                                    echo "<td>" . $row['jenis_usaha'] . "</td>";
                                                    
                                                    // Dokumen Pendukung
                                                    echo "<td>";
                                                    if (!empty($row['dokumen_pendukung'])) {
                                                        $file_ext = pathinfo($row['dokumen_pendukung'], PATHINFO_EXTENSION);
                                                        $icon_class = 'fa-file';
                                                        
                                                        if (in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png'])) {
                                                            $icon_class = 'fa-file-image';
                                                        } elseif (strtolower($file_ext) == 'pdf') {
                                                            $icon_class = 'fa-file-pdf';
                                                        }
                                                        
                                                        echo "<a href='../../" . $row['dokumen_pendukung'] . "' target='_blank' title='Lihat Dokumen'>";
                                                        echo "<i class='fas " . $icon_class . " doc-icon'></i>";
                                                        echo "</a>";
                                                    } else {
                                                        echo "<i class='fas fa-times text-muted'></i>";
                                                    }
                                                    echo "</td>";
                                                    
                                                    // Status verifikasi dengan dropdown
                                                    $status_verifikasi_class = '';
                                                    if ($row['status_verifikasi'] == 'Terverifikasi') {
                                                        $status_verifikasi_class = 'success';
                                                    } elseif ($row['status_verifikasi'] == 'Ditolak') {
                                                        $status_verifikasi_class = 'danger';
                                                    } else {
                                                        $status_verifikasi_class = 'warning';
                                                    }
                                                    
                                                    echo "<td>
                                                        <div class='dropdown'>
                                                            <button class='btn btn-" . $status_verifikasi_class . " btn-sm dropdown-toggle' type='button' id='verifyDropdown" . $row['merchant_id'] . "' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                                " . $row['status_verifikasi'] . "
                                                            </button>
                                                            <div class='dropdown-menu' aria-labelledby='verifyDropdown" . $row['merchant_id'] . "'>
                                                                <h6 class='dropdown-header'>Ubah Status Verifikasi</h6>
                                                                <a class='dropdown-item' href='merchant.php?action=verify&id=" . $row['merchant_id'] . "&status=Belum Diverifikasi'>Belum Diverifikasi</a>
                                                                <a class='dropdown-item' href='merchant.php?action=verify&id=" . $row['merchant_id'] . "&status=Terverifikasi'>Terverifikasi</a>
                                                                <a class='dropdown-item' href='merchant.php?action=verify&id=" . $row['merchant_id'] . "&status=Ditolak'>Ditolak</a>
                                                            </div>
                                                        </div>
                                                    </td>";
                                                    
                                                    // Status merchant dengan dropdown
                                                    $status_merchant_class = '';
                                                    if ($row['status_merchant'] == 'Aktif') {
                                                        $status_merchant_class = 'success';
                                                    } elseif ($row['status_merchant'] == 'Blacklist') {
                                                        $status_merchant_class = 'danger';
                                                    } else {
                                                        $status_merchant_class = 'secondary';
                                                    }
                                                    
                                                    echo "<td>
                                                        <div class='dropdown'>
                                                            <button class='btn btn-" . $status_merchant_class . " btn-sm dropdown-toggle' type='button' id='statusDropdown" . $row['merchant_id'] . "' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                                " . $row['status_merchant'] . "
                                                            </button>
                                                            <div class='dropdown-menu' aria-labelledby='statusDropdown" . $row['merchant_id'] . "'>
                                                                <h6 class='dropdown-header'>Ubah Status Merchant</h6>
                                                                <a class='dropdown-item' href='merchant.php?action=status&id=" . $row['merchant_id'] . "&status=Aktif'>Aktif</a>
                                                                <a class='dropdown-item' href='merchant.php?action=status&id=" . $row['merchant_id'] . "&status=Nonaktif'>Nonaktif</a>
                                                                <a class='dropdown-item' href='merchant.php?action=status&id=" . $row['merchant_id'] . "&status=Blacklist'>Blacklist</a>
                                                            </div>
                                                        </div>
                                                    </td>";
                                                    
                                                    echo "<td>" . date('d-m-Y', strtotime($row['tanggal_terdaftar'])) . "</td>";
                                                    echo "<td>
                                                            <div class='btn-group'>
                                                                <a href='detail.php?id=" . $row['merchant_id'] . "' class='btn btn-info btn-sm' title='Detail'>
                                                                    <i class='fa fa-eye'></i>
                                                                </a>
                                                                <a href='edit.php?id=" . $row['merchant_id'] . "' class='btn btn-primary btn-sm' title='Edit'>
                                                                    <i class='fa fa-edit'></i>
                                                                </a>
                                                                <a href='#' onclick='confirmDelete(" . $row['merchant_id'] . ")' class='btn btn-danger btn-sm' title='Hapus'>
                                                                    <i class='fa fa-trash'></i>
                                                                </a>
                                                            </div>
                                                          </td>";
                                                    echo "</tr>";
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
                    Apakah Anda yakin ingin menghapus data merchant ini? Data yang dihapus tidak dapat dikembalikan.
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
            $('#merchant-table').DataTable({
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
                },
                "responsive": true
            });
        });
        
        function confirmDelete(id) {
            $('#btn-delete').attr('href', 'merchant.php?action=delete&id=' + id);
            $('#deleteModal').modal('show');
        }
    </script>
</body>
</html>

