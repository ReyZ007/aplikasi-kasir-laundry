<?php
session_start();
include "../include/koneksi.php";
include "../include/header.php";

if (!isset($_SESSION['id_petugas']) || $_SESSION['level'] != "petugas") {
    header("Location: ../index.php");
    exit();
}

$id_petugas = $_SESSION['id_petugas'];
$query = "SELECT * FROM akun WHERE id_petugas = '$id_petugas'";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($result);

$update_success = false;
$error_message = "";

if (isset($_POST['update'])) {
    $new_username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_username) || empty($new_password) || empty($confirm_password)) {
        $error_message = "Username, Password, dan Konfirmasi Password tidak boleh kosong!";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        // Check if the new username already exists
        $check_query = "SELECT * FROM akun WHERE username = '$new_username' AND id_petugas != '$id_petugas'";
        $check_result = mysqli_query($koneksi, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Username sudah digunakan oleh akun lain!";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE akun SET username = '$new_username', password = '$hashed_password' WHERE id_petugas = '$id_petugas'";

            if (mysqli_query($koneksi, $update_query)) {
                $update_success = true;
                session_destroy(); // Logout paksa setelah perubahan data
            } else {
                $update_success = false;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbarui Profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding-top: 25px;
        }

        .container {
            max-width: 450px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            padding: 30px;
            text-align: center;
            position: relative;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user-icon {
            font-size: 60px;
            color: #667eea;
            margin-bottom: 20px;
            animation: bounce alternate 2s;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-30px); }
            60% { transform: translateY(-15px); }
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 8px rgba(102, 126, 234, 0.5);
        }

        .btn-update {
            width: 100%;
            padding: 12px;
            background-color: #667eea;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-update:hover {
            background-color: #564bb2;
            transform: translateY(-2px);
        }

        .error-message {
            color: #ff4444;
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: 500;
        }

        .modal {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.3s ease-in-out;
            max-width: 300px;
            position: relative;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #333;
        }

        .close:hover {
            color: #ff0000;
        }

        .success-icon {
            font-size: 40px;
            color: #28a745;
            margin-bottom: 10px;
            animation: spin 1s ease-in-out;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .modal h2 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .modal p {
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <?php include "../include/navbar.php"; ?>
    <div class="container mt-5">
        <i class="fas fa-user-circle user-icon"></i>
        <h2>Perbarui Profil</h2>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"> <?php echo $error_message; ?> </div>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="username">Username Baru</label>
                <input type="text" class="form-control" name="username" id="username" value="<?= $data['username'] ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit" name="update" class="btn-update">Perbarui Profil</button>
        </form>
    </div>

    <div id="successModal" class="modal">
        <div class="modal-content">
            <button class="close" onclick="closeModal()">&times;</button>
            <i class="fas fa-check-circle success-icon"></i>
            <h2>Profil Berhasil Diperbarui! 🎉</h2>
            <p>Silakan login kembali setelah perubahan ini.</p>
        </div>
    </div>

    <script>
        function validateForm() {
            var username = document.getElementById("username").value.trim();
            var password = document.getElementById("password").value.trim();
            var confirm_password = document.getElementById("confirm_password").value.trim();

            if (username === "" || password === "" || confirm_password === "") {
                alert("Username, Password, dan Konfirmasi Password tidak boleh kosong!");
                return false;
            }

            if (password !== confirm_password) {
                alert("Password dan Konfirmasi Password tidak cocok!");
                return false;
            }

            return true;
        }

        function closeModal() {
            document.getElementById("successModal").style.display = "none";
            window.location.href = "../beranda/index.php"; 
        }

        <?php if ($update_success): ?>
            document.getElementById("successModal").style.display = "flex";
        <?php endif; ?>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>