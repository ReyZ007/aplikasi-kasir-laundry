<?php
session_start();
include "../include/koneksi.php";

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transaksi_ids'])) {
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
        $response['success'] = true;
    }
}

echo json_encode($response);
?>
