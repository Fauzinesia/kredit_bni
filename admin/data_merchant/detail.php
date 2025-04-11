<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';
require_once $root_path . 'config/koneksi.php';

// Cek apakah ada parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID merchant tidak valid";
    header("Location: merchant.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Proses perubahan status verifikasi jika ada
if (isset($_POST['update_verification'])) {
    $status_verifikasi = mysqli_real_escape_string($koneksi, $_POST['status_verifikasi']);
    
    $query_update = "UPDATE tb_merchant SET status_verifikasi = ? WHERE merchant_id = ?";
    $stmt_update = mysqli_prepare($koneksi, $query_update);
    mysqli_stmt_bind_param($stmt_update, "si", $status_verifikasi, $id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['success'] = "Status verifikasi merchant berhasil diubah menjadi " . $status_verifikasi;
    } else {
        $_SESSION['error'] = "Gagal mengubah status verifikasi: " . mysqli_error($koneksi);
    }
    
    // Redirect ke halaman detail yang sama
    header("Location: detail.php?id=" . $id);
    exit;
}

// Proses perubahan status merchant jika ada
if (isset($_POST['update_status'])) {
    $status_merchant = mysqli_real_escape_string($koneksi, $_POST['status_merchant']);
    
    $query_update = "UPDATE tb_merchant SET status_merchant = ? WHERE merchant_id = ?";
    $stmt_update = mysqli_prepare($koneksi, $query_update);
    mysqli_stmt_bind_param($stmt_update, "si", $status_merchant, $id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['success'] = "Status merchant berhasil diubah menjadi " . $status_merchant;
    } else {
        $_SESSION['error'] = "Gagal mengubah status merchant: " . mysqli_error($koneksi);
    }
    
    // Redirect ke halaman detail yang sama
    header("Location: detail.php?id=" . $id);
    exit;
}

// Ambil data merchant berdasarkan ID
$query = "SELECT * FROM tb_merchant WHERE merchant_id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Data merchant tidak ditemukan";
    header("Location: merchant.php");
    exit;
}

$merchant = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Detail Merchant - Sistem Kredit BNI</title>
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
    <style>
        .merchant-img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
        }
        .status-badge {
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 4px;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        .detail-value {
            font-weight: 400;
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
                        <h4 class="page-title">Detail Merchant</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="merchant.php">Data Merchant</a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Detail Merchant</a></li>
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
                                        <h4 class="card-title">Informasi Merchant</h4>
                                        <div class="ml-auto">
                                            <a href="merchant.php" class="btn btn-primary btn-round">
                                                <i class="fa fa-arrow-left mr-1"></i> Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center mb-4">
                                            <?php if (!empty($merchant['foto_merchant']) && file_exists($root_path . $merchant['foto_merchant'])): ?>
                                                <img src="../../<?= $merchant['foto_merchant'] ?>" class="merchant-img" alt="Foto Merchant">
                                            <?php else: ?>
                                                <div class="border p-5 rounded">
                                                    <i class="fas fa-store fa-5x text-muted"></i>
                                                    <p class="mt-3 text-muted">Tidak ada foto</p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="mt-3">
                                                <h5><?= $merchant['nama_merchant'] ?></h5>
                                                <p class="text-muted"><?= $merchant['kode_merchant'] ?></p>
                                            </div>
                                            
                                            <div class="mt-3">
                                                <!-- Status Verifikasi -->
                                                <form method="post" action="" class="mb-3">
                                                    <div class="form-group">
                                                        <label for="status_verifikasi">Status Verifikasi:</label>
                                                        <div class="input-group">
                                                            <select class="form-control" id="status_verifikasi" name="status_verifikasi">
                                                                <option value="Belum Diverifikasi" <?= ($merchant['status_verifikasi'] == 'Belum Diverifikasi') ? 'selected' : '' ?>>Belum Diverifikasi</option>
                                                                <option value="Terverifikasi" <?= ($merchant['status_verifikasi'] == 'Terverifikasi') ? 'selected' : '' ?>>Terverifikasi</option>
                                                                <option value="Ditolak" <?= ($merchant['status_verifikasi'] == 'Ditolak') ? 'selected' : '' ?>>Ditolak</option>
                                                            </select>
                                                            <div class="input-group-append">
                                                                <button type="submit" name="update_verification" class="btn btn-primary">
                                                                    <i class="fa fa-save"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                
                                                <!-- Status Merchant -->
                                                <form method="post" action="">
                                                    <div class="form-group">
                                                        <label for="status_merchant">Status Merchant:</label>
                                                        <div class="input-group">
                                                            <select class="form-control" id="status_merchant" name="status_merchant">
                                                                <option value="Aktif" <?= ($merchant['status_merchant'] == 'Aktif') ? 'selected' : '' ?>>Aktif</option>
                                                                <option value="Nonaktif" <?= ($merchant['status_merchant'] == 'Nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                                                                <option value="Blacklist" <?= ($merchant['status_merchant'] == 'Blacklist') ? 'selected' : '' ?>>Blacklist</option>
                                                            </select>
                                                            <div class="input-group-append">
                                                                <button type="submit" name="update_status" class="btn btn-primary">
                                                                    <i class="fa fa-save"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h4>Informasi Merchant</h4>
                                                    <hr>
                                                    
                                                    <div class="mb-3">
                                                        <p class="detail-label mb-1">Nama Merchant</p>
                                                        <p class="detail-value"><?= $merchant['nama_merchant'] ?></p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <p class="detail-label mb-1">Kode Merchant</p>
                                                        <p class="detail-value"><?= $merchant['kode_merchant'] ?></p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <p class="detail-label mb-1">Jenis Usaha</p>
                                                        <p class="detail-value"><?= $merchant['jenis_usaha'] ?></p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <p class="detail-label mb-1">Tanggal Terdaftar</p>
                                                        <p class="detail-value"><?= tanggal_indo(date('Y-m-d', strtotime($merchant['tanggal_terdaftar']))) ?></p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <p class="detail-label mb-1">Alamat</p>
                                                        <p class="detail-value">
                                                            <?= $merchant['alamat'] ?><br>
                                                            <?= $merchant['kota'] ?>, <?= $merchant['provinsi'] ?> <?= $merchant['kode_pos'] ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <h4>Informasi Pemilik</h4>
                                                    <hr>
                                                    
                                                    <div class="mb-3">
                                                        <p class="detail-label mb-1">Nama Pemilik</p>
                                                        <p class="detail-value"><?= $merchant['nama_pemilik'] ?></p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <p class="detail-label mb-1">NIK Pemilik</p>
                                                        <p class="detail-value"><?= $merchant['nik_pemilik'] ?></p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <p class="detail-label mb-1">Kontak</p>
                                                        <p class="detail-value"><?= $merchant['kontak'] ?></p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <p class="detail-label mb-1">Email</p>
                                                        <p class="detail-value"><?= $merchant['email'] ?></p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <p class="detail-label mb-1">Dokumen Pendukung</p>
                                                        <p class="detail-value">
                                                            <?php if (!empty($merchant['dokumen_pendukung']) && file_exists($root_path . $merchant['dokumen_pendukung'])): ?>
                                                                <a href="../../<?= $merchant['dokumen_pendukung'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-file-download mr-1"></i> Lihat Dokumen
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted">Tidak ada dokumen</span>
                                                                <?php endif; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-4">
                                                <div class="col-md-12">
                                                    <h4>Catatan</h4>
                                                    <hr>
                                                    <div class="mb-3">
                                                        <p class="detail-value">
                                                            <?php if (!empty($merchant['catatan'])): ?>
                                                                <?= nl2br($merchant['catatan']) ?>
                                                            <?php else: ?>
                                                                <span class="text-muted">Tidak ada catatan</span>
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-4">
                                                <div class="col-md-12">
                                                    <div class="d-flex justify-content-end">
                                                        <a href="edit.php?id=<?= $merchant['merchant_id'] ?>" class="btn btn-primary mr-2">
                                                            <i class="fa fa-edit mr-1"></i> Edit
                                                        </a>
                                                        <a href="#" onclick="confirmDelete(<?= $merchant['merchant_id'] ?>)" class="btn btn-danger">
                                                            <i class="fa fa-trash mr-1"></i> Hapus
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
    <script src="../../assets/js/kaiadmin.min.js"></script>
    <script>
        function confirmDelete(id) {
            $('#btn-delete').attr('href', 'merchant.php?action=delete&id=' + id);
            $('#deleteModal').modal('show');
        }
    </script>
</body>
</html>