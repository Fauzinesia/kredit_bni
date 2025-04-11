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

// Proses form jika ada submit
if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($koneksi, $_POST['confirm_password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    
    // Validasi input
    $errors = [];
    
    if (empty($nama)) {
        $errors[] = "Nama harus diisi";
    }
    
    if (empty($username)) {
        $errors[] = "Username harus diisi";
    } else {
        // Cek apakah username sudah digunakan
        $check_username = "SELECT id FROM tb_user WHERE username = ?";
        $stmt_check = mysqli_prepare($koneksi, $check_username);
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $errors[] = "Username sudah digunakan, silakan pilih username lain";
        }
    }
    
    if (empty($password)) {
        $errors[] = "Password harus diisi";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak sesuai";
    }
    
    if (empty($role)) {
        $errors[] = "Role harus dipilih";
    }
    
    // Jika tidak ada error, simpan data
    if (empty($errors)) {
        // Enkripsi password dengan MD5
        $hashed_password = md5($password);
        
        $query = "INSERT INTO tb_user (nama, username, password, role) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssss", $nama, $username, $hashed_password, $role);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Data pengguna berhasil ditambahkan";
            header("Location: pengguna.php");
            exit;
        } else {
            $errors[] = "Gagal menyimpan data: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Tambah Pengguna - Sistem Kredit BNI</title>
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
                        <h4 class="page-title">Tambah Pengguna</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="pengguna.php">Data Pengguna</a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Tambah Pengguna</a></li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Form Tambah Pengguna</div>
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
                                    <form method="post" action="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nama" name="nama" value="<?= isset($_POST['nama']) ? $_POST['nama'] : '' ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="username">Username <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="username" name="username" value="<?= isset($_POST['username']) ? $_POST['username'] : '' ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">Password <span class="text-danger">*</span></label>
                                                    <input type="password" class="form-control" id="password" name="password" required>
                                                    <small class="text-muted">Password minimal 6 karakter</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="confirm_password">Konfirmasi Password <span class="text-danger">*</span></label>
                                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="role">Role <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="role" name="role" required>
                                                        <option value="">-- Pilih Role --</option>
                                                        <option value="Admin" <?= (isset($_POST['role']) && $_POST['role'] == 'Admin') ? 'selected' : '' ?>>Admin</option>
                                                        <option value="Operator" <?= (isset($_POST['role']) && $_POST['role'] == 'Operator') ? 'selected' : '' ?>>Operator</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-action">
                                            <button type="submit" name="submit" class="btn btn-primary">
                                                <i class="fa fa-save mr-1"></i> Simpan
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
        // Validasi form
        $(document).ready(function() {
            $('form').submit(function(e) {
                var password = $('#password').val();
                var confirm_password = $('#confirm_password').val();
                
                if (password.length < 6) {
                    alert('Password minimal 6 karakter');
                    e.preventDefault();
                    return false;
                }
                
                if (password !== confirm_password) {
                    alert('Konfirmasi password tidak sesuai');
                    e.preventDefault();
                    return false;
                }
                
                return true;
            });
        });
    </script>
</body>
</html>
