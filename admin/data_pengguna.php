<?php
/**
 * HALAMAN DATA PENGGUNA — versi preview UI (data dummy, belum terhubung database)
 * Jalankan: php -S localhost:8000  lalu buka http://localhost:8000/users.php
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
    ];
    $body = $paths[$name] ?? '';
    return "<svg width=\"$size\" height=\"$size\" viewBox=\"0 0 24 24\" $stroke>$body</svg>";
}

// ---------- DATA DUMMY (nanti diganti query PDO: SELECT * FROM users ORDER BY created_at DESC) ----------
$usersData = [
    ['id' => 1, 'induk' => '20231001', 'nama' => 'Rangga Prasetyo', 'username' => 'rangga.p', 'telp' => '0812-3456-7801', 'role' => 'user',  'dibuat' => '2026-01-14'],
    ['id' => 2, 'induk' => '20231002', 'nama' => 'Sinta Wulandari', 'username' => 'sinta.w',  'telp' => '0813-2211-9087', 'role' => 'user',  'dibuat' => '2026-01-15'],
    ['id' => 3, 'induk' => 'ADM-001',  'nama' => 'Budi Santoso',    'username' => 'budi.admin','telp' => '0811-0099-2233', 'role' => 'admin', 'dibuat' => '2025-11-02'],
    ['id' => 4, 'induk' => '20231045', 'nama' => 'Fajar Nugroho',   'username' => 'fajar.n',  'telp' => '0857-6612-4409', 'role' => 'user',  'dibuat' => '2026-02-03'],
    ['id' => 5, 'induk' => '20231110', 'nama' => 'Aulia Rahma',     'username' => 'aulia.r',  'telp' => '0821-7734-1120', 'role' => 'user',  'dibuat' => '2026-02-20'],
];

// ---------- PENCARIAN & FILTER ROLE (dari query string, nanti tinggal ganti WHERE di SQL) ----------
$q           = trim($_GET['q'] ?? '');
$roleFilter  = $_GET['role'] ?? 'Semua';
$roleOptions = ['Semua', 'admin', 'user'];

$filtered = array_filter($usersData, function ($u) use ($q, $roleFilter) {
    $matchQ = $q === '' || stripos($u['nama'], $q) !== false || stripos($u['username'], $q) !== false || stripos($u['induk'], $q) !== false;
    $matchR = $roleFilter === 'Semua' || $u['role'] === $roleFilter;
    return $matchQ && $matchR;
});
usort($filtered, fn($a, $b) => strcmp($b['dibuat'], $a['dibuat']));

$belumCount = 2; // dummy badge di sidebar, nanti diganti query COUNT status 'Belum Dikerjakan' di tabel aduan
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Pengguna — Sarpras</title>
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
        <a href="kategori_barang.php" class="nav-item"><?= icon('tags') ?><span class="label">Kategori Barang</span></a>
        <a href="data_pengguna.php" class="nav-item active"><?= icon('users') ?><span class="label">Data Pengguna</span></a>

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
                    <div class="eyebrow"><?= count($filtered) ?> akun</div>
                    <h1 class="section-title">Data Pengguna</h1>
                </div>
                <a href="#" class="btn btn-primary"><?= icon('plus', 15) ?> Tambah pengguna</a>
            </div>

            <form method="get" class="toolbar">
                <div class="search-box">
                    <?= icon('search', 15, '#6B756C') ?>
                    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama, username, atau no. induk...">
                </div>
                <div class="filter-group">
                    <?php foreach ($roleOptions as $opt): ?>
                        <button type="submit" name="role" value="<?= htmlspecialchars($opt) ?>"
                                class="filter-chip <?= $roleFilter === $opt ? 'active' : '' ?>">
                            <?= htmlspecialchars($opt) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </form>

            <div class="table-card">
                <table>
                    <thead>
                    <tr>
                        <th style="width:110px">No. induk</th>
                        <th>Nama</th>
                        <th style="width:140px">Username</th>
                        <th style="width:150px">No. telp</th>
                        <th style="width:90px">Role</th>
                        <th style="width:110px">Terdaftar</th>
                        <th style="width:80px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($filtered)): ?>
                        <tr><td colspan="7" style="text-align:center;color:var(--sub);padding:30px;">Tidak ada pengguna yang cocok dengan pencarian.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($filtered as $u): ?>
                        <?php
                            $parts   = explode(' ', trim($u['nama']));
                            $initial = strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
                        ?>
                        <tr>
                            <td class="mono"><?= htmlspecialchars($u['induk']) ?></td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar"><?= $initial ?></div>
                                    <div class="user-name"><?= htmlspecialchars($u['nama']) ?></div>
                                </div>
                            </td>
                            <td class="mono"><?= htmlspecialchars($u['username']) ?></td>
                            <td class="mono"><?= htmlspecialchars($u['telp']) ?></td>
                            <td><span class="role-badge <?= $u['role'] === 'admin' ? 'admin' : '' ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                            <td class="mono"><?= htmlspecialchars($u['dibuat']) ?></td>
                            <td>
                                <div class="row-actions">
                                    <a href="#" class="btn-ghost"><?= icon('pencil', 13) ?></a>
                                    <button class="btn-danger-ghost"><?= icon('trash', 13) ?></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>