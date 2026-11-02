<?php 
session_start();
include "../include/koneksi.php";
include "../include/header.php";

if ($_SESSION['level'] == "admin") {
    include "../include/navbar_admin.php";
    $tgl_awal = date('Y-m-d');
    $tgl_akhir = date('Y-m-d');
    if (isset($_GET['tgl_awal']) && isset($_GET['tgl_akhir'])) {
        $tgl_awal = $_GET['tgl_awal'];
        $tgl_akhir = $_GET['tgl_akhir'];
    }
?>
<!-- Tambahkan link Font Awesome -->
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
    .form-inline {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    .form-inline .form-control {
        border-radius: 10px;
        padding: 10px 15px;
    }
    .form-inline .btn {
        border-radius: 10px;
        padding: 10px 20px;
    }
</style>

<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <!-- Tambahkan icon pada judul -->
        <h3 class="text-center text-primary mt-4 mb-4">
            <i class="fas fa-clipboard-check me-2"></i>Laporan Pekerjaan
        </h3>
        
        <form action="laporan_pekerjaan.php" method="GET" class="form-inline">
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-calendar-alt"></i></span>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>" required>
            </div>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-calendar-alt"></i></span>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>" required>
            </div>
            <button type="submit" class="btn btn-primary d-flex align-items-center">
                <i class="fa-solid fa-search me-2"></i> Tampilkan
            </button>
        </form>

        <?php
            $sql = "SELECT transaksi.*, customer.nama_customer, petugas.nama_petugas 
                    FROM transaksi 
                    INNER JOIN customer ON transaksi.id_customer = customer.id_customer
                    INNER JOIN petugas ON transaksi.id_petugas = petugas.id_petugas
                    WHERE transaksi.status = 'Selesai' AND tgl_transaksi >= '$tgl_awal' AND tgl_transaksi <= '$tgl_akhir'
                    ORDER BY transaksi.tgl_transaksi DESC";
            $sql_eksekusi = mysqli_query($koneksi, $sql);
        ?>

        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center">
                <thead class="table-primary">
                    <tr>
                        <th>No.</th>
                        <th>Tanggal Transaksi</th>
                        <th>Waktu Transaksi</th>
                        <th>Nama Pelanggan</th>
                        <th>Nama Petugas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        while ($data = mysqli_fetch_array($sql_eksekusi)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= date('Y-m-d', strtotime($data['tgl_transaksi'])); ?></td>
                        <td><?= $data['waktu_transaksi']; ?></td>
                        <td><?= $data['nama_customer']; ?></td>
                        <td><?= $data['nama_petugas']; ?></td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fa-solid fa-check"></i> <?= $data['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php
    include "../include/footer.php";
} else {
    header("Location: ../index.php");
    exit();
}
?>
