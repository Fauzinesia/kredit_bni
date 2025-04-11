<!-- Navbar Start -->
<header class="app-header">
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container-fluid">
      <!-- Sidebar toggle button -->
      <a class="navbar-brand d-xl-none d-block" href="javascript:void(0)" id="sidebarCollapse">
        <i class="ti ti-menu-2 fs-6"></i>
      </a>

      <!-- Navbar Brand (for smaller screens) -->
      <a class="navbar-brand d-flex align-items-center" href="./dashboard.php">
        <img src="../assets/images/logos/RSHAA.ico" alt="logo" width="30" class="me-2">
        <span class="fw-bold d-none d-md-block">RSUD H.A.Aziz</span>
      </a>

      <!-- Navbar Right -->
      <div class="d-flex align-items-center ms-auto">
        <!-- Date & Time -->
        <div class="me-3 d-none d-md-block">
          <span class="text-muted">
            <?= date("l, d F Y") ?>
          </span>
        </div>

        <!-- User Dropdown -->
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="../assets/images/profile/user-1.jpg" alt="user" class="rounded-circle" width="35">
              <span class="ms-2 d-none d-lg-inline fw-semibold text-dark">
                <?php
                  echo $_SESSION['nama'] ?? 'User';
                ?>
              </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="./profile.php"><i class="ti ti-user me-2"></i> Profil</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="../logout.php"><i class="ti ti-logout me-2 text-danger"></i> Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>
<!-- Navbar End -->
