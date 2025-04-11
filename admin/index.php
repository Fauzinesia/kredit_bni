<?php
session_start();

// Include file koneksi database
require_once '../config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['login_user'])) {
    header("location: ../login.php");
    exit;
}

// Cek apakah user memiliki role Admin
if ($_SESSION['role'] != 'Admin') {
    header("location: ../unauthorized.php"); // Redirect ke halaman tidak berwenang
    exit;
}

// Sertakan file header
include 'includes/header.php';

// Sertakan file sidebar
include 'includes/sidebar.php';
?>

<div class="main-panel">
  <?php
  // Sertakan file navbar
  include 'includes/navbar.php';
  ?>

  <!-- Konten halaman admin -->
  <div class="container">
    <div class="page-inner">
      <!-- Konten dashboard admin -->
      <div class="page-header">
        <h4 class="page-title">Dashboard Admin</h4>
        <ul class="breadcrumbs">
          <li class="nav-home">
            <a href="index.php">
              <i class="fas fa-home"></i>
            </a>
          </li>
          <li class="separator">
            <i class="fas fa-angle-right"></i>
          </li>
          <li class="nav-item">
            <a href="#">Dashboard</a>
          </li>
        </ul>
      </div>

      <div class="row">
        <!-- Statistik Kredit -->
        <div class="col-sm-6 col-md-3">
          <div class="card card-stats card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fas fa-credit-card text-primary"></i>
                  </div>
                </div>
                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">Total Kredit</p>
                    <?php
                    $sql_total = "SELECT COUNT(*) as total FROM tb_kredit";
                    $result_total = mysqli_query($koneksi, $sql_total);
                    $total_kredit = mysqli_fetch_assoc($result_total)['total'];
                    ?>
                    <h4 class="card-title"><?php echo number_format($total_kredit); ?></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-sm-6 col-md-3">
          <div class="card card-stats card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fas fa-check-circle text-success"></i>
                  </div>
                </div>
                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">Disetujui</p>
                    <?php
                    $sql_disetujui = "SELECT COUNT(*) as total FROM tb_kredit WHERE status_kredit = 'Disetujui'";
                    $result_disetujui = mysqli_query($koneksi, $sql_disetujui);
                    $total_disetujui = mysqli_fetch_assoc($result_disetujui)['total'];
                    ?>
                    <h4 class="card-title"><?php echo number_format($total_disetujui); ?></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-sm-6 col-md-3">
          <div class="card card-stats card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fas fa-clock text-warning"></i>
                  </div>
                </div>
                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">Dalam Proses</p>
                    <?php
                    $sql_proses = "SELECT COUNT(*) as total FROM tb_kredit WHERE status_kredit = 'Dalam Proses' OR status_kredit = 'Diajukan'";
                    $result_proses = mysqli_query($koneksi, $sql_proses);
                    $total_proses = mysqli_fetch_assoc($result_proses)['total'];
                    ?>
                    <h4 class="card-title"><?php echo number_format($total_proses); ?></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-sm-6 col-md-3">
          <div class="card card-stats card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fas fa-times-circle text-danger"></i>
                  </div>
                </div>
                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">Ditolak</p>
                    <?php
                    $sql_ditolak = "SELECT COUNT(*) as total FROM tb_kredit WHERE status_kredit = 'Ditolak'";
                    $result_ditolak = mysqli_query($koneksi, $sql_ditolak);
                    $total_ditolak = mysqli_fetch_assoc($result_ditolak)['total'];
                    ?>
                    <h4 class="card-title"><?php echo number_format($total_ditolak); ?></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Total Nilai Kredit -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <div class="card-title">Total Nilai Kredit</div>
              <div class="card-category">Kredit yang disetujui</div>
            </div>
            <div class="card-body pb-0">
              <?php
              $sql_nilai = "SELECT SUM(jumlah_kredit) as total_nilai FROM tb_kredit WHERE status_kredit = 'Disetujui'";
              $result_nilai = mysqli_query($koneksi, $sql_nilai);
              $total_nilai = mysqli_fetch_assoc($result_nilai)['total_nilai'] ?: 0;
              
              // Hitung persentase persetujuan
              $persentase = ($total_kredit > 0) ? ($total_disetujui / $total_kredit) * 100 : 0;
              ?>
              <h1 class="mb-1"><?php echo rupiah($total_nilai); ?></h1>
              <p class="text-success">
                <i class="fas fa-arrow-up"></i> <?php echo number_format($persentase, 1); ?>% tingkat persetujuan
              </p>
            </div>
          </div>
        </div>
        
        <!-- Jenis Kredit -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <div class="card-title">Jenis Kredit</div>
              <div class="card-category">Distribusi berdasarkan jenis</div>
            </div>
            <div class="card-body pb-0">
              <?php
              // Changed 'jenis_kredit' to 'tipe_kredit' or another possible column name
              $sql_jenis = "SELECT nama_kredit as jenis, COUNT(*) as jumlah FROM tb_kredit GROUP BY nama_kredit ORDER BY jumlah DESC";
              $result_jenis = mysqli_query($koneksi, $sql_jenis);              
              
              if (mysqli_num_rows($result_jenis) > 0) {
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered">';
                echo '<thead><tr><th>Jenis Kredit</th><th>Jumlah</th></tr></thead>';
                echo '<tbody>';
                
                while ($row = mysqli_fetch_assoc($result_jenis)) {
                  echo '<tr>';
                  echo '<td>' . $row['jenis'] . '</td>';
                  echo '<td>' . $row['jumlah'] . '</td>';
                  echo '</tr>';
                }
                
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
              } else {
                echo '<p>Tidak ada data jenis kredit</p>';
              }
              ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Pengajuan Kredit Terbaru -->
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title">Pengajuan Kredit Terbaru</h4>
                <a href="data_kredit/kredit.php" class="btn btn-primary btn-round btn-sm ml-auto">
                  <i class="fa fa-eye"></i> Lihat Semua
                </a>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>No. Kredit</th>
                      <th>Nasabah</th>
                      <th>Jenis Kredit</th>
                      <th>Jumlah</th>
                      <th>Tanggal Pengajuan</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql_terbaru = "SELECT * FROM tb_kredit ORDER BY tanggal_pengajuan DESC LIMIT 5";
                    $result_terbaru = mysqli_query($koneksi, $sql_terbaru);
                    
                    if (mysqli_num_rows($result_terbaru) > 0) {
                      while ($row = mysqli_fetch_assoc($result_terbaru)) {
                        // Tentukan class badge berdasarkan status
                        $statusClass = '';
                        switch ($row['status_kredit']) {
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
                        $tanggal_pengajuan = date('d-m-Y', strtotime($row['tanggal_pengajuan']));
                        
                        // Format jumlah kredit
                        $jumlah_rupiah = rupiah($row['jumlah_kredit']);
                        
                        echo "<tr>";
                        echo "<td>" . $row['no_kredit'] . "</td>";
                        echo "<td>" . $row['nama_nasabah'] . "</td>";
                        echo "<td>" . $row['nama_kredit'] . "</td>"; // Changed from jenis_kredit to tipe_kredit
                        echo "<td>" . $jumlah_rupiah . "</td>";
                        echo "<td>" . $tanggal_pengajuan . "</td>";
                        echo "<td><span class='badge " . $statusClass . "'>" . $row['status_kredit'] . "</span></td>";
                        echo "</tr>";
                      }
                    } else {
                      echo "<tr><td colspan='6' class='text-center'>Tidak ada data kredit terbaru</td></tr>";
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
  </div>

  <?php
  // Sertakan file footer
  include 'includes/footer.php';
  ?>
</div>
