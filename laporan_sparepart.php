<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Sparepart</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
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
.sidebar a i { margin-right: 10px; font-size: 18px; }
.sidebar a:hover, .sidebar a.active { background: #34495e; padding-left: 25px; }
.sidebar .logout {
    background: #e74c3c;
    margin: 15px;
    border-radius: 5px;
    justify-content: center;
}
.toggle-btn {
    font-size: 22px;
    cursor: pointer;
    background: none;
    border: none;
    color: white;
    margin: 10px;
}
.content {
    margin-left: 220px;
    padding: 20px;
    transition: 0.3s;
}
header.page-header {
    background: #fff;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
header.page-header h1 { margin: 0; font-size: 22px; color: #2c3e50; }
header.page-header span { font-size: 16px; color: #7f8c8d; }

.card {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}
th {
    background: #3498db;
    color: white;
}
tr:nth-child(even) { background: #f8f9fa; }

.sidebar.closed { width: 60px; }
.sidebar.closed h2, .sidebar.closed a span { display: none; }
.sidebar.closed a { justify-content: center; }
.content.expanded { margin-left: 60px; }

.submenu .submenu-content {
    display: none;
    flex-direction: column;
    background: #34495e;
}
.submenu.open .submenu-content { display: flex; }
</style>
</head>
<body>

<div id="sidebar" class="sidebar">
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <h2><i class="fas fa-warehouse"></i> <span>Inventaris</span></h2>
    <a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
    <a href="kelola_user.php"><i class="fas fa-user-gear"></i> <span>Kelola User</span></a>
    <a href="sparepart.php"><i class="fas fa-cogs"></i> <span>Manajemen Sparepart</span></a>
    <a href="masuk.php"><i class="fas fa-arrow-down"></i> <span>Stok Masuk</span></a>
    <a href="keluar.php"><i class="fas fa-arrow-up"></i> <span>Stok Keluar</span></a>
    <a href="laporan_sparepart.php"><i class="fas fa-file-alt"></i> <span>Laporan Sparepart</span></a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<div id="content" class="content">
    <header class="page-header">
        <h1><b>Laporan Sparepart</b></h1>
        <span>👋 Halo, <?= $_SESSION['username']; ?>!</span>
    </header>
<div class="card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="export_laporan_sparepart.php" target="_blank" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>

    <div class="card">
        <h2><i class="fas fa-database"></i> Data Laporan Sparepart</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Sparepart</th>
                        <th>Nama Sparepart</th>
                        <th>Total Masuk</th>
                        <th>Total Keluar</th>
                        <th>Stok Sekarang</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
$query = "
    SELECT 
        s.kode_sparepart,
        s.nama_sparepart,
        COALESCE(SUM(tm.jumlah), 0) AS total_masuk,
        COALESCE((
            SELECT SUM(tk.jumlah) 
            FROM transaksi_keluar tk 
            WHERE tk.id_sparepart = s.id_sparepart
        ), 0) AS total_keluar,
        s.stok,
        (
            SELECT MAX(tm2.tanggal)
            FROM transaksi_masuk tm2
            WHERE tm2.id_sparepart = s.id_sparepart
        ) AS tanggal_masuk_terakhir,
        (
            SELECT MAX(tk2.tanggal)
            FROM transaksi_keluar tk2
            WHERE tk2.id_sparepart = s.id_sparepart
        ) AS tanggal_keluar_terakhir
    FROM sparepart s
    LEFT JOIN transaksi_masuk tm ON s.id_sparepart = tm.id_sparepart
    GROUP BY s.id_sparepart
    ORDER BY s.nama_sparepart ASC
";
$data = mysqli_query($koneksi, $query);
$no = 1;
if (mysqli_num_rows($data) > 0) {
    while ($row = mysqli_fetch_assoc($data)) {
        // Format tanggal (opsional)
        $tgl_masuk = $row['tanggal_masuk_terakhir'] ? date('d-m-Y', strtotime($row['tanggal_masuk_terakhir'])) : '-';
        $tgl_keluar = $row['tanggal_keluar_terakhir'] ? date('d-m-Y', strtotime($row['tanggal_keluar_terakhir'])) : '-';

        echo "<tr>
                <td>{$no}</td>
                <td>{$row['kode_sparepart']}</td>
                <td>{$row['nama_sparepart']}</td>
                <td>{$row['total_masuk']} <span class='text-muted' style='font-size:13px;'>($tgl_masuk)</span></td>
                <td>{$row['total_keluar']} <span class='text-muted' style='font-size:13px;'>($tgl_keluar)</span></td>
                <td><b>{$row['stok']}</b></td>
            </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='6' class='text-muted text-center'>Belum ada data sparepart</td></tr>";
}
?>


                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('closed');
    document.getElementById('content').classList.toggle('expanded');
}
document.querySelectorAll('.submenu-toggle').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault(); 
        this.parentElement.classList.toggle('open');
    });
});
</script>

</body>
</html>
