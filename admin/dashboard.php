<?php
session_start();
require '../config/koneksi.php';

// Guard — cuma admin yang login boleh buka halaman ini
if (empty($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$adminName = $_SESSION['nama'];

// Hitung jumlah aduan per status
$counts = ['Belum Dikerjakan' => 0, 'Sedang Dikerjakan' => 0, 'Selesai' => 0];
$hasil = mysqli_query($koneksi, "SELECT status, COUNT(*) AS jumlah FROM aduan GROUP BY status");
while ($row = mysqli_fetch_assoc($hasil)) {
    $counts[$row['status']] = (int)$row['jumlah'];
}
$total      = array_sum($counts);
$belumCount = $counts['Belum Dikerjakan'];

// 5 aduan terbaru, dipetakan ke key yang sama seperti yang dipakai di foreach bawah (id, barang, kategori, lokasi, status)
$aduanTerbaru = [];
$queryRecent = mysqli_query($koneksi, "
    SELECT a.id_aduan, a.barang_aduan, a.lokasi, a.status, k.nama_kategori
    FROM aduan a
    LEFT JOIN kategori_barang k ON k.id_kategori = a.kategori_id
    ORDER BY a.tanggal DESC
    LIMIT 5
");
while ($row = mysqli_fetch_assoc($queryRecent)) {
    $aduanTerbaru[] = [
        'id'       => $row['id_aduan'],
        'barang'   => $row['barang_aduan'],
        'kategori' => $row['nama_kategori'] ?? '-',
        'lokasi'   => $row['lokasi'],
        'status'   => $row['status'],
    ];
}

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
        <a href="dashboard.php" class="nav-item active"><?= icon('grid') ?> <span>Dashboard</span></a>
        <a href="data_aduan.php" class="nav-item"><?= icon('clipboard') ?> <span>Data Aduan</span>
            <?php if ($belumCount > 0): ?><span class="badge"><?= $belumCount ?></span><?php endif; ?>
        </a>
        <a href="kategori_barang.php" class="nav-item"><?= icon('tags') ?> <span>Kategori Barang</span></a>
        <a href="data_pengguna.php" class="nav-item"><?= icon('users') ?> <span>Data Pengguna</span></a>
        
        <div style="flex:1"></div>
        <div class="sidebar-footer">
            <a href="../auth/logout.php" onclick="return confirm('Yakin Ingin Logout?')" class="nav-item"><?= icon('logout') ?><span class="label">Keluar</span></a>
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
            <?php if (empty($aduanTerbaru)): ?>
                <p style="color:var(--sub)">Belum ada aduan yang masuk.</p>
            <?php endif; ?>
            <?php foreach ($aduanTerbaru as $a): ?>
                <a class="recent-row" href="data_aduan.php?id=<?= (int)$a['id'] ?>">
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