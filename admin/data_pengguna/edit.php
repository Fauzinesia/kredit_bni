<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';
require_once $root_path . 'config/koneksi.php';

// Pastikan ID pengguna tersedia
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// First, let's check the table structure to find the correct column name
$check_columns = "SHOW COLUMNS FROM tb_user";
$columns_result = mysqli_query($koneksi, $check_columns);
$id_column = 'id'; // Default column name

if ($columns_result) {
    while ($column = mysqli_fetch_assoc($columns_result)) {
        // Look for likely primary key column names
        if ($column['Key'] == 'PRI' || 
            $column['Field'] == 'id' || 
            $column['Field'] == 'user_id' || 
            $column['Field'] == 'id_user' || 
            $column['Field'] == 'userid') {
            $id_column = $column['Field'];
            break;
        }
    }
}

// Query untuk mengambil data pengguna berdasarkan ID
$query = "SELECT * FROM tb_user WHERE $id_column = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Jika data tidak ditemukan, redirect ke halaman pengguna
if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit;
}

$user = mysqli_fetch_assoc($result);

// Proses form jika ada submit
if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $level = mysqli_real_escape_string($koneksi, $_POST['level']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    
    // Validasi input
    $errors = [];
    
    if (empty($nama)) {
        $errors[] = "Nama harus diisi";
    }
    
    if (empty($username)) {
        $errors[] = "Username harus diisi";
    } else {
        // Cek apakah username sudah digunakan oleh pengguna lain
        $check_query = "SELECT $id_column FROM tb_user WHERE username = ? AND $id_column != ?";
        $check_stmt = mysqli_prepare($koneksi, $check_query);
        mysqli_stmt_bind_param($check_stmt, "si", $username, $id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "Username sudah digunakan, silakan pilih username lain";
        }
    }
    
    // Validasi email jika diisi
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    // Upload foto profil jika ada
    $foto_profil = isset($user['foto_profil']) ? $user['foto_profil'] : ''; // Default ke nilai yang sudah ada
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['foto_profil']['type'], $allowed_types)) {
            $errors[] = "Tipe file foto profil tidak didukung. Gunakan JPG, JPEG, atau PNG";
        } elseif ($_FILES['foto_profil']['size'] > $max_size) {
            $errors[] = "Ukuran file foto profil terlalu besar (maksimal 2MB)";
        } else {
            $upload_dir = $root_path . 'uploads/users/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['foto_profil']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
                // Hapus file lama jika ada
                if (!empty($user['foto_profil']) && file_exists($root_path . $user['foto_profil'])) {
                    unlink($root_path . $user['foto_profil']);
                }
                $foto_profil = 'uploads/users/' . $file_name;
            } else {
                $errors[] = "Gagal mengupload foto profil";
            }
        }
    }
    
    // Jika tidak ada error, update data
    if (empty($errors)) {
        // Cek apakah password diubah
        $password_query = "";
        $password_param = "";
        $types = "sssss";
        $params = [$nama, $username, $email, $level, $status];
        
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $password_query = ", password = ?";
            $types .= "s";
            $params[] = $password;
        }
        
        // Tambahkan foto profil ke parameter jika kolom ada
        $foto_query = "";
        if (array_key_exists('foto_profil', $user)) {
            $foto_query = ", foto_profil = ?";
            $types .= "s";
            $params[] = $foto_profil;
        }
        
        // Tambahkan ID ke parameter
        $types .= "i";
        $params[] = $id;
        
        $query = "UPDATE tb_user SET 
                  nama = ?, 
                  username = ?, 
                  email = ?, 
                  level = ?, 
                  status = ?
                  $password_query
                  $foto_query
                  WHERE $id_column = ?";
        
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Data pengguna berhasil diperbarui";
            header("Location: index.php");
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
    <title>Edit Pengguna - Sistem Kredit BNI</title>
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
                        <h4 class="page-title">Edit Pengguna</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="index.php">Data Pengguna</a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Edit Pengguna</a></li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Form Edit Pengguna</div>
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
                                                    <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nama" name="nama" value="<?= isset($user['nama']) ? $user['nama'] : ''; ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="username">Username <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="username" name="username" value="<?= isset($user['username']) ? $user['username'] : ''; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?= isset($user['email']) ? $user['email'] : ''; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                                                    <input type="password" class="form-control" id="password" name="password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="level">Level <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="level" name="level" required>
                                                        <option value="admin" <?= (isset($user['level']) && $user['level'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                        <option value="petugas" <?= (isset($user['level']) && $user['level'] == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                                                        <option value="nasabah" <?= (isset($user['level']) && $user['level'] == 'nasabah') ? 'selected' : ''; ?>>Nasabah</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status">Status <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="status" name="status" required>
                                                        <option value="aktif" <?= (isset($user['status']) && $user['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                                        <option value="nonaktif" <?= (isset($user['status']) && $user['status'] == 'nonaktif') ? 'selected' : ''; ?>>Non-Aktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if (array_key_exists('foto_profil', $user)): ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="foto_profil">Foto Profil</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="foto_profil" name="foto_profil" accept="image/jpeg,image/png,image/jpg">
                                                        <label class="custom-file-label" for="foto_profil">Pilih file...</label>
                                                        <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                                                    </div>
                                                    <?php if (!empty($user['foto_profil']) && file_exists($root_path . $user['foto_profil'])): ?>
                                                        <div class="mt-2">
                                                            <p>Foto saat ini:</p>
                                                            <img src="../../<?= $user['foto_profil']; ?>" class="preview-img" alt="Foto Profil">
                                                        </div>
                                                    <?php endif; ?>
                                                    <div id="preview_foto" class="mt-2"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="card-action">
                                            <button type="submit" name="submit" class="btn btn-primary">
                                                <i class="fa fa-save mr-1"></i> Simpan Perubahan
                                            </button>
                                            <a href="pengguna.php" class="btn btn-danger">
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
    <script src="../../assets/js/kaiadmin.min.js"></script>
    <script>
        $(document).ready(function() {
            // Preview foto profil saat dipilih
            $('#foto_profil').change(function() {
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
        });
    </script>
</body>
</html>
