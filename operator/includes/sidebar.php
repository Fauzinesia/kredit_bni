<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <a href="index.php" class="logo">
        <img
          src="/kredit_bni/assets/img/logo.PNG"
          alt="BNI"
          class="navbar-brand"
          height="30"
        />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
    <!-- End Logo Header -->
  </div>
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <div class="user">
        <div class="avatar-sm float-left mr-2">
          <img src="../assets/img/profile.jpg" alt="Profile" class="avatar-img rounded-circle">
        </div>
        <div class="info">
          <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
            <span>
              <?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User'; ?>
              <span class="user-level"><?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?></span>
            </span>
          </a>
        </div>
      </div>
      <ul class="nav nav-secondary">
        <li class="nav-item">
        <a href="/kredit_bni/admin/index.php">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>
        
        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">DATA KREDIT</h4>
        </li>
        
        <!-- Data Kredit -->
        <li class="nav-item">
        <a href="/kredit_bni/admin/data_kredit/kredit.php">
            <i class="fas fa-credit-card"></i>
            <p>Data Kredit</p>
          </a>
        </li>

        <!-- Data Kredit Rumah -->
        <li class="nav-item">
        <a href="/kredit_bni/admin/data_kredit_rumah/kredit_rumah.php">
            <i class="fas fa-home"></i>
            <p>Data Kredit Rumah</p>
          </a>
        </li>
        
        <!-- Data Angsuran Kredit -->
        <li class="nav-item">
        <a href="/kredit_bni/admin/data_angsuran_kredit/angsuran_kredit.php">
            <i class="fas fa-money-bill-wave"></i>
            <p>Data Angsuran Kredit</p>
          </a>
        </li>
        
        <!-- Data Looser -->
        <li class="nav-item">
        <a href="/kredit_bni/admin/data_looser/looser.php">
            <i class="fas fa-user-times"></i>
            <p>Data Looser</p>
          </a>
        </li> 
        
        <!-- Data Merchant -->
        <li class="nav-item">
        <a href="/kredit_bni/admin/data_merchant/merchant.php">
            <i class="fas fa-store"></i>
            <p>Data Merchant</p>
          </a>
        </li> 
        
        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">DATA KARYAWAN</h4>
        </li>
        
        <!-- Data Absensi Karyawan -->
        <li class="nav-item">
        <a href="/kredit_bni/admin/data_absensi/absensi.php">
            <i class="fas fa-clipboard-check"></i>
            <p>Data Absensi Karyawan</p>
          </a>
        </li>
        
        <!-- Data Kinerja Karyawan -->
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#kinerjaKaryawan">
            <i class="fas fa-chart-line"></i>
            <p>Data Kinerja Karyawan</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="kinerjaKaryawan">
            <ul class="nav nav-collapse">
              <li>
                <a href="kinerja/index.php">
                  <span class="sub-item">Penilaian Kinerja</span>
                </a>
              </li>
              <li>
                <a href="kinerja/input.php">
                  <span class="sub-item">Input Kinerja</span>
                </a>
              </li>
              <li>
                <a href="kinerja/laporan.php">
                  <span class="sub-item">Laporan Kinerja</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        
        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">PENGATURAN</h4>
        </li>
        
        <!-- Menu Pengguna -->
        <li class="nav-item">
        <a href="/kredit_bni/admin/data_pengguna/pengguna.php">
        <i class="fas fa-user-cog"></i>
            <p>Data Pengguna</p>
          </a>
        </li> 
        
        <!-- Menu Pengaturan -->
        <li class="nav-item">
          <a href="pengaturan.php">
            <i class="fas fa-cog"></i>
            <p>Pengaturan Aplikasi</p>
          </a>
        </li>
        
        <!-- Menu Logout -->
        <li class="nav-item">
          <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <p>Logout</p>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
<!-- End Sidebar -->

<!-- Script untuk memperbaiki fungsi klik pada sidebar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Aktifkan collapse untuk Bootstrap 5
  var collapsibleElements = document.querySelectorAll('[data-bs-toggle="collapse"]');
  collapsibleElements.forEach(function(element) {
    element.addEventListener('click', function(e) {
      e.preventDefault();
      var target = document.querySelector(this.getAttribute('href'));
      if (target) {
        if (target.classList.contains('show')) {
          target.classList.remove('show');
          this.setAttribute('aria-expanded', 'false');
        } else {
          target.classList.add('show');
          this.setAttribute('aria-expanded', 'true');
        }
      }
    });
  });
  
  // Highlight menu aktif
  var currentLocation = window.location.pathname;
  var menuItems = document.querySelectorAll('.nav-item a');
  menuItems.forEach(function(item) {
    var menuPath = item.getAttribute('href');
    if (currentLocation.includes(menuPath) && menuPath !== '#' && menuPath !== '') {
      item.classList.add('active');
      
      // Jika menu berada dalam collapse, buka collapse-nya
      var parent = item.closest('.collapse');
      if (parent) {
        parent.classList.add('show');
        var trigger = document.querySelector('[href="#' + parent.id + '"]');
        if (trigger) {
          trigger.setAttribute('aria-expanded', 'true');
          trigger.classList.remove('collapsed');
        }
      }
    }
  });
});
</script>
