<?php
/**
 * RIWAYAT ADUAN — panel user, versi preview UI (data dummy, belum terhubung database)
 * Jalankan: php -S localhost:8000  lalu buka http://localhost:8000/user_riwayat.php
 */

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

// ---------- DATA DUMMY (nanti diganti session user login + query PDO WHERE user_id = ...) ----------
$userName = 'Rangga Prasetyo';

$aduanData = [
    1042 => [
        'id' => 1042, 'barang' => 'AC Ruang Kelas 3B mati total', 'kategori' => 'Elektronik',
        'jumlah' => 1, 'lokasi' => 'Gedung B, Lt. 2, R.3B',
        'isi' => 'AC sudah tidak menyala sejak Senin pagi, sudah dicoba remote baru tetap tidak merespon.',
        'status' => 'Belum Dikerjakan', 'tanggal' => '01/08/2026',
        'lampiran' => ['foto_ac_1.jpg', 'foto_ac_2.jpg'],
        'riwayat' => [
            ['label' => 'Aduan diajukan', 'tanggal' => '01/08/2026 08:12', 'done' => true],
            ['label' => 'Ditinjau admin', 'tanggal' => null, 'done' => false],
            ['label' => 'Dikerjakan', 'tanggal' => null, 'done' => false],
            ['label' => 'Selesai', 'tanggal' => null, 'done' => false],
        ],
        'komentar' => [
            ['dari' => 'Budi Santoso', 'peran' => 'admin', 'isi' => 'Sudah dijadwalkan teknisi hari Kamis.', 'tanggal' => '01/08/2026 10:20'],
        ],
    ],
    1038 => [
        'id' => 1038, 'barang' => 'Plafon ruang rapat retak', 'kategori' => 'Bangunan',
        'jumlah' => 1, 'lokasi' => 'Gedung A, Lt. 3, R. Rapat',
        'isi' => 'Terdapat retakan cukup panjang di plafon, dikhawatirkan bisa runtuh.',
        'status' => 'Belum Dikerjakan', 'tanggal' => '02/08/2026',
        'lampiran' => ['plafon_retak.jpg'],
        'riwayat' => [
            ['label' => 'Aduan diajukan', 'tanggal' => '02/08/2026 16:20', 'done' => true],
            ['label' => 'Ditinjau admin', 'tanggal' => null, 'done' => false],
            ['label' => 'Dikerjakan', 'tanggal' => null, 'done' => false],
            ['label' => 'Selesai', 'tanggal' => null, 'done' => false],
        ],
        'komentar' => [],
    ],
    1030 => [
        'id' => 1030, 'barang' => 'Kran taman belakang macet', 'kategori' => 'Sanitasi',
        'jumlah' => 1, 'lokasi' => 'Taman Belakang, dekat gazebo',
        'isi' => 'Kran air taman belakang macet, tidak bisa dibuka/ditutup sama sekali.',
        'status' => 'Sedang Dikerjakan', 'tanggal' => '18/07/2026',
        'lampiran' => ['kran_macet.jpg'],
        'riwayat' => [
            ['label' => 'Aduan diajukan', 'tanggal' => '18/07/2026 09:00', 'done' => true],
            ['label' => 'Ditinjau admin', 'tanggal' => '19/07/2026 08:40', 'done' => true],
            ['label' => 'Dikerjakan', 'tanggal' => '20/07/2026 13:15', 'done' => true],
            ['label' => 'Selesai', 'tanggal' => null, 'done' => false],
        ],
        'komentar' => [
            ['dari' => 'Budi Santoso', 'peran' => 'admin', 'isi' => 'Sedang menunggu suku cadang keran baru.', 'tanggal' => '20/07/2026 13:15'],
            ['dari' => 'Rangga Prasetyo', 'peran' => 'user', 'isi' => 'Baik, terima kasih infonya.', 'tanggal' => '20/07/2026 14:02'],
        ],
    ],
    1021 => [
        'id' => 1021, 'barang' => 'Lampu koridor lantai 1 mati', 'kategori' => 'Elektronik',
        'jumlah' => 2, 'lokasi' => 'Gedung A, Koridor Lt. 1',
        'isi' => '2 lampu di koridor utama mati, area jadi gelap saat malam hari.',
        'status' => 'Selesai', 'tanggal' => '30/06/2026',
        'lampiran' => [],
        'riwayat' => [
            ['label' => 'Aduan diajukan', 'tanggal' => '30/06/2026 07:45', 'done' => true],
            ['label' => 'Ditinjau admin', 'tanggal' => '30/06/2026 09:10', 'done' => true],
            ['label' => 'Dikerjakan', 'tanggal' => '01/07/2026 10:00', 'done' => true],
            ['label' => 'Selesai', 'tanggal' => '01/07/2026 15:30', 'done' => true],
        ],
        'komentar' => [
            ['dari' => 'Budi Santoso', 'peran' => 'admin', 'isi' => 'Lampu sudah diganti dengan yang baru.', 'tanggal' => '01/07/2026 15:30'],
        ],
    ],
];

// ---------- FILTER & PENCARIAN ----------
$q            = trim($_GET['q'] ?? '');
$statusFilter = $_GET['status'] ?? 'Semua';
$statusOptions = ['Semua', 'Belum Dikerjakan', 'Sedang Dikerjakan', 'Selesai'];

$filtered = array_filter($aduanData, function ($a) use ($q, $statusFilter) {
    $matchQ = $q === '' || stripos($a['barang'], $q) !== false || (string)$a['id'] === $q;
    $matchS = $statusFilter === 'Semua' || $a['status'] === $statusFilter;
    return $matchQ && $matchS;
});
uasort($filtered, fn($a, $b) => strcmp($b['tanggal'], $a['tanggal']));

$counts = ['Belum Dikerjakan' => 0, 'Sedang Dikerjakan' => 0, 'Selesai' => 0];
foreach ($aduanData as $a) { $counts[$a['status']]++; }

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
<title>Riwayat Aduan — Sarpras</title>
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
button { font-family: 'Inter', sans-serif; }
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
.sidebar-footer { border-top: 1px solid rgba(255,255,255,0.12); padding-top: 14px; margin-top: auto; }

.main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
.topbar { display: flex; align-items: center; justify-content: space-between; padding: 14px 28px; border-bottom: 1px solid var(--line); background: var(--surface); }
.topbar-date { font-family: 'IBM Plex Mono', monospace; font-size: 12px; color: var(--sub); }
.topbar-right { display: flex; align-items: center; gap: 16px; }
.avatar-circle { width: 30px; height: 30px; border-radius: 50%; background: var(--primary-faint); color: var(--primary); display: flex; align-items: center; justify-content: center; font-family: 'Barlow Condensed', sans-serif; font-weight: 700; font-size: 13px; }
.admin-name { font-size: 12.5px; font-weight: 600; }
.admin-role { font-size: 10.5px; color: var(--sub); }
.bell-btn { background: none; border: none; cursor: pointer; color: var(--sub); position: relative; }
.content { padding: 26px 28px; flex: 1; }

.section-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.eyebrow { font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: var(--primary); letter-spacing: .08em; text-transform: uppercase; margin-bottom: 4px; }
.section-title { font-family: 'Barlow Condensed', sans-serif; font-size: 28px; font-weight: 700; margin: 0; }
.section-desc { font-size: 13px; color: var(--sub); margin-top: 4px; }

.btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid var(--line); background: var(--surface); color: var(--ink); }
.btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }

.stat-strip { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
.stat-pill { display: flex; align-items: center; gap: 8px; background: var(--surface); border: 1px solid var(--line); padding: 8px 14px; border-radius: 20px; font-size: 12.5px; color: var(--sub); }
.stat-pill .dot { width: 7px; height: 7px; border-radius: 50%; }
.stat-pill strong { color: var(--ink); font-family: 'IBM Plex Mono', monospace; font-weight: 600; }
.stat-pill.dot-red .dot { background: var(--red); }
.stat-pill.dot-amber .dot { background: var(--amber); }
.stat-pill.dot-green .dot { background: var(--green); }
.stat-pill.dot-all .dot { background: var(--primary); }

.pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 4px; font-family: 'IBM Plex Mono', monospace; font-size: 11px; font-weight: 500; letter-spacing: .02em; text-transform: uppercase; border: 1px solid transparent; }
.pill-dot { width: 7px; height: 7px; border-radius: 50%; }
.pill.status-belum { background: var(--red-faint); color: var(--red); border-color: rgba(166,58,46,.2); }
.pill.status-belum .pill-dot { background: var(--red); }
.pill.status-proses { background: var(--amber-faint); color: var(--amber); border-color: rgba(180,113,26,.2); }
.pill.status-proses .pill-dot { background: var(--amber); }
.pill.status-selesai { background: var(--green-faint); color: var(--green); border-color: rgba(61,122,85,.2); transform: rotate(-1.5deg); }
.pill.status-selesai .pill-dot { background: var(--green); }

.toolbar { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
.search-box { display: flex; align-items: center; gap: 8px; border: 1px solid var(--line); border-radius: 6px; padding: 8px 12px; background: var(--surface); flex: 1; min-width: 220px; }
.search-box input { border: none; outline: none; font-family: 'Inter', sans-serif; font-size: 13px; flex: 1; background: transparent; }
.filter-group { display: flex; gap: 6px; flex-wrap: wrap; }
.filter-chip { padding: 8px 13px; border-radius: 6px; cursor: pointer; font-size: 12.5px; font-weight: 500; border: 1px solid var(--line); background: var(--surface); color: var(--ink); }
.filter-chip.active { border-color: var(--primary); background: var(--primary); color: #fff; }

.timeline-card { display: flex; gap: 16px; background: var(--surface); border: 1px solid var(--line); border-radius: 10px; padding: 16px 18px; margin-bottom: 10px; cursor: pointer; }
.timeline-card:hover { border-color: var(--primary); }
.timeline-mark { width: 10px; height: 10px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
.timeline-mark.belum { background: var(--red); }
.timeline-mark.proses { background: var(--amber); }
.timeline-mark.selesai { background: var(--green); }
.timeline-body { flex: 1; min-width: 0; }
.timeline-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
.timeline-title { font-family: 'Barlow Condensed', sans-serif; font-weight: 700; font-size: 17px; }
.timeline-meta { font-size: 12px; color: var(--sub); margin-top: 4px; display: flex; gap: 12px; flex-wrap: wrap; }
.timeline-meta span { display: flex; align-items: center; gap: 4px; }
.timeline-id { font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: var(--sub); }

.empty-state { text-align: center; padding: 60px 20px; color: var(--sub); }
.empty-state svg { opacity: .35; margin-bottom: 10px; }

/* ---------- modal ---------- */
.modal-backdrop { position: fixed; inset: 0; background: rgba(27,35,31,.45); display: flex; align-items: center; justify-content: center; z-index: 50; padding: 20px; }
.modal-box { background: var(--surface); width: 600px; max-width: 100%; max-height: 88vh; overflow-y: auto; border-radius: 10px; border: 1px solid var(--line); }
.modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid var(--line); position: sticky; top: 0; background: var(--surface); }
.modal-eyebrow { font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: var(--sub); }
.modal-title { font-family: 'Barlow Condensed', sans-serif; font-size: 22px; font-weight: 700; }
.modal-close { background: none; border: none; cursor: pointer; color: var(--sub); padding: 6px; }
.modal-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 20px; }
.meta-line { display: flex; gap: 22px; flex-wrap: wrap; font-size: 13px; }
.meta-line .item { display: flex; align-items: center; gap: 6px; color: var(--sub); }
.field-label { font-size: 11px; font-weight: 600; color: var(--sub); text-transform: uppercase; letter-spacing: .03em; margin-bottom: 8px; }
.keluhan-box { font-size: 14px; line-height: 1.6; margin: 0; background: var(--paper); padding: 12px 14px; border-radius: 6px; }

/* progress tracker */
.progress-track { display: flex; }
.progress-step { flex: 1; text-align: center; position: relative; }
.progress-step::before {
  content: ''; position: absolute; top: 11px; left: -50%; width: 100%; height: 2px; background: var(--line); z-index: 0;
}
.progress-step:first-child::before { display: none; }
.progress-step.done::before { background: var(--primary); }
.progress-circle {
  width: 24px; height: 24px; border-radius: 50%; background: var(--surface); border: 2px solid var(--line);
  display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; position: relative; z-index: 1; color: var(--sub);
}
.progress-step.done .progress-circle { background: var(--primary); border-color: var(--primary); color: #fff; }
.progress-label { font-size: 11px; font-weight: 600; color: var(--sub); }
.progress-step.done .progress-label { color: var(--ink); }
.progress-time { font-size: 10px; color: var(--sub); font-family: 'IBM Plex Mono', monospace; margin-top: 2px; }

.lampiran-chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border: 1px solid var(--line); border-radius: 6px; font-size: 12px; font-family: 'IBM Plex Mono', monospace; background: var(--paper); margin: 0 8px 8px 0; }

.komentar-item { display: flex; gap: 10px; margin-bottom: 12px; }
.komentar-avatar { width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-family: 'Barlow Condensed', sans-serif; font-weight: 700; font-size: 11.5px; }
.komentar-avatar.admin { background: var(--primary-faint); color: var(--primary); }
.komentar-avatar.user { background: var(--amber-faint); color: var(--amber); }
.komentar-bubble { flex: 1; background: var(--paper); border-radius: 8px; padding: 8px 12px; }
.komentar-head { display: flex; align-items: center; gap: 8px; }
.komentar-nama { font-size: 12px; font-weight: 600; }
.komentar-tag { font-family: 'IBM Plex Mono', monospace; font-size: 9px; text-transform: uppercase; padding: 1px 5px; border-radius: 3px; background: var(--primary-faint); color: var(--primary); }
.komentar-time { font-weight: 400; color: var(--sub); font-family: 'IBM Plex Mono', monospace; font-size: 10px; margin-left: auto; }
.komentar-text { font-size: 13px; margin-top: 3px; }
.komentar-form textarea { width: 100%; border: 1px solid var(--line); border-radius: 6px; padding: 10px; font-family: 'Inter', sans-serif; font-size: 13px; resize: vertical; box-sizing: border-box; }
.komentar-submit-row { display: flex; justify-content: flex-end; margin-top: 8px; }

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
                    <div class="eyebrow"><?= count($filtered) ?> aduan</div>
                    <h1 class="section-title">Riwayat Aduan</h1>
                    <div class="section-desc">Semua aduan yang pernah kamu ajukan, beserta status terkininya.</div>
                </div>
                <a href="user_dashboard.php" class="btn btn-primary"><?= icon('plus', 15) ?> Ajukan aduan baru</a>
            </div>

            <div class="stat-strip">
                <div class="stat-pill dot-all"><span class="dot"></span> Total <strong><?= count($aduanData) ?></strong></div>
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
                    <a class="timeline-card" href="user_riwayat.php?<?= http_build_query(['q' => $q, 'status' => $statusFilter, 'id' => $a['id']]) ?>">
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
                                <span><?= icon('package', 12) ?> <?= htmlspecialchars($a['kategori']) ?></span>
                                <span><?= icon('pin', 12) ?> <?= htmlspecialchars($a['lokasi']) ?></span>
                                <span><?= icon('paperclip', 12) ?> <?= count($a['lampiran']) ?></span>
                                <span><?= icon('message', 12) ?> <?= count($a['komentar']) ?></span>
                                <span style="margin-left:auto;font-family:'IBM Plex Mono',monospace;"><?= $a['tanggal'] ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php if ($detail): ?>
<div class="modal-backdrop" onclick="if(event.target===this) window.location='user_riwayat.php?<?= $backQuery ?>'">
    <div class="modal-box">
        <div class="modal-head">
            <div>
                <div class="modal-eyebrow">ADUAN #<?= $detail['id'] ?></div>
                <div class="modal-title"><?= htmlspecialchars($detail['barang']) ?></div>
            </div>
            <a class="modal-close" href="user_riwayat.php?<?= $backQuery ?>"><?= icon('x', 20) ?></a>
        </div>
        <div class="modal-body">
            <div class="meta-line">
                <div class="item"><?= icon('package', 14) ?> <?= htmlspecialchars($detail['kategori']) ?> &middot; <?= $detail['jumlah'] ?> unit</div>
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
                    <?php foreach ($detail['lampiran'] as $f): ?>
                        <span class="lampiran-chip"><?= icon('paperclip', 12) ?> <?= htmlspecialchars($f) ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div>
                <div class="field-label"><?= icon('message', 13) ?> Percakapan (<?= count($detail['komentar']) ?>)</div>
                <?php if (empty($detail['komentar'])): ?>
                    <div style="font-size:13px;color:var(--sub);font-style:italic;margin-bottom:10px;">Belum ada balasan dari admin.</div>
                <?php endif; ?>
                <?php foreach ($detail['komentar'] as $k): ?>
                    <?php $initial = strtoupper(substr($k['dari'], 0, 1)); ?>
                    <div class="komentar-item">
                        <div class="komentar-avatar <?= $k['peran'] ?>"><?= $initial ?></div>
                        <div class="komentar-bubble">
                            <div class="komentar-head">
                                <span class="komentar-nama"><?= htmlspecialchars($k['dari']) ?></span>
                                <?php if ($k['peran'] === 'admin'): ?><span class="komentar-tag">Admin</span><?php endif; ?>
                                <span class="komentar-time"><?= $k['tanggal'] ?></span>
                            </div>
                            <div class="komentar-text"><?= htmlspecialchars($k['isi']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <form method="post" action="tambah_balasan.php" class="komentar-form">
                    <input type="hidden" name="id_aduan" value="<?= $detail['id'] ?>">
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
</body>
</html>