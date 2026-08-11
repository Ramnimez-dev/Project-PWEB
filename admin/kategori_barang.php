<?php
/**
 * HALAMAN KATEGORI BARANG — versi preview UI (data dummy, belum terhubung database)
 * Jalankan: php -S localhost:8000  lalu buka http://localhost:8000/kategori.php
 */

function icon(string $name, int $size = 17, string $color = 'currentColor'): string
{
    $stroke = "stroke=\"$color\" stroke-width=\"2\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"";
    $paths = [
        'grid'      => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
        'clipboard' => '<path d="M9 4h6a1 1 0 0 1 1 1v1H8V5a1 1 0 0 1 1-1Z"/><rect x="5" y="6" width="14" height="15" rx="2"/><path d="M9 12h6M9 16h6"/>',
        'tags'      => '<path d="M12 2 3 11v0a2 2 0 0 0 0 2.8l6.2 6.2a2 2 0 0 0 2.8 0L21 11V4a2 2 0 0 0-2-2h-7Z"/><circle cx="8.5" cy="8.5" r="1.2"/>',
        'users'     => '<circle cx="9" cy="8" r="3.2"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16.5 7a3 3 0 1 1 0 6"/><path d="M17.5 14a5.5 5.5 0 0 1 4 5.3"/>',
        'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/>',
        'bell'      => '<path d="M6 8a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6Z"/><path d="M10 20a2 2 0 0 0 4 0"/>',
        'chevron'   => '<path d="m6 9 6 6 6-6"/>',
        'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
        'plus'      => '<path d="M12 5v14M5 12h14"/>',
        'pencil'    => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'trash'     => '<path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>',
        'x'         => '<path d="M18 6 6 18M6 6l12 12"/>',
    ];
    $body = $paths[$name] ?? '';
    return "<svg width=\"$size\" height=\"$size\" viewBox=\"0 0 24 24\" $stroke>$body</svg>";
}

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
<title>Kategori Barang — Sarpras</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">SP</div>
            <div>
                <div class="brand-name">SARPRAS</div>
                <div class="brand-sub">PANEL ADMIN</div>
            </div>
        </div>

        <div class="nav-label">Menu</div>
        <a href="dashboard.php" class="nav-item"><?= icon('grid') ?><span class="label">Dashboard</span></a>
        <a href="data_aduan.php" class="nav-item"><?= icon('clipboard') ?><span class="label">Data Aduan</span>
            <?php if ($belumCount > 0): ?><span class="badge"><?= $belumCount ?></span><?php endif; ?>
        </a>
        <a href="kategori_barang.php" class="nav-item active"><?= icon('tags') ?><span class="label">Kategori Barang</span></a>
        <a href="data_pengguna.php" class="nav-item"><?= icon('users') ?><span class="label">Data Pengguna</span></a>

        <div style="flex:1"></div>
        <div class="sidebar-footer">
            <a href="#" class="nav-item"><?= icon('logout') ?><span class="label">Keluar</span></a>
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
                    <?= icon('bell', 18) ?>
                    <?php if ($belumCount > 0): ?><span class="bell-dot"></span><?php endif; ?>
                </button>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div class="avatar-circle">BS</div>
                    <div>
                        <div class="admin-name">Budi Santoso</div>
                        <div class="admin-role">Administrator</div>
                    </div>
                    <?= icon('chevron', 14, '#6B756C') ?>
                </div>
            </div>
        </header>

        <div class="content">
            <div class="section-header">
                <div>
                    <div class="eyebrow"><?= count($filtered) ?> kategori</div>
                    <h1 class="section-title">Kategori Barang</h1>
                </div>
                <a href="kategori.php?mode=tambah" class="btn btn-primary"><?= icon('plus', 15) ?> Tambah kategori</a>
            </div>

            <form method="get" class="toolbar">
                <div class="search-box">
                    <?= icon('search', 15, '#6B756C') ?>
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
                            <div class="kategori-icon"><?= icon('tags', 16) ?></div>
                            <div class="kategori-name"><?= htmlspecialchars($k['nama']) ?></div>
                            <div class="kategori-count"><?= $k['jumlah'] ?> laporan tercatat</div>
                            <div class="kategori-actions">
                                <a href="kategori.php?mode=edit&id=<?= $k['id'] ?>" class="btn-ghost"><?= icon('pencil', 13) ?></a>
                                <form method="post" action="kategori_hapus.php" onsubmit="return confirm('Hapus kategori &quot;<?= htmlspecialchars($k['nama']) ?>&quot;?');" style="display:inline;">
                                    <input type="hidden" name="id_kategori" value="<?= $k['id'] ?>">
                                    <button type="submit" class="btn-danger-ghost"><?= icon('trash', 13) ?></button>
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