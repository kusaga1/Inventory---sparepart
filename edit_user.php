<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update = "UPDATE users SET nama='$nama', role='$role', password='$password' WHERE id='$id'";
    } else {
        $update = "UPDATE users SET nama='$nama', role='$role' WHERE id='$id'";
    }
    
    mysqli_query($koneksi, $update);
    header("Location: kelola_user.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f6fa;
    }

    /* Sidebar */
    .sidebar {
        height: 100vh;
        width: 220px;
        position: fixed;
        top: 0;
        left: 0;
        background: #2c3e50;
        color: white;
        transition: 0.3s;
        overflow-x: hidden;
        box-shadow: 2px 0 8px rgba(0,0,0,0.2);
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
        font-size: 16px;
        color: white;
        transition: 0.3s;
    }
    .sidebar a i {
        margin-right: 10px;
        font-size: 18px;
    }
    .sidebar a:hover {
        background: #34495e;
        padding-left: 25px;
    }
    .sidebar .logout {
        background: #e74c3c;
        margin: 15px;
        border-radius: 5px;
        justify-content: center;
    }

    /* Konten */
    .content {
        margin-left: 220px;
        padding: 20px;
        transition: 0.3s;
    }

    /* Header */
    header {
        background: #fff;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    header h1 {
        margin: 0;
        font-size: 22px;
        color: #2c3e50;
    }
    header span {
        font-size: 16px;
        color: #7f8c8d;
    }

    /* Form */
    form {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        max-width: 500px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    label {
        display: block;
        margin-top: 15px;
        font-weight: 600;
        color: #2c3e50;
    }
    input, select {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 15px;
        background: #fdfdfd;
        transition: 0.2s;
    }
    input:focus, select:focus {
        border-color: #3498db;
        outline: none;
        box-shadow: 0 0 4px rgba(52, 152, 219, 0.3);
    }

    /* Tombol utama */
    button {
        margin-top: 20px;
        padding: 10px 20px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 15px;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    button:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }

    /* Tombol kembali */
    a.back-btn {
        display: inline-block;
        margin-bottom: 15px;
        background: #7f8c8d;
        color: white;
        padding: 8px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        transition: 0.3s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    a.back-btn:hover {
        background: #95a5a6;
        transform: translateY(-2px);
    }

    /* Responsif */
    @media (max-width: 768px) {
        .sidebar { width: 100%; height: auto; position: relative; }
        .content { margin-left: 0; }
        form { width: 100%; }
    }
</style>

</head>
<body>

<!-- Sidebar (copy sama dari kelola_user.php) -->
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

<!-- Konten utama -->
<div class="content">
    <header>
        <h1>Edit User</h1>
        <span>👋 Halo, <?php echo $_SESSION['username']; ?>!</span>
    </header>

    <a href="kelola_user.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>

    <form method="POST">
        <label>Username</label>
        <input type="text" value="<?php echo htmlspecialchars($data['username']); ?>" disabled>

        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required>

        <label>Password (opsional)</label>
        <input type="password" name="password">

        <label>Role</label>
        <select name="role">
            <option value="admin" <?php if($data['role']=='admin') echo 'selected'; ?>>Admin</option>
            <option value="user" <?php if($data['role']=='user') echo 'selected'; ?>>User</option>
        </select>

        <button type="submit" name="update">Update</button>
    </form>
</div>

</body>
</html>
