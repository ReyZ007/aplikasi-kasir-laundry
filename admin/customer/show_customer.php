<?php
session_start();
include "../include/koneksi.php";
include "../include/header.php";

// Hanya untuk user level "petugas"
if ($_SESSION['level'] != "petugas") {
    header("Location: ../index.php");
    exit();
}

// ====== PROSES TAMBAH CUSTOMER (POST) ======
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_customer'])) {
    // Gunakan mysqli_real_escape_string untuk mencegah SQL Injection
    $nama_customer = mysqli_real_escape_string($koneksi, $_POST['nama_customer']);
    $no_telp       = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    
    $insert_sql = "INSERT INTO customer (nama_customer, no_telp)
                   VALUES ('$nama_customer', '$no_telp')";
    if (mysqli_query($koneksi, $insert_sql)) {
        // Setelah insert, redirect dengan parameter GET agar tidak terjadi form resubmission
        header("Location: show_customer.php?added=1");
        exit();
    } else {
        // Jika terjadi error, redirect dengan parameter error
        header("Location: show_customer.php?error=1");
        exit();
    }
}

include "../include/navbar.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Customer</title>
    <style>
        body {
            padding-top: 70px; /* Jika menggunakan navbar fixed-top */
            font-family: 'Poppins', sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .btn-action {
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .modal-content {
            border-radius: 15px !important;
            overflow: hidden;
        }
        .modal-header {
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: white;
            border: none;
        }
        .btn-close {
            background: transparent !important;
            box-shadow: none !important;
        }
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
        }
    </style>
</head>
<body>
<div class="container mt-5 pt-4">
    <div class="card shadow-lg p-4 border-0">
        <h3 class="text-center text-primary mt-4 mb-4">
            <i class="fas fa-users me-2"></i><b>Data Customer</b>
        </h3>

        <!-- Alert Notification -->
        <?php
        if (isset($_GET['added'])) {
            echo "<div class='alert alert-success' role='alert'>
                    <i class='fa-solid fa-circle-check me-2'></i>Customer berhasil ditambahkan!
                  </div>";
        }
        if (isset($_GET['edited'])) {
            echo "<div class='alert alert-success' role='alert'>
                    <i class='fa-solid fa-circle-check me-2'></i>Customer berhasil diubah!
                  </div>";
        }
        if (isset($_GET['deleted'])) {
            echo "<div class='alert alert-success' role='alert'>
                    <i class='fa-solid fa-circle-check me-2'></i>Customer berhasil dihapus!
                  </div>";
        }
        if (isset($_GET['error'])) {
            echo "<div class='alert alert-danger' role='alert'>
                    <i class='fa-solid fa-circle-xmark me-2'></i>Terjadi kesalahan saat menambahkan Customer.
                  </div>";
        }
        ?>

        <div class="d-flex justify-content-between mb-3">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus-circle"></i> Tambah Customer
            </button>
            <form class="d-flex" method="GET" action="">
                <input class="form-control me-2" type="search" name="search" placeholder="Cari Customer" aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <div class="table-responsive rounded-3">
            <table class="table table-hover align-middle">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="py-3">No</th>
                        <th class="py-3">Nama Customer</th>
                        <th class="py-3">Kontak</th>
                        <th class="py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    $sql = "SELECT * FROM customer 
                            WHERE nama_customer LIKE '%$search%' 
                               OR no_telp LIKE '%$search%'";
                    $sql_eksekusi = mysqli_query($koneksi, $sql);
                    if ($sql_eksekusi && mysqli_num_rows($sql_eksekusi) > 0):
                        $no = 1;
                        while ($data = mysqli_fetch_array($sql_eksekusi)):
                    ?>
                    <tr class="shadow-sm">
                        <td class="fw-bold"><?= $no++; ?></td>
                        <td><?= htmlspecialchars($data['nama_customer']); ?></td>
                        <td>
                            <a href="tel:<?= htmlspecialchars($data['no_telp']); ?>" class="text-decoration-none">
                                <i class="fas fa-phone-alt me-2 text-success"></i>
                                <?= htmlspecialchars($data['no_telp']); ?>
                            </a>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <!-- Tombol Lihat -->
                                <button class="btn btn-info btn-action text-white" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalLihat<?= $data['id_customer']; ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <!-- Tombol Edit -->
                                <button class="btn btn-warning btn-action text-white" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit<?= $data['id_customer']; ?>">
                                    <i class="fas fa-edit text-white"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Lihat -->
                    <div class="modal fade" id="modalLihat<?= $data['id_customer']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-id-card me-2"></i>Detail Customer
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="bg-light p-3 rounded">
                                                <small class="text-muted">Nama Lengkap</small>
                                                <p class="mb-0 fw-bold"><?= htmlspecialchars($data['nama_customer']); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="bg-light p-3 rounded">
                                                <small class="text-muted">Nomor Telepon</small>
                                                <p class="mb-0 fw-bold"><?= htmlspecialchars($data['no_telp']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $data['id_customer']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-user-edit me-2"></i>Edit Customer
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="edit_customer.php" method="POST">
                                        <input type="hidden" name="id_customer" value="<?= $data['id_customer']; ?>">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Nama Customer</label>
                                            <input type="text" class="form-control form-control-lg" 
                                                   name="nama_customer" 
                                                   value="<?= htmlspecialchars($data['nama_customer']); ?>" 
                                                   required>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Nomor Telepon</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-primary text-white">
                                                    <i class="fas fa-phone"></i>
                                                </span>
                                                <input type="tel" class="form-control form-control-lg" 
                                                       name="no_telp" 
                                                       value="<?= htmlspecialchars($data['no_telp']); ?>" 
                                                       required pattern="08[0-9]{2}-[0-9]{4}-[0-9]{4,5}" maxlength="15" inputmode="numeric"
                                                       placeholder="Contoh: 0812-3456-7890"
                                                       title="Format: 0812-3456-7890" oninput="formatTelp(this)">
                                            </div>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>Tidak ada data Customer yang ditemukan
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Tambah Customer Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Form tambah customer diarahkan ke file ini sendiri -->
                <form action="" method="POST">
                    <input type="hidden" name="tambah_customer" value="1">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control form-control-lg" 
                               name="nama_customer" 
                               placeholder="Masukkan nama lengkap" 
                               required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nomor Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="tel" class="form-control form-control-lg" 
                                   name="no_telp" 
                                   placeholder="Contoh: 0812-3456-7890" 
                                   required pattern="08[0-9]{2}-[0-9]{4}-[0-9]{4,5}" maxlength="15" inputmode="numeric"
                                   title="Format: 0812-3456-7890" oninput="formatTelp(this)">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function formatTelp(input) {
    var angka = input.value.replace(/\D/g, '').substring(0, 13);
    var hasil = angka.substring(0, 4);
    if (angka.length > 4) hasil += '-' + angka.substring(4, 8);
    if (angka.length > 8) hasil += '-' + angka.substring(8);
    input.value = hasil;
}
</script>

<?php
    echo "</div>"; // Close card
    echo "</div>"; // Close container
    include "../include/footer.php";
?>
