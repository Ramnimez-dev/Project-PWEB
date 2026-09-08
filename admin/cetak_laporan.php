<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['nama']) || ($_SESSION['role'] ?? '') !== 'admin') {
    die("Akses ditolak.");
}

if(!isset($_SESSION['nomor_induk']) || ($_SESSION['role'] ?? '') !== 'admin') {
    die("Akses ditolak.");
}

$jenis = $_GET['jenis'] ?? 'harian';
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$bulan = $_GET['bulan'] ?? date('Y-m');
$tahun = $_GET['tahun'] ?? date('Y');

$judul_waktu = "";
$kondisi = "";
$params = [];
$types = "";

if ($jenis === 'harian') {
    $judul_waktu = date('d F Y', strtotime($tanggal));
    $kondisi = "DATE(a.tanggal) = ?";
    $params[] = $tanggal;
    $types = "s";
} elseif ($jenis === 'bulanan') {
    $judul_waktu = date('F Y', strtotime($bulan . '-01'));
    $kondisi = "DATE_FORMAT(a.tanggal, '%Y-%m') = ?";
    $params[] = $bulan;
    $types = "s";
} elseif ($jenis === 'tahunan') {
    $judul_waktu = "Tahun " . $tahun;
    $kondisi = "YEAR(a.tanggal) = ?";
    $params[] = $tahun;
    $types = "s";
}

$sql = "SELECT a.id_aduan, a.barang_aduan, a.lokasi, a.status, a.tanggal, 
        k.nama_kategori, u.nama AS pelapor
        FROM aduan a
        LEFT JOIN kategori_barang k ON k.id_kategori = a.kategori_id
        LEFT JOIN users u ON u.id_user = a.user_id
        WHERE $kondisi
        ORDER BY a.tanggal ASC";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan <?= $judul_waktu ?> - SarPras</title>
    <link rel="shortcut icon" href="../img/logo sapras.png">
    <link rel="stylesheet" href="style.css">
</head>
<body onload="window.print()">
    <div class="page">
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <!-- Ubah path src sesuai lokasi logo Anda -->
            <img src="../img/haloLogo.png" alt="Logo">
            <div class="kop-teks">
                <h1><b>Halo SarPras</b></h1>
                <h3>Aplikasi Aduan Sarana Prasarana SMK Taruna Bangsa</h3>
                <p>Jl. Kaliabang Tengah, Perwira, Bekasi Utara, Kota Bekasi<br>
                Telp: (021) 1234567 | Email: info@smktb.sch.id</p>
            </div>
        </div>

        <div class="judul-laporan">
            <h3>LAPORAN DATA ADUAN SARPRAS</h3>
            <p>Periode: <?= $judul_waktu ?></p>
        </div>

        <!-- TABEL DATA -->
        <table class="tbl-laporan">
            <thead>
                <tr>
                    <th style="width: 5%" class="th-laporan">No</th>
                    <th style="width: 15%" class="th-laporan">Tanggal</th>
                    <th style="width: 20%" class="th-laporan">Barang & Lokasi</th>
                    <th style="width: 15%" class="th-laporan">Kategori</th>
                    <th style="width: 20%" class="th-laporan">Pelapor</th>
                    <th style="width: 15%" class="th-laporan">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($result)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;" class="td-laporan">Tidak ada data aduan pada periode ini.</td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($result as $row): ?>
                    <tr>
                        <td style="text-align:center;"><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['barang_aduan']) ?></strong><br>
                            <span style="font-size: 12px;"><?= htmlspecialchars($row['lokasi']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['pelapor'] ?? '-') ?></td>
                        <td style="text-align:center;"><?= htmlspecialchars($row['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- LEGALISASI / TANDA TANGAN -->
        <div class="legalisasi">
            <div class="ttd-box">
                <p>Bekasi, <?= date('d F Y') ?><br>Mengetahui,<br>Administrator SarPras</p>
                <br><br><br><br>
                <!-- Ruang kosong untuk tanda tangan basah / stempel -->
                <p class="ttd-nama"><?= htmlspecialchars($_SESSION['nama']) ?></p>
                <p>NIP. <?= htmlspecialchars($_SESSION['nomor_induk']) ?></p>
            </div>
        </div>
    </div>
</body>
</html>
