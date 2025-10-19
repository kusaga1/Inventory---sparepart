<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

// Ambil data sparepart
$query = "SELECT * FROM sparepart ORDER BY id_sparepart ASC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Sparepart</title>
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
        /* Tabel laporan */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background: #2c3e50;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        .btn-print {
            display: inline-block;
            margin-bottom: 15px;
            padding: 10px 20px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-print:hover {
            background: #219150;
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
            padding-left: 10px; 
        }
        .submenu.open .submenu-content {
            display: flex; /* Baru terbuka ketika diklik */
        }



        

    </style>
</head>
<body>
    <div id="sidebar" class="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <h2><i class="fas fa-warehouse"></i> <span>Inventaris</span></h2>
        <a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
        <a href="sparepart.php"><i class="fas fa-cogs"></i> <span>Manajemen Sparepart</span></a>
        <a href="masuk.php"><i class="fas fa-arrow-down"></i> <span>Stok Masuk</span></a>
        <a href="keluar.php"><i class="fas fa-arrow-up"></i> <span>Stok Keluar</span></a>
        <div class="submenu">
        <a href="javascript:void(0)" class="submenu-toggle">
            <i class="fas fa-file-alt"></i> <span>Laporan Sparepart</span> <i class="fas fa-caret-down" style="margin-left:auto;"></i>
        </a>
        <div class="submenu-content">
            <a href="laporan_masuk.php"><i class="fas fa-arrow-down"></i> <span>Laporan Masuk</span></a>
            <a href="laporan_keluar.php"><i class="fas fa-arrow-up"></i> <span>Laporan Keluar</span></a>
        </div>
    </div>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>
    
    

    <div id="content" class="content">
        <header>
            <h1>Laporan Sparepart</h1>
            <span>👋 Halo, <?php echo $_SESSION['username']; ?>!</span>
        </header>

        <a href="cetak_laporan_sparepart.php" target="_blank" class="btn-print"><i class="fas fa-print"></i> Cetak Laporan</a>

        <table>
            <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                    </tr>
                </thead>

            <tbody>
                <?php
                $no = 1;
                while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['id_sparepart']}</td>
                    <td>{$row['kode_sparepart']}</td>
                    <td>{$row['nama_sparepart']}</td>
                    <td>{$row['kategori']}</td>
                    <td>{$row['satuan']}</td>
                    <td>{$row['stok']}</td>
                </tr>";
                $no++;
                }

                ?>
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
