<?php
/**
 * DASHBOARD USER — versi preview UI (data dummy, belum terhubung database)
 * Jalankan: php -S localhost:8000  lalu buka http://localhost:8000/user_dashboard.php
 */

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
<style>
@import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap');

:root {
  --paper: #F2F3ED; --surface: #FFFFFF; --ink: #1B231F; --sub: #6B756C; --line: #DCE0D6;
  --primary: #2C4A38; --primary-dark: #1D3226; --primary-faint: #E4EAE3;
  --amber: #B4711A; --amber-faint: #F6E7D2; --red: #A63A2E; --red-faint: #F3DED9;
  --green: #3D7A55; --green-faint: #DEEAE1; --gold: #C9A15A;
}
* { box-sizing: border-box; }
body { margin: 0; background: var(--paper); color: var(--ink); font-family: 'Inter', sans-serif; font-size: 14px; }
a { color: inherit; text-decoration: none; }
button, input, select, textarea { font-family: 'Inter', sans-serif; }
.layout { display: flex; min-height: 100vh; }

.sidebar { width: 232px; flex-shrink: 0; background: var(--primary-dark); display: flex; flex-direction: column; padding: 22px 12px; }
.brand { display: flex; align-items: center; gap: 10px; padding: 0 12px; margin-bottom: 30px; }
.brand-mark { width: 30px; height: 30px; border-radius: 6px; background: var(--gold); display: flex; align-items: center; justify-content: center; font-family: 'Barlow Condensed', sans-serif; font-weight: 700; font-size: 15px; color: var(--primary-dark); }
.brand-name { font-family: 'Barlow Condensed', sans-serif; font-weight: 700; font-size: 17px; color: #fff; line-height: 1.1; }
.brand-sub { font-size: 10px; color: rgba(255,255,255,0.5); letter-spacing: .04em; }
.nav-label { font-size: 10.5px; color: rgba(255,255,255,0.4); padding: 0 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .06em; }
.nav-item { display: flex; align-items: center; gap: 12px; width: 100%; padding: 10px 14px; margin-bottom: 4px; border: none; border-left: 3px solid transparent; cursor: pointer; text-align: left; color: rgba(255,255,255,0.62); font-size: 13.5px; font-weight: 500; }
.nav-item span.label { flex: 1; }
.nav-item.active { background: rgba(255,255,255,0.12); border-left-color: var(--gold); color: #fff; }
.nav-item .badge { font-family: 'IBM Plex Mono', monospace; font-size: 10.5px; background: rgba(255,255,255,0.15); color: #fff; padding: 1px 6px; border-radius: 3px; }
.sidebar-footer { border-top: 1px solid rgba(255,255,255,0.12); padding-top: 14px; margin-top: auto; }

.main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
.topbar { display: flex; align-items: center; justify-content: space-between; padding: 14px 28px; border-bottom: 1px solid var(--line); background: var(--surface); }
.topbar-date { font-family: 'IBM Plex Mono', monospace; font-size: 12px; color: var(--sub); }
.topbar-right { display: flex; align-items: center; gap: 16px; }
.avatar-circle { width: 30px; height: 30px; border-radius: 50%; background: var(--primary-faint); color: var(--primary); display: flex; align-items: center; justify-content: center; font-family: 'Barlow Condensed', sans-serif; font-weight: 700; font-size: 13px; }
.admin-name { font-size: 12.5px; font-weight: 600; }
.admin-role { font-size: 10.5px; color: var(--sub); }
.bell-btn { background: none; border: none; cursor: pointer; color: var(--sub); position: relative; }
.bell-dot { position: absolute; top: -3px; right: -3px; width: 8px; height: 8px; border-radius: 50%; background: var(--red); border: 1.5px solid var(--surface); }
.content { padding: 26px 28px; flex: 1; }

.section-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.eyebrow { font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: var(--primary); letter-spacing: .08em; text-transform: uppercase; margin-bottom: 4px; }
.section-title { font-family: 'Barlow Condensed', sans-serif; font-size: 28px; font-weight: 700; margin: 0; }
.section-desc { font-size: 13px; color: var(--sub); margin-top: 4px; }

.stat-grid { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 26px; }
.stat-card { border-radius: 8px; padding: 16px 18px; flex: 1; min-width: 150px; position: relative; overflow: hidden; background: var(--surface); border: 1px solid var(--line); }
.stat-card .dot { position: absolute; top: 12px; right: 12px; width: 9px; height: 9px; border-radius: 50%; opacity: .35; background: var(--ink); }
.stat-label { font-size: 11.5px; font-weight: 500; color: var(--sub); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px; }
.stat-value { font-family: 'Barlow Condensed', sans-serif; font-size: 30px; font-weight: 700; line-height: 1; }
.stat-card.tone-red { background: var(--red-faint); border-color: rgba(166,58,46,.2); }
.stat-card.tone-red .stat-label, .stat-card.tone-red .stat-value { color: var(--red); }
.stat-card.tone-red .dot { background: var(--red); }
.stat-card.tone-amber { background: var(--amber-faint); border-color: rgba(180,113,26,.2); }
.stat-card.tone-amber .stat-label, .stat-card.tone-amber .stat-value { color: var(--amber); }
.stat-card.tone-amber .dot { background: var(--amber); }
.stat-card.tone-green { background: var(--green-faint); border-color: rgba(61,122,85,.2); }
.stat-card.tone-green .stat-label, .stat-card.tone-green .stat-value { color: var(--green); }
.stat-card.tone-green .dot { background: var(--green); }

.pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 4px; font-family: 'IBM Plex Mono', monospace; font-size: 11px; font-weight: 500; letter-spacing: .02em; text-transform: uppercase; border: 1px solid transparent; }
.pill-dot { width: 7px; height: 7px; border-radius: 50%; }
.pill.status-belum { background: var(--red-faint); color: var(--red); border-color: rgba(166,58,46,.2); }
.pill.status-belum .pill-dot { background: var(--red); }
.pill.status-proses { background: var(--amber-faint); color: var(--amber); border-color: rgba(180,113,26,.2); }
.pill.status-proses .pill-dot { background: var(--amber); }
.pill.status-selesai { background: var(--green-faint); color: var(--green); border-color: rgba(61,122,85,.2); transform: rotate(-1.5deg); }
.pill.status-selesai .pill-dot { background: var(--green); }

.alert { padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
.alert-success { background: var(--green-faint); color: var(--green); }

.layout-2col { display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap; }
.col-form { flex: 1.4; min-width: 320px; }
.col-side { flex: 1; min-width: 280px; }

.card { background: var(--surface); border: 1px solid var(--line); border-radius: 10px; padding: 22px; }
.card-title { font-family: 'Barlow Condensed', sans-serif; font-size: 19px; font-weight: 700; margin-bottom: 4px; }
.card-sub { font-size: 12.5px; color: var(--sub); margin-bottom: 18px; }

.field { margin-bottom: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: var(--sub); text-transform: uppercase; letter-spacing: .03em; margin-bottom: 6px; display: block; }
.field-input, .field-select, .field-textarea {
  width: 100%; border: 1px solid var(--line); border-radius: 6px; padding: 10px 12px;
  font-family: 'Inter', sans-serif; font-size: 13.5px; box-sizing: border-box; background: var(--surface); color: var(--ink);
}
.field-textarea { resize: vertical; min-height: 90px; }
.field-row { display: flex; gap: 12px; }
.field-row .field { flex: 1; }

.upload-box {
  border: 1.5px dashed var(--line); border-radius: 8px; padding: 20px; text-align: center;
  color: var(--sub); font-size: 12.5px; cursor: pointer; background: var(--paper);
}
.upload-box svg { margin-bottom: 6px; opacity: .6; }
.upload-box strong { color: var(--primary); }

.btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 10px 18px; border-radius: 6px; font-size: 13.5px; font-weight: 500; cursor: pointer; border: 1px solid var(--line); background: var(--surface); color: var(--ink); width: 100%; }
.btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }

.list-label { font-size: 11px; font-weight: 600; color: var(--sub); text-transform: uppercase; letter-spacing: .03em; margin-bottom: 10px; }
.recent-row { display: flex; align-items: center; gap: 12px; background: var(--surface); border: 1px solid var(--line); border-left: 4px solid var(--primary); border-radius: 8px; padding: 11px 14px; margin-bottom: 8px; }
.recent-row .pin { width: 8px; height: 8px; border-radius: 50%; background: var(--paper); border: 1px solid var(--line); flex-shrink: 0; }
.recent-title { font-weight: 600; font-size: 12.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.recent-meta { font-size: 11px; color: var(--sub); margin-top: 2px; display: flex; gap: 8px; }
.see-all { display: block; text-align: center; font-size: 12.5px; color: var(--primary); font-weight: 500; margin-top: 10px; }

@media (max-width: 860px) { .sidebar { display: none; } .content { padding: 18px; } }
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