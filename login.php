<?php
session_start();
include "koneksi.php";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil data user
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);

    // Cek apakah user ada
    if ($data) {
        // Verifikasi password dengan password_verify
        if (password_verify($password, $data['password'])) {
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];

            header("Location: index.php");
            exit;
        } else {
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Inventaris Sparepart</title>
    <style>
        /* Background Gradient */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2563eb, #ffffffff);
        }

        /* Container dengan efek glass */
        .login-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 40px 30px;
            width: 360px;
            text-align: center;
            color: #fff;
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
            animation: fadeIn 1s ease-in-out;
        }

        /* Animasi muncul */
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        /* Judul */
        .login-container h2 {
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: 600;
        }

        /* Input */
        .input-group {
            margin-bottom: 15px;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            background: rgba(255,255,255,0.8);
            transition: 0.3s;
        }

        .input-group input:focus {
            background: #fff;
            box-shadow: 0 0 0 2px #60a5fa;
        }

        /* Tombol */
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #3b82f6, #ffffffff);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        /* Footer */
        .footer {
            margin-top: 15px;
            font-size: 12px;
            color: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔧 Login Inventaris Sparepart</h2>
        <form method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
    
    </div>
</body>
</html>
