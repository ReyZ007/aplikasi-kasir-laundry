<?php 
    $preUrl = explode ("/", $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    $url    = (isset($_SERVER['HTTPS'])?"https":"http")."://".$preUrl[0]."/".$preUrl[1]."/";
    define("BASE_URL",$url);
    // echo BASE_URL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet"/>
    <title>Rumah Laundry</title>
</head>
<body>
