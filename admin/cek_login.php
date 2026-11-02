<?php
    session_start();
    include "include/koneksi.php";

    // Ambil data dari form login
    $username   = $_POST['username'];
    $password   = $_POST['password'];
    $pwhash     = md5($password);

    // Query untuk mencocokkan username dan password
    $sql        = "SELECT * FROM akun a INNER JOIN petugas p ON a.id_petugas=p.id_petugas WHERE username='$username' AND password='$pwhash'";
    $sql_eksekusi = mysqli_query($koneksi, $sql);
    $hitung     = mysqli_num_rows($sql_eksekusi);
    $data       = mysqli_fetch_array($sql_eksekusi);

    // Jika ada satu baris hasil query (login berhasil)
    if ($hitung == 1) {
        // Set session berdasarkan data pengguna
        $_SESSION['level'] = $data['level'];
        $_SESSION['id_petugas'] = $data['id_petugas'];
        $_SESSION['nama_petugas'] = $data['nama_petugas'];

        // Redirect berdasarkan level pengguna
        if ($data['level'] == 'admin') {
            header("location: beranda/index_admin.php");
        } else if ($data['level'] == 'petugas') {
            header("location: beranda/index.php");
        } else {
            echo "Level pengguna tidak valid.";
        }
    } else {
        // Jika login gagal
        echo "Nama pengguna atau kata sandi salah.";
    }
?>