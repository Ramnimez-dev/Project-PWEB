<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['nama'])) {
    header("Location: ../auth/login.php");
    exit();
}

$adminName = $_SESSION['nama'];
$potongNama = explode(' ', trim($adminName));
$inisial = strtoupper(substr($potongNama[0], 0, 1) . substr(end($potongNama), 0, 1));

// ---------- MENGAMBIL DATA DARI DATABASE ----------
$queryKategori = "SELECT k.id_kategori AS id, k.nama_kategori AS nama, COUNT(a.id_aduan) AS jumlah FROM kategori_barang k LEFT JOIN aduan a ON k.id_kategori = a.kategori_id GROUP BY k.id_kategori, k.nama_kategori";

$resultKategori = mysqli_query($koneksi, $queryKategori);


$kategoriData = [];
while ($row = mysqli_fetch_assoc($resultKategori)) {
    $kategoriData[] = [
        'id'     => (int)$row['id'],
        'nama'   => $row['nama'],
        'jumlah' => (int)$row['jumlah']
    ];
}

// ---------- PENCARIAN & FILTERING ----------
$q = trim($_GET['q'] ?? '');

$filtered = array_filter($kategoriData, fn($k) => $q === '' || stripos($k['nama'], $q) !== false);
usort($filtered, fn($a, $b) => strcmp($a['nama'], $b['nama']));

$counts = ['Belum Dikerjakan' => 0, 'Sedang Dikerjakan' => 0, 'Selesai' => 0];
$hasil = mysqli_query($koneksi, "SELECT status, COUNT(*) AS jumlah FROM aduan GROUP BY status");
while ($row = mysqli_fetch_assoc($hasil)) {
    $counts[$row['status']] = (int)$row['jumlah'];
}
$total      = array_sum($counts);
$belumCount = $counts['Belum Dikerjakan'];

$result = mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM users");
$row = mysqli_fetch_assoc($result);
$totally = array_sum($row);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kategori Barang — Halo SarPras</title>
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

        <div class="nav-label">Menu</div>
        <a href="dashboard.php" class="nav-item"><span class="label">Beranda</span></a>
        <a href="data_aduan.php" class="nav-item"><span class="label">Data Aduan</span>
            <?php if ($total > 0): ?><span class="badge"><?= $total ?></span><?php endif; ?>
        </a>
        <a href="kategori_barang.php" class="nav-item active"><span class="label">Kategori Barang</span></a>
        <a href="data_pengguna.php" class="nav-item"><span class="label">Data Pengguna</span>
        <?php if ($totally > 0): ?><span class="badge"><?= $totally ?></span><?php endif; ?>
        </a>
        <a href="laporan.php" class="nav-item"><span class="label">Laporan</span></a>

        <div style="flex:1"></div>
        <div class="sidebar-footer">
            <a href="../auth/logout.php" onclick="return confirm('Yakin Ingin Logout?')" class="nav-item"><span class="label">Keluar</span></a>
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
                <div style="display:flex;align-items:center;gap:8px;">
                    <div class="avatar-circle"><?= $inisial ?></div>
                    <div>
                        <div class="admin-name"><?= $adminName ?></div>
                        <div class="admin-role">Administrator</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="content">
            <div class="section-header">
                <div>
                    <div class="eyebrow"><?= count($filtered) ?> kategori</div>
                    <h1 class="section-title">Kategori Barang</h1>
                </div>
                <button type="button" class="btn btn-primary" onclick="openTambahKategoriModal()">Tambah kategori</button>
            </div>

            <form method="get" class="toolbar">
                <div class="search-box">
                    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari kategori...">
                    <?php if (!empty($q)): ?>
                    <a href="kategori_barang.php" class="btn-reset" title="Reset Pencarian">Reset</a>
                    <?php endif; ?>
                </div>
            </form>

            <?php if (empty($filtered)): ?>
                <div class="empty-state">
                    <div>Tidak ada kategori yang cocok dengan pencarian "<?= htmlspecialchars($q) ?>".</div>
                </div>
            <?php else: ?>
                <div class="kategori-grid">
                    <?php foreach ($filtered as $i => $k): 
                        $kataKategori = explode(' ', trim($k['nama']));
                        if (count($kataKategori) > 1) {
                            $iconInisial = strtoupper(substr($kataKategori[0], 0, 1) . substr(end($kataKategori), 0, 1));
                        } else {
                            $iconInisial = strtoupper(substr($k['nama'], 0, 2));
                        }    
                    ?>
                        <div class="kategori-card">
                            <div class="kategori-rank">#<?= str_pad($k['id'], 2, '0', STR_PAD_LEFT) ?></div>
                            <div class="kategori-icon"><?= $iconInisial ?></div>
                            <div class="kategori-name"><?= htmlspecialchars($k['nama']) ?></div>
                            <div class="kategori-count"><?= $k['jumlah'] ?> laporan tercatat</div>
                            <div class="kategori-actions">
                                <button type="button" class="btn-ghost" onclick="openEditKategoriModal('<?= $k['id'] ?>', '<?= htmlspecialchars($k['nama'], ENT_QUOTES) ?>')">Edit</button>
                                <a href="proses_kategori.php?aksi=delete&id=<?= $k['id'] ?>" class="btn-danger-ghost" onclick="return confirm('Hapus kategori &quot;<?= htmlspecialchars($k['nama']) ?>&quot;?');">Hapus</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal-overlay" id="modalTambahKategori">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Tambah Kategori Barang</h3>
            <button class="modal-close" onclick="closeKategoriModal('modalTambahKategori')">&times;</button>
        </div>
        <form action="proses_kategori.php?aksi=tambah" method="post" class="modal-form">
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" placeholder="Contoh: Elektronik, Furnitur" required autofocus>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeKategoriModal('modalTambahKategori')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal-overlay" id="modalEditKategori">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Edit Kategori Barang</h3>
            <button class="modal-close" onclick="closeKategoriModal('modalEditKategori')">&times;</button>
        </div>
        <form action="proses_kategori.php?aksi=edit" method="post" class="modal-form">
            <input type="hidden" name="id_kategori" id="edit_kategori_id">
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" id="edit_kategori_nama" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeKategoriModal('modalEditKategori')">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openTambahKategoriModal() {
    document.getElementById('modalTambahKategori').classList.add('active');
}

function openEditKategoriModal(id, nama) {
    document.getElementById('edit_kategori_id').value = id;
    document.getElementById('edit_kategori_nama').value = nama;
    document.getElementById('modalEditKategori').classList.add('active');
}

function closeKategoriModal(id) {
    document.getElementById(id).classList.remove('active');
}
</script>
</body>
</html>