<?php
// Function Icon & Helper (Shorthand)
function icon(string $name, int $size = 17, string $color = 'currentColor'): string {
    $s = "width=\"$size\" height=\"$size\" viewBox=\"0 0 24 24\" stroke=\"$color\" stroke-width=\"2\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"";
    $p = [
        'grid' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
        'clipboard' => '<path d="M9 4h6a1 1 0 0 1 1 1v1H8V5a1 1 0 0 1 1-1Z"/><rect x="5" y="6" width="14" height="15" rx="2"/><path d="M9 12h6M9 16h6"/>',
        'tags' => '<path d="M12 2 3 11v0a2 2 0 0 0 0 2.8l6.2 6.2a2 2 0 0 0 2.8 0L21 11V4a2 2 0 0 0-2-2h-7Z"/><circle cx="8.5" cy="8.5" r="1.2"/>',
        'users' => '<circle cx="9" cy="8" r="3.2"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16.5 7a3 3 0 1 1 0 6"/><path d="M17.5 14a5.5 5.5 0 0 1 4 5.3"/>',
        'bell' => '<path d="M6 8a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6Z"/><path d="M10 20a2 2 0 0 0 4 0"/>',
        'chevron' => '<path d="m6 9 6 6 6-6"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
    ];
    return "<svg $s>" . ($p[$name] ?? '') . "</svg>";
}

function statusPill(string $status): string {
    $map = ['Belum Dikerjakan' => 'status-belum', 'Sedang Dikerjakan' => 'status-proses', 'Selesai' => 'status-selesai'];
    $class = $map[$status] ?? 'status-belum';
    return '<span class="pill ' . $class . '"><span class="pill-dot"></span>' . htmlspecialchars($status) . '</span>';
}

// Data Dummy & Stats Calculation (Lebih singkat)
$aduanDummy = [
    ['id' => 1042, 'barang' => 'AC Ruang Kelas 3B mati total', 'kategori' => 'Elektronik', 'lokasi' => 'Gedung B, Lt. 2, R.3B', 'status' => 'Belum Dikerjakan'],
    ['id' => 1041, 'barang' => 'Kursi kuliah patah bagian sandaran', 'kategori' => 'Furnitur', 'lokasi' => 'Gedung A, Lt. 1, R.1A', 'status' => 'Sedang Dikerjakan'],
    ['id' => 1040, 'barang' => 'Wastafel toilet lantai 2 bocor', 'kategori' => 'Sanitasi', 'lokasi' => 'Gedung C, Lt. 2, Toilet Pria', 'status' => 'Selesai'],
    ['id' => 1039, 'barang' => 'Wifi lab komputer tidak stabil', 'kategori' => 'Jaringan & IT', 'lokasi' => 'Gedung D, Lab Komputer 1', 'status' => 'Sedang Dikerjakan'],
    ['id' => 1038, 'barang' => 'Plafon ruang rapat retak', 'kategori' => 'Bangunan', 'lokasi' => 'Gedung A, Lt. 3, R. Rapat', 'status' => 'Belum Dikerjakan'],
];

$statMap = array_count_values(array_column($aduanDummy, 'status'));
$counts = array_merge(['Belum Dikerjakan' => 0, 'Sedang Dikerjakan' => 0, 'Selesai' => 0], $statMap);
$total = count($aduanDummy);
$adminName = 'Budi Santoso';

// Format Tanggal Singkat
$hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][date('w')];
$bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][date('n')];
$tanggalText = "$hari, " . date('j') . " $bulan " . date('Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Sarpras</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">SP</div>
            <div><div class="brand-name">SARPRAS</div><div class="brand-sub">PANEL ADMIN</div></div>
        </div>
        <div class="nav-label">Menu</div>
        <a href="#" class="nav-item active"><?= icon('grid') ?> <span>Dasbor</span></a>
        <a href="#" class="nav-item"><?= icon('clipboard') ?> <span>Data Aduan</span>
            <?php if ($counts['Belum Dikerjakan']): ?><span class="badge"><?= $counts['Belum Dikerjakan'] ?></span><?php endif; ?>
        </a>
        <a href="#" class="nav-item"><?= icon('tags') ?> <span>Kategori Barang</span></a>
        <a href="#" class="nav-item"><?= icon('users') ?> <span>Data Pengguna</span></a>
        <div class="sidebar-footer">
            <a href="#" class="nav-item"><?= icon('logout') ?> <span>Keluar</span></a>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="topbar-date"><?= $tanggalText ?></div>
            <div class="topbar-right">
                <button class="bell-btn" aria-label="Notifikasi">
                    <?= icon('bell', 18) ?>
                    <?php if ($counts['Belum Dikerjakan']): ?><span class="bell-dot"></span><?php endif; ?>
                </button>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div class="avatar-circle">BS</div>
                    <div>
                        <div style="font-size:12.5px;font-weight:600;"><?= htmlspecialchars($adminName) ?></div>
                        <div style="font-size:10.5px;color:var(--sub);">Administrator</div>
                    </div>
                    <?= icon('chevron', 14, '#6B756C') ?>
                </div>
            </div>
        </header>

        <div class="content">
            <div class="eyebrow">Ringkasan hari ini</div>
            <h1 class="section-title">Dasbor Sarpras</h1>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">Total aduan</div>
                    <div class="stat-value"><?= $total ?></div>
                    <div class="stat-sub">Sepanjang periode aktif</div>
                </div>
                <div class="stat-card tone-red">
                    <div class="stat-label">Belum dikerjakan</div>
                    <div class="stat-value"><?= $counts['Belum Dikerjakan'] ?></div>
                    <div class="stat-sub">Perlu ditinjau</div>
                </div>
                <div class="stat-card tone-amber">
                    <div class="stat-label">Sedang dikerjakan</div>
                    <div class="stat-value"><?= $counts['Sedang Dikerjakan'] ?></div>
                    <div class="stat-sub">Dalam proses</div>
                </div>
                <div class="stat-card tone-green">
                    <div class="stat-label">Selesai</div>
                    <div class="stat-value"><?= $counts['Selesai'] ?></div>
                    <div class="stat-sub">Sudah ditutup</div>
                </div>
            </div>

            <div class="list-label">Aduan masuk terbaru</div>
            <?php foreach ($aduanDummy as $a): ?>
                <a class="recent-row" href="#">
                    <div style="flex:1;min-width:0;">
                        <div class="recent-title"><?= htmlspecialchars($a['barang']) ?></div>
                        <div class="recent-meta">
                            <span>#<?= $a['id'] ?></span>
                            <span><?= htmlspecialchars($a['kategori']) ?></span>
                            <span><?= htmlspecialchars($a['lokasi']) ?></span>
                        </div>
                    </div>
                    <?= statusPill($a['status']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
</div>
</body>
</html>