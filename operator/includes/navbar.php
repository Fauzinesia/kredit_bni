<div class="main-header">
  <div class="main-header-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <a href="index.php" class="logo">
        <img
          src="../assets/img/kaiadmin/logo_light.svg"
          alt="navbar brand"
          class="navbar-brand"
          height="20"
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
  <!-- Navbar Header -->
  <nav
    class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
  >
    <div class="container-fluid">
      <nav
        class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
      >
        <div class="input-group">
          <div class="input-group-prepend">
            <button type="submit" class="btn btn-search pe-1">
              <i class="fa fa-search search-icon"></i>
            </button>
          </div>
          <input
            type="text"
            placeholder="Search ..."
            class="form-control"
          />
        </div>
      </nav>

      <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
        <li
          class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none"
        >
          <a
            class="nav-link dropdown-toggle"
            data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-expanded="false"
            aria-haspopup="true"
          >
            <i class="fa fa-search"></i>
          </a>
          <ul class="dropdown-menu dropdown-search animated fadeIn">
            <form class="navbar-left navbar-form nav-search">
              <div class="input-group">
                <input
                  type="text"
                  placeholder="Search ..."
                  class="form-control"
                />
              </div>
            </form>
          </ul>
        </li>
        
        <!-- Profil dan Logout (Versi Sederhana) -->
        <li class="nav-item">
          <div class="d-flex align-items-center">
            <div class="avatar-sm me-2">
              <img src="../assets/img/profile.jpg" alt="Profile" class="avatar-img rounded-circle" />
            </div>
            <div class="d-none d-md-block me-3">
              <span class="fw-bold"><?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User'; ?></span>
              <small class="d-block text-muted"><?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?></small>
            </div>
            <a href="/kredit_bni/logout.php" class="btn btn-sm btn-danger" title="Logout">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </div>
        </li>
      </ul>
    </div>
  </nav>
  <!-- End Navbar -->
</div>
