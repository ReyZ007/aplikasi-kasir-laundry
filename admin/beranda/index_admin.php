<?php 
session_start();
include "../include/koneksi.php";
include "../include/header.php";

if(isset($_SESSION['level'])) {
    if($_SESSION['level'] == "admin") { 
        include "../include/navbar_admin.php"; 
    } else {
        header("Location: ../index.php");
        exit;
    }
    
    $hari_ini = date('Y-m-d');
    $sql_transaksi = "SELECT transaksi.*, customer.nama_customer, 
                      (SELECT SUM(total_harga) FROM detail_transaksi WHERE detail_transaksi.id_transaksi = transaksi.id_transaksi) AS total_harga
                      FROM transaksi 
                      INNER JOIN customer ON transaksi.id_customer = customer.id_customer
                      WHERE tgl_transaksi = '$hari_ini'
                      ORDER BY id_transaksi DESC
                      LIMIT 5";
    $result_transaksi = mysqli_query($koneksi, $sql_transaksi);
    
    // Data untuk statistik
    $total_petugas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM petugas"))['total'];
    $total_jenis = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM jenis_cucian"))['total'];
    $total_akun = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM akun"))['total'];
    $total_transaksi_hari = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi WHERE tgl_transaksi = '$hari_ini'"))['total'];

    // Data untuk grafik pendapatan
    $pendapatan_harian = [];
    for ($i = 6; $i >= 0; $i--) {
        $tanggal = date('Y-m-d', strtotime("-$i days"));
        $result = mysqli_query($koneksi, "SELECT SUM(d.total_harga) AS total 
                                          FROM detail_transaksi d
                                          INNER JOIN transaksi t ON d.id_transaksi = t.id_transaksi
                                          WHERE DATE(t.tgl_transaksi) = '$tanggal'");
        $pendapatan_harian[] = [
            'tanggal' => date('d M', strtotime($tanggal)),
            'total' => mysqli_fetch_assoc($result)['total'] ?? 0
        ];
    }

    // Data untuk grafik distribusi layanan
    $status_counts = [];
    $statuses = ['Proses', 'Dicuci', 'Disetrika', 'Selesai'];
    foreach ($statuses as $status) {
        $result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi WHERE status = '$status'");
        $status_counts[] = mysqli_fetch_assoc($result)['total'];
    }

    // Calculate today's revenue
    $result = mysqli_query($koneksi, "SELECT SUM(d.total_harga) AS total 
                                      FROM detail_transaksi d
                                      INNER JOIN transaksi t ON d.id_transaksi = t.id_transaksi
                                      WHERE DATE(t.tgl_transaksi) = CURDATE()");
    $pendapatan_hari_ini = mysqli_fetch_assoc($result)['total'] ?? 0;

    // Calculate this month's revenue
    $result = mysqli_query($koneksi, "SELECT SUM(d.total_harga) AS total 
                                      FROM detail_transaksi d
                                      INNER JOIN transaksi t ON d.id_transaksi = t.id_transaksi
                                      WHERE MONTH(t.tgl_transaksi) = MONTH(CURDATE()) 
                                      AND YEAR(t.tgl_transaksi) = YEAR(CURDATE())");
    $pendapatan_bulan_ini = mysqli_fetch_assoc($result)['total'] ?? 0;

    // Calculate this year's revenue
    $result = mysqli_query($koneksi, "SELECT SUM(d.total_harga) AS total 
                                      FROM detail_transaksi d
                                      INNER JOIN transaksi t ON d.id_transaksi = t.id_transaksi
                                      WHERE YEAR(t.tgl_transaksi) = YEAR(CURDATE())");
    $pendapatan_tahun_ini = mysqli_fetch_assoc($result)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
        padding-top: 10px; /* Jika menggunakan navbar fixed-top */
        }
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --success-color: #06d6a0;
            --warning-color: #ffd166;
        }
        
        .dashboard-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .recent-orders {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .welcome-section {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%);
            border-radius: 15px;
            overflow: hidden;
            color: white;
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid p-4 mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</h2>
            <div class="date-box p-2 rounded">
                <span class="text-muted"><?= date('l, d F Y') ?></span>
            </div>
        </div>

        <!-- Welcome Section -->

        <!-- Statistik Cards -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-card bg-white p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="card-icon bg-primary bg-opacity-10">
                            <i class="fas fa-user-check fa-2x text-primary"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-0">Total Petugas</h6>
                            <h3 class="mb-0 fw-bold"><?= $total_petugas ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-card bg-white p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="card-icon bg-success bg-opacity-10">
                            <i class="fas fa-list-ul fa-2x text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-0">Jenis Layanan</h6>
                            <h3 class="mb-0 fw-bold"><?= $total_jenis ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-card bg-white p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="card-icon bg-warning bg-opacity-10">
                            <i class="fas fa-users fa-2x text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-0">Total Akun</h6>
                            <h3 class="mb-0 fw-bold"><?= $total_akun ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-card bg-white p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="card-icon bg-danger bg-opacity-10">
                            <i class="fas fa-exchange-alt fa-2x text-danger"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-0">Transaksi Hari Ini</h6>
                            <h3 class="mb-0 fw-bold"><?= $total_transaksi_hari ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-card bg-white p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="card-icon bg-info bg-opacity-10">
                            <i class="fas fa-dollar-sign fa-2x text-info"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-0">Pendapatan Hari Ini</h6>
                            <h3 class="mb-0 fw-bold"> <?= number_format($pendapatan_hari_ini, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-card bg-white p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="card-icon bg-primary bg-opacity-10">
                            <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-0">Pendapatan Bulan Ini</h6>
                            <h3 class="mb-0 fw-bold"><?= number_format($pendapatan_bulan_ini, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-card bg-white p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="card-icon bg-success bg-opacity-10">
                            <i class="fas fa-calendar fa-2x text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-0">Pendapatan Tahun Ini</h6>
                            <h3 class="mb-0 fw-bold"><?= number_format($pendapatan_tahun_ini, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik dan Recent Orders -->
        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="recent-orders p-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-clock me-2"></i>Transaksi Terbaru</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Nama Customer</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($transaksi = mysqli_fetch_assoc($result_transaksi)): ?>
                                <tr>
                                    <td>#<?= $transaksi['id_transaksi'] ?></td>
                                    <td><?= $transaksi['nama_customer'] ?></td>
                                    <td><?= date('d M Y', strtotime($transaksi['tgl_transaksi'])) ?></td>
                                    <td>Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="status-badge bg-success text-white">
                                            <?= ucfirst($transaksi['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="bg-white p-4 rounded-3 shadow-sm mb-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-chart-line me-2"></i>Pendapatan Harian</h5>
                    <canvas id="pendapatanChart"></canvas>
                </div>
                <div class="bg-white p-4 rounded-3 shadow-sm">
                    <h5 class="fw-bold mb-4"><i class="fas fa-chart-pie me-2"></i>Distribusi Status</h5>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <?php include "../include/footer.php"; ?>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data untuk grafik pendapatan harian
        const pendapatanData = <?= json_encode($pendapatan_harian) ?>;
        const labels = pendapatanData.map(data => data.tanggal);
        const data = pendapatanData.map(data => data.total);

        // Grafik Pendapatan Harian
        const ctxPendapatan = document.getElementById('pendapatanChart').getContext('2d');
        new Chart(ctxPendapatan, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan Harian',
                    data: data,
                    backgroundColor: 'rgba(67, 97, 238, 0.2)',
                    borderColor: 'rgba(67, 97, 238, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Grafik Distribusi Status
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Proses', 'Dicuci', 'Disetrika', 'Selesai'],
                datasets: [{
                    data: <?= json_encode($status_counts) ?>,
                    backgroundColor: [
                        '#ffd166', // Proses
                        '#4361ee', // Dicuci
                        '#4895ef', // Disetrika
                        '#06d6a0'  // Selesai
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php
} else {
    header("Location: ../index.php");
    exit;
}
?>