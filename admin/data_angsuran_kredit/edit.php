<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';
require_once $root_path . 'config/koneksi.php';

// Cek apakah ada parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID Angsuran tidak ditemukan";
    header("Location: angsuran_kredit.php");
    exit;
}

$angsuran_id = $_GET['id'];

// Ambil data angsuran berdasarkan ID
$sql_angsuran = "SELECT * FROM tb_angsuran_kredit WHERE angsuran_id = $angsuran_id";
$result_angsuran = mysqli_query($koneksi, $sql_angsuran);

if (!$result_angsuran || mysqli_num_rows($result_angsuran) == 0) {
    $_SESSION['error'] = "Data angsuran tidak ditemukan";
    header("Location: angsuran_kredit.php");
    exit;
}

$data_angsuran = mysqli_fetch_assoc($result_angsuran);

// Ambil data kredit untuk dropdown
$sql_kredit = "SELECT kredit_id, no_kredit, nama_nasabah, nama_kredit FROM tb_kredit";
$result_kredit = mysqli_query($koneksi, $sql_kredit);

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kredit_id = $_POST['kredit_id'];
    
    // Ambil no_kredit dari database berdasarkan kredit_id
    $query_no_kredit = "SELECT no_kredit FROM tb_kredit WHERE kredit_id = $kredit_id";
    $result_no_kredit = mysqli_query($koneksi, $query_no_kredit);
    $row_no_kredit = mysqli_fetch_assoc($result_no_kredit);
    $no_kredit = $row_no_kredit['no_kredit'];
    
    $tanggal_angsuran = $_POST['tanggal_angsuran'];
    $jumlah_angsuran = str_replace(['.', ','], ['', '.'], $_POST['jumlah_angsuran']);
    $sisa_pokok_kredit = str_replace(['.', ','], ['', '.'], $_POST['sisa_pokok_kredit']);
    $angsuran_pokok = str_replace(['.', ','], ['', '.'], $_POST['angsuran_pokok']);
    $angsuran_bunga = str_replace(['.', ','], ['', '.'], $_POST['angsuran_bunga']);
    $total_angsuran = str_replace(['.', ','], ['', '.'], $_POST['total_angsuran']);
    $status_pembayaran = $_POST['status_pembayaran'];
    $denda = isset($_POST['denda']) && !empty($_POST['denda']) ? str_replace(['.', ','], ['', '.'], $_POST['denda']) : 0;
    
    // Tanggal pembayaran, metode pembayaran, dan bukti pembayaran
    $tanggal_pembayaran = !empty($_POST['tanggal_pembayaran']) ? $_POST['tanggal_pembayaran'] : null;
    $metode_pembayaran = !empty($_POST['metode_pembayaran']) ? $_POST['metode_pembayaran'] : null;
    
    // Ambil bukti pembayaran yang sudah ada
    $bukti_pembayaran = $data_angsuran['bukti_pembayaran'];
    
    // Handle file upload jika ada
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['size'] > 0) {
        $target_dir = "../../uploads/bukti_pembayaran/";
        
        // Buat direktori jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["bukti_pembayaran"]["name"], PATHINFO_EXTENSION);
        $new_filename = "bukti_" . time() . "_" . rand(1000, 9999) . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Cek apakah file adalah gambar
        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array(strtolower($file_extension), $allowed_types)) {
            $_SESSION['error'] = "Hanya file JPG, JPEG, PNG, dan PDF yang diperbolehkan.";
            header("Location: edit.php?id=$angsuran_id");
            exit;
        }
        
        // Upload file
        if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
            // Hapus file lama jika ada
            if (!empty($bukti_pembayaran) && file_exists($target_dir . $bukti_pembayaran)) {
                unlink($target_dir . $bukti_pembayaran);
            }
            $bukti_pembayaran = $new_filename;
        } else {
            $_SESSION['error'] = "Gagal mengupload file bukti pembayaran.";
            header("Location: edit.php?id=$angsuran_id");
            exit;
        }
    }
    
    // Gunakan query langsung untuk menghindari masalah dengan prepared statement
    $query = "UPDATE tb_angsuran_kredit SET 
                no_kredit = '$no_kredit', 
                kredit_id = $kredit_id, 
                tanggal_angsuran = '$tanggal_angsuran', 
                jumlah_angsuran = $jumlah_angsuran, 
                sisa_pokok_kredit = $sisa_pokok_kredit, 
                angsuran_pokok = $angsuran_pokok, 
                angsuran_bunga = $angsuran_bunga, 
                total_angsuran = $total_angsuran, 
                status_pembayaran = '$status_pembayaran', 
                denda = $denda, 
                tanggal_pembayaran = " . ($tanggal_pembayaran ? "'$tanggal_pembayaran'" : "NULL") . ", 
                metode_pembayaran = " . ($metode_pembayaran ? "'$metode_pembayaran'" : "NULL") . ", 
                bukti_pembayaran = " . ($bukti_pembayaran ? "'$bukti_pembayaran'" : "NULL") . " 
              WHERE angsuran_id = $angsuran_id";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Data angsuran kredit berhasil diperbarui.";
        header("Location: angsuran_kredit.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Edit Data Angsuran Kredit - Sistem Kredit BNI</title>
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
                        <h4 class="page-title">Edit Data Angsuran Kredit</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="angsuran_kredit.php">Data Angsuran Kredit</a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Edit Data</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Form Edit Data Angsuran Kredit</h4>
                                </div>
                                <div class="card-body">
                                    <?php
                                    if (isset($_SESSION['error'])) {
                                        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                                        unset($_SESSION['error']);
                                    }
                                    ?>
                                    <form method="post" action="" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kredit_id">Pilih Kredit</label>
                                                    <select class="form-control" id="kredit_id" name="kredit_id" required onchange="updateNoKredit(this)">
                                                        <option value="">-- Pilih Kredit --</option>
                                                        <?php
                                                        if ($result_kredit && mysqli_num_rows($result_kredit) > 0) {
                                                            while ($row = mysqli_fetch_assoc($result_kredit)) {
                                                                $selected = ($row['kredit_id'] == $data_angsuran['kredit_id']) ? 'selected' : '';
                                                                echo "<option value='{$row['kredit_id']}' data-no-kredit='{$row['no_kredit']}' $selected>{$row['no_kredit']} - {$row['nama_nasabah']} ({$row['nama_kredit']})</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="no_kredit_display">No. Kredit</label>
                                                    <input type="text" class="form-control" id="no_kredit_display" value="<?php echo $data_angsuran['no_kredit']; ?>" readonly>
                                                    <input type="hidden" id="no_kredit" name="no_kredit" value="<?php echo $data_angsuran['no_kredit']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="tanggal_angsuran">Tanggal Angsuran</label>
                                                    <input type="date" class="form-control" id="tanggal_angsuran" name="tanggal_angsuran" value="<?php echo $data_angsuran['tanggal_angsuran']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="jumlah_angsuran">Jumlah Angsuran</label>
                                                    <input type="text" class="form-control number-format" id="jumlah_angsuran" name="jumlah_angsuran" value="<?php echo number_format($data_angsuran['jumlah_angsuran'], 0, ',', '.'); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sisa_pokok_kredit">Sisa Pokok Kredit</label>
                                                    <input type="text" class="form-control number-format" id="sisa_pokok_kredit" name="sisa_pokok_kredit" value="<?php echo number_format($data_angsuran['sisa_pokok_kredit'], 0, ',', '.'); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="angsuran_pokok">Angsuran Pokok</label>
                                                    <input type="text" class="form-control number-format" id="angsuran_pokok" name="angsuran_pokok" value="<?php echo number_format($data_angsuran['angsuran_pokok'], 0, ',', '.'); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="angsuran_bunga">Angsuran Bunga</label>
                                                    <input type="text" class="form-control number-format" id="angsuran_bunga" name="angsuran_bunga" value="<?php echo number_format($data_angsuran['angsuran_bunga'], 0, ',', '.'); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="total_angsuran">Total Angsuran</label>
                                                    <input type="text" class="form-control number-format" id="total_angsuran" name="total_angsuran" value="<?php echo number_format($data_angsuran['total_angsuran'], 0, ',', '.'); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_pembayaran">Status Pembayaran</label>
                                                    <select class="form-control" id="status_pembayaran" name="status_pembayaran" required>
                                                        <option value="Belum Dibayar" <?php echo ($data_angsuran['status_pembayaran'] == 'Belum Dibayar') ? 'selected' : ''; ?>>Belum Dibayar</option>
                                                        <option value="Proses" <?php echo ($data_angsuran['status_pembayaran'] == 'Proses') ? 'selected' : ''; ?>>Proses</option>
                                                        <option value="Lunas" <?php echo ($data_angsuran['status_pembayaran'] == 'Lunas') ? 'selected' : ''; ?>>Lunas</option>
                                                        <option value="Terlambat" <?php echo ($data_angsuran['status_pembayaran'] == 'Terlambat') ? 'selected' : ''; ?>>Terlambat</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="denda">Denda (jika ada)</label>
                                                    <input type="text" class="form-control number-format" id="denda" name="denda" value="<?php echo number_format($data_angsuran['denda'], 0, ',', '.'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row" id="pembayaran-details">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tanggal_pembayaran">Tanggal Pembayaran</label>
                                                    <input type="date" class="form-control" id="tanggal_pembayaran" name="tanggal_pembayaran" value="<?php echo $data_angsuran['tanggal_pembayaran']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="metode_pembayaran">Metode Pembayaran</label>
                                                    <select class="form-control" id="metode_pembayaran" name="metode_pembayaran">
                                                        <option value="">-- Pilih Metode --</option>
                                                        <option value="Transfer Bank" <?php echo ($data_angsuran['metode_pembayaran'] == 'Transfer Bank') ? 'selected' : ''; ?>>Transfer Bank</option>
                                                        <option value="Tunai" <?php echo ($data_angsuran['metode_pembayaran'] == 'Tunai') ? 'selected' : ''; ?>>Tunai</option>
                                                        <option value="Debit" <?php echo ($data_angsuran['metode_pembayaran'] == 'Debit') ? 'selected' : ''; ?>>Debit</option>
                                                        <option value="Mobile Banking" <?php echo ($data_angsuran['metode_pembayaran'] == 'Mobile Banking') ? 'selected' : ''; ?>>Mobile Banking</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="bukti_pembayaran">Bukti Pembayaran (JPG, PNG, PDF)</label>
                                                    <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran">
                                                    <small class="form-text text-muted">Upload bukti pembayaran baru jika ingin mengubah</small>
                                                    
                                                    <?php if (!empty($data_angsuran['bukti_pembayaran'])): ?>
                                                    <div class="mt-2">
                                                        <p>Bukti Pembayaran Saat Ini:</p>
                                                        <?php
                                                        $file_ext = pathinfo($data_angsuran['bukti_pembayaran'], PATHINFO_EXTENSION);
                                                        $file_path = "../../uploads/bukti_pembayaran/" . $data_angsuran['bukti_pembayaran'];
                                                        
                                                        if (in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png'])) {
                                                            echo "<img src='$file_path' class='img-thumbnail' style='max-width: 200px;'>";
                                                        } else {
                                                            echo "<a href='$file_path' target='_blank' class='btn btn-sm btn-info'>Lihat Dokumen</a>";
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group mt-4">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            <a href="angsuran_kredit.php" class="btn btn-danger">Batal</a>
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
    <script src="../../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../../assets/js/kaiadmin.min.js"></script>
    <script src="../../assets/js/plugin/cleave/cleave.min.js"></script>
    <script>
        // Fungsi untuk mengupdate no_kredit saat kredit dipilih
        function updateNoKredit(selectElement) {
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var noKredit = selectedOption.getAttribute('data-no-kredit');
            document.getElementById('no_kredit').value = noKredit;
            document.getElementById('no_kredit_display').value = noKredit;
        }
        
        $(document).ready(function() {
            // Format input angka dengan pemisah ribuan
            var numberFormat = document.querySelectorAll('.number-format');
            numberFormat.forEach(function(element) {
                new Cleave(element, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalMark: ',',
                    delimiter: '.'
                });
            });
            
            // Hitung total angsuran otomatis
            $('#angsuran_pokok, #angsuran_bunga, #denda').on('input', function() {
                calculateTotal();
            });
            
            function calculateTotal() {
                var pokok = parseFloat($('#angsuran_pokok').val().replace(/\./g, '').replace(',', '.')) || 0;
                var bunga = parseFloat($('#angsuran_bunga').val().replace(/\./g, '').replace(',', '.')) || 0;
                var denda = parseFloat($('#denda').val().replace(/\./g, '').replace(',', '.')) || 0;
                
                var total = pokok + bunga + denda;
                
                // Format total ke format rupiah
                var formattedTotal = new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(total);
                
                // Ganti , dengan . untuk format Cleave
                formattedTotal = formattedTotal.replace(/\./g, 'X').replace(/,/g, '.').replace(/X/g, '.');
                
                $('#total_angsuran').val(formattedTotal);
            }
            
            // Toggle tampilan field pembayaran berdasarkan status
            $('#status_pembayaran').change(function() {
                togglePembayaranDetails();
            });
            
            function togglePembayaranDetails() {
                var status = $('#status_pembayaran').val();
                if (status === 'Lunas' || status === 'Proses') {
                    $('#pembayaran-details').show();
                } else {
                    $('#pembayaran-details').hide();
                }
            }
            
            // Set initial state berdasarkan status yang dipilih
            togglePembayaranDetails();
            
            // Pastikan no_kredit terisi saat form disubmit
            $('form').submit(function() {
                var kredit_id = $('#kredit_id').val();
                if (kredit_id) {
                    var selectedOption = $('#kredit_id option:selected');
                    var noKredit = selectedOption.data('no-kredit');
                    $('#no_kredit').val(noKredit);
                }
            });
        });
    </script>
</body>
</html>
