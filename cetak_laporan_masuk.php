<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require 'vendor/autoload.php';
include "koneksi.php";

use Dompdf\Dompdf;

// Ambil data sparepart masuk
$query = "SELECT m.id_masuk, s.nama_sparepart, m.jumlah, m.supplier, m.tanggal 
          FROM transaksi_masuk m 
          JOIN sparepart s ON m.id_sparepart = s.id_sparepart 
          ORDER BY m.tanggal DESC";
$result = mysqli_query($koneksi, $query);

// Buat HTML
$html = "
<h2 style='text-align:center;'>Laporan Sparepart Masuk</h2>
<table border='1' cellspacing='0' cellpadding='6' width='100%'>
<thead>
<tr style='background:#2c3e50; color:white;'>
    <th>No</th>
    <th>Nama Sparepart</th>
    <th>Jumlah</th>
    <th>Supplier</th>
    <th>Tanggal</th>
</tr>
</thead>
<tbody>";

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $html .= "
    <tr>
        <td>{$no}</td>
        <td>{$row['nama_sparepart']}</td>
        <td>{$row['jumlah']}</td>
        <td>{$row['supplier']}</td>
        <td>{$row['tanggal']}</td>
    </tr>";
    $no++;
}

$html .= "</tbody></table>";

// Buat PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan_sparepart_masuk.pdf", ["Attachment" => true]);
