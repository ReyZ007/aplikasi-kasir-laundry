<?php
    include "include/header.php";
?>
<!-- Awal Body -->

<section class="vh-100" style="background-color: #f0f8ff;">
  <style>
    /* Custom Font */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
    
    body {
      margin: 0;
      padding: 0;
      background-image: linear-gradient(rgba(80, 139, 252, 0.1), rgba(80, 139, 252, 0.1)), url('bgl.jpg');
      background-size: cover;
      background-position: center;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      position: relative;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .login-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .form-control {
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      padding: 15px 20px 15px 45px;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #508bfc;
      box-shadow: 0 0 15px rgba(80, 139, 252, 0.2);
    }

    .input-icon {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      color: #508bfc;
      transition: all 0.3s ease;
    }

    .btn-login {
      background: linear-gradient(135deg, #508bfc, #6c5ce7);
      border: none;
      padding: 12px 30px;
      border-radius: 10px;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(80, 139, 252, 0.4);
    }

    .brand-logo {
      width: 80px;
      margin-bottom: 20px;
      filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
    }
  </style>

  <div class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="login-card p-5">
          <div class="text-center mb-4">
            <h3 class="mb-3 fw-bold" style="color: #508bfc;">Rumah Laundry</h3>
            <!-- <div class="animated-border"></div> --> 
            <!-- Garis biru dihapus -->
          </div>

          <form action="cek_login.php" method="POST">
            <div class="form-group mb-4 position-relative">
              <i class="fas fa-user input-icon"></i>
              <input type="text" name="username" class="form-control" placeholder="Nama Pengguna" required>
            </div>

            <div class="form-group mb-4 position-relative">
              <i class="fas fa-lock input-icon"></i>
              <input type="password" name="password" class="form-control" placeholder="Kata Sandi" required>
            </div>

            <button type="submit" class="btn btn-login btn-block text-white mb-4">
              <i class="fas fa-sign-in-alt me-2"></i>Masuk
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Akhir Body -->
<?php
    include "include/footer.php";
?>
