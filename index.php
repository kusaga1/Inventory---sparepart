<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php"; // koneksi ke database

// Total sparepart (jumlah baris/tabel)
$query_sparepart = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM sparepart");
$total_sparepart = mysqli_fetch_assoc($query_sparepart)['total'];

// Stok masuk hari ini
$today = date('Y-m-d');
$query_masuk = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM transaksi_masuk WHERE DATE(tanggal) = '$today'");
$total_masuk = mysqli_fetch_assoc($query_masuk)['total'] ?? 0;

// Stok keluar hari ini
$query_keluar = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM transaksi_keluar WHERE DATE(tanggal) = '$today'");
$total_keluar = mysqli_fetch_assoc($query_keluar)['total'] ?? 0;

// Jumlah pengguna aktif
$query_user = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users");
$total_users = mysqli_fetch_assoc($query_user)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Inventaris</title>
    <!-- Font Awesome -->
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
        /* Tombol garis 3 */
        .toggle-btn {
            font-size: 22px;
            cursor: pointer;
            background: none;
            border: none;
            color: white;
            margin: 10px;
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
        /* Card Dashboard */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card i {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .card h3 {
            margin: 5px 0;
            font-size: 20px;
            color: #2c3e50;
        }
        .card p {
            color: #7f8c8d;
            font-size: 14px;
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

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <h2><i class="fas fa-warehouse"></i> <span>Inventaris</span></h2>
        <a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
        <a href="kelola_user.php"><i class="fas fa-user-cog"></i> <span>Kelola User</span></a>
        <a href="sparepart.php"><i class="fas fa-cogs"></i> <span>Manajemen Sparepart</span></a>
        <a href="masuk.php"><i class="fas fa-arrow-down"></i> <span>Stok Masuk</span></a>
        <a href="keluar.php"><i class="fas fa-arrow-up"></i> <span>Stok Keluar</span></a>
        <a href="laporan_sparepart.php"><i class="fas fa-file-alt"></i> <span>Laporan Sparepart</span></a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>

    <!-- Konten -->
    <div id="content" class="content">
        <header>
            <h1>Dashboard Inventaris</h1>
            <span>👋 Halo, <?php echo $_SESSION['username']; ?>!</span>
        </header>

        <!-- Cards Ringkasan -->
        <div class="cards">
            <div class="card">
                <i class="fas fa-cogs" style="color:#3498db;"></i>
                <h3><?php echo $total_sparepart; ?></h3>
                <p>Total Sparepart</p>
            </div>
            <div class="card">
                <i class="fas fa-arrow-down" style="color:#27ae60;"></i>
                <h3><?php echo $total_masuk; ?></h3>
                <p>Stok Masuk Hari Ini</p>
            </div>
            <div class="card">
                <i class="fas fa-arrow-up" style="color:#e67e22;"></i>
                <h3><?php echo $total_keluar; ?></h3>
                <p>Stok Keluar Hari Ini</p>
            </div>
            <div class="card">
                <i class="fas fa-users" style="color:#8e44ad;"></i>
                <h3><?php echo $total_users; ?></h3>
                <p>Pengguna Aktif</p>
            </div>
        </div>
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