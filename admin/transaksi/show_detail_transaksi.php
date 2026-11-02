<?php
ob_start();
session_start();
include "../include/koneksi.php";
include "../include/header.php";

// Pastikan id_transaksi ada
if (!isset($_GET['id_transaksi'])) {
    header("Location: show_transaksi.php");
    exit();
}

$id_transaksi = $_GET['id_transaksi'];

// Ambil data transaksi + customer + petugas
$query = "SELECT t.*, c.nama_customer, p.nama_petugas
          FROM transaksi t
          INNER JOIN customer c ON t.id_customer = c.id_customer
          INNER JOIN petugas p ON t.id_petugas = p.id_petugas
          WHERE t.id_transaksi = '$id_transaksi'";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

// Ambil data jenis_cucian untuk form
$jenis_cucian_query = "SELECT * FROM jenis_cucian";
$jenis_cucian_result = mysqli_query($koneksi, $jenis_cucian_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Jika klik "Simpan Transaksi"
    if (isset($_POST['simpan_transaksi'])) {
        $status_transaksi = $_POST['status_transaksi'];

        // Update status transaksi => "Proses" dan status pembayaran
        $update_status_query = "UPDATE transaksi SET status = 'Proses', status_transaksi = '$status_transaksi' WHERE id_transaksi = '$id_transaksi'";
        mysqli_query($koneksi, $update_status_query);

        // Kembali ke show_transaksi
        header("Location: show_transaksi.php");
        exit();
    }
    // Jika klik "hapus_detail"
    if (isset($_POST['hapus_detail'])) {
        $id_detail_transaksi = $_POST['id_detail_transaksi'];
        $delete_query = "DELETE FROM detail_transaksi WHERE id_detail_transaksi = '$id_detail_transaksi'";
        mysqli_query($koneksi, $delete_query);
        header("Location: show_detail_transaksi.php?id_transaksi=$id_transaksi");
        exit();
    }
    // Jika menambah detail cucian
    else {
        $id_jenis_cucian = $_POST['id_jenis_cucian'];
        $berat = $_POST['berat'];

        // Ambil harga dari jenis_cucian
        $harga_query = "SELECT harga FROM jenis_cucian WHERE id_jenis_cucian = '$id_jenis_cucian'";
        $harga_result = mysqli_query($koneksi, $harga_query);

        if ($harga_result) {
            $harga_data = mysqli_fetch_assoc($harga_result);
            $harga = $harga_data['harga'];
            $total_harga = $berat * $harga;

            // Cek apakah sudah ada baris detail utk jenis_cucian ini
            $check_query = "SELECT * FROM detail_transaksi
                            WHERE id_transaksi = '$id_transaksi' 
                              AND id_jenis_cucian = '$id_jenis_cucian'";
            $check_result = mysqli_query($koneksi, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                // Update entry lama
                $existing_data = mysqli_fetch_assoc($check_result);
                $new_berat = $existing_data['berat'] + $berat;
                $new_total_harga = $new_berat * $harga;

                $update_query = "UPDATE detail_transaksi
                                 SET berat = '$new_berat', total_harga = '$new_total_harga'
                                 WHERE id_transaksi = '$id_transaksi'
                                   AND id_jenis_cucian = '$id_jenis_cucian'";
                mysqli_query($koneksi, $update_query);
            } else {
                // Tambah entry baru
                $insert_query = "INSERT INTO detail_transaksi 
                                 (id_transaksi, id_jenis_cucian, berat, total_harga)
                                 VALUES ('$id_transaksi', '$id_jenis_cucian', '$berat', '$total_harga')";
                mysqli_query($koneksi, $insert_query);
            }
        }
        // Setelah menambah detail, redirect ke halaman ini lagi
        header("Location: show_detail_transaksi.php?id_transaksi=$id_transaksi");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Transaksi</title>
  <!-- Pastikan Bootstrap & Font Awesome dimuat via header.php -->
  <style>
    body {
      background: #f0f2f5;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
    }
    .container {
      
      margin-bottom: 3rem;
      padding-top: 15px; /* menyesuaikan agar tidak tertutup navbar */
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      border: none;
    }
    .card h3 {
      font-weight: 600;
    }
    /* Tombol custom */
    .btn-custom {
      background: linear-gradient(135deg, #007bff, #4e73df);
      color: #fff;
      border: none;
      transition: all 0.3s ease;
    }
    .btn-custom:hover {
      background: linear-gradient(135deg, #0056b3, #2e59d9);
      color: #fff;
    }
    /* Tabel */
    .table thead {
      background-color: #007bff;
      color: #fff;
    }
    .table tbody tr:hover {
      background-color: #f8f9fa;
    }
    .table tfoot td {
      font-weight: 600;
      background-color: #f8f9fa;
    }
    /* Tombol simpan & kembali */
    .btn-save {
      background-color: #28a745;
      color: #fff;
      transition: all 0.3s ease;
    }
    .btn-save:hover {
      background-color: #218838;
      color: #fff;
    }
    .btn-back {
      background-color: #6c757d;
      color: #fff;
      transition: all 0.3s ease;
    }
    .btn-back:hover {
      background-color: #5a6268;
      color: #fff;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="card p-4">
    <div class="card-body">
      <h3 class="mb-4 text-primary">
        <i class="fas fa-info-circle me-2"></i>Detail Transaksi
      </h3>
      <!-- Info Transaksi -->
      <div class="row">
        <div class="col-md-6">
          <table class="table table-bordered">
            <tr>
              <th class="bg-light" style="width: 40%;">ID Transaksi</th>
              <td><?= htmlspecialchars($data['id_transaksi']); ?></td>
            </tr>
            <tr>
              <th class="bg-light">Nama Customer</th>
              <td><?= htmlspecialchars($data['nama_customer']); ?></td>
            </tr>
            <tr>
              <th class="bg-light">Nama Petugas</th>
              <td><?= htmlspecialchars($data['nama_petugas']); ?></td>
            </tr>
          </table>
        </div>
        <!-- Form Tambah Jenis Cucian -->
        <div class="col-md-6">
          <div class="border rounded p-3">
            <h5 class="text-primary mb-3"><i class="fas fa-plus-circle me-1"></i> Tambah Jenis Cucian</h5>
            <form method="POST" action="">
              <div class="mb-3">
                <label for="id_jenis_cucian" class="form-label fw-bold">
                  <i class="fas fa-tshirt me-1"></i>Nama Jenis Cucian
                </label>
                <select class="form-select" id="id_jenis_cucian" name="id_jenis_cucian" required>
                  <?php 
                  // Reload data jenis_cucian
                  $jenis_cucian_result = mysqli_query($koneksi, "SELECT * FROM jenis_cucian");
                  while ($jenis_cucian = mysqli_fetch_assoc($jenis_cucian_result)): ?>
                    <option value="<?= $jenis_cucian['id_jenis_cucian']; ?>">
                      <?= htmlspecialchars($jenis_cucian['nama_jenis_cucian']); ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="berat" class="form-label fw-bold">
                  <i class="fas fa-weight-hanging me-1"></i>Berat (kg)
                </label>
                <input type="number" class="form-control" id="berat" name="berat" step="0.01" required>
              </div>
              <button type="submit" class="btn btn-custom w-100">
                <i class="fas fa-plus"></i> Tambah Detail
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Tabel Detail Transaksi -->
      <h5 class="mt-4 mb-3 text-primary">
        <i class="fas fa-list me-1"></i>Daftar Jenis Cucian
      </h5>
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
          <thead>
            <tr>
              <th>Nama Jenis Cucian</th>
              <th>Berat (kg)</th>
              <th>Harga Satuan</th>
              <th>Total Harga</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $total_semua_harga = 0;
            $detail_query = "SELECT dt.*, jc.nama_jenis_cucian, jc.harga 
                             FROM detail_transaksi dt
                             INNER JOIN jenis_cucian jc 
                             ON dt.id_jenis_cucian = jc.id_jenis_cucian 
                             WHERE dt.id_transaksi = '$id_transaksi'";
            $detail_result = mysqli_query($koneksi, $detail_query);
            $detail_count = mysqli_num_rows($detail_result);

            while ($detail = mysqli_fetch_assoc($detail_result)):
              $total_harga = $detail['berat'] * $detail['harga'];
              $total_semua_harga += $total_harga;
            ?>
              <tr>
                <td><?= htmlspecialchars($detail['nama_jenis_cucian']); ?></td>
                <td><?= htmlspecialchars($detail['berat']); ?></td>
                <td>Rp <?= number_format($detail['harga'], 0, ',', '.'); ?></td>
                <td>Rp <?= number_format($total_harga, 0, ',', '.'); ?></td>
                <td class="text-center">
                  <form method="POST" action="" onsubmit="return confirm('Yakin ingin menghapus jenis cucian ini?');">
                    <input type="hidden" name="id_detail_transaksi" value="<?= $detail['id_detail_transaksi']; ?>">
                    <button type="submit" name="hapus_detail" class="btn btn-danger btn-sm">
                      <i class="fas fa-trash-alt"></i> Hapus
                    </button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-end"><strong>Total Harga Keseluruhan</strong></td>
              <td colspan="2"><strong>Rp <?= number_format($total_semua_harga, 0, ',', '.'); ?></strong></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Pilihan Status Pembayaran -->
      <div class="mt-4">
        <h5 class="text-primary"><i class="fas fa-money-bill-wave me-1"></i>Status Pembayaran</h5>
        <form method="POST" action="">
          <div class="mb-3" style="max-width: 50%;">
            <select class="form-select" name="status_transaksi" required>
              <option value="" disabled selected>Pilih Status Pembayaran</option>
              <option value="0">Belum Melakukan Pembayaran</option>
              <option value="1">Sudah Melakukan Pembayaran</option>
            </select>
          </div>
          <!-- Tombol Simpan & Kembali -->
          <div class="row mt-4 g-2">
            <div class="col-12 col-md-6">
              <!-- Kembali => tidak mengubah status -->
              <a href="show_transaksi.php" class="btn btn-back w-100">
                <i class="fas fa-arrow-left me-1"></i>Kembali
              </a>
            </div>
            <div class="col-12 col-md-6">
              <button type="submit" name="simpan_transaksi" class="btn btn-save w-100"
                <?= $detail_count == 0 ? 'disabled' : ''; ?>
                onclick="return confirm('Apakah Anda yakin ingin menyimpan transaksi ini?')">
                <i class="fas fa-save me-1"></i>Simpan Transaksi
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include "../include/footer.php"; ?>
