<?php 
session_start();
include "../include/koneksi.php";
include "../include/header.php";

// Hanya admin yang boleh mengakses halaman ini
if ($_SESSION['level'] != "admin") {
    header("Location: ../index.php");
    exit();
}

// Proses Tambah Akun
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $level = $_POST['level'];
    $id_petugas = (int) $_POST['id_petugas'];

    $sql = "INSERT INTO akun (username, password, level, id_petugas) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt && mysqli_stmt_bind_param($stmt, "sssi", $username, $password, $level, $id_petugas) && mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Akun berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan akun: " . mysqli_error($koneksi);
    }
    if ($stmt) {
        mysqli_stmt_close($stmt);
    }

    header("Location: show_akun_admin.php");
    exit();
}

// Proses Edit Akun
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id_akun = (int) $_POST['id_akun'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $level = $_POST['level'];
    $id_petugas = (int) $_POST['id_petugas'];

    $sql = "UPDATE akun SET username = ?, password = ?, level = ?, id_petugas = ? WHERE id_akun = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt && mysqli_stmt_bind_param($stmt, "sssii", $username, $password, $level, $id_petugas, $id_akun) && mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Akun berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui akun: " . mysqli_error($koneksi);
    }
    if ($stmt) {
        mysqli_stmt_close($stmt);
    }

    header("Location: show_akun_admin.php");
    exit();
}

// Proses Hapus Akun
if (isset($_GET['hapus_akun'])) {
    $id_akun = (int) $_GET['hapus_akun'];

    // Pastikan ID akun valid
    $query_check = "SELECT * FROM akun WHERE id_akun = ?";
    $stmt = mysqli_prepare($koneksi, $query_check);
    mysqli_stmt_bind_param($stmt, "i", $id_akun);
    mysqli_stmt_execute($stmt);
    $result_check = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($result_check) > 0) {
        $query_hapus = "DELETE FROM akun WHERE id_akun = ?";
        $stmt = mysqli_prepare($koneksi, $query_hapus);
        mysqli_stmt_bind_param($stmt, "i", $id_akun);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['message'] = "Akun berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Akun tidak ditemukan.";
    }

    header("Location: show_akun_admin.php");
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
            <i class="fas fa-user-circle me-2"></i><b>Data Akun</b>
        </h3>

        <!-- Notifikasi Pesan -->
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> " . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> " . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <!-- Tombol Tambah -->
        <div class="d-flex justify-content-between mb-3">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus-circle"></i> Tambah Akun
            </button>
            <form class="d-flex" method="GET" action="">
                <input class="form-control me-2" type="search" name="search" placeholder="Cari Akun" aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <?php
        $sql = "SELECT * FROM akun";
        $sql_eksekusi = mysqli_query($koneksi, $sql);
        ?>

        <!-- Tabel Data Akun -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No.</th>
                        <th>Username</th>
                        <th>Level</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($sql_eksekusi) {
                        $no = 1;
                        while ($data = mysqli_fetch_array($sql_eksekusi)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($data['username']); ?></td>
                        <td><?= htmlspecialchars($data['level']); ?></td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <!-- Tombol Lihat -->
                                <button class="btn btn-info btn-sm text-white" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalLihat<?= $data['id_akun']; ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <!-- Tombol Edit -->
                                <button class="btn btn-warning btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit<?= $data['id_akun']; ?>">
                                    <i class="fas fa-edit text-white"></i> 
                                </button>
                                <!-- Tombol Hapus -->
                                <a href="show_akun_admin.php?hapus_akun=<?= $data['id_akun']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Lihat Akun -->
                    <div class="modal fade" id="modalLihat<?= $data['id_akun']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-id-card me-2"></i>Detail Akun
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="bg-light p-3 rounded">
                                                <small class="text-muted">Username</small>
                                                <p class="mb-0 fw-bold"><?= htmlspecialchars($data['username']); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="bg-light p-3 rounded">
                                                <small class="text-muted">Level</small>
                                                <p class="mb-0 fw-bold"><?= htmlspecialchars($data['level']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Edit Akun -->
                    <div class="modal fade" id="modalEdit<?= $data['id_akun']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title text-white">
                                        <i class="fas fa-edit"></i> Edit Akun
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="show_akun_admin.php" method="POST">
                                        <input type="hidden" name="id_akun" value="<?= $data['id_akun']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($data['username']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="password" class="form-control" name="password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Level</label>
                                            <select class="form-control" name="level" required>
                                                <option value="admin" <?= ($data['level'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                <option value="petugas" <?= ($data['level'] == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">ID Petugas</label>
                                            <select class="form-control" name="id_petugas" required>
                                                <?php 
                                                $query_petugas = mysqli_query($koneksi, "SELECT * FROM petugas");
                                                while ($row = mysqli_fetch_array($query_petugas)) {
                                                    echo "<option value='".$row['id_petugas']."' ".(($data['id_petugas'] == $row['id_petugas']) ? 'selected' : '').">".$row['nama_petugas']."</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="edit">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='4'>Tidak ada data akun.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Akun -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-plus-circle"></i> Tambah Akun
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="show_akun_admin.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select class="form-control" name="level" required>
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID Petugas</label>
                        <select class="form-control" name="id_petugas" required>
                            <?php 
                            $query_petugas = mysqli_query($koneksi, "SELECT * FROM petugas");
                            while ($row = mysqli_fetch_array($query_petugas)) {
                                echo "<option value='".$row['id_petugas']."'>".$row['nama_petugas']."</option>";
                            }
                            ?>
                        </select>
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
