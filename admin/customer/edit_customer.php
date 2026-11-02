<?php
session_start();
include "../include/koneksi.php";

// Proses edit data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_customer   = $_POST['id_customer'];
    $nama_customer = $_POST['nama_customer'];
    $no_telp       = $_POST['no_telp'];

    $sql = "UPDATE customer 
            SET nama_customer = '$nama_customer', no_telp = '$no_telp'
            WHERE id_customer = '$id_customer'";
    mysqli_query($koneksi, $sql);

    // Redirect kembali ke show_customer.php dengan notifikasi
    header("Location: show_customer.php?edited=1");
    exit();
} else {
    // Jika user mengakses file ini tanpa POST, kembali ke show_customer
    header("Location: show_customer.php");
    exit();
}
?>
