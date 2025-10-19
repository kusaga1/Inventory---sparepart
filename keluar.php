<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$notif = "";

// Proses simpan
if (isset($_POST['simpan'])) {
    $id_sparepart = (int) $_POST['id_sparepart'];
    $jumlah = (int) $_POST['jumlah'];
    $pemakai = mysqli_real_escape_string($koneksi, $_POST['pemakai']);
    $tanggal = date("Y-m-d");

    $cek = mysqli_query($koneksi, "SELECT stok FROM sparepart WHERE id_sparepart=$id_sparepart");
    $data = mysqli_fetch_assoc($cek);

    if ($data['stok'] >= $jumlah) {
        mysqli_query($koneksi, "INSERT INTO transaksi_keluar (id_sparepart, tanggal, jumlah, pemakai) 
            VALUES ('$id_sparepart','$tanggal','$jumlah','$pemakai')");
        mysqli_query($koneksi, "UPDATE sparepart SET stok = stok - $jumlah WHERE id_sparepart=$id_sparepart");
        $notif = "<div class='alert success'>✅ Transaksi berhasil disimpan!</div>";
    } else {
        $notif = "<div class='alert error'>❌ Stok tidak mencukupi!</div>";
    }
}

// Ambil riwayat transaksi
$riwayat = mysqli_query($koneksi, "SELECT tk.*, s.nama_sparepart 
    FROM transaksi_keluar tk 
    JOIN sparepart s ON tk.id_sparepart = s.id_sparepart 
    ORDER BY tk.id_keluar DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
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
.sidebar a:hover, .sidebar a.active {
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
header.page-header h1 {
    margin: 0;
    font-size: 22px;
    color: #2c3e50;
}
header.page-header span {
    font-size: 16px;
    color: #7f8c8d;
}
/* Card, Form & Table */
.card {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}
.card h2 {
    margin-bottom: 15px;
}
label {
    font-weight: bold;
    margin-top: 10px;
}
select, input {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ced4da;
    transition: 0.2s;
}
select:focus, input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 6px rgba(52,152,219,0.3);
}
button.btn-submit {
    background: #27ae60;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    font-size: 15px;
    transition: 0.2s;
    margin-top: 15px;
}
button.btn-submit:hover {
    background: #219150;
}
.alert {
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    text-align: center;
    font-weight: bold;
}
.alert.success { background: #2ecc71; color: white; }
.alert.error { background: #e74c3c; color: white; }
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
th, td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}
th {
    background: #3498db;
    color: white;
}
tr:hover { background: #f1f1f1; }
/* Sidebar closed */
.sidebar.closed { width: 60px; }
.sidebar.closed h2, .sidebar.closed a span { display: none; }
.sidebar.closed a { justify-content: center; }
.content.expanded { margin-left: 60px; }
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
    <a href="sparepart.php"><i class="fas fa-cogs"></i> <span>Manajemen Sparepart</span></a>
    <a href="masuk.php"><i class="fas fa-arrow-down"></i> <span>Stok Masuk</span></a>
    <a href="keluar.php" class="active"><i class="fas fa-arrow-up"></i> <span>Stok Keluar</span></a>
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

<!-- Konten -->
<div id="content" class="content">
    <header class="page-header">
        <h1><b>Stok Keluar</b></h1>
        <span>👋 Halo, <?= $_SESSION['username']; ?>!</span>
    </header>

    <div class="card">
        <h2><i class="bi bi-pencil-square"></i> Form Input Stok Keluar</h2>
        <?= $notif; ?>
        <form method="POST">
            <label for="id_sparepart">Nama Sparepart</label>
            <select name="id_sparepart" id="id_sparepart" required>
                <option value="">-- Pilih Sparepart --</option>
                <?php
                $q = mysqli_query($koneksi, "SELECT * FROM sparepart ORDER BY nama_sparepart ASC");
                while($r=mysqli_fetch_array($q)){
                    echo "<option value='$r[id_sparepart]'>$r[nama_sparepart] (Stok: $r[stok])</option>";
                }
                ?>
            </select>

            <label for="jumlah">Jumlah</label>
            <input type="number" name="jumlah" id="jumlah" placeholder="Masukkan jumlah" min="1" required>

            <label for="pemakai">Pemakai</label>
            <input type="text" name="pemakai" id="pemakai" placeholder="Nama pemakai" required>

            <button type="submit" name="simpan" class="btn-submit"><i class="fas fa-save"></i> Simpan</button>
        </form>
    </div>

    <div class="card">
    <h2><i class="fas fa-history"></i> Riwayat Transaksi Keluar</h2>
    <div class="table-responsive mt-3">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Sparepart</th>
                    <th>Jumlah</th>
                    <th>Pemakai</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $no = 1;
            if(mysqli_num_rows($riwayat) > 0){
                while($row = mysqli_fetch_assoc($riwayat)){ ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['nama_sparepart']; ?></td>
                        <td><?= $row['jumlah']; ?></td>
                        <td><?= $row['pemakai']; ?></td>
                        <td><?= $row['tanggal']; ?></td>
                    </tr>
                <?php }
            } else {
                echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada data stok keluar</td></tr>";
            }
            ?>
            </tbody>
        </table>
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
</script>

</body>
</html>
