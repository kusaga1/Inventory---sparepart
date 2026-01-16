<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";
require 'vendor/autoload.php'; // Library PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$message = "";

// Tambah data
if (isset($_POST['tambah'])) {
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']); 
    $stok_minimum = (int)$_POST['stok_minimum'];
    $stok = isset($_POST['stok']) ? (int)$_POST['stok'] : 0; // default stok awal

    // === BATAS MAKSIMUM STOK ===
    $stok_maksimum = 100; // ubah sesuai kebutuhan

    if ($stok > $stok_maksimum) {
        echo "<script>alert('❌ Gagal menambahkan! Stok melebihi batas maksimum ($stok_maksimum).');</script>";
    } else {
        $insert = mysqli_query($koneksi, "
            INSERT INTO sparepart (kode_sparepart, nama_sparepart, satuan, stok)
            VALUES ('$kode', '$nama', '$satuan', '$stok')
            ON DUPLICATE KEY UPDATE 
                nama_sparepart = VALUES(nama_sparepart),
                satuan = VALUES(satuan),
                stok = VALUES(stok)
        ");

        if ($insert) {
            echo "<script>alert('✅ Sparepart berhasil ditambahkan!');</script>";
        } else {
            echo "<script>alert('❌ Gagal menambahkan data!');</script>";
        }
    }
}



// Hapus data
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $hapus = mysqli_query($koneksi, "DELETE FROM sparepart WHERE id_sparepart=$id");
    if (!$hapus) {
        echo "<script>alert('Data tidak bisa dihapus, masih digunakan di tabel lain!');</script>";
    }
}
// === Import Excel ===
if (isset($_POST['import'])) {
    $file_mimes = [
        'application/vnd.ms-excel', 
        'text/xls', 
        'text/xlsx', 
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (isset($_FILES['file_excel']['name']) && in_array($_FILES['file_excel']['type'], $file_mimes)) {
        $arr_file = explode('.', $_FILES['file_excel']['name']);
        $extension = end($arr_file);

        if ($extension == 'csv') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        $spreadsheet = $reader->load($_FILES['file_excel']['tmp_name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $sukses = 0;
        $gagal = 0;

          // Loop mulai dari baris ke-2 (header di baris 1)
        for ($i = 1; $i < count($sheetData); $i++) {
            $kode = mysqli_real_escape_string($koneksi, $sheetData[$i][0]);
            $nama = mysqli_real_escape_string($koneksi, $sheetData[$i][1]); 
            $satuan = mysqli_real_escape_string($koneksi, $sheetData[$i][2]);
            $stok = isset($sheetData[$i][3]) && $sheetData[$i][3] !== '' ? (int)$sheetData[$i][3] : 0;


            if ($kode != '' && $nama != '') {   
                $insert = mysqli_query($koneksi, "INSERT INTO sparepart 
                    (kode_sparepart, nama_sparepart, satuan, stok)
                    VALUES ('$kode','$nama','$satuan','$stok')");
                if ($insert) $sukses++; else $gagal++;
            }
        }

        $message = "✅ Import selesai. Berhasil: $sukses | Gagal: $gagal";
    } else {
        $message = "❌ Format file tidak didukung. Gunakan file .xls atau .xlsx.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Manajemen Sparepart</title>
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
    /* Form & Table */
    .card {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .card h2 {
        margin-top: 0;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th, .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }
    .table th {
        background: #2c3e50;
        color: #fff;
    }
    .btn-custom {
        border-radius: 8px;
        padding: 5px 12px;
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
    .form-control {
        border-radius: 6px;
        padding: 5px 10px;
        transition: 0.3s;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0,123,255,0.3);
    }
    .btn-gradient {
        background: linear-gradient(90deg, #007d9cff, #000875ff);
        color: white;
        border: none;
        border-radius: 6px;
        transition: 0.3s;
    }
    .btn-gradient:hover {
        background: linear-gradient(90deg, #008a9cff, #002088ff);
    }
    .table th, .table td {
        vertical-align: middle;
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
    <a href="kelola_user.php"><i class="fas fa-user-gear"></i> <span>Kelola User</span></a>
    <a href="sparepart.php" class="active"><i class="fas fa-cogs"></i> <span>Manajemen Sparepart</span></a>
    <a href="masuk.php"><i class="fas fa-arrow-down"></i> <span>Stok Masuk</span></a>
    <a href="keluar.php"><i class="fas fa-arrow-up"></i> <span>Stok Keluar</span></a>
    <a href="laporan_sparepart.php"><i class="fas fa-file-alt"></i> <span>Laporan Sparepart</span></a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<!-- Konten -->
<div id="content" class="content">
    <header>
        <h1>Manajemen Sparepart</h1>
        <span>👋 Halo, <?php echo $_SESSION['username']; ?>!</span>
    </header>

<!-- Form Tambah -->
<div class="card" style="background: #e8f0fe; border-left: 5px solid #007bff;">
    <h2 style="color:#007bff;"><i class="fas fa-plus-circle"></i> Tambah Sparepart</h2>
    <form method="POST" class="row g-3 mt-2">
        <div class="col-md-4">
            <label class="form-label">Kode Sparepart</label>
            <input type="text" name="kode" class="form-control" placeholder="Masukkan kode" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Nama Sparepart</label>
            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama" required>
        </div>
        
        <div class="col-md-3">
            <label class="form-label">Satuan</label>
            <input type="text" name="satuan" class="form-control" placeholder="Pcs">
        </div>
        
        <div class="col-md-3">
            <label class="form-label">Stok Minimum</label>
            <input type="number" name="stok_minimum" class="form-control" placeholder="5" value="5">
        </div>
        <div class="col-md-3 d-grid align-self-end">
            <button type="submit" name="tambah" class="btn btn-gradient btn-lg w-100">
                    <i class="fas fa-plus"></i> Tambah Sparepart
                </button>
        </div>
    </form>
</div>

<!-- Form Import Excel -->
    <div class="card" style="background: #eafaf1; border-left: 5px solid #27ae60;">
        <h2><i class="fas fa-file-excel"></i> Import Sparepart dari Excel</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file_excel" required>
            <button type="submit" name="import" class="btn btn-import"><i class="fas fa-upload"></i> Import</button>
        </form>
        <p style="font-size:14px;color:#555;margin-top:8px;">
            Format kolom (urutkan di Excel): <b>Kode | Nama | Satuan | Stok</b>
        </p>
    </div>


    <!-- Tabel Sparepart -->
    <div class="card">
        <h2>Daftar Sparepart</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Nama</th>         
                    <th>Satuan</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $data = mysqli_query($koneksi, "SELECT * FROM sparepart ORDER BY id_sparepart DESC");
            if(mysqli_num_rows($data) > 0){
                while($d = mysqli_fetch_assoc($data)){
                    echo "<tr>
                        <td>{$d['id_sparepart']}</td>
                        <td>{$d['kode_sparepart']}</td>
                        <td>{$d['nama_sparepart']}</td>
                        <td>{$d['satuan']}</td>
                        <td>{$d['stok']}</td>
                        <td>
                            <a href='?hapus={$d['id_sparepart']}' class='btn btn-danger btn-custom'
                                onclick=\"return confirm('Yakin hapus data ini?');\">
                                <i class='fas fa-trash'></i> Hapus
                            </a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8' style='color:#7f8c8d;'>Belum ada data sparepart.</td></tr>";
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


</body>
</html>
