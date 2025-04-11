<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';
require_once $root_path . 'config/koneksi.php';

// Pastikan ID merchant tersedia
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: merchant.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Query untuk mengambil data merchant berdasarkan ID
$query = "SELECT * FROM tb_merchant WHERE merchant_id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Jika data tidak ditemukan, redirect ke halaman merchant
if (mysqli_num_rows($result) == 0) {
    header("Location: merchant.php");
    exit;
}

$merchant = mysqli_fetch_assoc($result);

// Proses form jika ada submit
if (isset($_POST['submit'])) {
    $nama_merchant = mysqli_real_escape_string($koneksi, $_POST['nama_merchant']);
    $nama_pemilik = mysqli_real_escape_string($koneksi, $_POST['nama_pemilik']);
    $nik_pemilik = mysqli_real_escape_string($koneksi, $_POST['nik_pemilik']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $provinsi = mysqli_real_escape_string($koneksi, $_POST['provinsi']);
    $kota = mysqli_real_escape_string($koneksi, $_POST['kota']);
    $kode_pos = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $jenis_usaha = mysqli_real_escape_string($koneksi, $_POST['jenis_usaha']);
    $npwp = mysqli_real_escape_string($koneksi, $_POST['npwp']);
    $status_verifikasi = mysqli_real_escape_string($koneksi, $_POST['status_verifikasi']);
    $status_merchant = mysqli_real_escape_string($koneksi, $_POST['status_merchant']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    
    // Validasi input
    $errors = [];
    
    if (empty($nama_merchant)) {
        $errors[] = "Nama merchant harus diisi";
    }
    
    if (empty($nama_pemilik)) {
        $errors[] = "Nama pemilik harus diisi";
    }
    
    // Validasi email jika diisi
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    // Upload foto merchant jika ada
    $foto_merchant = $merchant['foto_merchant']; // Default ke nilai yang sudah ada
    if (isset($_FILES['foto_merchant']) && $_FILES['foto_merchant']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['foto_merchant']['type'], $allowed_types)) {
            $errors[] = "Tipe file foto merchant tidak didukung. Gunakan JPG, JPEG, atau PNG";
        } elseif ($_FILES['foto_merchant']['size'] > $max_size) {
            $errors[] = "Ukuran file foto merchant terlalu besar (maksimal 2MB)";
        } else {
            $upload_dir = $root_path . 'uploads/merchant/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['foto_merchant']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['foto_merchant']['tmp_name'], $target_file)) {
                // Hapus file lama jika ada
                if (!empty($merchant['foto_merchant']) && file_exists($root_path . $merchant['foto_merchant'])) {
                    unlink($root_path . $merchant['foto_merchant']);
                }
                $foto_merchant = 'uploads/merchant/' . $file_name;
            } else {
                $errors[] = "Gagal mengupload foto merchant";
            }
        }
    }
    
    // Upload dokumen pendukung jika ada
    $dokumen_pendukung = $merchant['dokumen_pendukung']; // Default ke nilai yang sudah ada
    if (isset($_FILES['dokumen_pendukung']) && $_FILES['dokumen_pendukung']['error'] == 0) {
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['dokumen_pendukung']['type'], $allowed_types)) {
            $errors[] = "Tipe file dokumen pendukung tidak didukung. Gunakan PDF, JPG, JPEG, atau PNG";
        } elseif ($_FILES['dokumen_pendukung']['size'] > $max_size) {
            $errors[] = "Ukuran file dokumen pendukung terlalu besar (maksimal 5MB)";
        } else {
            $upload_dir = $root_path . 'uploads/dokumen/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['dokumen_pendukung']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['dokumen_pendukung']['tmp_name'], $target_file)) {
                // Hapus file lama jika ada
                if (!empty($merchant['dokumen_pendukung']) && file_exists($root_path . $merchant['dokumen_pendukung'])) {
                    unlink($root_path . $merchant['dokumen_pendukung']);
                }
                $dokumen_pendukung = 'uploads/dokumen/' . $file_name;
            } else {
                $errors[] = "Gagal mengupload dokumen pendukung";
            }
        }
    }
    
    // Jika tidak ada error, update data
    if (empty($errors)) {
        $query = "UPDATE tb_merchant SET 
                  nama_merchant = ?, 
                  nama_pemilik = ?, 
                  nik_pemilik = ?, 
                  alamat = ?, 
                  provinsi = ?, 
                  kota = ?, 
                  kode_pos = ?, 
                  kontak = ?, 
                  email = ?, 
                  jenis_usaha = ?, 
                  npwp = ?, 
                  status_verifikasi = ?, 
                  status_merchant = ?, 
                  foto_merchant = ?, 
                  dokumen_pendukung = ?, 
                  keterangan = ? 
                  WHERE merchant_id = ?";
        
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssssssssssssssssi", $nama_merchant, $nama_pemilik, $nik_pemilik, $alamat, $provinsi, $kota, $kode_pos, $kontak, $email, $jenis_usaha, $npwp, $status_verifikasi, $status_merchant, $foto_merchant, $dokumen_pendukung, $keterangan, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Data merchant berhasil diperbarui";
            header("Location: merchant.php");
            exit;
        } else {
            $errors[] = "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Edit Merchant - Sistem Kredit BNI</title>
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
    <link rel="stylesheet" href="../../assets/css/select2.min.css" />
    <style>
        .preview-img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 5px;
            margin-top: 10px;
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
                        <h4 class="page-title">Edit Merchant</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="merchant.php">Data Merchant</a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Edit Merchant</a></li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Form Edit Merchant</div>
                                </div>
                                <div class="card-body">
                                    <?php
                                    if (!empty($errors)) {
                                        echo '<div class="alert alert-danger"><ul>';
                                        foreach ($errors as $error) {
                                            echo '<li>' . $error . '</li>';
                                        }
                                        echo '</ul></div>';
                                    }
                                    ?>
                                    <form method="post" action="" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kode_merchant">Kode Merchant</label>
                                                    <input type="text" class="form-control" id="kode_merchant" value="<?= $merchant['kode_merchant']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nama_merchant">Nama Merchant <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nama_merchant" name="nama_merchant" value="<?= $merchant['nama_merchant']; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nama_pemilik">Nama Pemilik <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nama_pemilik" name="nama_pemilik" value="<?= $merchant['nama_pemilik']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nik_pemilik">NIK Pemilik</label>
                                                    <input type="text" class="form-control" id="nik_pemilik" name="nik_pemilik" maxlength="16" value="<?= $merchant['nik_pemilik']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="alamat">Alamat</label>
                                                    <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= $merchant['alamat']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="provinsi">Provinsi</label>
                                                    <input type="text" class="form-control" id="provinsi" name="provinsi" value="<?= $merchant['provinsi']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kota">Kota/Kabupaten</label>
                                                    <input type="text" class="form-control" id="kota" name="kota" value="<?= $merchant['kota']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form
                                                <div class="form-group">
                                                    <label for="kode_pos">Kode Pos</label>
                                                    <input type="text" class="form-control" id="kode_pos" name="kode_pos" maxlength="10" value="<?= $merchant['kode_pos']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="kontak">Nomor Telepon/HP</label>
                                                    <input type="text" class="form-control" id="kontak" name="kontak" value="<?= $merchant['kontak']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?= $merchant['email']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="jenis_usaha">Jenis Usaha</label>
                                                    <select class="form-control select2" id="jenis_usaha" name="jenis_usaha">
                                                        <option value="">-- Pilih Jenis Usaha --</option>
                                                        <option value="Retail" <?= ($merchant['jenis_usaha'] == 'Retail') ? 'selected' : ''; ?>>Retail</option>
                                                        <option value="Kuliner" <?= ($merchant['jenis_usaha'] == 'Kuliner') ? 'selected' : ''; ?>>Kuliner</option>
                                                        <option value="Fashion" <?= ($merchant['jenis_usaha'] == 'Fashion') ? 'selected' : ''; ?>>Fashion</option>
                                                        <option value="Elektronik" <?= ($merchant['jenis_usaha'] == 'Elektronik') ? 'selected' : ''; ?>>Elektronik</option>
                                                        <option value="Jasa" <?= ($merchant['jenis_usaha'] == 'Jasa') ? 'selected' : ''; ?>>Jasa</option>
                                                        <option value="Otomotif" <?= ($merchant['jenis_usaha'] == 'Otomotif') ? 'selected' : ''; ?>>Otomotif</option>
                                                        <option value="Kesehatan" <?= ($merchant['jenis_usaha'] == 'Kesehatan') ? 'selected' : ''; ?>>Kesehatan</option>
                                                        <option value="Pendidikan" <?= ($merchant['jenis_usaha'] == 'Pendidikan') ? 'selected' : ''; ?>>Pendidikan</option>
                                                        <option value="Lainnya" <?= ($merchant['jenis_usaha'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="npwp">NPWP</label>
                                                    <input type="text" class="form-control" id="npwp" name="npwp" maxlength="25" value="<?= $merchant['npwp']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status_verifikasi">Status Verifikasi</label>
                                                    <select class="form-control" id="status_verifikasi" name="status_verifikasi" required>
                                                        <option value="Belum Diverifikasi" <?= ($merchant['status_verifikasi'] == 'Belum Diverifikasi') ? 'selected' : ''; ?>>Belum Diverifikasi</option>
                                                        <option value="Terverifikasi" <?= ($merchant['status_verifikasi'] == 'Terverifikasi') ? 'selected' : ''; ?>>Terverifikasi</option>
                                                        <option value="Ditolak" <?= ($merchant['status_verifikasi'] == 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status_merchant">Status Merchant</label>
                                                    <select class="form-control" id="status_merchant" name="status_merchant" required>
                                                        <option value="Aktif" <?= ($merchant['status_merchant'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                                                        <option value="Nonaktif" <?= ($merchant['status_merchant'] == 'Nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                                                        <option value="Blacklist" <?= ($merchant['status_merchant'] == 'Blacklist') ? 'selected' : ''; ?>>Blacklist</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="foto_merchant">Foto Merchant</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="foto_merchant" name="foto_merchant" accept="image/jpeg,image/png,image/jpg">
                                                        <label class="custom-file-label" for="foto_merchant">Pilih file...</label>
                                                        <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                                                    </div>
                                                    <?php if (!empty($merchant['foto_merchant']) && file_exists($root_path . $merchant['foto_merchant'])): ?>
                                                        <div class="mt-2">
                                                            <p>Foto saat ini:</p>
                                                            <img src="../../<?= $merchant['foto_merchant']; ?>" class="preview-img" alt="Foto Merchant">
                                                        </div>
                                                    <?php endif; ?>
                                                    <div id="preview_foto" class="mt-2"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="dokumen_pendukung">Dokumen Pendukung</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="dokumen_pendukung" name="dokumen_pendukung" accept="application/pdf,image/jpeg,image/png,image/jpg">
                                                        <label class="custom-file-label" for="dokumen_pendukung">Pilih file...</label>
                                                        <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maks: 5MB</small>
                                                    </div>
                                                    <?php if (!empty($merchant['dokumen_pendukung'])): ?>
                                                        <div class="mt-2">
                                                            <p>Dokumen saat ini:</p>
                                                            <?php 
                                                            $file_ext = pathinfo($merchant['dokumen_pendukung'], PATHINFO_EXTENSION);
                                                            if (in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png']) && file_exists($root_path . $merchant['dokumen_pendukung'])): 
                                                            ?>
                                                                <img src="../../<?= $merchant['dokumen_pendukung']; ?>" class="preview-img" alt="Dokumen Pendukung">
                                                            <?php else: ?>
                                                                <p>File <?= strtoupper($file_ext); ?> - <a href="../../<?= $merchant['dokumen_pendukung']; ?>" target="_blank">Lihat Dokumen</a></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="keterangan">Keterangan</label>
                                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= $merchant['keterangan']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-action">
                                            <button type="submit" name="submit" class="btn btn-primary">
                                                <i class="fa fa-save mr-1"></i> Simpan Perubahan
                                            </button>
                                            <a href="merchant.php" class="btn btn-danger">
                                                <i class="fa fa-times mr-1"></i> Batal
                                            </a>
                                        </div>
                                    </form>
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
    <script src="../../assets/js/plugin/select2/select2.min.js"></script>
    <script src="../../assets/js/kaiadmin.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2').select2({
                theme: "bootstrap"
            });
            
            // Preview foto merchant saat dipilih
            $('#foto_merchant').change(function() {
                const file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        $('#preview_foto').html('<img src="' + event.target.result + '" class="preview-img">');
                    }
                    reader.readAsDataURL(file);
                    
                    // Update label dengan nama file
                    let fileName = file.name;
                    if(fileName.length > 25) {
                        fileName = fileName.substring(0, 22) + '...';
                    }
                    $(this).next('.custom-file-label').html(fileName);
                } else {
                    $('#preview_foto').html('');
                    $(this).next('.custom-file-label').html('Pilih file...');
                }
            });
            
            // Update label dokumen pendukung saat dipilih
            $('#dokumen_pendukung').change(function() {
                const file = this.files[0];
                if (file) {
                    let fileName = file.name;
                    if(fileName.length > 25) {
                        fileName = fileName.substring(0, 22) + '...';
                    }
                    $(this).next('.custom-file-label').html(fileName);
                } else {
                    $(this).next('.custom-file-label').html('Pilih file...');
                }
            });
            
            // Validasi NIK hanya angka
            $('#nik_pemilik').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 16) {
                    this.value = this.value.substring(0, 16);
                }
            });
            
            // Validasi kode pos hanya angka
            $('#kode_pos').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            
            // Format NPWP
            $('#npwp').on('input', function() {
                let value = this.value.replace(/[^\d]/g, '');
                if (value.length > 15) {
                    value = value.substring(0, 15);
                }
                
                // Format: 00.000.000.0-000.000
                if (value.length > 0) {
                    let formattedValue = '';
                    for (let i = 0; i < value.length; i++) {
                        if (i === 2 || i === 5 || i === 8 || i === 9 || i === 12) {
                            if (i === 9) {
                                formattedValue += '-';
                            } else {
                                formattedValue += '.';
                            }
                        }
                        formattedValue += value[i];
                    }
                    this.value = formattedValue;
                }
            });
        });
    </script>
</body>
</html>
