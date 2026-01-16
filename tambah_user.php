<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

if (isset($_POST['simpan'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    $query = "INSERT INTO users (username, nama, password, role) VALUES ('$username', '$nama', '$password', '$role')";
    mysqli_query($koneksi, $query);

    header("Location: kelola_user.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f6fa;
    }

    .sidebar {
        height: 100vh;
        width: 220px;
        position: fixed;
        top: 0;
        left: 0;
        background: #2c3e50;
        color: white;
    }

    .sidebar h2 {
        margin: 0;
        padding: 15px;
        background: #1a252f;
        text-align: center;
        font-size: 20px;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        text-decoration: none;
        color: white;
    }

    .sidebar a:hover {
        background: #34495e;
    }

    .sidebar a i {
        margin-right: 10px;
    }

    .logout {
        background: #e74c3c;
        margin: 15px;
        border-radius: 5px;
        justify-content: center;
    }

    .content {
        margin-left: 220px;
        padding: 20px;
    }

    header {
        background: #fff;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    h1, h2 {
        margin: 0;
        font-size: 22px;
        color: #2c3e50;
    }

    form {
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        max-width: 500px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    label {
        display: block;
        margin-top: 12px;
        font-weight: 600;
    }

    input, select {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }

    button {
        margin-top: 20px;
        padding: 10px 20px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 15px;
    }

    button:hover {
        background: #2980b9;
    }

    a.back-btn {
        display: inline-block;
        margin-bottom: 15px;
        background: #7f8c8d;
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        text-decoration: none;
    }

    a.back-btn:hover {
        background: #95a5a6;
    }
</style>

</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2><i class="fas fa-warehouse"></i> <span>Inventaris</span></h2>
    <a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
    <a href="kelola_user.php"><i class="fas fa-user-gear"></i> <span>Kelola User</span></a>
    <a href="sparepart.php"><i class="fas fa-cogs"></i> <span>Manajemen Sparepart</span></a>
    <a href="masuk.php"><i class="fas fa-arrow-down"></i> <span>Stok Masuk</span></a>
    <a href="keluar.php"><i class="fas fa-arrow-up"></i> <span>Stok Keluar</span></a>
    <a href="laporan_sparepart.php"><i class="fas fa-file-alt"></i> <span>Laporan Sparepart</span></a>
    
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<!-- Konten Utama -->
<div class="content">
    <header>
        <h1>Tambah User Baru</h1>
        <span>👋 Halo, <?php echo $_SESSION['username']; ?>!</span>
    </header>

    <a href="kelola_user.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Nama Lengkap</label>
        <input type="text" name="nama" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Role</label>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>

        <button type="submit" name="simpan">Simpan</button>
    </form>
</div>

</body>
</html>
