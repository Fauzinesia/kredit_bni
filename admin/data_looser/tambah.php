<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kredit_bni/';
require_once $root_path . 'config/koneksi.php';

// Ambil data kredit untuk dropdown
$sql_kredit = "SELECT k.kredit_id, k.no_kredit, k.nama_nasabah, k.nama_kredit, k.jumlah_kredit 
               FROM tb_kredit k 
               WHERE k.status_kredit = 'Disetujui' 
               AND k.kredit_id NOT IN (SELECT kredit_id FROM tb_looser WHERE status_npl IN ('Ringan', 'Sedang', 'Berat'))
               ORDER BY k.nama_nasabah ASC";
$result_kredit = mysqli_query($koneksi, $sql_kredit);

// Proses form jika ada submit
if (isset($_POST['submit'])) {
    $kredit_id = mysqli_real_escape_string($koneksi, $_POST['kredit_id']);
    $no_kredit = mysqli_real_escape_string($koneksi, $_POST['no_kredit']);
    $nama_nasabah = mysqli_real_escape_string($koneksi, $_POST['nama_nasabah']);
    $total_tunggakan = mysqli_real_escape_string($koneksi, $_POST['total_tunggakan']);
    $jatuh_tempo = mysqli_real_escape_string($koneksi, $_POST['jatuh_tempo']);
    $status_npl = mysqli_real_escape_string($koneksi, $_POST['status_npl']);
    $tanggal_masuk_npl = mysqli_real_escape_string($koneksi, $_POST['tanggal_masuk_npl']);
    $tindakan = mysqli_real_escape_string($koneksi, $_POST['tindakan']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    
    // Validasi input
    $errors = [];
    
    if (empty($kredit_id)) {
        $errors[] = "Kredit harus dipilih";
    }
    
    if (empty($total_tunggakan)) {
        $errors[] = "Total tunggakan harus diisi";
    }
    
    if (empty($jatuh_tempo)) {
        $errors[] = "Tanggal jatuh tempo harus diisi";
    }
    
    if (empty($status_npl)) {
        $errors[] = "Status NPL harus dipilih";
    }
    
    if (empty($tanggal_masuk_npl)) {
        $errors[] = "Tanggal masuk NPL harus diisi";
    }
    
    if (empty($tindakan)) {
        $errors[] = "Tindakan harus dipilih";
    }
    
    // Jika tidak ada error, simpan data
    if (empty($errors)) {
        $query = "INSERT INTO tb_looser (kredit_id, no_kredit, nama_nasabah, total_tunggakan, jatuh_tempo, status_npl, tanggal_masuk_npl, tindakan, keterangan, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "issdssss", $kredit_id, $no_kredit, $nama_nasabah, $total_tunggakan, $jatuh_tempo, $status_npl, $tanggal_masuk_npl, $tindakan, $keterangan);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Data nasabah bermasalah berhasil ditambahkan";
            header("Location: looser.php");
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
    <title>Tambah Nasabah Bermasalah - Sistem Kredit BNI</title>
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
                        <h4 class="page-title">Tambah Nasabah Bermasalah</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="looser.php">Data Nasabah Bermasalah</a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Tambah Data</a></li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Form Tambah Nasabah Bermasalah</div>
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
                                                    <label for="kredit_id">Pilih Kredit</label>
                                                    <select class="form-control select2" id="kredit_id" name="kredit_id" required>
                                                        <option value="">-- Pilih Kredit --</option>
                                                        <?php
                                                        if (mysqli_num_rows($result_kredit) > 0) {
                                                            while ($kredit = mysqli_fetch_assoc($result_kredit)) {
                                                                echo '<option value="' . $kredit['kredit_id'] . '" 
                                                                    data-no-kredit="' . $kredit['no_kredit'] . '" 
                                                                    data-nama="' . $kredit['nama_nasabah'] . '"
                                                                    data-jumlah="' . $kredit['jumlah_kredit'] . '">' . 
                                                                    $kredit['no_kredit'] . ' - ' . $kredit['nama_nasabah'] . ' (' . $kredit['nama_kredit'] . ')' . 
                                                                '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="no_kredit">No. Kredit</label>
                                                    <input type="text" class="form-control" id="no_kredit" name="no_kredit" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nama_nasabah">Nama Nasabah</label>
                                                    <input type="text" class="form-control" id="nama_nasabah" name="nama_nasabah" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="jumlah_kredit">Jumlah Kredit</label>
                                                    <input type="text" class="form-control" id="jumlah_kredit" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="total_tunggakan">Total Tunggakan</label>
                                                    <input type="number" class="form-control" id="total_tunggakan" name="total_tunggakan" step="0.01" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="jatuh_tempo">Jatuh Tempo</label>
                                                    <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status_npl">Status NPL</label>
                                                    <select class="form-control" id="status_npl" name="status_npl" required>
                                                        <option value="">-- Pilih Status NPL --</option>
                                                        <option value="Ringan">Ringan</option>
                                                        <option value="Sedang">Sedang</option>
                                                        <option value="Berat">Berat</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tanggal_masuk_npl">Tanggal Masuk NPL</label>
                                                    <input type="date" class="form-control" id="tanggal_masuk_npl" name="tanggal_masuk_npl" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tindakan">Tindakan</label>
                                                    <select class="form-control" id="tindakan" name="tindakan" required>
                                                        <option value="">-- Pilih Tindakan --</option>
                                                        <option value="Surat Teguran">Surat Teguran</option>
                                                        <option value="Restrukturisasi">Restrukturisasi</option>
                                                        <option value="Somasi">Somasi</option>
                                                        <option value="Lelang Jaminan">Lelang Jaminan</option>
                                                        <option value="Hapus Buku">Hapus Buku</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="keterangan">Keterangan</label>
                                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-action">
                                            <button type="submit" name="submit" class="btn btn-primary">
                                                <i class="fa fa-save mr-1"></i> Simpan
                                            </button>
                                            <a href="looser.php" class="btn btn-danger">
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
            
            // Mengisi data otomatis saat kredit dipilih
            $('#kredit_id').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                
                // Ambil data dari atribut data-*
                var noKredit = selectedOption.data('no-kredit');
                var nama = selectedOption.data('nama');
                var jumlahKredit = selectedOption.data('jumlah');
                
                // Isi field dengan data yang diambil
                $('#no_kredit').val(noKredit);
                $('#nama_nasabah').val(nama);
                
                // Format jumlah kredit ke format rupiah
                if (jumlahKredit) {
                    $('#jumlah_kredit').val(formatRupiah(jumlahKredit));
                } else {
                    $('#jumlah_kredit').val('');
                }
                
                // Set tanggal default untuk tanggal masuk NPL (hari ini)
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
                var yyyy = today.getFullYear();
                
                today = yyyy + '-' + mm + '-' + dd;
                $('#tanggal_masuk_npl').val(today);
            });
            
            // Format angka ke format rupiah
            function formatRupiah(angka) {
                var number_string = angka.toString(),
                    split = number_string.split('.'),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                    
                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                
                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return 'Rp ' + rupiah;
            }
            
            // Debug: Tampilkan pesan di konsol untuk memastikan script berjalan
            console.log('Script initialized');
            
            // Trigger change event pada kredit_id jika sudah ada nilai yang dipilih
            if ($('#kredit_id').val() !== '') {
                $('#kredit_id').trigger('change');
            }
        });
    </script>
</body>
</html>
