<?php
session_start();
include "../include/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_customer = $_POST['nama_customer'];
    $no_telp = $_POST['no_telp'];

    $sql = "INSERT INTO customer (nama_customer, no_telp) VALUES (?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $nama_customer, $no_telp);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Customer berhasil ditambahkan.";
    } else {
        $_SESSION['error'] = "Gagal menambahkan customer: " . mysqli_error($koneksi);
    }

    header("Location: show_customer.php");
    exit();
}
?>
