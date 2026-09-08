<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['nama']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$adminName = $_SESSION['nama'];
$currentPage = basename($_SERVER['PHP_SELF']);
$potongNama = explode(' ', trim($adminName));
$inisial = strtoupper(substr($potongNama[0], 0, 1) . substr(end($potongNama), 0, 1));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - SarPras</title>
    <link rel="shortcut icon" href="../img/logo sapras.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="layout">

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark"><img src="../img/logo sapras.png" alt="logo sarpras"></div>
            <div>
                <div class="brand-name">SARPRAS</div>
                <div class="brand-sub">PANEL ADMIN</div>
            </div>
        </div>

        <div class="nav-label">Menu</div>
        <a href="dashboard.php" class="nav-item"><span class="label">Beranda</span></a>
        <a href="data_aduan.php" class="nav-item"><span class="label">Data Aduan</span>
        </a>
        <a href="kategori_barang.php" class="nav-item"><span class="label">Kategori Barang</span></a>
        <a href="data_pengguna.php" class="nav-item"><span class="label">Data Pengguna</span></a>
        <a href="laporan.php" class="nav-item active"><span class="label">Laporan</span></a>

        <div style="flex:1"></div>
        <div class="sidebar-footer">
            <a href="../auth/logout.php" onclick="return confirm('Yakin Ingin Logout?')" class="nav-item"><span class="label">Keluar</span></a>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="topbar-date"><?php
                $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                echo $hari[date('w')] . ', ' . date('j') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');
            ?></div>
            <div class="topbar-right">
                <button class="bell-btn" aria-label="Notifikasi">
                </button>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div class="avatar-circle"><?= $inisial ?></div>
                    <div>
                        <div class="admin-name"><?= $adminName ?></div>
                        <div class="admin-role">Administrator</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="content">
            <div class="section-header">
                <h1 class="section-title">Cetak Laporan Aduan</h1>
            </div>

            <div class="filter-card">
                <form action="cetak_laporan.php" method="GET" target="_blank">
                    <div class="form-group">
                        <label>Jenis Laporan</label>
                        <select name="jenis" id="jenis_laporan" class="form-control" onchange="toggleInput()">
                            <option value="harian">Harian</option>
                            <option value="bulanan">Bulanan</option>
                            <option value="tahunan">Tahunan</option>
                        </select>
                    </div>

                    <div class="form-group" id="input_harian">
                        <label>Pilih Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="form-group hidden" id="input_bulanan">
                        <label>Pilih Bulan</label>
                        <input type="month" name="bulan" class="form-control" value="<?= date('Y-m') ?>">
                    </div>

                    <div class="form-group hidden" id="input_tahunan">
                        <label>Pilih Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" min="2020" max="2099">
                    </div>

                    <button type="submit" class="btn-print">Preview & Cetak PDF</button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
function toggleInput() {
    const jenis = document.getElementById('jenis_laporan').value;
    document.getElementById('input_harian').classList.add('hidden');
    document.getElementById('input_bulanan').classList.add('hidden');
    document.getElementById('input_tahunan').classList.add('hidden');

    if (jenis === 'harian') document.getElementById('input_harian').classList.remove('hidden');
    else if (jenis === 'bulanan') document.getElementById('input_bulanan').classList.remove('hidden');
    else if (jenis === 'tahunan') document.getElementById('input_tahunan').classList.remove('hidden');
}
</script>
</body>
</html>
