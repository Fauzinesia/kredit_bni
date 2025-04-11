<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';
require_once $root_path . 'config/koneksi.php';

// Filter data
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$filter_user = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Query dasar
$query = "SELECT a.*, u.nama 
          FROM tb_absensi_karyawan a 
          LEFT JOIN tb_user u ON a.user_id = u.id 
          WHERE 1=1";

// Tambahkan filter
if (!empty($filter_bulan) && !empty($filter_tahun)) {
    $query .= " AND MONTH(a.tanggal) = '$filter_bulan' AND YEAR(a.tanggal) = '$filter_tahun'";
}

if (!empty($filter_user)) {
    $query .= " AND a.user_id = '$filter_user'";
}

$query .= " ORDER BY a.tanggal DESC, a.jam_masuk DESC";
$result = mysqli_query($koneksi, $query);

// Ambil data user untuk dropdown filter
$query_user = "SELECT id, nama FROM tb_user ORDER BY nama ASC";
$result_user = mysqli_query($koneksi, $query_user);

// Fungsi untuk mendapatkan nama bulan
function getNamaBulan($bulan) {
    $nama_bulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    
    return $nama_bulan[$bulan] ?? '';
}

// Judul laporan
$judul_laporan = "Laporan Absensi Karyawan";
if (!empty($filter_bulan) && !empty($filter_tahun)) {
    $judul_laporan .= " - " . getNamaBulan($filter_bulan) . " " . $filter_tahun;
}

// Jika ada filter user, tambahkan nama user ke judul
if (!empty($filter_user)) {
    $query_nama = "SELECT nama FROM tb_user WHERE id = '$filter_user'";
    $result_nama = mysqli_query($koneksi, $query_nama);
    if ($result_nama && mysqli_num_rows($result_nama) > 0) {
        $nama_user = mysqli_fetch_assoc($result_nama)['nama'];
        $judul_laporan .= " - " . $nama_user;
    }
}

// Cek apakah ini mode cetak
$is_print_mode = isset($_GET['print']) && $_GET['print'] == 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Laporan Absensi - Sistem Kredit BNI</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../../assets/img/logo.PNG" type="image/x-icon" />
    
    <?php if (!$is_print_mode): ?>
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
    <?php else: ?>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
    <?php endif; ?>
    
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-hadir {
            background-color: #1cc88a;
            color: white;
        }
        .status-izin {
            background-color: #36b9cc;
            color: white;
        }
        .status-sakit {
            background-color: #f6c23e;
            color: white;
        }
        .status-cuti {
            background-color: #4e73df;
            color: white;
        }
        .status-alpha {
            background-color: #e74a3b;
            color: white;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .card-header {
                background-color: #f8f9fa !important;
                color: #000 !important;
            }
            body {
                font-size: 12pt;
            }
            .container-fluid {
                width: 100%;
                padding: 0;
            }
            .table {
                width: 100%;
                border-collapse: collapse;
            }
            .table th, .table td {
                border: 1px solid #ddd;
                padding: 8px;
            }
            .page-header {
                text-align: center;
                margin-bottom: 20px;
            }
            .company-info {
                text-align: center;
                margin-bottom: 20px;
            }
            .company-info img {
                max-width: 100px;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <?php if (!$is_print_mode): ?>
    <div class="wrapper">
        <?php include "../../admin/includes/sidebar.php"; ?>
        <div class="main-panel">
            <?php include "../../admin/includes/navbar.php"; ?>
            <?php include "../../admin/includes/header.php"; ?>
            <div class="container-fluid">
                <div class="page-inner">
                    <div class="page-header">
                        <h4 class="page-title">Laporan Absensi</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="absensi.php">Data Absensi</a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Laporan Absensi</a></li>
                        </ul>
                    </div>
    <?php else: ?>
    <div class="container-fluid mt-4">
        <div class="company-info">
            <img src="../../assets/img/logo.PNG" alt="Logo BNI">
            <h3>PT. Bank Negara Indonesia (Persero) Tbk.</h3>
            <p>Jl. Terminal Karamat, Barabai Utara, Kec. Barabai, Kabupaten Hulu Sungai Tengah, Kalimantan Selatan 71352</p>
        </div>
    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="card-title"><?= $judul_laporan; ?></h4>
                                        <?php if (!$is_print_mode): ?>
                                        <div class="ml-auto">
                                            <button onclick="window.print()" class="btn btn-success btn-sm mr-2">
                                                <i class="fa fa-print mr-1"></i> Cetak
                                            </button>
                                            <a href="<?= $_SERVER['PHP_SELF'] . '?print=true&' . http_build_query($_GET); ?>" target="_blank" class="btn btn-primary btn-sm">
                                                <i class="fa fa-file-pdf mr-1"></i> PDF
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if (!$is_print_mode): ?>
                                <div class="card-body no-print">
                                    <form method="GET" action="" class="mb-4">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="bulan">Bulan</label>
                                                    <select class="form-control" id="bulan" name="bulan">
                                                        <option value="">Semua Bulan</option>
                                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                                            <?php $bulan = sprintf("%02d", $i); ?>
                                                            <option value="<?= $bulan; ?>" <?= ($filter_bulan == $bulan) ? 'selected' : ''; ?>>
                                                                <?= getNamaBulan($bulan); ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tahun">Tahun</label>
                                                    <select class="form-control" id="tahun" name="tahun">
                                                        <option value="">Semua Tahun</option>
                                                        <?php 
                                                        $tahun_sekarang = date('Y');
                                                        for ($i = $tahun_sekarang; $i >= $tahun_sekarang - 5; $i--): 
                                                        ?>
                                                            <option value="<?= $i; ?>" <?= ($filter_tahun == $i) ? 'selected' : ''; ?>>
                                                                <?= $i; ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="user_id">Karyawan</label>
                                                    <select class="form-control" id="user_id" name="user_id">
                                                        <option value="">Semua Karyawan</option>
                                                        <?php 
                                                        if ($result_user && mysqli_num_rows($result_user) > 0) {
                                                            while ($row = mysqli_fetch_assoc($result_user)) {
                                                                $selected = ($filter_user == $row['id']) ? 'selected' : '';
                                                                echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['nama']) . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <button type="submit" class="btn btn-primary btn-block">
                                                        <i class="fa fa-filter"></i> Filter
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <?php if ($is_print_mode): ?>
                                    <div class="page-header text-center mb-4">
                                        <h4><?= $judul_laporan; ?></h4>
                                        <p>Tanggal Cetak: <?= date('d-m-Y H:i:s'); ?></p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Tanggal</th>
                                                    <th>Jam Masuk</th>
                                                    <th>Jam Keluar</th>
                                                    <th>Status</th>
                                                    <th>Lokasi</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                if (mysqli_num_rows($result) > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        // Determine status class
                                                        $statusClass = '';
                                                        switch ($row['status_absensi']) {
                                                            case 'Hadir':
                                                                $statusClass = 'status-hadir';
                                                                break;
                                                            case 'Izin':
                                                                $statusClass = 'status-izin';
                                                                break;
                                                            case 'Sakit':
                                                                $statusClass = 'status-sakit';
                                                                break;
                                                            case 'Cuti':
                                                                $statusClass = 'status-cuti';
                                                                break;
                                                            case 'Alpha':
                                                                $statusClass = 'status-alpha';
                                                                break;
                                                        }
                                                        
                                                         // Format tanggal
                                                         $tanggal = date('d-m-Y', strtotime($row['tanggal']));
                                                        
                                                         echo "<tr>";
                                                         echo "<td>" . $no++ . "</td>";
                                                         echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                                         echo "<td>" . $tanggal . "</td>";
                                                         echo "<td>" . ($row['jam_masuk'] ? date('H:i', strtotime($row['jam_masuk'])) : '-') . "</td>";
                                                         echo "<td>" . ($row['jam_keluar'] ? date('H:i', strtotime($row['jam_keluar'])) : '-') . "</td>";
                                                         echo "<td><span class='status-badge " . $statusClass . "'>" . $row['status_absensi'] . "</span></td>";
                                                         echo "<td>" . (empty($row['lokasi_absensi']) ? '-' : htmlspecialchars($row['lokasi_absensi'])) . "</td>";
                                                         echo "<td>" . (empty($row['keterangan']) ? '-' : htmlspecialchars($row['keterangan'])) . "</td>";
                                                         echo "</tr>";
                                                     }
                                                 } else {
                                                     echo "<tr><td colspan='8' class='text-center'>Tidak ada data absensi</td></tr>";
                                                 }
                                                 ?>
                                             </tbody>
                                         </table>
                                     </div>
                                     
                                     <?php if ($is_print_mode): ?>
                                     <div class="row mt-5">
                                         <div class="col-md-6 offset-md-6 text-right">
                                             <p>Jakarta, <?= date('d F Y'); ?></p>
                                             <br><br><br>
                                             <p>(_________________________)</p>
                                             <p>Manager HRD</p>
                                         </div>
                                     </div>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <?php if (!$is_print_mode): ?>
                 <?php include "../../admin/includes/footer.php"; ?>
                 <?php endif; ?>
             </div>
         </div>
     </div>
     
     <?php if (!$is_print_mode): ?>
     <script src="../../assets/js/core/jquery-3.7.1.min.js"></script>
     <script src="../../assets/js/core/popper.min.js"></script>
     <script src="../../assets/js/core/bootstrap.min.js"></script>
     <script src="../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
     <script src="../../assets/js/kaiadmin.min.js"></script>
     <script>
         $(document).ready(function() {
             // Auto submit form when filter changes
             $('#bulan, #tahun, #user_id').change(function() {
                 $(this).closest('form').submit();
             });
             
             <?php if ($is_print_mode): ?>
             // Auto print when in print mode
             window.onload = function() {
                 window.print();
             }
             <?php endif; ?>
         });
     </script>
     <?php else: ?>
     <script>
         window.onload = function() {
             window.print();
         }
     </script>
     <?php endif; ?>
 </body>
 </html>
 