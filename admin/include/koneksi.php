<?php 
    $server ="localhost";
    $user   ="root";
    $password ="";
    $database   ="laundry";

    $koneksi    = mysqli_connect($server,$user,$password)or die ("Gagal konek<br>");

    $db      = mysqli_select_db($koneksi,$database) or die("gagal pilih database");
?> 