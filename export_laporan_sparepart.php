<?php
require 'vendor/autoload.php'; // pastikan sudah composer install dompdf
include "koneksi.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// Konfigurasi Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Ambil data dari database
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

// Buat HTML untuk PDF
$html = '
<html>
<head>
<style>
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
}
h2 {
    text-align: center;
    color: #2c3e50;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
th, td {
    border: 1px solid #000;
    padding: 6px;
    text-align: center;
}
th {
    background-color: #3498db;
    color: white;
}
tr:nth-child(even) {
    background-color: #f2f2f2;
}
</style>
</head>
<body>
<h2>LAPORAN DATA SPAREPART</h2>
<table>
<thead>
<tr>
    <th>No</th>
    <th>Kode Sparepart</th>
    <th>Nama Sparepart</th>
    <th>Total Masuk (Tanggal)</th>
    <th>Total Keluar (Tanggal)</th>
    <th>Stok Sekarang</th>
</tr>
</thead>
<tbody>
';

$no = 1;
while ($row = mysqli_fetch_assoc($data)) {
    $tgl_masuk = $row['tanggal_masuk_terakhir'] ? date('d-m-Y', strtotime($row['tanggal_masuk_terakhir'])) : '-';
    $tgl_keluar = $row['tanggal_keluar_terakhir'] ? date('d-m-Y', strtotime($row['tanggal_keluar_terakhir'])) : '-';

    $html .= "
    <tr>
        <td>{$no}</td>
        <td>{$row['kode_sparepart']}</td>
        <td>{$row['nama_sparepart']}</td>
        <td>{$row['total_masuk']} ({$tgl_masuk})</td>
        <td>{$row['total_keluar']} ({$tgl_keluar})</td>
        <td><b>{$row['stok']}</b></td>
    </tr>";
    $no++;
}

$html .= '
</tbody>
</table>
<p style="margin-top:20px; text-align:right;">Dicetak pada: '.date('d-m-Y H:i').'</p>
</body>
</html>
';

// Render ke PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'potrait');
$dompdf->render();
$dompdf->stream("Laporan_Sparepart.pdf", array("Attachment" => false));
exit;
?>
