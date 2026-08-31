<?php
session_start();
require '../config/koneksi.php';


function icon(string $name, int $size = 17, string $color = 'currentColor'): string
{
    $stroke = "stroke=\"$color\" stroke-width=\"2\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"";
    $paths = [
        'grid'      => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
        'history'   => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/><path d="M12 7v5l4 2"/>',
        'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/>',
        'bell'      => '<path d="M6 8a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6Z"/><path d="M10 20a2 2 0 0 0 4 0"/>',
        'chevron'   => '<path d="m6 9 6 6 6-6"/>',
        'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
        'paperclip' => '<path d="M17.5 6.5 8.6 15.4a3 3 0 1 0 4.2 4.2l8-8a5 5 0 0 0-7-7l-8.4 8.3"/>',
        'upload'    => '<path d="M12 16V4"/><path d="m6 10 6-6 6 6"/><path d="M4 20h16"/>',
        'pin'       => '<path d="M12 21s7-6.5 7-11.5A7 7 0 0 0 5 9.5C5 14.5 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.2"/>',
        'send'      => '<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>',
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

// ---------- DATA DUMMY (nanti diganti session user login + query PDO) ----------
$userName = 'Rangga Prasetyo';

$kategoriOptions = ['Elektronik', 'Furnitur', 'Sanitasi', 'Jaringan & IT', 'Bangunan'];

$aduanSaya = [
    ['id' => 1042, 'barang' => 'AC Ruang Kelas 3B mati total', 'status' => 'Belum Dikerjakan', 'tanggal' => '2026-08-01'],
    ['id' => 1038, 'barang' => 'Plafon ruang rapat retak', 'status' => 'Belum Dikerjakan', 'tanggal' => '2026-08-02'],
    ['id' => 1030, 'barang' => 'Kran taman belakang macet', 'status' => 'Sedang Dikerjakan', 'tanggal' => '2026-07-18'],
    ['id' => 1021, 'barang' => 'Lampu koridor lantai 1 mati', 'status' => 'Selesai', 'tanggal' => '2026-06-30'],
];

$counts = ['Belum Dikerjakan' => 0, 'Sedang Dikerjakan' => 0, 'Selesai' => 0];
foreach ($aduanSaya as $a) {
    $counts[$a['status']]++;
}
$totalSaya = count($aduanSaya);

// ---------- HANDLE SUBMIT FORM (dummy, nanti diganti INSERT INTO aduan) ----------
$berhasilKirim = isset($_GET['sukses']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // nanti di sini: validasi + INSERT INTO aduan (user_id, kategori_id, barang_aduan, jumlah_barang, lokasi, isi_keluhan, status, tanggal)
    // lalu simpan file lampiran ke tabel lampiran
    header('Location: user_dashboard.php?sukses=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Sarpras</title>
<link rel="stylesheet" href="style.css">
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
        <a href="dashboard.php" class="nav-item active"><?= icon('grid') ?><span class="label">Dashboard</span></a>
        <a href="riwayat.php" class="nav-item"><?= icon('history') ?><span class="label">Riwayat Aduan</span></a>

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
                    <div class="avatar-circle">RP</div>
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
                    <div class="eyebrow">Selamat datang</div>
                    <h1 class="section-title">Halo, <?= htmlspecialchars(explode(' ', $userName)[0]) ?> 👋</h1>
                    <div class="section-desc">Laporkan kerusakan sarana & prasarana di sini.</div>
                </div>
            </div>

            <?php if ($berhasilKirim): ?>
                <div class="alert alert-success"><?= icon('send', 15) ?> Aduan berhasil dikirim! Admin akan segera meninjau laporanmu.</div>
            <?php endif; ?>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="dot"></div>
                    <div class="stat-label">Total diajukan</div>
                    <div class="stat-value"><?= $totalSaya ?></div>
                </div>
                <div class="stat-card tone-red">
                    <div class="dot"></div>
                    <div class="stat-label">Belum dikerjakan</div>
                    <div class="stat-value"><?= $counts['Belum Dikerjakan'] ?></div>
                </div>
                <div class="stat-card tone-amber">
                    <div class="dot"></div>
                    <div class="stat-label">Sedang dikerjakan</div>
                    <div class="stat-value"><?= $counts['Sedang Dikerjakan'] ?></div>
                </div>
                <div class="stat-card tone-green">
                    <div class="dot"></div>
                    <div class="stat-label">Selesai</div>
                    <div class="stat-value"><?= $counts['Selesai'] ?></div>
                </div>
            </div>

            <div class="layout-2col">
                <div class="col-form">
                    <div class="card">
                        <div class="card-title">Ajukan aduan baru</div>
                        <div class="card-sub">Isi detail kerusakan atau kerusakan barang/fasilitas selengkap mungkin.</div>

                        <form method="post" enctype="multipart/form-data">
                            <div class="field">
                                <label class="field-label">Nama barang / fasilitas</label>
                                <input type="text" name="barang_aduan" class="field-input" placeholder="Misal: AC ruang kelas mati total" required>
                            </div>

                            <div class="field-row">
                                <div class="field">
                                    <label class="field-label">Kategori</label>
                                    <select name="kategori_id" class="field-select" required>
                                        <option value="" disabled selected>Pilih kategori</option>
                                        <?php foreach ($kategoriOptions as $k): ?>
                                            <option value="<?= htmlspecialchars($k) ?>"><?= htmlspecialchars($k) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label">Jumlah barang</label>
                                    <input type="number" name="jumlah_barang" class="field-input" min="1" value="1" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="field-label">Lokasi</label>
                                <input type="text" name="lokasi" class="field-input" placeholder="Misal: Gedung B, Lt. 2, R.3B" required>
                            </div>

                            <div class="field">
                                <label class="field-label">Isi keluhan</label>
                                <textarea name="isi_keluhan" class="field-textarea" placeholder="Jelaskan kondisi kerusakan secara detail..." required></textarea>
                            </div>

                            <div class="field">
                                <label class="field-label">Lampiran foto (opsional)</label>
                                <label class="upload-box" for="lampiran">
                                    <div><?= icon('upload', 22) ?></div>
                                    <div><strong>Klik untuk unggah</strong> atau seret file ke sini</div>
                                    <div style="margin-top:2px;">JPG, PNG, atau PDF, maks. 5MB</div>
                                </label>
                                <input type="file" id="lampiran" name="lampiran[]" multiple accept=".jpg,.jpeg,.png,.pdf" style="display:none;">
                            </div>

                            <button type="submit" class="btn btn-primary"><?= icon('send', 15) ?> Kirim aduan</button>
                        </form>
                    </div>
                </div>

                <div class="col-side">
                    <div class="card">
                        <div class="list-label" style="margin-bottom:14px;">Aduan terbaru kamu</div>
                        <?php foreach (array_slice($aduanSaya, 0, 4) as $a): ?>
                            <div class="recent-row">
                                <div class="pin"></div>
                                <div style="flex:1;min-width:0;">
                                    <div class="recent-title"><?= htmlspecialchars($a['barang']) ?></div>
                                    <div class="recent-meta"><span>#<?= $a['id'] ?></span><span><?= $a['tanggal'] ?></span></div>
                                </div>
                                <?= statusPill($a['status']) ?>
                            </div>
                        <?php endforeach; ?>
                        <a href="user_riwayat.php" class="see-all">Lihat semua riwayat &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>