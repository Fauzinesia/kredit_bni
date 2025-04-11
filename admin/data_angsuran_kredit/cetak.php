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

// Query untuk mengambil data angsuran kredit
$sql = "SELECT ak.*, k.nama_nasabah, k.nama_kredit FROM tb_angsuran_kredit ak
        LEFT JOIN tb_kredit k ON ak.kredit_id = k.kredit_id
        ORDER BY ak.tanggal_angsuran DESC";
$result = mysqli_query($koneksi, $sql);

// Dapatkan tanggal hari ini untuk header laporan
$tanggal_cetak = tanggal_indo(date('Y-m-d'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Angsuran Kredit - Sistem Kredit BNI</title>
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
        <div class="title">LAPORAN DATA ANGSURAN KREDIT</div>
        <div class="subtitle">PT. BANK NEGARA INDONESIA (PERSERO) TBK.</div>
        <div>Tanggal Cetak: <?php echo $tanggal_cetak; ?></div>
    </div>
    
    <button class="no-print" onclick="window.print()">Cetak Laporan</button>
    <button class="no-print" onclick="window.location.href='angsuran_kredit.php'">Kembali</button>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Kredit</th>
                <th>Nasabah</th>
                <th>Jenis Kredit</th>
                <th>Tanggal Angsuran</th>
                <th>Angsuran Pokok</th>
                <th>Angsuran Bunga</th>
                <th>Denda</th>
                <th>Total Angsuran</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                $no = 1;
                $total_semua = 0;
                
                while ($row = mysqli_fetch_assoc($result)) {
                    // Pastikan kolom yang diperlukan ada
                    $status = isset($row['status_pembayaran']) ? $row['status_pembayaran'] : 'Tidak Diketahui';
                    $no_kredit = isset($row['no_kredit']) ? $row['no_kredit'] : 'Tidak Ada';
                    $nama_nasabah = isset($row['nama_nasabah']) ? $row['nama_nasabah'] : 'Tidak Ada';
                    $nama_kredit = isset($row['nama_kredit']) ? $row['nama_kredit'] : 'Tidak Ada';
                    $angsuran_pokok = isset($row['angsuran_pokok']) ? $row['angsuran_pokok'] : 0;
                    $angsuran_bunga = isset($row['angsuran_bunga']) ? $row['angsuran_bunga'] : 0;
                    $denda = isset($row['denda']) ? $row['denda'] : 0;
                    $total_angsuran = isset($row['total_angsuran']) ? $row['total_angsuran'] : 0;
                    
                    // Format tanggal angsuran
                    $tanggal_angsuran = isset($row['tanggal_angsuran']) ? date('Y-m-d', strtotime($row['tanggal_angsuran'])) : date('Y-m-d');
                    $tanggal_indo = tanggal_indo($tanggal_angsuran);
                    
                    // Format angka ke rupiah
                    $angsuran_pokok_rupiah = rupiah($angsuran_pokok);
                    $angsuran_bunga_rupiah = rupiah($angsuran_bunga);
                    $denda_rupiah = rupiah($denda);
                    $total_angsuran_rupiah = rupiah($total_angsuran);
                    
                    // Tambahkan ke total keseluruhan
                    $total_semua += $total_angsuran;
                    
                    // Tentukan class badge berdasarkan status
                    $statusClass = '';
                    switch ($status) {
                        case 'Lunas':
                            $statusClass = 'badge-primary';
                            break;
                        case 'Belum Dibayar':
                            $statusClass = 'badge-warning';
                            break;
                        case 'Terlambat':
                            $statusClass = 'badge-danger';
                            break;
                        case 'Proses':
                            $statusClass = 'badge-info';
                            break;
                        default:
                            $statusClass = 'badge-secondary';
                            break;
                    }
                    
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . $no_kredit . "</td>";
                    echo "<td>" . $nama_nasabah . "</td>";
                    echo "<td>" . $nama_kredit . "</td>";
                    echo "<td>" . $tanggal_indo . "</td>";
                    echo "<td>" . $angsuran_pokok_rupiah . "</td>";
                    echo "<td>" . $angsuran_bunga_rupiah . "</td>";
                    echo "<td>" . $denda_rupiah . "</td>";
                    echo "<td>" . $total_angsuran_rupiah . "</td>";
                    echo "<td><span class='badge " . $statusClass . "'>" . $status . "</span></td>";
                    echo "</tr>";
                }
                
                // Tampilkan total keseluruhan
                echo "<tr style='font-weight: bold;'>";
                echo "<td colspan='8' style='text-align: right;'>Total Keseluruhan:</td>";
                echo "<td colspan='2'>" . rupiah($total_semua) . "</td>";
                echo "</tr>";
            } else {
                echo "<tr><td colspan='10' class='text-center'>Tidak ada data angsuran kredit</td></tr>";
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
