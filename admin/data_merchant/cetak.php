<?php
// Mulai session
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Tentukan path ke root project
$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';

// Include file konfigurasi database menggunakan path absolut
require_once $root_path . 'config/koneksi.php';

// Query untuk mengambil data merchant
$sql = "SELECT * FROM tb_merchant ORDER BY tanggal_terdaftar DESC";
$result = mysqli_query($koneksi, $sql);

// Dapatkan tanggal hari ini untuk header laporan
$tanggal_cetak = tanggal_indo(date('Y-m-d'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Merchant - Sistem Kredit BNI</title>
    <link rel="icon" href="../../assets/img/logo.PNG" type="image/x-icon" />
    
    <!-- CSS untuk cetak -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 80px;
            vertical-align: middle;
        }
        .title {
            font-size: 18pt;
            font-weight: bold;
            margin: 10px 0;
        }
        .subtitle {
            font-size: 14pt;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10pt;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        @media print {
            @page {
                size: landscape;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="../../assets/img/logo.PNG" alt="Logo BNI" class="logo">
        <div class="title">LAPORAN DATA MERCHANT</div>
        <div class="subtitle">PT. BANK NEGARA INDONESIA (PERSERO) TBK.</div>
        <div>Tanggal Cetak: <?php echo $tanggal_cetak; ?></div>
    </div>
    
    <button class="no-print" onclick="window.print()">Cetak Laporan</button>
    <button class="no-print" onclick="window.location.href='merchant.php'">Kembali</button>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Merchant</th>
                <th>Nama Merchant</th>
                <th>Pemilik</th>
                <th>NIK Pemilik</th>
                <th>Kontak</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>Jenis Usaha</th>
                <th>Status Verifikasi</th>
                <th>Status Merchant</th>
                <th>Tanggal Terdaftar</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    // Tentukan class badge berdasarkan status verifikasi
                    $verifikasiClass = '';
                    switch ($row['status_verifikasi']) {
                        case 'Terverifikasi':
                            $verifikasiClass = 'badge-success';
                            break;
                        case 'Ditolak':
                            $verifikasiClass = 'badge-danger';
                            break;
                        default:
                            $verifikasiClass = 'badge-warning';
                            break;
                    }
                    
                    // Tentukan class badge berdasarkan status merchant
                    $merchantClass = '';
                    switch ($row['status_merchant']) {
                        case 'Aktif':
                            $merchantClass = 'badge-success';
                            break;
                        case 'Blacklist':
                            $merchantClass = 'badge-danger';
                            break;
                        default:
                            $merchantClass = 'badge-secondary';
                            break;
                    }
                    
                    // Format tanggal terdaftar
                    $tanggal_terdaftar = date('Y-m-d', strtotime($row['tanggal_terdaftar']));
                    $tanggal_indo = tanggal_indo($tanggal_terdaftar);
                    
                    // Alamat lengkap
                    $alamat_lengkap = $row['alamat'];
                    if (!empty($row['kota'])) {
                        $alamat_lengkap .= ', ' . $row['kota'];
                    }
                    if (!empty($row['provinsi'])) {
                        $alamat_lengkap .= ', ' . $row['provinsi'];
                    }
                    if (!empty($row['kode_pos'])) {
                        $alamat_lengkap .= ' ' . $row['kode_pos'];
                    }
                    
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . $row['kode_merchant'] . "</td>";
                    echo "<td>" . $row['nama_merchant'] . "</td>";
                    echo "<td>" . $row['nama_pemilik'] . "</td>";
                    echo "<td>" . $row['nik_pemilik'] . "</td>";
                    echo "<td>" . $row['kontak'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $alamat_lengkap . "</td>";
                    echo "<td>" . $row['jenis_usaha'] . "</td>";
                    echo "<td><span class='badge " . $verifikasiClass . "'>" . $row['status_verifikasi'] . "</span></td>";
                    echo "<td><span class='badge " . $merchantClass . "'>" . $row['status_merchant'] . "</span></td>";
                    echo "<td>" . $tanggal_indo . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='12' class='text-center'>Tidak ada data merchant</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Jakarta, <?php echo $tanggal_cetak; ?></p>
        <br><br><br>
        <p>(_________________________)</p>
        <p>Manager Kredit</p>
    </div>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            // Uncomment line below to automatically open print dialog when page loads
            // window.print();
        }
    </script>
</body>
</html>
