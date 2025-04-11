<?php
// Mulai session
session_start();

// Tampilkan semua error (untuk debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['login_user']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        header("location: admin/index.php");
        exit;
    } else if ($_SESSION['role'] == 'Operator') {
        header("location: operator/index.php");
        exit;
    }
}

// Koneksi database langsung di file ini
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'kredit_bni';

$koneksi = mysqli_connect($host, $username, $password, $database);
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Fungsi anti_injection
function anti_injection($data) {
    global $koneksi;
    $filter = mysqli_real_escape_string($koneksi, stripslashes(strip_tags(htmlspecialchars($data, ENT_QUOTES))));
    return $filter;
}

// Variabel untuk menyimpan pesan error
$error = "";
$success = "";

// Cek apakah form login telah disubmit
if (isset($_POST['login'])) {
    // Ambil data dari form
    $username = anti_injection($_POST['username']);
    $password = anti_injection($_POST['password']);
    
    // Query untuk memeriksa user
    $query = "SELECT * FROM tb_user WHERE username = '$username' AND password = MD5('$password')";
    $result = mysqli_query($koneksi, $query);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        // Login berhasil, buat session
        $_SESSION['login_user'] = $username;
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['user_id'] = $row['id'];
        
        // Set pesan sukses sebelum redirect
        $success = "Login berhasil! Anda akan dialihkan dalam 3 detik...";
        
        // Redirect dengan JavaScript setelah menampilkan pesan
        echo "<script>
            setTimeout(function() {
                window.location.href = '" . ($row['role'] == 'Admin' ? 'admin/index.php' : 'operator/index.php') . "';
            }, 3000);
        </script>";
    } else {
        // Username atau password salah
        $error = "Username atau Password tidak valid!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Login - Sistem Kredit BNI</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
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
                urls: ["assets/css/fonts.min.css"],
            },
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    
    <style>
        :root {
            --bni-orange: #006675;
            --bni-blue: #da550e;
            --bni-light-blue: #7fe4a1
            --bni-dark-blue: #da550e;
        }
        
        body {
            background: linear-gradient(135deg, var(--bni-dark-blue), var(--bni-blue));
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .login-container {
            width: 400px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border-top: 5px solid var(--bni-orange);
        }
        
        .login-header {
            background-color: var(--bni-blue);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        
        .login-header:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--bni-orange);
        }
        
        .login-header img {
            height: 40px;
            margin-bottom: 10px;
        }
        
        .login-header h3 {
            margin: 0;
            font-weight: 600;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            height: 45px;
            border-radius: 5px;
            box-shadow: none;
            border: 1px solid #e0e0e0;
        }
        
        .form-control:focus {
            border-color: var(--bni-blue);
            box-shadow: 0 0 0 0.2rem rgba(0, 82, 156, 0.25);
        }
        
        .btn-login {
            background-color: var(--bni-orange);
            color: white;
            border: none;
            height: 45px;
            border-radius: 5px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background-color: #e55c00;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 102, 0, 0.3);
        }
        
        .login-footer {
            text-align: center;
            padding: 15px;
            border-top: 1px solid #f1f1f1;
            color: #757575;
            font-size: 13px;
            background-color: #f9f9f9;
        }
        
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 14px;
            color: var(--bni-blue);
        }
        
        .input-icon input {
            padding-left: 40px;
        }
        
        .form-check-input:checked {
            background-color: var(--bni-orange);
            border-color: var(--bni-orange);
        }
        
        label {
            color: var(--bni-blue);
            font-weight: 500;
        }
        
        .animated-background {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        
        .circle {
            position: absolute;
            border-radius: 50%;
        }
        
        .circle-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            left: -150px;
            background: rgba(0, 82, 156, 0.2);
        }
        
        .circle-2 {
            width: 500px;
            height: 500px;
            bottom: -250px;
            right: -250px;
            background: rgba(255, 102, 0, 0.1);
        }
        
        .circle-3 {
            width: 200px;
            height: 200px;
            top: 50%;
            right: 50px;
            background: rgba(0, 124, 194, 0.15);
        }
        
        .bni-logo-text {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            color: white;
        }
        
        .bni-logo-text span {
            color: var(--bni-orange);
        }
        
        .bni-tagline {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="animated-background">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
    </div>
    
    <div class="login-container">
        <div class="login-header">
        <div class="logo-container">
            <img src="assets/img/logo.PNG" alt="BNI Logo" class="bni-logo">
        </div>
            <h3>Sistem Kredit BNI</h3>
            <p class="bni-tagline mb-0">Bank Negara Indonesia KC Barabai</p>
    </div>
        
        <div class="login-body">
            <?php if(isset($error) && $error != ""): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <?php if(isset($success) && $success != ""): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle mr-2"></i> <?php echo $success; ?>
            </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                
                <button type="submit" name="login" class="btn btn-login">LOGIN</button>
            </form>
        </div>
        
        <div class="login-footer">
            © 2025 Bank Negara Indonesia KC Barabai
        </div>
    </div>

    <!--   Core JS Files   -->
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Animasi sederhana untuk form login
            $('.login-container').css({
                'opacity': 0,
                'transform': 'translateY(20px)'
            }).animate({
                opacity: 1,
                transform: 'translateY(0)'
            }, 800);
            
            // Efek hover pada input
            $('.form-control').focus(function() {
                $(this).parent().addClass('input-focus');
            }).blur(function() {
                $(this).parent().removeClass('input-focus');
            });
            
            // Validasi form sederhana
            $('form').submit(function(e) {
                var username = $('#username').val();
                var password = $('#password').val();
                
                if(username.trim() === '' || password.trim() === '') {
                    e.preventDefault();
                    alert('Username dan Password harus diisi!');
                    return false;
                }
            });
        });
    </script>
</body>
</html>
