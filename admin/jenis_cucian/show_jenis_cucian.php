<?php
session_start();
include "../include/koneksi.php";

// Check if user is logged in and has the correct level
if ($_SESSION['level'] != "admin") {
    header("Location: ../index.php");
    exit();
}

// Proses Tambah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nama_jenis_cucian = $_POST['nama_jenis_cucian'];
    $harga = (int) $_POST['harga'];

    $stmt = mysqli_prepare($koneksi, "INSERT INTO jenis_cucian (nama_jenis_cucian, harga) VALUES (?, ?)");
    if ($stmt && mysqli_stmt_bind_param($stmt, "si", $nama_jenis_cucian, $harga) && mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Jenis cucian berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan jenis cucian: " . mysqli_error($koneksi);
    }
    if ($stmt) {
        mysqli_stmt_close($stmt);
    }

    header("Location: show_jenis_cucian.php");
    exit();
}

// Proses Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id_jenis_cucian = (int) $_POST['id_jenis_cucian'];
    $nama_jenis_cucian = $_POST['nama_jenis_cucian'];
    $harga = (int) $_POST['harga'];

    $stmt = mysqli_prepare($koneksi, "UPDATE jenis_cucian SET nama_jenis_cucian = ?, harga = ? WHERE id_jenis_cucian = ?");
    if ($stmt && mysqli_stmt_bind_param($stmt, "sii", $nama_jenis_cucian, $harga, $id_jenis_cucian) && mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Jenis cucian berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui jenis cucian: " . mysqli_error($koneksi);
    }
    if ($stmt) {
        mysqli_stmt_close($stmt);
    }

    header("Location: show_jenis_cucian.php");
    exit();
}

// Proses Hapus
if (isset($_GET['hapus_jenis_cucian'])) {
    $id_jenis_cucian = (int) $_GET['hapus_jenis_cucian'];

    // Pastikan ID jenis cucian valid
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM jenis_cucian WHERE id_jenis_cucian = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_jenis_cucian);
    mysqli_stmt_execute($stmt);
    $result_check = mysqli_stmt_get_result($stmt);
    $jenis_cucian_ditemukan = mysqli_num_rows($result_check) > 0;
    mysqli_stmt_close($stmt);
    if ($jenis_cucian_ditemukan) {
        // Hapus jenis cucian
        $stmt = mysqli_prepare($koneksi, "DELETE FROM jenis_cucian WHERE id_jenis_cucian = ?");
        mysqli_stmt_bind_param($stmt, "i", $id_jenis_cucian);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['message'] = "Jenis cucian berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Jenis cucian tidak ditemukan.";
    }

    header("Location: show_jenis_cucian.php");
    exit();
}

include "../include/header.php";
?>

<!-- Extra CSS dan Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-o8C1NxQjYK8LM+DEb77JYdE6v9FsRyrpO01HdJk3TvP2dy1X+hZzE7lIrAF3V9/pHQzovt1CebhxSiy1r7a3MQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    body {
        padding-top: 70px; /* Jika menggunakan navbar fixed-top */
        background: #f0f2f5;
        font-family: 'Poppins', sans-serif;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-top: 20px;
    }
    .card h3 {
        font-weight: bold;
    }
    .table th, .table td {
        vertical-align: middle !important;
    }
    .modal-header {
        border-bottom: 2px solid #dee2e6;
    }
    .btn i {
        margin-right: 4px;
    }
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
    }
</style>

<?php
include "../include/navbar_admin.php";
?>

<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h3 class="text-center text-primary mt-2 mb-4">
            <i class="fas fa-tshirt me-2"></i><b>Data Jenis Cucian</b>
        </h3>

        <!-- Notifikasi (Pesan Sukses / Error) -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> 
                <?= $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> 
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Tombol Tambah -->
        <div class="d-flex justify-content-between mb-3">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus-circle"></i> Tambah Jenis Cucian
            </button>
            <form class="d-flex" method="GET" action="">
                <input class="form-control me-2" type="search" name="search" placeholder="Cari Jenis Cucian" aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Ambil Data dari Database -->
        <?php
        $sql = "SELECT * FROM jenis_cucian";
        $sql_eksekusi = mysqli_query($koneksi, $sql);
        ?>

        <!-- Tabel Jenis Cucian -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No.</th>
                        <th>Nama Jenis Cucian</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sql_eksekusi && mysqli_num_rows($sql_eksekusi) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($data = mysqli_fetch_array($sql_eksekusi)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($data['nama_jenis_cucian']); ?></td>
                                <td>Rp <?= number_format($data['harga'], 0, ',', '.'); ?></td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <!-- Tombol Lihat -->
                                        <button class="btn btn-info btn-sm text-white" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalLihat<?= $data['id_jenis_cucian']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- Tombol Edit -->
                                        <button class="btn btn-warning btn-sm text-white" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit<?= $data['id_jenis_cucian']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Tombol Hapus -->
                                        <a href="show_jenis_cucian.php?hapus_jenis_cucian=<?= $data['id_jenis_cucian']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus jenis cucian ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Lihat Jenis Cucian -->
                            <div class="modal fade" id="modalLihat<?= $data['id_jenis_cucian']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-id-card me-2"></i>Detail Jenis Cucian
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <div class="bg-light p-3 rounded">
                                                        <small class="text-muted">Nama Jenis Cucian</small>
                                                        <p class="mb-0 fw-bold"><?= htmlspecialchars($data['nama_jenis_cucian']); ?></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="bg-light p-3 rounded">
                                                        <small class="text-muted">Harga</small>
                                                        <p class="mb-0 fw-bold">Rp <?= number_format($data['harga'], 0, ',', '.'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="modalEdit<?= $data['id_jenis_cucian']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title text-white">
                                                <i class="fas fa-edit"></i> Edit Jenis Cucian
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="show_jenis_cucian.php" method="POST">
                                                <input type="hidden" name="id_jenis_cucian" value="<?= $data['id_jenis_cucian']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Jenis Cucian</label>
                                                    <input type="text" class="form-control" name="nama_jenis_cucian" value="<?= htmlspecialchars($data['nama_jenis_cucian']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Harga</label>
                                                    <input type="number" class="form-control" name="harga" value="<?= htmlspecialchars($data['harga']); ?>" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary" name="edit">
                                                    <i class="fas fa-save"></i> Simpan
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Tidak ada data jenis cucian.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> <!-- end table-responsive -->
    </div> <!-- end card -->
</div> <!-- end container -->

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-plus-circle"></i> Tambah Jenis Cucian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="show_jenis_cucian.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Jenis Cucian</label>
                        <input type="text" class="form-control" name="nama_jenis_cucian" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control" name="harga" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" name="add">
                        <i class="fas fa-plus-circle"></i> Tambah
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include "../include/footer.php";
?>
