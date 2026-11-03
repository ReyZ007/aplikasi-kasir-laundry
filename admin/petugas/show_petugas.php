<?php
session_start();
include "../include/koneksi.php";
include "../include/header.php";

// Hanya admin yang boleh mengakses halaman ini
if ($_SESSION['level'] != "admin") {
    header("Location: ../index.php");
    exit();
}

// Proses Tambah & Edit Petugas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Tambah Petugas
    if (isset($_POST['add'])) {
        $nama_petugas = $_POST['nama_petugas'];
        $no_telp      = $_POST['no_telp'];
        $alamat       = $_POST['alamat'];
        $email        = $_POST['email'];

        $stmt = mysqli_prepare($koneksi, "INSERT INTO petugas (nama_petugas, no_telp, alamat, email) VALUES (?, ?, ?, ?)");
        if ($stmt && mysqli_stmt_bind_param($stmt, "ssss", $nama_petugas, $no_telp, $alamat, $email) && mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Petugas berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Gagal menambahkan petugas: " . mysqli_error($koneksi);
        }
        if ($stmt) {
            mysqli_stmt_close($stmt);
        }
        header("Location: show_petugas.php");
        exit();
    }

    // Edit Petugas
    if (isset($_POST['edit'])) {
        $id_petugas   = (int) $_POST['id_petugas'];
        $nama_petugas = $_POST['nama_petugas'];
        $no_telp      = $_POST['no_telp'];
        $alamat       = $_POST['alamat'];
        $email        = $_POST['email'];

        $stmt = mysqli_prepare($koneksi, "UPDATE petugas SET nama_petugas = ?, no_telp = ?, alamat = ?, email = ? WHERE id_petugas = ?");
        if ($stmt && mysqli_stmt_bind_param($stmt, "ssssi", $nama_petugas, $no_telp, $alamat, $email, $id_petugas) && mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Petugas berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui petugas: " . mysqli_error($koneksi);
        }
        if ($stmt) {
            mysqli_stmt_close($stmt);
        }
        header("Location: show_petugas.php");
        exit();
    }
}

// Proses Hapus Petugas
if (isset($_GET['hapus_petugas'])) {
    $id_petugas = (int) $_GET['hapus_petugas'];

    // Hapus akun terkait terlebih dahulu
    $stmt = mysqli_prepare($koneksi, "DELETE FROM akun WHERE id_petugas = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_petugas);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Hapus transaksi terkait terlebih dahulu
    $stmt = mysqli_prepare($koneksi, "DELETE dt FROM detail_transaksi dt INNER JOIN transaksi t ON dt.id_transaksi = t.id_transaksi WHERE t.id_petugas = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_petugas);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $stmt = mysqli_prepare($koneksi, "DELETE FROM transaksi WHERE id_petugas = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_petugas);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Pastikan petugas ada sebelum menghapus
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM petugas WHERE id_petugas = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_petugas);
    mysqli_stmt_execute($stmt);
    $result_check = mysqli_stmt_get_result($stmt);
    $petugas_ditemukan = mysqli_num_rows($result_check) > 0;
    mysqli_stmt_close($stmt);
    if ($petugas_ditemukan) {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM petugas WHERE id_petugas = ?");
        mysqli_stmt_bind_param($stmt, "i", $id_petugas);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['message'] = "Petugas berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Petugas tidak ditemukan.";
    }

    header("Location: show_petugas.php");
    exit();
}

include "../include/navbar_admin.php";
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

<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h3 class="text-center text-primary mt-2 mb-4">
            <i class="fas fa-users me-2"></i><b>Data Petugas</b>
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

        <!-- Tombol Tambah dan Search -->
        <div class="d-flex justify-content-between mb-3">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus-circle"></i> Tambah Petugas
            </button>
            <form class="d-flex" method="GET" action="">
                <input class="form-control me-2" type="search" name="search" placeholder="Cari Petugas" aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <?php 
            // Ambil data petugas dari database
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $like = "%" . $search . "%";
            $stmt = mysqli_prepare($koneksi, "SELECT * FROM petugas WHERE nama_petugas LIKE ? OR no_telp LIKE ? OR alamat LIKE ? OR email LIKE ?");
            mysqli_stmt_bind_param($stmt, "ssss", $like, $like, $like, $like);
            mysqli_stmt_execute($stmt);
            $sql_eksekusi = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
        ?>

        <!-- Tabel Data Petugas -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No.</th>
                        <th>Nama Petugas</th>
                        <th>Nomor Telepon</th>
                        <th>Alamat</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sql_eksekusi && mysqli_num_rows($sql_eksekusi) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($data = mysqli_fetch_array($sql_eksekusi)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($data['nama_petugas']); ?></td>
                                <td><?= htmlspecialchars($data['no_telp']); ?></td>
                                <td><?= htmlspecialchars($data['alamat']); ?></td>
                                <td><?= htmlspecialchars($data['email']); ?></td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <!-- Tombol Lihat -->
                                        <button class="btn btn-info btn-sm text-white" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalLihat<?= $data['id_petugas']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- Tombol Edit -->
                                        <button class="btn btn-warning btn-sm text-white" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit<?= $data['id_petugas']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Tombol Hapus -->
                                        <a href="show_petugas.php?hapus_petugas=<?= $data['id_petugas']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus petugas ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Lihat Petugas -->
                            <div class="modal fade" id="modalLihat<?= $data['id_petugas']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-id-card me-2"></i>Detail Petugas
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="bg-light p-3 rounded">
                                                        <small class="text-muted">Nama Petugas</small>
                                                        <p class="mb-0 fw-bold"><?= htmlspecialchars($data['nama_petugas']); ?></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="bg-light p-3 rounded">
                                                        <small class="text-muted">Nomor Telepon</small>
                                                        <p class="mb-0 fw-bold"><?= htmlspecialchars($data['no_telp']); ?></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="bg-light p-3 rounded">
                                                        <small class="text-muted">Alamat</small>
                                                        <p class="mb-0 fw-bold"><?= htmlspecialchars($data['alamat']); ?></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="bg-light p-3 rounded">
                                                        <small class="text-muted">Email</small>
                                                        <p class="mb-0 fw-bold"><?= htmlspecialchars($data['email']); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Edit Petugas -->
                            <div class="modal fade" id="modalEdit<?= $data['id_petugas']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title text-white">
                                                <i class="fas fa-edit"></i> Edit Petugas
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="show_petugas.php" method="POST">
                                                <input type="hidden" name="id_petugas" value="<?= $data['id_petugas']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Petugas</label>
                                                    <input type="text" class="form-control" name="nama_petugas" value="<?= htmlspecialchars($data['nama_petugas']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nomor Telepon</label>
                                                    <input type="tel" class="form-control" name="no_telp" value="<?= htmlspecialchars($data['no_telp']); ?>" required pattern="08[0-9]{2}-[0-9]{4}-[0-9]{4,5}" maxlength="15" inputmode="numeric" placeholder="Contoh: 0812-3456-7890" title="Format: 0812-3456-7890" oninput="formatTelp(this)">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Alamat</label>
                                                    <input type="text" class="form-control" name="alamat" value="<?= htmlspecialchars($data['alamat']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($data['email']); ?>" required>
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
                            <td colspan="6">Tidak ada data petugas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div><!-- end table-responsive -->
    </div><!-- end card -->
</div><!-- end container -->

<!-- Modal Tambah Petugas -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user-plus"></i> Tambah Petugas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="show_petugas.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Petugas</label>
                        <input type="text" class="form-control" name="nama_petugas" placeholder="Masukkan nama petugas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control" name="no_telp" placeholder="Contoh: 0812-3456-7890" required pattern="08[0-9]{2}-[0-9]{4}-[0-9]{4,5}" maxlength="15" inputmode="numeric" title="Format: 0812-3456-7890" oninput="formatTelp(this)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" class="form-control" name="alamat" placeholder="Masukkan alamat" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Masukkan email" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" name="add">
                        <i class="fas fa-plus-circle"></i> Tambah Petugas
                    </button>
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

<?php include "../include/footer.php"; ?>