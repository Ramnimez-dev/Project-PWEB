<?php

session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['nama']) || ($_SESSION['role'] ?? '') !== 'user') {
    header('Location: ../auth/login.php');
    exit;
}

$userName = $_SESSION['nama'];
$userId   = $_SESSION['id_user'];

function icon(string $name, int $size = 17, string $color = 'currentColor'): string
{
    $stroke = "stroke=\"$color\" stroke-width=\"2\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"";
    $paths = [
        'grid'      => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
        'history'   => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/><path d="M12 7v5l4 2"/>',
        'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/>',
        'bell'      => '<path d="M6 8a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6Z"/><path d="M10 20a2 2 0 0 0 4 0"/>',
        'chevron'   => '<path d="m6 9 6 6 6-6"/>',
        'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
        'x'         => '<path d="M18 6 6 18M6 6l12 12"/>',
        'pin'       => '<path d="M12 21s7-6.5 7-11.5A7 7 0 0 0 5 9.5C5 14.5 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.2"/>',
        'package'   => '<path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/><path d="m20.7 7-8.7-5-8.7 5 8.7 5 8.7-5Z"/>',
        'paperclip' => '<path d="M17.5 6.5 8.6 15.4a3 3 0 1 0 4.2 4.2l8-8a5 5 0 0 0-7-7l-8.4 8.3"/>',
        'message'   => '<path d="M21 12a8 8 0 1 1-3.2-6.4L21 4l-1 4.6A7.9 7.9 0 0 1 21 12Z"/>',
        'send'      => '<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>',
        'plus'      => '<path d="M12 5v14M5 12h14"/>',
        'check'     => '<path d="m20 6-11 11-5-5"/>',
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

// ---------- PROSES KIRIM BALASAN USER (INSERT KE komentar_aduan) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = $_POST['action'] ?? '';
    $idAduan   = (int)($_POST['id_aduan'] ?? 0);
    $qRedirect = $_POST['redirect_q'] ?? '';
    $statusRedirect = $_POST['redirect_status'] ?? 'Semua';

    $balasanBerhasil = false;

    if ($action === 'tambah_balasan' && $idAduan > 0) {
        $isiBalasan = trim($_POST['balasan'] ?? '');
        if ($isiBalasan !== '') {
            // Pastikan aduan ini benar milik user yang sedang login
            $cek = mysqli_prepare($koneksi, "SELECT id_aduan FROM aduan WHERE id_aduan = ? AND user_id = ?");
            mysqli_stmt_bind_param($cek, 'ii', $idAduan, $userId);
            mysqli_stmt_execute($cek);
            $milikSaya = mysqli_stmt_get_result($cek)->fetch_assoc();

            if ($milikSaya) {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO komentar_aduan (aduan_id, admin_id, komentar, tanggal) VALUES (?, ?, ?, NOW())");
                mysqli_stmt_bind_param($stmt, 'iis', $idAduan, $userId, $isiBalasan);
                if (mysqli_stmt_execute($stmt)) {
                    $balasanBerhasil = true;
                }
            }
        }
    }

    $queryParams = ['q' => $qRedirect, 'status' => $statusRedirect, 'id' => $idAduan];
    if ($balasanBerhasil) {
        $queryParams['balasan_sukses'] = 1;
    }
    header('Location: riwayat.php?' . http_build_query($queryParams));
    exit;
}

// ---------- FILTER & PENCARIAN ----------
$q             = trim($_GET['q'] ?? '');
$statusFilter  = $_GET['status'] ?? 'Semua';
$statusOptions = ['Semua', 'Belum Dikerjakan', 'Sedang Dikerjakan', 'Selesai'];
$balasanSukses = isset($_GET['balasan_sukses']);

$sql = "
    SELECT a.id_aduan AS id, a.barang_aduan AS barang, a.lokasi, a.status, a.tanggal,
           k.nama_kategori AS kategori,
           (SELECT COUNT(*) FROM lampiran l WHERE l.aduan_id = a.id_aduan) AS jml_lampiran,
           (SELECT COUNT(*) FROM komentar_aduan c WHERE c.aduan_id = a.id_aduan) AS jml_komentar
    FROM aduan a
    LEFT JOIN kategori_barang k ON k.id_kategori = a.kategori_id
    WHERE a.user_id = ?
";
$types  = 'i';
$params = [$userId];

if ($q !== '') {
    $sql .= " AND (a.barang_aduan LIKE ? OR a.id_aduan = ?)";
    $types .= 'si';
    $params[] = "%$q%";
    $params[] = is_numeric($q) ? (int)$q : 0;
}
if ($statusFilter !== 'Semua' && in_array($statusFilter, $statusOptions, true)) {
    $sql .= " AND a.status = ?";
    $types .= 's';
    $params[] = $statusFilter;
}
$sql .= " ORDER BY a.tanggal DESC";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$filtered = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// ---------- HITUNG STATUS (dari seluruh aduan milik user ini) ----------
$counts = ['Belum Dikerjakan' => 0, 'Sedang Dikerjakan' => 0, 'Selesai' => 0];
$stmtC  = mysqli_prepare($koneksi, "SELECT status, COUNT(*) AS jumlah FROM aduan WHERE user_id = ? GROUP BY status");
mysqli_stmt_bind_param($stmtC, 'i', $userId);
mysqli_stmt_execute($stmtC);
$hasilHitung = mysqli_stmt_get_result($stmtC);
while ($row = mysqli_fetch_assoc($hasilHitung)) {
    if (isset($counts[$row['status']])) {
        $counts[$row['status']] = (int)$row['jumlah'];
    }
}
$totalSaya = array_sum($counts);

// ---------- DETAIL UNTUK MODAL ----------
$detail = null;
if (!empty($_GET['id'])) {
    $detailId = (int)$_GET['id'];

    $stmtD = mysqli_prepare($koneksi, "
        SELECT a.id_aduan AS id, a.barang_aduan AS barang, a.jumlah_barang AS jumlah,
               a.lokasi, a.isi_keluhan AS isi, a.status, a.tanggal,
               k.nama_kategori AS kategori
        FROM aduan a
        LEFT JOIN kategori_barang k ON k.id_kategori = a.kategori_id
        WHERE a.id_aduan = ? AND a.user_id = ?
    ");
    mysqli_stmt_bind_param($stmtD, 'ii', $detailId, $userId);
    mysqli_stmt_execute($stmtD);
    $detail = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtD));

    if ($detail) {
        // Lampiran
        $stmtL = mysqli_prepare($koneksi, "SELECT nama_file FROM lampiran WHERE aduan_id = ? ORDER BY tanggal");
        mysqli_stmt_bind_param($stmtL, 'i', $detailId);
        mysqli_stmt_execute($stmtL);
        $detail['lampiran'] = array_column(mysqli_fetch_all(mysqli_stmt_get_result($stmtL), MYSQLI_ASSOC), 'nama_file');

        // Komentar (admin_id bisa berasal dari admin ATAU user, dibedakan lewat users.role)
        $stmtK = mysqli_prepare($koneksi, "
            SELECT u.nama AS dari, u.role AS peran, c.komentar AS isi, c.tanggal
            FROM komentar_aduan c
            LEFT JOIN users u ON u.id_user = c.admin_id
            WHERE c.aduan_id = ?
            ORDER BY c.tanggal
        ");
        mysqli_stmt_bind_param($stmtK, 'i', $detailId);
        mysqli_stmt_execute($stmtK);
        $detail['komentar'] = mysqli_fetch_all(mysqli_stmt_get_result($stmtK), MYSQLI_ASSOC);

        // Progres penanganan diturunkan dari kolom status (skema DB belum menyimpan tanggal per-tahap)
        $urutan = ['Belum Dikerjakan' => 0, 'Sedang Dikerjakan' => 1, 'Selesai' => 2];
        $idx    = $urutan[$detail['status']] ?? 0;
        $detail['riwayat'] = [
            ['label' => 'Aduan diajukan', 'tanggal' => date('d/m/Y H:i', strtotime($detail['tanggal'])), 'done' => true],
            ['label' => 'Ditinjau admin', 'tanggal' => null, 'done' => $idx >= 1],
            ['label' => 'Dikerjakan',     'tanggal' => null, 'done' => $idx >= 1],
            ['label' => 'Selesai',        'tanggal' => null, 'done' => $idx >= 2],
        ];
    }
}
$backQuery = http_build_query(['q' => $q, 'status' => $statusFilter]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Aduan — Sarpras</title>
<link rel="stylesheet" href="style.css">
<style>
.lampiran-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(72px, 72px));
    gap: 8px;
}
.lampiran-thumb {
    display: block;
    width: 72px;
    height: 72px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
}
.lampiran-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.15s ease;
}
.lampiran-thumb:hover img {
    transform: scale(1.05);
}
.lampiran-thumb-file {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    color: #9ca3af;
    font-size: 10px;
    font-weight: 600;
}
.toast-notif {
    position: fixed;
    top: 20px;
    right: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid #bbf7d0;
    color: #16a34a;
    font-size: 13px;
    font-weight: 600;
    padding: 12px 18px;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    z-index: 9999;
    transition: opacity 0.3s ease;
}
</style>
</head>
<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">SP</div>
            <div>
                <div class="brand-name">SARPRAS</div>
                <div class="brand-sub">PANEL PELAPOR</div>
            </div>
        </div>

        <div class="nav-label">Menu</div>
        <a href="dashboard.php" class="nav-item"><?= icon('grid') ?><span class="label">Dashboard</span></a>
        <a href="riwayat.php" class="nav-item active"><?= icon('history') ?><span class="label">Riwayat Aduan</span></a>

        <div style="flex:1"></div>
        <div class="sidebar-footer">
            <a href="../auth/logout.php" onclick="return confirm('Yakin Ingin Logout?')" class="nav-item"><?= icon('logout') ?><span class="label">Keluar</span></a>
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
                <button class="bell-btn" aria-label="Notifikasi"><?= icon('bell', 18) ?></button>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div class="avatar-circle"><?php
                        $potongNama = explode(' ', trim($userName));
                        echo strtoupper(substr($potongNama[0], 0, 1) . substr(end($potongNama), 0, 1));
                    ?></div>
                    <div>
                        <div class="admin-name"><?= htmlspecialchars($userName) ?></div>
                        <div class="admin-role">Pelapor</div>
                    </div>
                    <?= icon('chevron', 14, '#6B756C') ?>
                </div>
            </div>
        </header>

        <div class="content">
            <div class="section-header">
                <div>
                    <div class="eyebrow"><?= count($filtered) ?> aduan</div>
                    <h1 class="section-title">Riwayat Aduan</h1>
                    <div class="section-desc">Semua aduan yang pernah kamu ajukan, beserta status terkininya.</div>
                </div>
                <a href="dashboard.php" class="btn btn-primary"><?= icon('plus', 15) ?> Ajukan aduan baru</a>
            </div>

            <div class="stat-strip">
                <div class="stat-pill dot-all"><span class="dot"></span> Total <strong><?= $totalSaya ?></strong></div>
                <div class="stat-pill dot-red"><span class="dot"></span> Belum dikerjakan <strong><?= $counts['Belum Dikerjakan'] ?></strong></div>
                <div class="stat-pill dot-amber"><span class="dot"></span> Sedang dikerjakan <strong><?= $counts['Sedang Dikerjakan'] ?></strong></div>
                <div class="stat-pill dot-green"><span class="dot"></span> Selesai <strong><?= $counts['Selesai'] ?></strong></div>
            </div>

            <form method="get" class="toolbar">
                <div class="search-box">
                    <?= icon('search', 15, '#6B756C') ?>
                    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama barang atau ID aduan...">
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

            <?php if (empty($filtered)): ?>
                <div class="empty-state">
                    <div><?= icon('history', 34) ?></div>
                    <div>Belum ada aduan yang cocok dengan pencarian ini.</div>
                </div>
            <?php else: ?>
                <?php foreach ($filtered as $a): ?>
                    <?php $markClass = match ($a['status']) { 'Belum Dikerjakan' => 'belum', 'Sedang Dikerjakan' => 'proses', default => 'selesai' }; ?>
                    <a class="timeline-card" href="riwayat.php?<?= http_build_query(['q' => $q, 'status' => $statusFilter, 'id' => $a['id']]) ?>">
                        <div class="timeline-mark <?= $markClass ?>"></div>
                        <div class="timeline-body">
                            <div class="timeline-top">
                                <div>
                                    <div class="timeline-id">#<?= $a['id'] ?></div>
                                    <div class="timeline-title"><?= htmlspecialchars($a['barang']) ?></div>
                                </div>
                                <?= statusPill($a['status']) ?>
                            </div>
                            <div class="timeline-meta">
                                <span><?= icon('package', 12) ?> <?= htmlspecialchars($a['kategori'] ?? '-') ?></span>
                                <span><?= icon('pin', 12) ?> <?= htmlspecialchars($a['lokasi']) ?></span>
                                <span><?= icon('paperclip', 12) ?> <?= (int)$a['jml_lampiran'] ?></span>
                                <span><?= icon('message', 12) ?> <?= (int)$a['jml_komentar'] ?></span>
                                <span style="margin-left:auto;font-family:'IBM Plex Mono',monospace;"><?= date('d/m/Y', strtotime($a['tanggal'])) ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php if ($balasanSukses): ?>
<div class="toast-notif" id="toastNotif">
    <?= icon('check', 16, '#16a34a') ?>
    <span>Balasan berhasil dikirim!</span>
</div>
<?php endif; ?>

<?php if ($detail): ?>
<div class="modal-backdrop" onclick="if(event.target===this) window.location='riwayat.php?<?= $backQuery ?>'">
    <div class="modal-box">
        <div class="modal-head">
            <div>
                <div class="modal-eyebrow">ADUAN #<?= $detail['id'] ?></div>
                <div class="modal-title"><?= htmlspecialchars($detail['barang']) ?></div>
            </div>
            <a class="modal-close" href="riwayat.php?<?= $backQuery ?>"><?= icon('x', 20) ?></a>
        </div>
        <div class="modal-body">

            <?php if ($balasanSukses): ?>
            <div class="alert-success">
                <?= icon('check', 14, '#16a34a') ?>
                <span>Balasan berhasil dikirim!</span>
            </div>
            <?php endif; ?>

            <div class="meta-line">
                <div class="item"><?= icon('package', 14) ?> <?= htmlspecialchars($detail['kategori'] ?? '-') ?> &middot; <?= (int)$detail['jumlah'] ?> unit</div>
                <div class="item"><?= icon('pin', 14) ?> <?= htmlspecialchars($detail['lokasi']) ?></div>
                <div class="item"><?= statusPill($detail['status']) ?></div>
            </div>

            <div>
                <div class="field-label">Progres penanganan</div>
                <div class="progress-track">
                    <?php foreach ($detail['riwayat'] as $step): ?>
                        <div class="progress-step <?= $step['done'] ? 'done' : '' ?>">
                            <div class="progress-circle"><?= $step['done'] ? icon('check', 12) : '' ?></div>
                            <div class="progress-label"><?= htmlspecialchars($step['label']) ?></div>
                            <?php if ($step['tanggal']): ?><div class="progress-time"><?= $step['tanggal'] ?></div><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <div class="field-label">Isi keluhan</div>
                <p class="keluhan-box"><?= nl2br(htmlspecialchars($detail['isi'])) ?></p>
            </div>

            <div>
                <div class="field-label"><?= icon('paperclip', 13) ?> Lampiran (<?= count($detail['lampiran']) ?>)</div>
                <?php if (empty($detail['lampiran'])): ?>
                    <div style="font-size:13px;color:var(--sub);font-style:italic;">Tidak ada lampiran.</div>
                <?php else: ?>
                    <div class="lampiran-grid">
                        <?php foreach ($detail['lampiran'] as $f):
                            $ext      = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                            $isGambar = in_array($ext, ['jpg', 'jpeg', 'png'], true);
                            $urlFile  = '../uploads/lampiran/' . rawurlencode($f);
                        ?>
                            <a href="<?= $urlFile ?>" target="_blank" rel="noopener" class="lampiran-thumb">
                                <?php if ($isGambar): ?>
                                    <img src="<?= $urlFile ?>" alt="<?= htmlspecialchars($f) ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="lampiran-thumb-file">
                                        <?= icon('paperclip', 22, '#9ca3af') ?>
                                        <span>PDF</span>
                                    </div>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <div class="field-label"><?= icon('message', 13) ?> Percakapan (<?= count($detail['komentar']) ?>)</div>
                <?php if (empty($detail['komentar'])): ?>
                    <div style="font-size:13px;color:var(--sub);font-style:italic;margin-bottom:10px;">Belum ada balasan dari admin.</div>
                <?php endif; ?>
                <?php foreach ($detail['komentar'] as $k): ?>
                    <?php
                        $initial = strtoupper(substr($k['dari'] ?? 'A', 0, 1));
                        $peran   = $k['peran'] ?? 'admin';
                    ?>
                    <div class="komentar-item">
                        <div class="komentar-avatar <?= $peran ?>"><?= $initial ?></div>
                        <div class="komentar-bubble">
                            <div class="komentar-head">
                                <span class="komentar-nama"><?= htmlspecialchars($k['dari'] ?? 'Admin') ?></span>
                                <?php if ($peran === 'admin'): ?><span class="komentar-tag">Admin</span><?php endif; ?>
                                <span class="komentar-time"><?= date('d/m/Y H:i', strtotime($k['tanggal'])) ?></span>
                            </div>
                            <div class="komentar-text"><?= htmlspecialchars($k['isi']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <form method="post" action="riwayat.php" class="komentar-form">
                    <input type="hidden" name="action" value="tambah_balasan">
                    <input type="hidden" name="id_aduan" value="<?= $detail['id'] ?>">
                    <input type="hidden" name="redirect_q" value="<?= htmlspecialchars($q) ?>">
                    <input type="hidden" name="redirect_status" value="<?= htmlspecialchars($statusFilter) ?>">
                    <textarea name="balasan" rows="2" placeholder="Tulis balasan atau pertanyaan tambahan..." required></textarea>
                    <div class="komentar-submit-row">
                        <button type="submit" class="btn btn-primary"><?= icon('send', 14) ?> Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($balasanSukses): ?>
<script>
    (function () {
        var toast = document.getElementById('toastNotif');
        setTimeout(function () {
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(function () { toast.remove(); }, 300);
            }
        }, 3000);
        var url = new URL(window.location);
        url.searchParams.delete('balasan_sukses');
        window.history.replaceState({}, '', url);
    })();
</script>
<?php endif; ?>
</body>
</html>