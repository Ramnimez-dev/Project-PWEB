<?php

session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['nama']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$adminName  = $_SESSION['nama'];
$adminId    = $_SESSION['id_user'];
$potongNama = explode(' ', trim($adminName));
$inisial    = strtoupper(substr($potongNama[0], 0, 1) . substr(end($potongNama), 0, 1));

// ---------- PROSES AKSI FORM (UPDATE STATUS & TAMBAH KOMENTAR) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action         = $_POST['action'] ?? '';
    $idAduan        = (int)($_POST['id_aduan'] ?? 0);
    $qRedirect      = $_POST['redirect_q'] ?? '';
    $statusRedirect = $_POST['redirect_status'] ?? 'Semua';

    $statusBerhasil   = false;
    $komentarBerhasil = false;

    if ($idAduan > 0) {
        // Aksi 1: Update Status
        if ($action === 'update_status') {
            $newStatus = $_POST['status'] ?? '';
            if (in_array($newStatus, ['Belum Dikerjakan', 'Sedang Dikerjakan', 'Selesai'], true)) {
                $stmt = mysqli_prepare($koneksi, "UPDATE aduan SET status = ? WHERE id_aduan = ?");
                mysqli_stmt_bind_param($stmt, 'si', $newStatus, $idAduan);
                if (mysqli_stmt_execute($stmt)) {
                    $statusBerhasil = true;
                }
            }
        }

        // Aksi 2: Tambah Komentar
        if ($action === 'tambah_komentar') {
            $isiKomentar = trim($_POST['komentar'] ?? '');
            if ($isiKomentar !== '') {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO komentar_aduan (aduan_id, admin_id, komentar, tanggal) VALUES (?, ?, ?, NOW())");
                mysqli_stmt_bind_param($stmt, 'iis', $idAduan, $adminId, $isiKomentar);
                if (mysqli_stmt_execute($stmt)) {
                    $komentarBerhasil = true;
                }
            }
        }
    }

    // Redirect kembali ke halaman ini agar pop-up tetap terbuka & data diperbarui
    $queryParams = ['q' => $qRedirect, 'status' => $statusRedirect, 'id' => $idAduan];
    if ($komentarBerhasil) {
        $queryParams['komentar_sukses'] = 1;
    }
    if ($statusBerhasil) {
        $queryParams['status_sukses'] = 1;
    }
    $queryStr = http_build_query($queryParams);
    header("Location: data_aduan.php?$queryStr");
    exit;
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

// ---------- FILTER & PENCARIAN (dibangun langsung sebagai query SQL) ----------
$q             = trim($_GET['q'] ?? '');
$statusFilter  = $_GET['status'] ?? 'Semua';
$statusOptions = ['Semua', 'Belum Dikerjakan', 'Sedang Dikerjakan', 'Selesai'];

// Flag notifikasi dari redirect setelah aksi form
$komentarSukses = isset($_GET['komentar_sukses']);
$statusSukses   = isset($_GET['status_sukses']);

$sql = "
    SELECT a.id_aduan AS id, a.barang_aduan AS barang, a.lokasi, a.status, a.tanggal,
           k.nama_kategori AS kategori, u.nama AS pelapor,
           (SELECT COUNT(*) FROM lampiran l WHERE l.aduan_id = a.id_aduan) AS jml_lampiran,
           (SELECT COUNT(*) FROM komentar_aduan c WHERE c.aduan_id = a.id_aduan) AS jml_komentar
    FROM aduan a
    LEFT JOIN kategori_barang k ON k.id_kategori = a.kategori_id
    LEFT JOIN users u ON u.id_user = a.user_id
    WHERE 1=1
";
$types  = '';
$params = [];

if ($q !== '') {
    $sql .= " AND (a.barang_aduan LIKE ? OR u.nama LIKE ? OR a.id_aduan = ?)";
    $like = "%$q%";
    $types .= 'ssi';
    $params[] = $like;
    $params[] = $like;
    $params[] = is_numeric($q) ? (int)$q : 0;
}
if ($statusFilter !== 'Semua' && in_array($statusFilter, $statusOptions, true)) {
    $sql .= " AND a.status = ?";
    $types .= 's';
    $params[] = $statusFilter;
}
$sql .= " ORDER BY a.tanggal DESC";

$stmt = mysqli_prepare($koneksi, $sql);
if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$filtered = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// ---------- HITUNG STATUS (dari seluruh data, bukan hasil filter) ----------
$belumCount = $prosesCount = $selesaiCount = 0;
$hasilHitung = mysqli_query($koneksi, "SELECT status, COUNT(*) AS jumlah FROM aduan GROUP BY status");
while ($row = mysqli_fetch_assoc($hasilHitung)) {
    if ($row['status'] === 'Belum Dikerjakan') $belumCount = (int)$row['jumlah'];
    if ($row['status'] === 'Sedang Dikerjakan') $prosesCount = (int)$row['jumlah'];
    if ($row['status'] === 'Selesai') $selesaiCount = (int)$row['jumlah'];
}

// ---------- DETAIL UNTUK MODAL ----------
$detail = null;
if (!empty($_GET['id'])) {
    $detailId = (int)$_GET['id'];

    $stmtD = mysqli_prepare($koneksi, "
        SELECT a.id_aduan AS id, a.barang_aduan AS barang, a.jumlah_barang AS jumlah,
               a.lokasi, a.isi_keluhan AS isi, a.status, a.tanggal,
               k.nama_kategori AS kategori, u.nama AS pelapor
        FROM aduan a
        LEFT JOIN kategori_barang k ON k.id_kategori = a.kategori_id
        LEFT JOIN users u ON u.id_user = a.user_id
        WHERE a.id_aduan = ?
    ");
    mysqli_stmt_bind_param($stmtD, 'i', $detailId);
    mysqli_stmt_execute($stmtD);
    $detail = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtD));

    if ($detail) {
        // Lampiran -> array nama file saja (biar cocok dipakai foreach seperti sebelumnya)
        $stmtL = mysqli_prepare($koneksi, "SELECT nama_file FROM lampiran WHERE aduan_id = ? ORDER BY tanggal");
        mysqli_stmt_bind_param($stmtL, 'i', $detailId);
        mysqli_stmt_execute($stmtL);
        $detail['lampiran'] = array_column(mysqli_fetch_all(mysqli_stmt_get_result($stmtL), MYSQLI_ASSOC), 'nama_file');

        // Komentar -> alias kolom biar cocok dengan key yang dipakai template ('admin', 'isi', 'tanggal')
        $stmtK = mysqli_prepare($koneksi, "
            SELECT u.nama AS admin, c.komentar AS isi, c.tanggal
            FROM komentar_aduan c
            LEFT JOIN users u ON u.id_user = c.admin_id
            WHERE c.aduan_id = ?
            ORDER BY c.tanggal
        ");
        mysqli_stmt_bind_param($stmtK, 'i', $detailId);
        mysqli_stmt_execute($stmtK);
        $detail['komentar'] = mysqli_fetch_all(mysqli_stmt_get_result($stmtK), MYSQLI_ASSOC);
    }
}

$backQuery = http_build_query(['q' => $q, 'status' => $statusFilter]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Aduan — SarPras</title>
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

        <div class="nav-label">Menu Utama</div>
        <a href="dashboard.php" class="nav-item"><span class="label">Dashboard</span></a>
        <a href="data_aduan.php" class="nav-item active"><span class="label">Data Aduan</span>
            <?php if ($belumCount > 0): ?><span class="badge"><?= $belumCount ?></span><?php endif; ?>
        </a>
        <a href="kategori_barang.php" class="nav-item"><span class="label">Kategori barang</span></a>
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
                $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
                echo $hari[date('w')] . ', ' . date('j') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');
            ?></div>
            <div class="topbar-right">
                <button class="bell-btn" aria-label="Notifikasi">
                    <?php if ($belumCount > 0): ?><span class="bell-dot"></span><?php endif; ?>
                </button>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div class="avatar-circle"><?= $inisial ?></div>
                    <div>
                        <div class="admin-name"><?= htmlspecialchars($adminName) ?></div>
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
                        <tr class="clickable" onclick="window.location='data_aduan.php?<?= http_build_query(['q' => $q, 'status' => $statusFilter, 'id' => $a['id']]) ?>'">
                            <td class="mono">#<?= $a['id'] ?></td>
                            <td>
                                <div class="row-title"><?= htmlspecialchars($a['barang']) ?></div>
                                <div class="row-sub"><?= htmlspecialchars($a['lokasi']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($a['kategori'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($a['pelapor'] ?? '-') ?></td>
                            <td><?= statusPill($a['status']) ?></td>
                            <td>
                                <div class="meta-icons">
                                    <span title="Lampiran"><?= icon('paperclip', 11) ?> <?= (int)$a['jml_lampiran'] ?></span>
                                    <span title="Komentar"><?= icon('message', 11) ?> <?= (int)$a['jml_komentar'] ?></span>
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

<!-- ================= TOAST NOTIFIKASI GLOBAL ================= -->
<?php if ($komentarSukses || $statusSukses): ?>
<div class="toast-notif" id="toastNotif">
    <?= icon('check', 16, '#16a34a') ?>
    <span><?= $komentarSukses ? 'Komentar berhasil dikirim!' : 'Status berhasil diperbarui!' ?></span>
</div>
<?php endif; ?>

<!-- ================= POP UP MODAL DETAIL ADUAN ================= -->
<?php if ($detail): ?>
<div class="modal-backdrop" onclick="if(event.target===this) window.location='data_aduan.php?<?= $backQuery ?>'">
    <div class="modal-box">
        
        <!-- Header Pop Up -->
        <div class="modal-head">
            <div>
                <div class="modal-eyebrow">ADUAN #<?= $detail['id'] ?></div>
                <h2 class="modal-title"><?= htmlspecialchars($detail['barang']) ?></h2>
                <div class="modal-meta">
                    <span><?= icon('package', 13, '#9ca3af') ?> <?= htmlspecialchars($detail['kategori'] ?? '-') ?> &middot; <?= (int)$detail['jumlah'] ?> unit</span>
                    <span><?= icon('pin', 13, '#9ca3af') ?> <?= htmlspecialchars($detail['lokasi']) ?></span>
                </div>
            </div>
            <a class="modal-close" href="data_aduan.php?<?= $backQuery ?>" aria-label="Tutup"><?= icon('x', 18, '#9ca3af') ?></a>
        </div>

        <!-- Body Pop Up -->
        <div class="modal-body">

            <?php if ($komentarSukses): ?>
            <div class="alert-success">
                <?= icon('check', 14, '#16a34a') ?>
                <span>Komentar berhasil dikirim!</span>
            </div>
            <?php endif; ?>

            <?php if ($statusSukses): ?>
            <div class="alert-success">
                <?= icon('check', 14, '#16a34a') ?>
                <span>Status berhasil diperbarui!</span>
            </div>
            <?php endif; ?>

            <!-- Isi Keluhan -->
            <div>
                <div class="field-label">ISI KELUHAN</div>
                <div class="keluhan-box"><?= nl2br(htmlspecialchars($detail['isi'])) ?></div>
            </div>

            <!-- Ubah Status (Mengirim Form ke Halaman Ini) -->
            <form method="post" action="data_aduan.php">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id_aduan" value="<?= $detail['id'] ?>">
                <input type="hidden" name="redirect_q" value="<?= htmlspecialchars($q) ?>">
                <input type="hidden" name="redirect_status" value="<?= htmlspecialchars($statusFilter) ?>">
                <div class="field-label">UBAH STATUS</div>
                <div class="status-form">
                    <?php foreach (['Belum Dikerjakan', 'Sedang Dikerjakan', 'Selesai'] as $s): ?>
                        <button type="submit" name="status" value="<?= $s ?>"
                                class="status-option <?= $detail['status'] === $s ? 'selected' : '' ?>">
                            <?= $s ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </form>

            <!-- Lampiran -->
            <div>
                <div class="field-label"><?= icon('paperclip', 13, '#9ca3af') ?> LAMPIRAN (<?= count($detail['lampiran']) ?>)</div>
                <?php if (empty($detail['lampiran'])): ?>
                    <div style="font-size:12px;color:#9ca3af;font-style:italic;">Tidak ada lampiran.</div>
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

            <!-- Komentar Admin -->
            <div>
                <div class="field-label"><?= icon('message', 13, '#9ca3af') ?> KOMENTAR ADMIN (<?= count($detail['komentar']) ?>)</div>
                
                <!-- Daftar Komentar -->
                <div class="komentar-list">
                    <?php foreach ($detail['komentar'] as $k): ?>
                        <div class="komentar-item">
                            <div class="komentar-header">
                                <span class="komentar-admin"><?= htmlspecialchars($k['admin'] ?? 'Admin') ?></span>
                                <span class="komentar-time"><?= date('Y-m-d H:i', strtotime($k['tanggal'])) ?></span>
                            </div>
                            <div class="komentar-text"><?= htmlspecialchars($k['isi']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Input Komentar (Mengirim Form ke Halaman Ini) -->
                <form method="post" action="data_aduan.php" class="komentar-form">
                    <input type="hidden" name="action" value="tambah_komentar">
                    <input type="hidden" name="id_aduan" value="<?= $detail['id'] ?>">
                    <input type="hidden" name="redirect_q" value="<?= htmlspecialchars($q) ?>">
                    <input type="hidden" name="redirect_status" value="<?= htmlspecialchars($statusFilter) ?>">
                    <textarea name="komentar" rows="2" placeholder="Tulis komentar untuk pelapor..." required></textarea>
                    <div style="display:flex; justify-content:flex-end; margin-top:8px;">
                        <button type="submit" class="btn-submit">Kirim komentar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($komentarSukses || $statusSukses): ?>
<script>
    (function () {
        // Sembunyikan toast otomatis setelah 3 detik
        var toast = document.getElementById('toastNotif');
        setTimeout(function () {
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(function () { toast.remove(); }, 300);
            }
        }, 3000);

        // Bersihkan query string flag sukses biar tidak muncul lagi saat refresh
        var url = new URL(window.location);
        url.searchParams.delete('komentar_sukses');
        url.searchParams.delete('status_sukses');
        window.history.replaceState({}, '', url);
    })();
</script>
<?php endif; ?>

</body>
</html>