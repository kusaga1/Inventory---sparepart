<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses ditolak! Hanya admin yang dapat mengakses halaman ini.');window.location='index.php';</script>";
    exit;
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php"; // koneksi ke database

// Ambil data user dari tabel users
$query = mysqli_query($koneksi, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User</title>
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

    /* Tombol */
    .btn {
        padding: 8px 14px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: 0.3s;
    }
    .btn:hover {
        opacity: 0.85;
        transform: translateY(-1px);
    }
    .btn-add {
        background: #2ecc71;
        color: white;
        margin-bottom: 15px;
        display: inline-block;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .btn-edit {
        background: #3498db;
        color: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .btn-delete {
        background: #e74c3c;
        color: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Tabel */
    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
        text-align: left;
        font-size: 15px;
    }
    th {
        background: #34495e;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    tr:hover {
        background: #f1f1f1;
    }

    /* Efek responsif */
    @media (max-width: 768px) {
        .sidebar { width: 100%; height: auto; position: relative; }
        .content { margin-left: 0; }
        table { font-size: 14px; }
    }
    /* Sidebar closed */
        .sidebar.closed {
            width: 60px;
        }
        .sidebar.closed h2,
        .sidebar.closed a span {
            display: none;
        }
        .sidebar.closed a {
            justify-content: center;
        }
        .content.expanded {
            margin-left: 60px;
        }
        .submenu .submenu-content {
            display: none; /* Default tertutup */
            flex-direction: column;
            background: #34495e;
        }
        .submenu.open .submenu-content {
            display: flex; /* Baru terbuka ketika diklik */
        }
/* Tombol garis 3 */
        .toggle-btn {
            font-size: 22px;
            cursor: pointer;
            background: none;
            border: none;
            color: white;
            margin: 10px;
        }
</style>

</head>
<body>

<!-- Sidebar  -->
<div id="sidebar" class="sidebar">
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <h2><i class="fas fa-warehouse"></i> <span>Inventaris</span></h2>

    <a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>

    <!-- Tampilkan menu Kelola User hanya untuk admin -->
    <?php if ($_SESSION['role'] == 'admin') { ?>
        <a href="kelola_user.php"><i class="fas fa-user-gear"></i> <span>Kelola User</span></a>
    <?php } ?>

    <a href="sparepart.php"><i class="fas fa-cogs"></i> <span>Manajemen Sparepart</span></a>
    <a href="masuk.php"><i class="fas fa-arrow-down"></i> <span>Stok Masuk</span></a>
    <a href="keluar.php"><i class="fas fa-arrow-up"></i> <span>Stok Keluar</span></a>
    <a href="laporan_sparepart.php"><i class="fas fa-file-alt"></i> <span>Laporan Sparepart</span></a>
    
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
</div>


<!-- Konten utama -->
<div class="content">
    <header>
        <h1>Kelola User</h1>
        <span>👋 Halo, <?php echo $_SESSION['username']; ?>!</span>
    </header>

    <a href="tambah_user.php" class="btn btn-add" style="background:#2ecc71;color:white;margin-bottom:10px;display:inline-block;">+ Tambah User</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($query)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td>
                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                        <a href="hapus_user.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Hapus user ini?')">Hapus</a>
                    </td>

            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('closed');
        document.getElementById('content').classList.toggle('expanded');
    }

    // Toggle submenu laporan
document.querySelectorAll('.submenu-toggle').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault(); 
        this.parentElement.classList.toggle('open'); // tambah/hapus class open
    });
});

</script>

</body>
</html>
