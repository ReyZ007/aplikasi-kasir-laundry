<?php
    session_start();
    date_default_timezone_set('Asia/Jakarta');
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
    .text-center {
        text-align: center !important;
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
            <i class="fa-solid fa-file-invoice-dollar me-2"></i>Laporan Pemasukan Keuangan
        </h3>
        
        <form action="laporan_pemasukan_keuangan.php" method="GET" class="form-inline">
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
            $sql = "SELECT * FROM detail_transaksi dt
                    INNER JOIN jenis_cucian jc ON dt.id_jenis_cucian = jc.id_jenis_cucian 
                    INNER JOIN transaksi tr ON dt.id_transaksi = tr.id_transaksi
                    INNER JOIN customer cs ON tr.id_customer = cs.id_customer
                    INNER JOIN petugas p ON tr.id_petugas = p.id_petugas
                    
                    WHERE tr.status = 'Selesai' AND tgl_transaksi >= '$tgl_awal' AND tgl_transaksi <= '$tgl_akhir'
                    ORDER BY tr.tgl_transaksi DESC";
            $sql_eksekusi = mysqli_query($koneksi, $sql);
        ?>

        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center">
                <thead class="table-primary">
                    <tr>
                        <th>No.</th>
                        <th>Tanggal Transaksi</th>
                        <th>Nama Customer</th>
                        <th>Nama Barang</th>
                        <th>Berat (Kg)</th>
                        <th>Nama Petugas</th>
                        <th>Status</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        $total_keseluruhan = 0;
                        while ($data = mysqli_fetch_array($sql_eksekusi)) {
                            $total_keseluruhan += $data['total_harga'];
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= date('Y-m-d', strtotime($data['tgl_transaksi'])); ?></td>
                        <td><?= $data['nama_customer']; ?></td>
                        <td><?= $data['nama_jenis_cucian']; ?></td>
                        <td><?= $data['berat']; ?></td>
                        <td><?= $data['nama_petugas']; ?></td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fa-solid fa-check"></i> <?= $data['status']; ?>
                            </span>
                        </td>
                        <td class="text-center">Rp <?= number_format($data['total_harga'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php } ?>
                    <tr class="table-dark">
                        <td colspan="7" class="text-end"><strong>Total Keseluruhan</strong></td>
                        <td class="text-center"><strong>Rp <?= number_format($total_keseluruhan, 0, ',', '.'); ?></strong></td>
                    </tr>
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
