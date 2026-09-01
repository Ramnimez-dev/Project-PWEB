<?php

session_start();
require '../config/koneksi.php';

if(!isset($_SESSION['nama'])) {
    header("Location: ../auth/login.php");
    exit();
}

$adminName = $_SESSION['nama'];
$potongNama = explode(' ', trim($adminName));
$inisial = strtoupper(substr($potongNama[0], 0, 1) . substr(end($potongNama), 0, 1));

$usersData = [];
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $usersData[] = [
            'id'       => $row['id'] ?? '',
            'induk'    => $row['nomor_induk'] ?? $row['no_induk'] ?? '-', 
            'nama'     => $row['nama'] ?? $row['nama_lengkap'] ?? '',
            'username' => $row['username'] ?? '',
            'password' => $row['password'] ?? '',
            'telp'     => $row['no_telp'] ?? $row['no_hp'] ?? '-',
            'role'     => $row['role'] ?? 'user',
            'dibuat'   => isset($row['created_at']) ? date('d/m/Y', strtotime($row['created_at'])) : date('d/m/Y')
        ];
    }
}

$q           = trim($_GET['q'] ?? '');
$roleFilter  = $_GET['role'] ?? 'Semua';
$roleOptions = ['Semua', 'admin', 'user'];

$filtered = array_filter($usersData, function ($u) use ($q, $roleFilter) {
    $matchQ = $q === '' || stripos($u['nama'], $q) !== false || stripos($u['username'], $q) !== false || stripos($u['induk'], $q) !== false;
    $matchR = $roleFilter === 'Semua' || $u['role'] === $roleFilter;
    return $matchQ && $matchR;
});



?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Pengguna — SarPras</title>
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
        <a href="dashboard.php" class="nav-item"><span class="label">Dashboard</span></a>
        <a href="data_aduan.php" class="nav-item"><span class="label">Data Aduan</span>
        </a>
        <a href="kategori_barang.php" class="nav-item"><span class="label">Kategori Barang</span></a>
        <a href="data_pengguna.php" class="nav-item active"><span class="label">Data Pengguna</span></a>

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
                <button class="bell-btn" aria-label="Notifikasi">
                </button>
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
                    <!-- perhitungan akun yang sudah terdaftar -->
                    <div class="eyebrow"><?= count($filtered) ?> akun</div>  
                    <h1 class="section-title">Data Pengguna</h1>
                </div>
                <a href="#" class="btn btn-primary">Tambah pengguna</a>
            </div>

            <form method="get" class="toolbar">
                <div class="search-box">
                    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama, username, atau no. induk...">

                    <?php if (!empty($q) || $roleFilter !== 'Semua'): ?>
                    <a href="data_pengguna.php" class="btn-reset" title="Reset Pencarian">
                    <?= icon('x', 14) ?> Reset
                    </a>
                    <?php endif; ?>
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
                        <th style="width:150px">Password</th>
                        <th style="width:150px">No. telp</th>
                        <th style="width:90px">Role</th>
                        <th style="width:110px">Terdaftar</th>
                        <th style="width:80px">Aksi</th>
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
                            <td class="mono"><?= htmlspecialchars($u['password']) ?></td>
                            <td class="mono"><?= htmlspecialchars($u['telp']) ?></td>
                            <td><span class="role-badge <?= $u['role'] === 'admin' ? 'admin' : '' ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                            <td class="mono"><?= htmlspecialchars($u['dibuat']) ?></td>
                            <td>
                                <div class="row-actions">
                                    <a href="#" class="btn-ghost">Edit</a>
                                    <a href="#" class="btn-ghost">Hapus</a>
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