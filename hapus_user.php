<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

// Batasi hanya admin yang boleh menghapus user
if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses ditolak! Hanya admin yang dapat menghapus user.');window.location='kelola_user.php';</script>";
    exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Hapus user berdasarkan ID
    $hapus = mysqli_query($koneksi, "DELETE FROM users WHERE id='$id'");

    if ($hapus) {
        // ✅ Reset ID agar urut kembali dari 1
        mysqli_query($koneksi, "SET @num := 0");
        mysqli_query($koneksi, "UPDATE users SET id = @num := @num + 1");
        mysqli_query($koneksi, "ALTER TABLE users AUTO_INCREMENT = 1");

        header("Location: kelola_user.php");
        exit;
    } else {
        die("Gagal menghapus user: " . mysqli_error($koneksi));
    }
} else {
    die("Parameter id tidak ditemukan.");
}
?>
