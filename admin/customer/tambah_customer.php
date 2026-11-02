<?php
session_start();
include "../include/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_customer = mysqli_real_escape_string($koneksi, $_POST['nama_customer']);
    $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);

    $sql = "INSERT INTO customer (nama_customer, no_telp) VALUES ('$nama_customer', '$no_telp')";
    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['message'] = "Customer berhasil ditambahkan.";
    } else {
        $_SESSION['error'] = "Gagal menambahkan customer: " . mysqli_error($koneksi);
    }

    header("Location: show_customer.php");
    exit();
}
?>
