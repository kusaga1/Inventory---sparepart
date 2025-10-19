<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$notif = "";

if (isset($_POST['simpan'])) {
    $id_sparepart = mysqli_real_escape_string($koneksi, $_POST['id_sparepart']);
    $jumlah       = (int) $_POST['jumlah'];
    $supplier     = mysqli_real_escape_string($koneksi, $_POST['supplier']);
    $tanggal      = date("Y-m-d");

    if ($jumlah > 0) {
        $insert = mysqli_query($koneksi, "INSERT INTO transaksi_masuk (id_sparepart, tanggal, jumlah, supplier) 
            VALUES ('$id_sparepart','$tanggal','$jumlah','$supplier')");

        $update = mysqli_query($koneksi, "UPDATE sparepart SET stok = stok + $jumlah WHERE id_sparepart='$id_sparepart'");

        if ($insert && $update) {
            $notif = "<div class='success'>✅ Data berhasil disimpan!</div>";
        } else {
            $notif = "<div class='error'>❌ Gagal menyimpan data!</div>";
        }
    } else {
        $notif = "<div class='error'>⚠️ Jumlah tidak valid!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">

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

    /* Form & Table */
    .card {
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .success, .error {
        margin: 10px 0;
        padding: 10px;
        border-radius: 6px;
        font-weight: bold;
    }
    .success { background: #2ecc71; color: white; }
    .error { background: #e74c3c; color: white; }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        transition: 0.2s;
    }
    .form-control:focus {
        border-color: #3498db;
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
    }
    button.btn-submit:hover {
        background: #219150;
    }

    table {
        width: 100%;
        border-collapse: collapse;
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
    tr:nth-child(even) {
        background: #f8f9fa;
    }

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
    <a href="masuk.php" class="active"><i class="fas fa-arrow-down"></i> <span>Stok Masuk</span></a>
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

<!-- Konten -->
<div id="content" class="content">
    <header class="page-header">
        <h1><b>Stok Masuk</b></h1>
        <span>👋 Halo, <?= $_SESSION['username']; ?>!</span>
    </header>

    <div class="notif"><?= $notif ?></div>

    <!-- Form Input Stok Masuk -->
    <div class="card">
        <h2><i class="bi bi-pencil-square"></i> Form Input Stok Masuk</h2>
        <form method="POST" class="row g-3 mt-2">
            <div class="col-md-4">
                <label for="id_sparepart">Pilih Sparepart</label>
                <select name="id_sparepart" id="id_sparepart" class="form-control" required>
                    <option value="">-- Pilih Sparepart --</option>
                    <?php
                    $q = mysqli_query($koneksi, "SELECT * FROM sparepart");
                    while($r = mysqli_fetch_array($q)){
                        echo "<option value='$r[id_sparepart]'>$r[nama_sparepart]</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="jumlah">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" min="1" class="form-control" placeholder="Jumlah masuk" required>
            </div>
            <div class="col-md-4">
                <label for="supplier">Supplier</label>
                <input type="text" name="supplier" id="supplier" class="form-control" placeholder="Nama supplier" required>
            </div>
            <div class="col-12 d-grid mt-2">
                <button type="submit" name="simpan" class="btn-submit btn-lg"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>

    <!-- Riwayat Stok Masuk -->
<div class="card">
    <h2><i class="fas fa-history"></i> Riwayat Stok Masuk</h2>
    <div class="table-responsive mt-3">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Sparepart</th>
                    <th>Jumlah</th>
                    <th>Supplier</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $riwayat = mysqli_query($koneksi, "SELECT t.tanggal, s.nama_sparepart, t.jumlah, t.supplier 
                                               FROM transaksi_masuk t 
                                               JOIN sparepart s ON t.id_sparepart = s.id_sparepart 
                                               ORDER BY t.tanggal DESC LIMIT 10");
            $no = 1;
            if(mysqli_num_rows($riwayat) > 0){
                while($row = mysqli_fetch_array($riwayat)){
                    echo "<tr>
                            <td>".$no++."</td>
                            <td>".$row['nama_sparepart']."</td>
                            <td>".$row['jumlah']."</td>
                            <td>".$row['supplier']."</td>
                            <td>".$row['tanggal']."</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada data stok masuk</td></tr>";
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
