<?php

session_start();
require '../config/koneksi.php';

if(!isset($_SESSION['nama'])) {
    header("Location: ../config/koneksi.php");
    exit();
}

$adminName = $_SESSION['nama'];
$potongNama = explode(' ', trim($adminName));
$inisial = strtoupper(substr($potongNama[0], 0, 1) . substr(end($potongNama), 0, 1));

// ---------- DATA DUMMY (nanti diganti query PDO ke tabel kategori_barang, join COUNT(*) dari aduan) ----------
$kategoriData = [
    ['id' => 1, 'nama' => 'Elektronik',    'jumlah' => 12],
    ['id' => 2, 'nama' => 'Furnitur',      'jumlah' => 27],
    ['id' => 3, 'nama' => 'Sanitasi',      'jumlah' => 9],
    ['id' => 4, 'nama' => 'Jaringan & IT', 'jumlah' => 6],
    ['id' => 5, 'nama' => 'Bangunan',      'jumlah' => 14],
];

// ---------- PENCARIAN (dari query string, nanti tinggal ganti WHERE nama_kategori LIKE di SQL) ----------
$q = trim($_GET['q'] ?? '');

$filtered = array_filter($kategoriData, fn($k) => $q === '' || stripos($k['nama'], $q) !== false);
usort($filtered, fn($a, $b) => strcmp($a['nama'], $b['nama']));

// ---------- MODE FORM (tambah / edit lewat query string, mirip pola di aduan.php) ----------
$mode      = $_GET['mode'] ?? null; // 'tambah' atau 'edit'
$editId    = isset($_GET['id']) ? (int)$_GET['id'] : null;
$editData  = null;
if ($mode === 'edit' && $editId) {
    foreach ($kategoriData as $k) {
        if ($k['id'] === $editId) { $editData = $k; break; }
    }
}

$belumCount = 2; // dummy badge sidebar, nanti diganti query COUNT status 'Belum Dikerjakan' di tabel aduan
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kategori Barang — SarPras</title>
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
        <a href="dashboard.php" class="nav-item"><span class="label">Dashboard</span></a>
        <a href="data_aduan.php" class="nav-item"><span class="label">Data Aduan</span>
            <?php if ($belumCount > 0): ?><span class="badge"><?= $belumCount ?></span><?php endif; ?>
        </a>
        <a href="kategori_barang.php" class="nav-item active"><span class="label">Kategori Barang</span></a>
        <a href="data_pengguna.php" class="nav-item"><span class="label">Data Pengguna</span></a>

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
                    <?php if ($belumCount > 0): ?><span class="bell-dot"></span><?php endif; ?>
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
                <div>
                    <div class="eyebrow"><?= count($filtered) ?> kategori</div>
                    <h1 class="section-title">Kategori Barang</h1>
                </div>
                <a href="kategori.php?mode=tambah" class="btn btn-primary">Tambah kategori</a>
            </div>

            <form method="get" class="toolbar">
                <div class="search-box">
                    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama kategori...">
                </div>
            </form>

            <?php if (empty($filtered)): ?>
                <div class="empty-state">
                    <div><?= icon('tags', 34) ?></div>
                    <div>Tidak ada kategori yang cocok dengan pencarian "<?= htmlspecialchars($q) ?>".</div>
                </div>
            <?php else: ?>
                <div class="kategori-grid">
                    <?php foreach ($filtered as $i => $k): ?>
                        <div class="kategori-card">
                            <div class="kategori-rank">#<?= str_pad($k['id'], 2, '0', STR_PAD_LEFT) ?></div>
                            <div class="kategori-icon"></div>
                            <div class="kategori-name"><?= htmlspecialchars($k['nama']) ?></div>
                            <div class="kategori-count"><?= $k['jumlah'] ?> laporan tercatat</div>
                            <div class="kategori-actions">
                                <a href="kategori.php?mode=edit&id=<?= $k['id'] ?>" class="btn-ghost"></a>
                                <form method="post" action="kategori_hapus.php" onsubmit="return confirm('Hapus kategori &quot;<?= htmlspecialchars($k['nama']) ?>&quot;?');" style="display:inline;">
                                    <input type="hidden" name="id_kategori" value="<?= $k['id'] ?>">
                                    <button type="submit" class="btn-danger-ghost"></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php if ($mode === 'tambah' || ($mode === 'edit' && $editData)): ?>
<div class="modal-backdrop" onclick="if(event.target===this) window.location='kategori.php?q=<?= urlencode($q) ?>'">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><?= $mode === 'tambah' ? 'Tambah kategori' : 'Edit kategori' ?></div>
            <a class="modal-close" href="kategori.php?q=<?= urlencode($q) ?>"><?= icon('x', 20) ?></a>
        </div>
        <form method="post" action="<?= $mode === 'tambah' ? 'kategori_tambah.php' : 'kategori_edit.php' ?>">
            <div class="modal-body">
                <?php if ($mode === 'edit'): ?>
                    <input type="hidden" name="id_kategori" value="<?= $editData['id'] ?>">
                <?php endif; ?>
                <div class="field-label">Nama kategori</div>
                <input type="text" name="nama_kategori" class="field-input" required
                       value="<?= $mode === 'edit' ? htmlspecialchars($editData['nama']) : '' ?>"
                       placeholder="Misal: Elektronik">
                <div class="modal-footer">
                    <a href="kategori.php?q=<?= urlencode($q) ?>" class="btn">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
</body>
</html>