<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title>Navbar Modern</title>
    <!-- Pastikan sudah memuat Font Awesome dan Boxicons di sini -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
      /* Googlefont Poppins */
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
      }
      body {
        min-height: 100vh;
        background-color: #f0f2f5; /* Latar belakang umum */
      }
      /* ===== NAVBAR STYLING ===== */
      nav {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 70px;
        /* Gradien biru agar lebih modern */
        background: linear-gradient(45deg, #578FCA, #3674B5);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        z-index: 99;
      }
      nav .navbar {
        height: 100%;
        max-width: 1250px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: auto;
        padding: 0 50px;
      }
      /* Logo */
      .navbar .logo a {
        font-size: 30px;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
      }
      .navbar .logo a:hover {
        color: #e9e9e9;
      }
      /* Nav links wrapper */
      .nav-links {
        line-height: 70px;
        height: 100%;
      }
      .links {
        display: flex;
        align-items: center;
      }
      .links li {
        position: relative;
        list-style: none;
      }
      .links li a {
        display: block;
        padding: 0 14px;
        text-decoration: none;
        white-space: nowrap;
        color: #fff;
        font-size: 15px;
        font-weight: 500;
        transition: background 0.3s ease, color 0.3s ease;
      }
      .links li a:hover {
        background: rgba(255, 255, 255, 0.1);
      }
      /* Arrow */
      .arrow {
        margin-left: 4px;
        font-size: 14px;
        transition: transform 0.3s ease;
      }
      .links li:hover .htmlcss-arrow,
      .links li:hover .js-arrow {
        transform: rotate(180deg);
      }
      /* Sub-menu */
      .sub-menu {
        position: absolute;
        top: 70px;
        left: 0;
        background: #3674B5;
        min-width: 180px;
        border-radius: 0 0 6px 6px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s ease;
        z-index: 2;
      }
      .links li:hover .sub-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0px);
      }
      .sub-menu li {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      }
      .sub-menu li:last-child {
        border-bottom: none;
      }
      .sub-menu a {
        color: #fff;
        font-size: 15px;
        font-weight: 500;
        padding: 10px 20px;
        display: flex;
        align-items: center;
      }
      .sub-menu a i {
        margin-right: 8px;
      }
      .sub-menu a:hover {
        background-color: rgba(255, 255, 255, 0.15);
      }
      /* =========== MOBILE RESPONSIVE =========== */
      .navbar .bx-menu {
        display: none;
        font-size: 28px;
        color: #fff;
        cursor: pointer;
      }
      /* Sidebar logo (untuk mode mobile) */
      .nav-links .sidebar-logo {
        display: none;
      }
      @media (max-width: 920px) {
        nav .navbar {
          max-width: 100%;
          padding: 0 25px;
        }
        nav .navbar .logo a {
          font-size: 27px;
        }
        nav .navbar .links li a {
          font-size: 15px;
          padding: 0 10px;
        }
      }
      @media (max-width: 800px) {
        /* Tombol menu muncul */
        .navbar .bx-menu {
          display: block;
        }
        .nav-links {
          position: fixed;
          top: 0;
          left: -100%;
          display: block;
          max-width: 270px;
          width: 100%;
          background: #9AC5F4;
          line-height: 40px;
          padding: 20px;
          box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
          transition: all 0.5s ease;
          z-index: 1000;
        }
        .nav-links.show1,
        .nav-links.show2,
        .nav-links.show3 {
          left: 0;
        }
        .nav-links .sidebar-logo {
          display: flex;
          align-items: center;
          justify-content: space-between;
        }
        .sidebar-logo .logo-name {
          font-size: 25px;
          color: #fff;
          font-weight: 600;
        }
        .sidebar-logo i,
        .navbar .bx-menu {
          font-size: 25px;
          color: #fff;
        }
        .links {
          display: block;
          margin-top: 20px;
        }
        .links li {
          display: block;
        }
        /* Sub-menu di mobile */
        .sub-menu {
          position: relative;
          top: 0;
          left: 0;
          transform: translateY(0);
          opacity: 1;
          visibility: visible;
          box-shadow: none;
          background: #578FCA;
          border-radius: 6px;
          display: none;
        }
        .links li:hover .sub-menu {
          display: none; /* Matikan hover di mobile */
        }
        .nav-links.show1 .htmlCss-sub-menu,
        .nav-links.show3 .js-sub-menu,
        .nav-links.show2 .more-sub-menu {
          display: block;
        }
        /* Arrow rotate di mobile */
        .nav-links.show1 .htmlcss-arrow,
        .nav-links.show3 .js-arrow {
          transform: rotate(180deg);
        }
      }
      @media (max-width: 370px) {
        nav .navbar .nav-links {
          max-width: 100%;
        }
      }
      /* =========== LOGOUT BUTTON STYLING =========== */
      .logout {
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .Btn {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 45px;
        height: 45px;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition-duration: 0.3s;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        background-color: #f44336; /* Warna merah logout */
      }
      .Btn:hover {
        width: 125px;
        border-radius: 40px;
        transition-duration: 0.3s;
      }
      .sign {
        width: 100%;
        transition-duration: 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .sign svg {
        width: 17px;
      }
      .sign svg path {
        fill: #fff;
      }
      .text {
        position: absolute;
        right: 0%;
        width: 0%;
        opacity: 0;
        color: #fff;
        font-size: 0.9em;
        font-weight: 600;
        transition-duration: 0.3s;
        text-align: center;
      }
      .Btn:hover .sign {
        width: 30%;
        padding-left: 20px;
      }
      .Btn:hover .text {
        opacity: 1;
        width: 70%;
        padding-right: 10px;
      }
      .Btn:active {
        transform: translate(2px, 2px);
      }
    </style>
  </head>
  <body>
    <nav>
      <div class="navbar">
        <!-- Icon menu (untuk mobile) -->
        <i class='bx bx-menu'></i>

        <!-- Logo -->
        <div class="logo">
          <a href="#">
            <i class="fas fa-tshirt"></i> Rumah Laundry
          </a>
        </div>

        <!-- Navigation Links -->
        <div class="nav-links">
          <div class="sidebar-logo">
            <span class="logo-name">Rumah Laundry</span>
            <i class='bx bx-x'></i>
          </div>
          <ul class="links">
            <!-- Contoh link -->
            <li>
              <a href="<?= BASE_URL; ?>admin/beranda/index.php">
                <i class="fas fa-home"></i> BERANDA
              </a>
            </li>
            <li>
              <a href="#">
                <i class="fas fa-database"></i> MASTER DATA
                <!-- Panah turun -->
                <i class='bx bxs-chevron-down htmlcss-arrow arrow'></i>
              </a>
              <!-- Sub-menu -->
              <ul class="htmlCss-sub-menu sub-menu">
                <li>
                  <a href="<?= BASE_URL; ?>admin/customer/show_customer.php">
                    <i class="fas fa-users"></i> Data Customer
                  </a>
                </li>
                <li>
                  <a href="<?= BASE_URL; ?>admin/akun/show_akun_petugas.php">
                    <i class="fas fa-user-edit"></i> Perbarui Profil
                  </a>
                </li>
              </ul>
            </li>
            <li>
              <a href="<?= BASE_URL; ?>admin/transaksi/show_transaksi.php">
                <i class="fas fa-exchange-alt"></i> Transaksi
              </a>
            </li>
          </ul>
        </div>

        <!-- Tombol Logout -->
        <div class="logout">
          <a href="<?= BASE_URL; ?>admin/logout.php" class="Btn" title="Logout">
            <div class="sign">
              <svg viewBox="0 0 512 512">
                <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/>
              </svg>
            </div>
            <div class="text">Logout</div>
          </a>
        </div>
      </div>
    </nav>

    <!-- Pastikan menautkan script.js jika diperlukan untuk toggle menu di mobile -->
    <script src="script.js"></script>
  </body>
</html>
