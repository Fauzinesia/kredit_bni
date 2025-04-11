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

// Ambil data absensi
$query = "SELECT a.*, u.nama 
          FROM tb_absensi_karyawan a 
          LEFT JOIN tb_user u ON a.user_id = u.id 
          ORDER BY a.tanggal DESC, a.jam_masuk DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Data Absensi - Sistem Kredit BNI</title>
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
        .foto-absensi {
            max-width: 100px;
            max-height: 100px;
            cursor: pointer;
        }
        .modal-foto img {
            max-width: 100%;
            max-height: 80vh;
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
                        <h4 class="page-title">Data Absensi</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                            <li class="separator"><i class="fas fa-angle-right"></i></li>
                            <li class="nav-item"><a href="#">Data Absensi</a></li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success">
                                    <?= $_SESSION['success']; ?>
                                    <?php unset($_SESSION['success']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger">
                                    <?= $_SESSION['error']; ?>
                                    <?php unset($_SESSION['error']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Daftar Absensi</h4>
                                        <div class="ml-auto">
                                            <a href="cetak.php" class="btn btn-info btn-round mr-2" target="_blank">
                                                <i class="fa fa-print mr-1"></i> Cetak
                                            </a>
                                            <a href="tambah.php" class="btn btn-primary btn-round">
                                                <i class="fa fa-plus mr-1"></i> Tambah Absensi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="absensi-table" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Tanggal</th>
                                                    <th>Jam Masuk</th>
                                                    <th>Jam Keluar</th>
                                                    <th>Status</th>
                                                    <th>Lokasi</th>
                                                    <th>Foto</th>
                                                    <th>Aksi</th>
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
                                                        echo "<td>";
                                                        if (!empty($row['foto_absensi']) && file_exists($root_path . $row['foto_absensi'])) {
                                                            echo "<img src='../../" . $row['foto_absensi'] . "' class='foto-absensi' data-toggle='modal' data-target='#fotoModal' data-foto='../../" . $row['foto_absensi'] . "'>";
                                                        } else {
                                                            echo "-";
                                                        }
                                                        echo "</td>";
                                                        echo "<td>";
                                                        echo "<div class='btn-group'>";
                                                        echo "<a href='detail.php?id=" . $row['absensi_id'] . "' class='btn btn-info btn-sm' title='Detail'><i class='fa fa-eye'></i></a>";
                                                        echo "<a href='edit.php?id=" . $row['absensi_id'] . "' class='btn btn-primary btn-sm' title='Edit'><i class='fa fa-edit'></i></a>";
                                                        echo "<a href='#' onclick='confirmDelete(" . $row['absensi_id'] . ")' class='btn btn-danger btn-sm' title='Hapus'><i class='fa fa-trash'></i></a>";
                                                        echo "</div>";
                                                        echo "</td>";
                                                        echo "</tr>";
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include "../../admin/includes/footer.php"; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal for displaying photo -->
    <div class="modal fade" id="fotoModal" tabindex="-1" role="dialog" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fotoModalLabel">Foto Absensi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-foto text-center">
                    <img src="" id="modalFoto" alt="Foto Absensi">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data absensi ini? Data yang dihapus tidak dapat dikembalikan.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a href="#" id="btn-delete" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/core/popper.min.js"></script>
    <script src="../../assets/js/core/bootstrap.min.js"></script>
    <script src="../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../../assets/js/kaiadmin.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#absensi-table').DataTable({
                "pageLength": 10,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data yang tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
            
            // Show photo in modal when clicked
            $('#fotoModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var foto = button.data('foto');
                var modal = $(this);
                modal.find('#modalFoto').attr('src', foto);
            });
        });
        
        // Function to confirm delete
        function confirmDelete(id) {
            $('#btn-delete').attr('href', 'hapus.php?id=' + id);
            $('#deleteModal').modal('show');
        }
    </script>
</body>
</html>
