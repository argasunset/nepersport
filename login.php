<?php
include 'koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $password = trim($_POST['password']);

    if (empty($nama) || empty($password)) {
        $error = "Nama dan Password tidak boleh kosong!";
    } else {
        if (!$conn) {
            die("Koneksi database gagal: " . mysqli_connect_error());
        }

        $stmt = $conn->prepare("SELECT * FROM user WHERE nama_user = ?");
        $stmt->bind_param("s", $nama);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (!empty($user['password_user']) && password_verify($password, $user['password_user'])) {
                // Set session
                session_regenerate_id(true);
                $_SESSION['id_user'] = $user['id_user']; // Tambahkan ID user ke session
                $_SESSION['nama_user'] = $user['nama_user'];
                $_SESSION['email'] = $user['email']; // Simpan email ke session
                $_SESSION['foto'] = !empty($user['foto']) ? $user['foto'] : 'assets/img/default.jpg'; // Cek apakah ada foto
                $_SESSION['role_user'] = $user['role_user'];

                if ($user['role_user'] === 'admin') {
                    header("Location: dashbord_admin.php");
                    exit();
                } else if ($user['role_user'] === 'user') {
                    header("Location: landing_page.php");
                    exit();
                }
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Nama tidak ditemukan!";
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('landingpage/yrt.jpg');
            background-size: cover;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 93%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: rgb(0, 0, 0);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: rgb(34, 34, 34);
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        .login-container a {
            text-decoration: none; 
            color: #007bff;          
            font-weight: bold;     
        }
        .login-container a:hover {
            text-decoration: underline;         
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST">
            <input type="text" name="nama" placeholder="Masukkan Username" required>
            <input type="password" name="password" placeholder="Masukkan Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error-message"><?= $error; ?></p>
        <?php endif; ?>
        <p style="text-align: center; margin-top: 15px;">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </p>
    </div>
</body>
</html>
