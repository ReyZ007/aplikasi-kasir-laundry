<?php
ob_start(); // Untuk menghindari "headers already sent"
session_start();
include "../include/koneksi.php";
include "../include/header.php";

date_default_timezone_set('Asia/Jakarta'); // Set zona waktu ke Jakarta

if (isset($_SESSION['level'])) {
    if ($_SESSION['level'] == "petugas" || $_SESSION['level'] == "admin") { 
        include "../include/navbar.php"; 
    } else { 
        header("Location: ../index.php");
        exit(); 
    }

    // Handle Tambah Transaksi (POST)
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_transaksi'])) {
        $id_customer      = (int) $_POST['id_customer'];
        $id_petugas       = (int) $_SESSION['id_petugas']; // Ambil id_petugas dari session
        $tgl_transaksi    = date('Y-m-d'); // Tanggal otomatis hari ini
        $waktu_transaksi  = date('H:i:s'); // Waktu otomatis saat ini
        $status           = 'Proses';

        // Tambahkan transaksi
        $query_tambah = "INSERT INTO transaksi (id_customer, id_petugas, tgl_transaksi, waktu_transaksi, status) 
                         VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query_tambah);
        mysqli_stmt_bind_param($stmt, "iisss", $id_customer, $id_petugas, $tgl_transaksi, $waktu_transaksi, $status);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Redirect agar tidak resubmit form
        header("Location: show_transaksi.php?added=1");
        exit();
    }

    // Handle Edit Transaksi (POST)
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_transaksi'])) {
        $id_transaksi = (int) $_POST['id_transaksi'];
        $status       = $_POST['status'];
        $status_transaksi = (int) $_POST['status_transaksi'];

        // Update status
        $query_edit = "UPDATE transaksi 
                       SET status = ?, status_transaksi = ?
                       WHERE id_transaksi = ?";
        $stmt = mysqli_prepare($koneksi, $query_edit);
        mysqli_stmt_bind_param($stmt, "sii", $status, $status_transaksi, $id_transaksi);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        header("Location: show_transaksi.php?edited=1");
        exit();
    }

    // Pagination setup
    $status_filter = isset($_GET['status']) ? $_GET['status'] : 'Proses';
    $limit = 10; // Number of entries per page
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $page = max(1, $page);
    $offset = ($page - 1) * $limit;

    $total_query = "SELECT COUNT(*) AS total FROM transaksi WHERE status = ?";
    $total_stmt = mysqli_prepare($koneksi, $total_query);
    mysqli_stmt_bind_param($total_stmt, "s", $status_filter);
    mysqli_stmt_execute($total_stmt);
    $total_result = mysqli_stmt_get_result($total_stmt);
    $total_data = mysqli_fetch_assoc($total_result);
    mysqli_stmt_close($total_stmt);
    $total_entries = $total_data['total'];
    $total_pages = ceil($total_entries / $limit);

    $sql = "SELECT t.*, c.nama_customer, c.no_telp, p.nama_petugas
            FROM transaksi t
            INNER JOIN customer c ON t.id_customer = c.id_customer
            INNER JOIN petugas p ON t.id_petugas = p.id_petugas
            WHERE t.status = ?
            ORDER BY t.id_transaksi DESC
            LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $status_filter, $limit, $offset);
    mysqli_stmt_execute($stmt);
    $sql_eksekusi = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Data Transaksi</title>
  <!-- Pastikan Bootstrap & Font Awesome telah dimuat di header.php -->
  
  <!-- Jika ingin memuat Font Awesome via CDN, bisa tambahkan:
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
        integrity="sha512-o8C1NxQjYK8LM+DEb77JYdE6v9FsRyrpO01HdJk3TvP2dy1X+hZzE7lIrAF3V9/pHQzovt1CebhxSiy1r7a3MQ==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  -->

  <style>
    /* Jika navbar fixed, tambahkan padding-top agar konten tidak tertutup */
    body {
      padding-top: 70px; /* Sesuaikan dengan tinggi navbar jika fixed */
      background: #f0f2f5;
      font-family: 'Poppins', sans-serif;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      max-width: 100%;
    }
    .card h3 {
      font-weight: 600;
    }
    .table-hover tbody tr:hover {
      background-color: #f8f9fa;
    }
    /* Warna thead */
    .table thead.table-primary th {
      background: #4e73df !important;
      color: #fff !important;
      border-color: #4e73df;
    }
    /* Tombol aksi */
    .btn-action {
      padding: 0.35rem 0.75rem;
      border-radius: 8px;
      transition: all 0.3s ease;
      margin-bottom: 5px;
    }
    /* Modal */
    .modal-content {
      border-radius: 15px;
      overflow: hidden;
    }
    .modal-header {
      color: white;
      border: none;
    }
    .modal-header .btn-close {
      background: transparent;
    }
    .form-control:focus {
      border-color: #4e73df;
      box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
    }
    /* Alert style */
    .alert {
      border-radius: 8px;
      margin-bottom: 15px;
    }
    /* Modal background colors */
    .modal-header.bg-primary {
      background-color: #4e73df;
    }
    .modal-header.bg-warning {
      background-color: #f6c23e;
    }
  </style>
</head>
<body>

<div class="container mt-5 pt-4">
  <div class="card shadow-lg p-4">
    <h3 class="text-center text-primary mt-4 mb-4">
      <b><i class="fa-solid fa-list me-2"></i>Data Transaksi</b>
    </h3>
    <!-- Alert Notification -->
    <?php if (isset($_GET['added'])): ?>
      <div class="alert alert-success" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>Transaksi berhasil ditambahkan!
      </div>
    <?php endif; ?>
    <?php if (isset($_GET['edited'])): ?>
      <div class="alert alert-success" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>Transaksi berhasil diubah!
      </div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
      <div class="alert alert-success" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>Transaksi berhasil dihapus!
      </div>
    <?php endif; ?>
    <div class="d-flex justify-content-start mb-3">
      <button class="btn btn-primary btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fa-solid fa-plus me-1"></i> Tambah Transaksi
      </button>
    </div>
    <div class="d-flex justify-content-center mb-3">
      <ul class="pagination">
        <li class="page-item <?= $status_filter == 'Proses' ? 'active' : ''; ?>">
          <a class="page-link" href="?status=Proses">Proses</a>
        </li>
        <li class="page-item <?= $status_filter == 'Dicuci' ? 'active' : ''; ?>">
          <a class="page-link" href="?status=Dicuci">Dicuci</a>
        </li>
        <li class="page-item <?= $status_filter == 'Disetrika' ? 'active' : ''; ?>">
          <a class="page-link" href="?status=Disetrika">Disetrika</a>
        </li>
        <li class="page-item <?= $status_filter == 'Selesai' ? 'active' : ''; ?>">
          <a class="page-link" href="?status=Selesai">Selesai</a>
        </li>
      </ul>
    </div>
    <div class="table-responsive">
      <table class="table table-hover table-bordered text-center align-middle">
        <thead class="table-primary">
          <tr>
            <th>No.</th>
            <th>Tanggal Transaksi</th>
            <th>Nama Pelanggan</th>
            <th>No. Telp</th>
            <th>Status Pembayaran</th>
            <th>Nama Petugas</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = $offset + 1;
          while ($data = mysqli_fetch_array($sql_eksekusi)):
              // Hitung total harga (jika diperlukan)
              $total_harga_query = "SELECT SUM(total_harga) AS total_harga_keseluruhan 
                                    FROM detail_transaksi 
                                    WHERE id_transaksi = ?";
              $total_harga_stmt = mysqli_prepare($koneksi, $total_harga_query);
              mysqli_stmt_bind_param($total_harga_stmt, "i", $data['id_transaksi']);
              mysqli_stmt_execute($total_harga_stmt);
              $total_harga_result = mysqli_stmt_get_result($total_harga_stmt);
              $total_harga_data = mysqli_fetch_assoc($total_harga_result);
              mysqli_stmt_close($total_harga_stmt);
              $total_harga_keseluruhan = $total_harga_data['total_harga_keseluruhan'] ?? 0;

              // Status
              $status = $data['status'];
              $status_pembayaran = $data['status_transaksi'] == 0 ? 'Belum Melakukan Pembayaran' : 'Sudah Melakukan Pembayaran';
          ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($data['tgl_transaksi']); ?></td>
            <td><?= htmlspecialchars($data['nama_customer']); ?></td>
            <td><?= htmlspecialchars($data['no_telp']); ?></td>
            <td>
              <span class="badge <?= $data['status_transaksi'] == 0 ? 'bg-danger' : 'bg-success'; ?>">
                <?= htmlspecialchars($status_pembayaran); ?>
              </span>
            </td>
            <td><?= htmlspecialchars($data['nama_petugas']); ?></td>
            <td>
              <span class="badge 
                <?php
                  if ($status == 'Proses') {
                    echo 'bg-warning';
                  } elseif ($status == 'Dicuci') {
                    echo 'bg-primary';
                  } elseif ($status == 'Disetrika') {
                    echo 'bg-info';
                  } elseif ($status == 'Selesai') {
                    echo 'bg-success';
                  }
                ?>">
                <?= htmlspecialchars($status); ?>
              </span>
            </td>
            <td>
              <?php
              // Cek apakah ada detail transaksi
              $detail_query = "SELECT * FROM detail_transaksi WHERE id_transaksi = ?";
              $detail_stmt = mysqli_prepare($koneksi, $detail_query);
              mysqli_stmt_bind_param($detail_stmt, "i", $data['id_transaksi']);
              mysqli_stmt_execute($detail_stmt);
              $detail_result = mysqli_stmt_get_result($detail_stmt);
              $detail_count = mysqli_num_rows($detail_result);
              mysqli_stmt_close($detail_stmt);
              ?>
              <?php if ($detail_count == 0): ?>
                <!-- Jika belum ada detail, tampilkan tombol lanjut & hapus -->
                <a href="show_detail_transaksi.php?id_transaksi=<?= $data['id_transaksi']; ?>" 
                   class="btn btn-warning btn-sm text-white btn-action"
                   title="Isi detail transaksi">
                  <i class="fa-solid fa-hand-point-right"></i>
                </a>
                <a href="show_transaksi.php?hapus_transaksi=<?= $data['id_transaksi']; ?>" 
                   class="btn btn-danger btn-sm text-white btn-action"
                   onclick="return confirm('Yakin ingin menghapus transaksi ini?');"
                   title="Hapus transaksi">
                  <i class="fa-solid fa-eraser"></i>
                </a>
              <?php else: ?>
                <!-- Jika sudah ada detail, tampilkan tombol edit & invoice -->
                <?php if ($data['status'] == 'Selesai' && $data['status_transaksi'] == 1): ?>
                  <button class="btn btn-info btn-sm text-white btn-action"
                          data-bs-toggle="modal" 
                          data-bs-target="#modalInvoice<?= $data['id_transaksi']; ?>"
                          title="Lihat invoice">
                    <i class="fa-solid fa-file-invoice"></i>
                  </button>
                <?php else: ?>
                  <button class="btn btn-primary btn-sm text-white btn-action" 
                          data-bs-toggle="modal" 
                          data-bs-target="#modalEdit<?= $data['id_transaksi']; ?>"
                          title="Ubah status">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button class="btn btn-info btn-sm text-white btn-action"
                          data-bs-toggle="modal" 
                          data-bs-target="#modalInvoice<?= $data['id_transaksi']; ?>"
                          title="Lihat invoice">
                    <i class="fa-solid fa-file-invoice"></i>
                  </button>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
          <!-- Modal Edit -->
          <div class="modal fade" id="modalEdit<?= $data['id_transaksi']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header bg-primary">
                  <h5 class="modal-title text-white">
                    <i class="fa-solid fa-pen-to-square me-2"></i>Edit Status Laundry
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="post">
                    <input type="hidden" name="id_transaksi" value="<?= $data['id_transaksi']; ?>">
                    <input type="hidden" name="edit_transaksi" value="1">
                    <?php if ($data['status'] != 'Selesai'): ?>
                    <div class="mb-3">
                      <label class="form-label fw-bold">Status</label>
                      <select class="form-control" name="status" required>
                        <option value="Proses"    <?= ($status == 'Proses') ? 'selected' : ''; ?>>Proses</option>
                        <option value="Dicuci"    <?= ($status == 'Dicuci') ? 'selected' : ''; ?>>Dicuci</option>
                        <option value="Disetrika" <?= ($status == 'Disetrika') ? 'selected' : ''; ?>>Disetrika</option>
                        <option value="Selesai"   <?= ($status == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                      </select>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="status" value="Selesai">
                    <?php endif; ?>
                    <?php if ($data['status_transaksi'] == 0): ?>
                    <div class="mb-3">
                      <label class="form-label fw-bold">Status Pembayaran</label>
                      <select class="form-control" name="status_transaksi" required>
                        <option value="0" <?= ($data['status_transaksi'] == 0) ? 'selected' : ''; ?>>
                          Belum Melakukan Pembayaran
                        </option>
                        <option value="1" <?= ($data['status_transaksi'] == 1) ? 'selected' : ''; ?>>
                          Sudah Melakukan Pembayaran
                        </option>
                      </select>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="status_transaksi" value="1">
                    <?php endif; ?>
                    <div class="d-grid">
                      <button type="submit" class="btn btn-success text-white">
                        <i class="fa-solid fa-save me-1"></i>Simpan
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
            <a class="page-link" href="?status=<?= $status_filter; ?>&page=<?= $i; ?>"><?= $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </div>

    <!-- Modal Invoice (dinamis) -->
    <?php
    // Ulang query untuk invoice
    $sql_invoice = "SELECT t.*, c.nama_customer, c.no_telp, p.nama_petugas
                    FROM transaksi t
                    INNER JOIN customer c ON t.id_customer = c.id_customer
                    INNER JOIN petugas p ON t.id_petugas = p.id_petugas
                    ORDER BY t.id_transaksi DESC";
    $result_invoice = mysqli_query($koneksi, $sql_invoice);
    if (!$result_invoice) {
        die("Query Error: " . mysqli_error($koneksi));
    }
    while ($data = mysqli_fetch_assoc($result_invoice)):
    ?>
    <div class="modal fade" id="modalInvoice<?= $data['id_transaksi']; ?>" tabindex="-1" aria-labelledby="invoiceLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h5 class="modal-title text-white" id="invoiceLabel">
              <i class="fa-solid fa-file-invoice me-2"></i>Invoice Transaksi
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="invoiceContent<?= $data['id_transaksi']; ?>">
            <div class="container">
              <div class="row mb-3">
                <div class="col-md-6">
                  <p><strong>ID Transaksi:</strong> <?= htmlspecialchars($data['id_transaksi']); ?></p>
                  <p><strong>Nama Pelanggan:</strong> <?= htmlspecialchars($data['nama_customer']); ?></p>
                  <p><strong>No. Telp:</strong> <?= htmlspecialchars($data['no_telp']); ?></p>
                  <p><strong>Status Pembayaran:</strong> 
                    <span class="badge <?= $data['status_transaksi'] == 0 ? 'bg-danger' : 'bg-success'; ?>">
                      <?= $data['status_transaksi'] == 0 ? 'Belum Melakukan Pembayaran' : 'Sudah Melakukan Pembayaran'; ?>
                    </span>
                  </p>
                </div>
                <div class="col-md-6 text-md-end">
                  <p><strong>Nama Petugas:</strong> <?= htmlspecialchars($data['nama_petugas']); ?></p>
                  <p><strong>Tanggal Transaksi:</strong> <?= htmlspecialchars($data['tgl_transaksi']); ?></p>
                  <p><strong>Waktu Transaksi:</strong> <?= htmlspecialchars($data['waktu_transaksi']); ?></p>
                  <p><strong>Status:</strong>
                    <?php
                      if ($data['status'] == 'Proses') {
                        echo '<span class="badge bg-warning">Proses</span>';
                      } elseif ($data['status'] == 'Dicuci') {
                        echo '<span class="badge bg-primary">Dicuci</span>';
                      } elseif ($data['status'] == 'Disetrika') {
                        echo '<span class="badge bg-info">Disetrika</span>';
                      } else {
                        echo '<span class="badge bg-success">Selesai</span>';
                      }
                    ?>
                  </p>
                </div>
              </div>

              <h5 class="text-center mt-3">Detail Jenis Cucian</h5>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Nama Jenis Cucian</th>
                    <th>Berat (kg)</th>
                    <th>Harga per kg</th>
                    <th>Total Harga</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $total_harga_keseluruhan = 0;
                  $detail_query = "SELECT d.*, j.nama_jenis_cucian, j.harga
                                   FROM detail_transaksi d
                                   INNER JOIN jenis_cucian j ON d.id_jenis_cucian = j.id_jenis_cucian
                                   WHERE d.id_transaksi = ?";
                  $stmt = mysqli_prepare($koneksi, $detail_query);
                  mysqli_stmt_bind_param($stmt, 'i', $data['id_transaksi']);
                  mysqli_stmt_execute($stmt);
                  $detail_result = mysqli_stmt_get_result($stmt);
                  while ($detail = mysqli_fetch_assoc($detail_result)):
                      $total_harga = $detail['berat'] * $detail['harga'];
                      $total_harga_keseluruhan += $total_harga;
                  ?>
                  <tr>
                    <td><?= htmlspecialchars($detail['nama_jenis_cucian']); ?></td>
                    <td><?= htmlspecialchars($detail['berat']); ?></td>
                    <td>Rp <?= number_format($detail['harga'], 0, ',', '.'); ?></td>
                    <td>Rp <?= number_format($total_harga, 0, ',', '.'); ?></td>
                  </tr>
                  <?php endwhile; ?>
                  <tr>
                    <td colspan="3" class="text-end"><strong>Total Keseluruhan:</strong></td>
                    <td><strong>Rp <?= number_format($total_harga_keseluruhan, 0, ',', '.'); ?></strong></td>
                  </tr>
                </tbody>
              </table>
              <h4 class="text-end mt-3">
                <strong>Total Harga Keseluruhan: Rp <?= number_format($total_harga_keseluruhan, 0, ',', '.'); ?></strong>
              </h4>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="printInvoice('invoiceContent<?= $data['id_transaksi']; ?>')">Cetak</button>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div><!-- End card -->
</div><!-- End container -->
  
<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Di sini kita tambahkan bg-primary dan text-white -->
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">
          <i class="fa-solid fa-plus me-2"></i>Tambah Transaksi
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="" method="post">
          <input type="hidden" name="tambah_transaksi" value="1">
          <div class="mb-3">
            <label class="form-label fw-bold">Nama Pelanggan</label>
            <select class="form-control" name="id_customer" required>
              <?php
              $query_customer = mysqli_query($koneksi, "SELECT * FROM customer");
              while ($row = mysqli_fetch_array($query_customer)) {
                  echo "<option value='" . $row['id_customer'] . "'>" . $row['nama_customer'] . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary text-white">
              <i class="fa-solid fa-plus-circle me-1"></i>Tambah
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function printInvoice(invoiceId) {
    var printContents = document.getElementById(invoiceId).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
    window.location.reload(); // Reload the page to restore the original state
}
</script>

<?php
include "../include/footer.php";
} else {
    header("Location: ../index.php");
    exit();
}
?>
