<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';
require_once $root_path . 'config/koneksi.php';

// Ambil data user untuk dropdown
$query_user = "SELECT id, nama FROM tb_user ORDER BY nama ASC";
$result_user = mysqli_query($koneksi, $query_user);

// Proses form jika ada data yang dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = mysqli_real_escape_string($koneksi, $_POST['user_id']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $jam_masuk = !empty($_POST['jam_masuk']) ? mysqli_real_escape_string($koneksi, $_POST['jam_masuk']) : null;
    $jam_keluar = !empty($_POST['jam_keluar']) ? mysqli_real_escape_string($koneksi, $_POST['jam_keluar']) : null;
    $status_absensi = mysqli_real_escape_string($koneksi, $_POST['status_absensi']);
    $keterangan = !empty($_POST['keterangan']) ? mysqli_real_escape_string($koneksi, $_POST['keterangan']) : null;
    $latitude = !empty($_POST['latitude']) ? mysqli_real_escape_string($koneksi, $_POST['latitude']) : null;
    $longitude = !empty($_POST['longitude']) ? mysqli_real_escape_string($koneksi, $_POST['longitude']) : null;
    $lokasi_absensi = !empty($_POST['lokasi_absensi']) ? mysqli_real_escape_string($koneksi, $_POST['lokasi_absensi']) : null;
    
    // Validasi data
    $errors = [];
    if (empty($user_id)) {
        $errors[] = "Nama karyawan harus dipilih";
    }
    if (empty($tanggal)) {
        $errors[] = "Tanggal harus diisi";
    }
    if (empty($status_absensi)) {
        $errors[] = "Status absensi harus dipilih";
    }
    
    // Jika status Hadir, jam masuk harus diisi
    if ($status_absensi == 'Hadir' && empty($jam_masuk)) {
        $errors[] = "Jam masuk harus diisi untuk status Hadir";
    }
    
    // Proses upload foto jika ada
    $foto_absensi = null;
    if (isset($_FILES['foto_absensi']) && $_FILES['foto_absensi']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['foto_absensi']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (!in_array(strtolower($ext), $allowed)) {
            $errors[] = "Format file tidak didukung. Gunakan format JPG, JPEG, atau PNG";
        } else {
            $upload_dir = 'uploads/absensi/';
            $upload_path = $root_path . $upload_dir;
            
            // Buat direktori jika belum ada
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            // Generate nama file unik
            $new_filename = 'absensi_' . date('YmdHis') . '_' . uniqid() . '.' . $ext;
            $destination = $upload_path . $new_filename;
            
            if (move_uploaded_file($_FILES['foto_absensi']['tmp_name'], $destination)) {
                $foto_absensi = $upload_dir . $new_filename;
            } else {
                $errors[] = "Gagal mengupload file";
            }
        }
    }
    
    // Jika tidak ada error, simpan data
    if (empty($errors)) {
        $query = "INSERT INTO tb_absensi_karyawan (user_id, tanggal, jam_masuk, jam_keluar, status_absensi, keterangan, latitude, longitude, lokasi_absensi, foto_absensi) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "isssssddss", $user_id, $tanggal, $jam_masuk, $jam_keluar, $status_absensi, $keterangan, $latitude, $longitude, $lokasi_absensi, $foto_absensi);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Data absensi berhasil ditambahkan";
            header("Location: absensi.php");
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
    <title>Tambah Absensi - Sistem Kredit BNI</title>
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
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map {
            height: 300px;
            width: 100%;
            margin-top: 10px;
            border-radius: 5px;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 5px;
            display: none;
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
                        <h4 class="page-title">Tambah Data Absensi</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="absensi.php">Data Absensi</a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Tambah Absensi</a></li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Form Tambah Absensi</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="user_id">Nama Karyawan <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="user_id" name="user_id" required>
                                                        <option value="">-- Pilih Karyawan --</option>
                                                        <?php 
                                                        if ($result_user && mysqli_num_rows($result_user) > 0) {
                                                            while ($row = mysqli_fetch_assoc($result_user)) {
                                                                $selected = (isset($_POST['user_id']) && $_POST['user_id'] == $row['id']) ? 'selected' : '';
                                                                echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['nama']) . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d'); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="jam_masuk">Jam Masuk</label>
                                                    <input type="time" class="form-control" id="jam_masuk" name="jam_masuk" value="<?= isset($_POST['jam_masuk']) ? $_POST['jam_masuk'] : ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="jam_keluar">Jam Keluar</label>
                                                    <input type="time" class="form-control" id="jam_keluar" name="jam_keluar" value="<?= isset($_POST['jam_keluar']) ? $_POST['jam_keluar'] : ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_absensi">Status Absensi <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="status_absensi" name="status_absensi" required>
                                                        <option value="">-- Pilih Status --</option>
                                                        <option value="Hadir" <?= (isset($_POST['status_absensi']) && $_POST['status_absensi'] == 'Hadir') ? 'selected' : ''; ?>>Hadir</option>
                                                        <option value="Izin" <?= (isset($_POST['status_absensi']) && $_POST['status_absensi'] == 'Izin') ? 'selected' : ''; ?>>Izin</option>
                                                        <option value="Sakit" <?= (isset($_POST['status_absensi']) && $_POST['status_absensi'] == 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                                                        <option value="Cuti" <?= (isset($_POST['status_absensi']) && $_POST['status_absensi'] == 'Cuti') ? 'selected' : ''; ?>>Cuti</option>
                                                        <option value="Alpha" <?= (isset($_POST['status_absensi']) && $_POST['status_absensi'] == 'Alpha') ? 'selected' : ''; ?>>Alpha</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="keterangan">Keterangan</label>
                                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= isset($_POST['keterangan']) ? $_POST['keterangan'] : ''; ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="foto_absensi">Foto Absensi</label>
                                                    <input type="file" class="form-control-file" id="foto_absensi" name="foto_absensi" accept="image/*" onchange="previewImage(this)">
                                                    <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                                                    <img id="preview" class="preview-image" src="#" alt="Preview">
                                                </div>
                                                <div class="form-group">
                                                    <label>Lokasi</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="lokasi_absensi" name="lokasi_absensi" placeholder="Lokasi akan terdeteksi otomatis" value="<?= isset($_POST['lokasi_absensi']) ? $_POST['lokasi_absensi'] : ''; ?>" readonly>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary" type="button" id="getLocation">
                                                                <i class="fa fa-map-marker-alt"></i> Deteksi Lokasi
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="latitude" name="latitude" value="<?= isset($_POST['latitude']) ? $_POST['latitude'] : ''; ?>">
                                                    <input type="hidden" id="longitude" name="longitude" value="<?= isset($_POST['longitude']) ? $_POST['longitude'] : ''; ?>">
                                                    <div id="map"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-action">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save"></i> Simpan
                                            </button>
                                            <a href="absensi.php" class="btn btn-danger">
                                                <i class="fa fa-times"></i> Batal
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
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        // Preview image before upload
        function previewImage(input) {
            var preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }
        
        // Map initialization
        let map;
        let marker;
        
        function initMap(lat = -6.2088, lng = 106.8456) { // Default to Jakarta coordinates
            // Create map
            map = L.map('map').setView([lat, lng], 15);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add marker
            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);
            
            // Update coordinates when marker is dragged
            marker.on('dragend', function(event) {
                var position = marker.getLatLng();
                updateLocation(position.lat, position.lng);
            });
            
            // Force map to update its size
            setTimeout(function() {
                map.invalidateSize();
            }, 100);
        }
        
        // Get current location
        document.getElementById('getLocation').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        // Update map
                        if (map && marker) {
                            map.setView([lat, lng], 15);
                            marker.setLatLng([lat, lng]);
                        } else {
                            initMap(lat, lng);
                        }
                        
                        // Update form fields
                        updateLocation(lat, lng);
                    },
                    function(error) {
                        let errorMessage;
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = "Akses lokasi ditolak oleh pengguna.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = "Informasi lokasi tidak tersedia.";
                                break;
                            case error.TIMEOUT:
                                errorMessage = "Waktu permintaan lokasi habis.";
                                break;
                            case error.UNKNOWN_ERROR:
                                errorMessage = "Terjadi kesalahan yang tidak diketahui.";
                                break;
                        }
                        alert("Error: " + errorMessage);
                    }
                );
            } else {
                alert("Geolocation tidak didukung oleh browser ini.");
            }
        });
        
        // Update location fields
        function updateLocation(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Use Nominatim for reverse geocoding (OpenStreetMap's service)
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('lokasi_absensi').value = data.display_name;
                    } else {
                        document.getElementById('lokasi_absensi').value = lat + ", " + lng;
                    }
                })
                .catch(error => {
                    console.error('Error getting address:', error);
                    document.getElementById('lokasi_absensi').value = lat + ", " + lng;
                });
        }
        
        // Initialize map when page loads
        window.onload = function() {
            // Check if we have saved coordinates
            const savedLat = parseFloat(document.getElementById('latitude').value);
            const savedLng = parseFloat(document.getElementById('longitude').value);
            
            if (!isNaN(savedLat) && !isNaN(savedLng)) {
                initMap(savedLat, savedLng);
            } else {
                initMap();
            }
            
            // Show image preview if already uploaded
            const preview = document.getElementById('preview');
            if (preview.src && preview.src !== window.location.href) {
                preview.style.display = 'block';
            }
        };
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const statusAbsensi = document.getElementById('status_absensi').value;
            const jamMasuk = document.getElementById('jam_masuk').value;
            
            if (statusAbsensi === 'Hadir' && !jamMasuk) {
                e.preventDefault();
                alert('Jam masuk harus diisi untuk status Hadir');
            }
        });
    </script>
</body>
</html>