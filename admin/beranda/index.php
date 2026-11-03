<?php 
session_start();
date_default_timezone_set('Asia/Jakarta');
include "../include/koneksi.php";
include "../include/header.php";

if (isset($_SESSION['level'])) {
    if ($_SESSION['level'] == "petugas" || $_SESSION['level'] == "admin") { 
        include "../include/navbar.php"; 
    } else { 
        header("Location: ../index.php"); 
        exit(); 
    }

    // Handle Hapus Transaksi
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_transaksi'])) {
        $transaksi_ids = $_POST['transaksi_ids'] ?? [];
        if (!empty($transaksi_ids)) {
            foreach ($transaksi_ids as $id_transaksi) {
                $id_transaksi = (int) $id_transaksi;
                $stmt = mysqli_prepare($koneksi, "DELETE FROM detail_transaksi WHERE id_transaksi = ?");
                mysqli_stmt_bind_param($stmt, "i", $id_transaksi);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                $stmt = mysqli_prepare($koneksi, "DELETE FROM transaksi WHERE id_transaksi = ?");
                mysqli_stmt_bind_param($stmt, "i", $id_transaksi);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Transaksi berhasil dihapus.'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Tidak ada transaksi yang dipilih.'];
        }
        header("Location: index.php");
        exit();
    }
?>

<style>
     body {
        padding-top: 10px; /* Jika menggunakan navbar fixed-top */
    }
    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        overflow: hidden;
    }
    
    .float-animation {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    
    .stats-card {
        transition: all 0.3s ease;
        border-left: 4px solid;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .transaction-table {
        border-collapse: collapse;
        width: 100%;
    }
    
    .transaction-table th,
    .transaction-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }
    
    .transaction-table th {
        background-color: #f8f9fa;
    }
    
    .alert-message {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<!-- Tambahkan mt-5 agar ada jarak dengan navbar -->
<div class="container-fluid p-4 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="bi bi-speedometer2 me-2"></i>Dashboard Petugas</h2>
        <div class="date-box">
            <span class="text-muted"><?= date('l, d F Y') ?></span>
        </div>
    </div>

    <!-- Pesan Alert -->
    <?php if (isset($_SESSION['message'])) : ?>
        <div class="alert-message alert-<?= $_SESSION['message']['type'] ?>">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Welcome Section -->
    <div class="welcome-section text-white mb-5 p-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-3">
                    Selamat Datang di <span class="text-warning">Rumah Laundry</span>
                </h1>
                <p class="lead mb-4">
                    "Nikmati layanan laundry premium dengan hasil bersih, wangi, dan cepat! 
                    Percayakan perawatan pakaian Anda kepada profesional kami."
                </p>
                <a href="../transaksi/show_transaksi.php" class="btn btn-light btn-lg rounded-pill px-4">
                    <i class="fas fa-plus-circle me-2"></i>Buat Transaksi Baru
                </a>
            </div>
            <div class="col-md-6 text-center">
                <img src="../img/beranda.png" alt="Laundry Service" 
                     class="float-animation img-fluid" style="max-width: 300px;">
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5 justify-content-center">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card card border-left-primary h-100 p-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle p-3 me-3">
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="text-muted mb-0">Transaksi Hari Ini</h5>
                            <h2 class="mb-0">
                                <?php
                                $result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi 
                                                                WHERE DATE(tgl_transaksi) = CURDATE()");
                                echo mysqli_fetch_assoc($result)['total'];
                                ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card card border-left-success h-100 p-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success text-white rounded-circle p-3 me-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="text-muted mb-0">Total Customer</h5>
                            <h2 class="mb-0">
                                <?php
                                $result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM customer");
                                echo mysqli_fetch_assoc($result)['total'];
                                ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card card border-left-warning h-100 p-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-white rounded-circle p-3 me-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="text-muted mb-0">Dalam Proses</h5>
                            <h2 class="mb-0">
                                <?php
                                $result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi 
                                                                WHERE status = 'Proses'");
                                echo mysqli_fetch_assoc($result)['total'];
                                ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card card border-left-info h-100 p-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle p-3 me-3">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="text-muted mb-0">Pendapatan Hari Ini</h5>
                            <h2 class="mb-0">
                                <?php
                                $result = mysqli_query($koneksi, "SELECT SUM(d.total_harga) AS total 
                                                                  FROM detail_transaksi d
                                                                  INNER JOIN transaksi t ON d.id_transaksi = t.id_transaksi
                                                                  WHERE DATE(t.tgl_transaksi) = CURDATE()");
                                $total_pendapatan = mysqli_fetch_assoc($result)['total'] ?? 0;
                                echo 'Rp ' . number_format($total_pendapatan, 0, ',', '.');
                                ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Section -->
    <div class="card border-0 shadow-lg mb-5">
        <div class="card-header bg-danger text-white p-3">
            <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Transaksi Jatuh Tempo</h4>
        </div>
        <div class="card-body p-4">
            <form method="POST">
                <div class="table-responsive">
                    <table class="transaction-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"> Pilih Semua
                                </th>
                                <th>ID Transaksi</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE status = 'Proses'");
                            $status_color = [
                                'Proses' => 'warning',
                                'Dicuci' => 'primary',
                                'Disetrika' => 'info'
                            ];
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'];
                                $color = $status_color[$status] ?? 'secondary';
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="transaksi-checkbox" 
                                           name="transaksi_ids[]" value="<?= $row['id_transaksi'] ?>">
                                </td>
                                <td>#<?= $row['id_transaksi'] ?></td>
                                <td><?= date('d M Y', strtotime($row['tgl_transaksi'])) ?></td>
                                <td>
                                    <span class="status-badge bg-<?= $color ?>"><?= $status ?></span>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                <script>
                function toggleSelectAll(source) {
                    const checkboxes = document.querySelectorAll('.transaksi-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = source.checked;
                    });
                    toggleDeleteButton();
                }

                function toggleDeleteButton() {
                    const checkboxes = document.querySelectorAll('.transaksi-checkbox:checked');
                    const deleteButton = document.querySelector('button[name="hapus_transaksi"]');
                    deleteButton.disabled = checkboxes.length === 0;
                }

                document.addEventListener('DOMContentLoaded', () => {
                    const checkboxes = document.querySelectorAll('.transaksi-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', toggleDeleteButton);
                    });
                    toggleDeleteButton();
                });

                function confirmDelete(event) {
                    event.preventDefault();
                    const confirmed = confirm("Apakah Anda yakin ingin menghapus transaksi terpilih?");
                    if (confirmed) {
                        const formData = new FormData(document.querySelector('form'));
                        fetch('hapus_transaksi.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Transaksi berhasil dihapus.');
                                location.reload();
                            } else {
                                alert('Gagal menghapus transaksi.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan.');
                        });
                    }
                }
                </script>
                
                <button type="submit" name="hapus_transaksi" class="btn btn-danger mt-4" disabled onclick="confirmDelete(event)">
                    <i class="fas fa-trash me-2"></i>Hapus Transaksi Terpilih
                </button>
            </form>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="row g-4">
        <?php
        $statuses = [
            'Proses' => ['color' => 'warning', 'icon' => 'clock'],
            'Dicuci' => ['color' => 'primary', 'icon' => 'tshirt'],
            'Disetrika' => ['color' => 'info', 'icon' => 'fire'],
            'Selesai' => ['color' => 'success', 'icon' => 'check-circle']
        ];
        
        foreach ($statuses as $status => $data) {
            $result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi WHERE status = '$status'");
            $count = mysqli_fetch_assoc($result)['total'];
        ?>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card card border-left-<?= $data['color'] ?> h-100 p-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-<?= $data['color'] ?> text-white rounded-circle p-3 me-3">
                            <i class="fas fa-<?= $data['icon'] ?> fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="text-muted mb-0">Status <?= $status ?></h5>
                            <h2 class="mb-0"><?= $count ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div><!-- /container-fluid -->

<?php
    include "../include/footer.php";
} else {
    header("Location: ../index.php");
    exit();
}
?>
