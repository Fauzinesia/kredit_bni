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

// Query untuk mengambil data kredit rumah
$sql = "SELECT * FROM tb_kredit_rumah ORDER BY tanggal_pengajuan DESC";
$result = mysqli_query($koneksi, $sql);

// Dapatkan tanggal hari ini untuk header laporan
$tanggal_cetak = tanggal_indo(date('Y-m-d'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Kredit Rumah - Sistem Kredit BNI</title>
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
        <div class="title">LAPORAN DATA KREDIT RUMAH</div>
        <div class="subtitle">PT. BANK NEGARA INDONESIA (PERSERO) TBK.</div>
        <div>Tanggal Cetak: <?php echo $tanggal_cetak; ?></div>
    </div>
    
    <button class="no-print" onclick="window.print()">Cetak Laporan</button>
    <button class="no-print" onclick="window.location.href='kredit_rumah.php'">Kembali</button>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Kredit</th>
                <th>Nasabah</th>
                <th>Jenis Kredit</th>
                <th>Jumlah</th>
                <th>Jangka Waktu</th>
                <th>Angsuran/Bulan</th>
                <th>Tanggal Pengajuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    // Pastikan kolom 'status', 'jumlah_kredit', 'angsuran_per_bulan', 'no_kredit', 'jenis_kredit', 'jangka_waktu', dan 'id' ada
                    $status = isset($row['status_kredit']) ? $row['status_kredit'] : 'Tidak Diketahui';
                    $jumlah_kredit = isset($row['jumlah_kredit']) ? $row['jumlah_kredit'] : 0;
                    $angsuran_per_bulan = isset($row['angsuran_per_bulan']) ? $row['angsuran_per_bulan'] : 0;
                    $no_kredit = isset($row['no_kredit']) ? $row['no_kredit'] : 'Tidak Ada';
                    $nama_kredit = isset($row['nama_kredit']) ? $row['nama_kredit'] : 'Tidak Ada';
                    $tenor = isset($row['tenor']) ? $row['tenor'] : 'Tidak Diketahui';

                    // Tentukan class badge berdasarkan status
                    $statusClass = '';
                    switch ($status) {
                        case 'Disetujui':
                            $statusClass = 'badge-success';
                            break;
                        case 'Ditolak':
                            $statusClass = 'badge-danger';
                            break;
                        case 'Dalam Proses':
                            $statusClass = 'badge-warning';
                            break;
                        case 'Diajukan':
                            $statusClass = 'badge-info';
                            break;
                        case 'Lunas':
                            $statusClass = 'badge-primary';
                            break;
                        default:
                            $statusClass = 'badge-secondary';
                            break;
                    }

                    // Format tanggal pengajuan
                    $tanggal_pengajuan = date('Y-m-d', strtotime($row['tanggal_pengajuan']));
                    $tanggal_indo = tanggal_indo($tanggal_pengajuan);

                    // Format jumlah kredit dan angsuran menggunakan fungsi rupiah()
                    $jumlah_kredit = rupiah($jumlah_kredit);
                    $angsuran_rupiah = rupiah($angsuran_per_bulan);

                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . $no_kredit . "</td>";
                    echo "<td>" . $row['nama_nasabah'] . "</td>";
                    echo "<td>" . $nama_kredit . "</td>";
                    echo "<td>" . $jumlah_kredit . "</td>";
                    echo "<td>" . $tenor . " bulan</td>";
                    echo "<td>" . $angsuran_rupiah . "</td>";
                    echo "<td>" . $tanggal_indo . "</td>";
                    echo "<td><span class='badge " . $statusClass . "'>" . $status . "</span></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9' class='text-center'>Tidak ada data kredit rumah</td></tr>";
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
