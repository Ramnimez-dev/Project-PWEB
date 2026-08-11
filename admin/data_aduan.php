<?php
/**
 * HALAMAN DATA ADUAN — versi preview UI Ringkas & Clean
 * Jalankan: php -S localhost:8000 lalu buka http://localhost:8000/aduan.php
 */

function icon(string $name, int $size = 16, string $color = 'currentColor'): string
{
    $stroke = "stroke=\"$color\" stroke-width=\"2\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"";
    $paths = [
        'grid'      => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
        'clipboard' => '<path d="M9 4h6a1 1 0 0 1 1 1v1H8V5a1 1 0 0 1 1-1Z"/><rect x="5" y="6" width="14" height="15" rx="2"/><path d="M9 12h6M9 16h6"/>',
        'tags'      => '<path d="M12 2 3 11v0a2 2 0 0 0 0 2.8l6.2 6.2a2 2 0 0 0 2.8 0L21 11V4a2 2 0 0 0-2-2h-7Z"/><circle cx="8.5" cy="8.5" r="1.2"/>',
        'users'     => '<circle cx="9" cy="8" r="3.2"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16.5 7a3 3 0 1 1 0 6"/><path d="M17.5 14a5.5 5.5 0 0 1 4 5.3"/>',
        'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/>',
        'paperclip' => '<path d="M17.5 6.5 8.6 15.4a3 3 0 1 0 4.2 4.2l8-8a5 5 0 0 0-7-7l-8.4 8.3"/>',
        'message'   => '<path d="M21 12a8 8 0 1 1-3.2-6.4L21 4l-1 4.6A7.9 7.9 0 0 1 21 12Z"/>',
        'bell'      => '<path d="M6 8a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6Z"/><path d="M10 20a2 2 0 0 0 4 0"/>',
        'chevron'   => '<path d="m6 9 6 6 6-6"/>',
        'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
        'x'         => '<path d="M18 6 6 18M6 6l12 12"/>',
        'pin'       => '<path d="M12 21s7-6.5 7-11.5A7 7 0 0 0 5 9.5C5 14.5 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.2"/>',
        'package'   => '<path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/><path d="m20.7 7-8.7-5-8.7 5 8.7 5 8.7-5Z"/>',
    ];
    $body = $paths[$name] ?? '';
    return "<svg width=\"$size\" height=\"$size\" viewBox=\"0 0 24 24\" $stroke>$body</svg>";
}

function statusPill(string $status): string
{
    $class = match ($status) {
        'Belum Dikerjakan' => 'status-belum',
        'Sedang Dikerjakan' => 'status-proses',
        'Selesai'           => 'status-selesai',
        default             => 'status-belum',
    };
    return '<span class="pill ' . $class . '"><span class="pill-dot"></span>' . htmlspecialchars($status) . '</span>';
}

// ---------- DATA DUMMY ----------
$aduanData = [
    1042 => [
        'id' => 1042, 'barang' => 'AC Ruang Kelas 3B mati total', 'kategori' => 'Elektronik',
        'pelapor' => 'Rangga Prasetyo', 'jumlah' => 1, 'lokasi' => 'Gedung B, Lt. 2, R.3B',
        'isi' => 'AC sudah tidak menyala sejak Senin pagi, sudah dicoba remote baru tetap tidak merespon.',
        'status' => 'Belum Dikerjakan', 'tanggal' => '2026-08-01 08:12',
        'lampiran' => ['foto_ac_1.jpg', 'foto_ac_2.jpg'],
        'komentar' => [
            ['admin' => 'Budi Santoso', 'isi' => 'Sudah dijadwalkan teknisi hari Kamis.', 'tanggal' => '01/08/2026 10:20'],
        ],
    ],
    1041 => [
        'id' => 1041, 'barang' => 'Kursi kuliah patah bagian sandaran', 'kategori' => 'Furnitur',
        'pelapor' => 'Sinta Wulandari', 'jumlah' => 3, 'lokasi' => 'Gedung A, Lt. 1, R.1A',
        'isi' => '3 kursi di baris belakang sandarannya lepas dan berbahaya untuk diduduki.',
        'status' => 'Sedang Dikerjakan', 'tanggal' => '2026-07-30 13:45',
        'lampiran' => ['kursi_rusak.jpg'],
        'komentar' => [
            ['admin' => 'Budi Santoso', 'isi' => 'Sudah dicek, menunggu suku cadang.', 'tanggal' => '31/07/2026 09:00'],
            ['admin' => 'Budi Santoso', 'isi' => 'Perbaikan dimulai besok pagi.', 'tanggal' => '02/08/2026 08:00'],
        ],
    ],
    1040 => [
        'id' => 1040, 'barang' => 'Wastafel toilet lantai 2 bocor', 'kategori' => 'Sanitasi',
        'pelapor' => 'Fajar Nugroho', 'jumlah' => 1, 'lokasi' => 'Gedung C, Lt. 2, Toilet Pria',
        'isi' => 'Pipa di bawah wastafel bocor, air menggenang di lantai.',
        'status' => 'Selesai', 'tanggal' => '2026-07-26 07:30',
        'lampiran' => ['wastafel_1.jpg', 'wastafel_2.jpg', 'nota_perbaikan.pdf'],
        'komentar' => [
            ['admin' => 'Budi Santoso', 'isi' => 'Sudah diperbaiki dan dicek ulang, aman.', 'tanggal' => '27/07/2026 11:15'],
        ],
    ],
    1039 => [
        'id' => 1039, 'barang' => 'Wifi lab komputer tidak stabil', 'kategori' => 'Jaringan & IT',
        'pelapor' => 'Aulia Rahma', 'jumlah' => 1, 'lokasi' => 'Gedung D, Lab Komputer 1',
        'isi' => 'Koneksi wifi putus-putus terutama saat jam praktikum siang.',
        'status' => 'Sedang Dikerjakan', 'tanggal' => '2026-07-29 11:05',
        'lampiran' => [],
        'komentar' => [
            ['admin' => 'Budi Santoso', 'isi' => 'Sedang koordinasi dengan tim IT pusat.', 'tanggal' => '30/07/2026 14:40'],
        ],
    ],
    1038 => [
        'id' => 1038, 'barang' => 'Plafon ruang rapat retak', 'kategori' => 'Bangunan',
        'pelapor' => 'Rangga Prasetyo', 'jumlah' => 1, 'lokasi' => 'Gedung A, Lt. 3, R. Rapat',
        'isi' => 'Terdapat retakan cukup panjang di plafon, dikhawatirkan bisa runtuh.',
        'status' => 'Belum Dikerjakan', 'tanggal' => '2026-08-02 16:20',
        'lampiran' => ['plafon_retak.jpg'],
        'komentar' => [],
    ],
    1037 => [
        'id' => 1037, 'barang' => 'Proyektor R.2C gambar buram', 'kategori' => 'Elektronik',
        'pelapor' => 'Sinta Wulandari', 'jumlah' => 1, 'lokasi' => 'Gedung B, Lt. 2, R.2C',
        'isi' => 'Gambar proyektor buram walau sudah diatur fokusnya.',
        'status' => 'Selesai', 'tanggal' => '2026-07-20 09:00',
        'lampiran' => ['proyektor.jpg'],
        'komentar' => [
            ['admin' => 'Budi Santoso', 'isi' => 'Lensa dibersihkan, sudah normal kembali.', 'tanggal' => '21/07/2026 10:00'],
        ],
    ],
];

// ---------- FILTER & PENCARIAN ----------
$q            = trim($_GET['q'] ?? '');
$statusFilter = $_GET['status'] ?? 'Semua';
$statusOptions = ['Semua', 'Belum Dikerjakan', 'Sedang Dikerjakan', 'Selesai'];

$filtered = array_filter($aduanData, function ($a) use ($q, $statusFilter) {
    $matchQ = $q === '' || stripos($a['barang'], $q) !== false || stripos($a['pelapor'], $q) !== false || (string)$a['id'] === $q;
    $matchS = $statusFilter === 'Semua' || $a['status'] === $statusFilter;
    return $matchQ && $matchS;
});
usort($filtered, fn($a, $b) => strcmp($b['tanggal'], $a['tanggal']));

$belumCount   = count(array_filter($aduanData, fn($a) => $a['status'] === 'Belum Dikerjakan'));
$prosesCount  = count(array_filter($aduanData, fn($a) => $a['status'] === 'Sedang Dikerjakan'));
$selesaiCount = count(array_filter($aduanData, fn($a) => $a['status'] === 'Selesai'));

// ---------- DETAIL UNTUK MODAL ----------
$detailId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$detail   = $detailId && isset($aduanData[$detailId]) ? $aduanData[$detailId] : null;

$backQuery = http_build_query(['q' => $q, 'status' => $statusFilter]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Aduan — Sarpras Panel</title>
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

        <div class="nav-label">Menu Utama</div>
        <a href="dashboard.php" class="nav-item"><?= icon('grid') ?><span class="label">Dashboard</span></a>
        <a href="data_aduan.php" class="nav-item active"><?= icon('clipboard') ?><span class="label">Data Aduan</span>
            <?php if ($belumCount > 0): ?><span class="badge"><?= $belumCount ?></span><?php endif; ?>
        </a>
        <a href="kategori_barang.php" class="nav-item"><?= icon('tags') ?><span class="label">Kategori barang</span></a>
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
                $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
                echo $hari[date('w')] . ', ' . date('j') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');
            ?></div>
            <div class="topbar-right">
                <button class="bell-btn" aria-label="Notifikasi">
                    <?= icon('bell', 16) ?>
                    <?php if ($belumCount > 0): ?><span class="bell-dot"></span><?php endif; ?>
                </button>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div class="avatar-circle">BS</div>
                    <div>
                        <div class="admin-name">Budi Santoso</div>
                        <div class="admin-role">Administrator</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="content">
            <div class="section-header">
                <h1 class="section-title">Data Aduan Sarpras</h1>
                <div class="stats-summary">
                    <div class="stat-pill">Belum: <strong><?= $belumCount ?></strong></div>
                    <div class="stat-pill">Proses: <strong><?= $prosesCount ?></strong></div>
                    <div class="stat-pill">Selesai: <strong><?= $selesaiCount ?></strong></div>
                </div>
            </div>

            <form method="get" class="toolbar">
                <div class="search-box">
                    <?= icon('search', 14, '#6C7570') ?>
                    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari barang, ID, atau pelapor...">
                </div>
                <div class="filter-group">
                    <?php foreach ($statusOptions as $opt): ?>
                        <button type="submit" name="status" value="<?= htmlspecialchars($opt) ?>"
                                class="filter-chip <?= $statusFilter === $opt ? 'active' : '' ?>">
                            <?= htmlspecialchars($opt) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </form>

            <div class="table-card">
                <table>
                    <thead>
                    <tr>
                        <th style="width:60px">ID</th>
                        <th>Barang & Lokasi</th>
                        <th style="width:120px">Kategori</th>
                        <th style="width:140px">Pelapor</th>
                        <th style="width:130px">Status</th>
                        <th style="width:60px">Info</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($filtered)): ?>
                        <tr><td colspan="6" style="text-align:center;color:var(--sub);padding:24px;">Tidak ada aduan yang ditemukan.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($filtered as $a): ?>
                        <tr class="clickable" onclick="window.location='aduan.php?<?= http_build_query(['q' => $q, 'status' => $statusFilter, 'id' => $a['id']]) ?>'">
                            <td class="mono">#<?= $a['id'] ?></td>
                            <td>
                                <div class="row-title"><?= htmlspecialchars($a['barang']) ?></div>
                                <div class="row-sub"><?= htmlspecialchars($a['lokasi']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($a['kategori']) ?></td>
                            <td><?= htmlspecialchars($a['pelapor']) ?></td>
                            <td><?= statusPill($a['status']) ?></td>
                            <td>
                                <div class="meta-icons">
                                    <span title="Lampiran"><?= icon('paperclip', 11) ?> <?= count($a['lampiran']) ?></span>
                                    <span title="Komentar"><?= icon('message', 11) ?> <?= count($a['komentar']) ?></span>
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

<?php if ($detail): ?>
<div class="modal-backdrop" onclick="if(event.target===this) window.location='aduan.php?<?= $backQuery ?>'">
    <div class="modal-box">
        <div class="modal-head">
            <div>
                <div class="modal-eyebrow">ADUAN #<?= $detail['id'] ?> &middot; <?= $detail['tanggal'] ?></div>
                <div class="modal-title"><?= htmlspecialchars($detail['barang']) ?></div>
            </div>
            <a class="modal-close" href="aduan.php?<?= $backQuery ?>"><?= icon('x', 18) ?></a>
        </div>
        <div class="modal-body">
            <div class="meta-grid">
                <div class="meta-item"><?= icon('package', 13) ?> Kategori: <strong><?= htmlspecialchars($detail['kategori']) ?> (<?= $detail['jumlah'] ?> unit)</strong></div>
                <div class="meta-item"><?= icon('pin', 13) ?> Lokasi: <strong><?= htmlspecialchars($detail['lokasi']) ?></strong></div>
                <div class="meta-item"><?= icon('users', 13) ?> Pelapor: <strong><?= htmlspecialchars($detail['pelapor']) ?></strong></div>
            </div>

            <div>
                <div class="field-label">Isi Keluhan</div>
                <p class="keluhan-box"><?= nl2br(htmlspecialchars($detail['isi'])) ?></p>
            </div>

            <form method="post" action="update_status.php">
                <input type="hidden" name="id_aduan" value="<?= $detail['id'] ?>">
                <input type="hidden" name="redirect_q" value="<?= htmlspecialchars($q) ?>">
                <input type="hidden" name="redirect_status" value="<?= htmlspecialchars($statusFilter) ?>">
                <div class="field-label">Status Pengerjaan</div>
                <div class="status-form">
                    <?php foreach (['Belum Dikerjakan', 'Sedang Dikerjakan', 'Selesai'] as $s): ?>
                        <button type="submit" name="status" value="<?= $s ?>"
                                class="status-option <?= $detail['status'] === $s ? 'selected' : '' ?>">
                            <?= $s ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </form>

            <div>
                <div class="field-label">Lampiran (<?= count($detail['lampiran']) ?>)</div>
                <?php if (empty($detail['lampiran'])): ?>
                    <div style="font-size:12px;color:var(--sub);font-style:italic;">Tidak ada lampiran.</div>
                <?php else: ?>
                    <?php foreach ($detail['lampiran'] as $f): ?>
                        <span class="lampiran-chip"><?= icon('paperclip', 11) ?> <?= htmlspecialchars($f) ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div>
                <div class="field-label">Komentar Admin (<?= count($detail['komentar']) ?>)</div>
                <?php foreach ($detail['komentar'] as $k): ?>
                    <div class="komentar-item">
                        <div class="komentar-admin"><?= htmlspecialchars($k['admin']) ?>
                            <span class="komentar-time">&middot; <?= $k['tanggal'] ?></span>
                        </div>
                        <div class="komentar-text"><?= htmlspecialchars($k['isi']) ?></div>
                    </div>
                <?php endforeach; ?>

                <form method="post" action="tambah_komentar.php" class="komentar-form" style="margin-top:10px;">
                    <input type="hidden" name="id_aduan" value="<?= $detail['id'] ?>">
                    <input type="hidden" name="redirect_q" value="<?= htmlspecialchars($q) ?>">
                    <input type="hidden" name="redirect_status" value="<?= htmlspecialchars($statusFilter) ?>">
                    <textarea name="komentar" rows="2" placeholder="Tulis catatan/respon admin..." required></textarea>
                    <div style="display:flex; justify-content:flex-end; margin-top:6px;">
                        <button type="submit" class="btn btn-primary">Kirim Komentar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
</body>
</html>