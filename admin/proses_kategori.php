<?php
session_start();
require '../config/koneksi.php';

// Proteksi akses
if (!isset($_SESSION['nama'])) {
    header("Location: ../auth/login.php");
    exit();
}

$aksi = $_GET['aksi'] ?? '';

// ---------- PROSES TAMBAH KATEGORI ----------
if ($aksi === 'tambah') {
    $namaKategori = trim($_POST['nama_kategori'] ?? '');

    if (!empty($namaKategori)) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO kategori_barang (nama_kategori) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $namaKategori);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['sukses'] = "Kategori berhasil ditambahkan!";
        } else {
            $_SESSION['gagal'] = "Gagal menambahkan kategori.";
        }
        mysqli_stmt_close($stmt);
    }
    
    header("Location: kategori_barang.php");
    exit();
}

// ---------- PROSES EDIT KATEGORI ----------
if ($aksi === 'edit') {
    $idKategori   = (int)($_POST['id_kategori'] ?? 0);
    $namaKategori = trim($_POST['nama_kategori'] ?? '');

    if ($idKategori > 0 && !empty($namaKategori)) {
        $stmt = mysqli_prepare($koneksi, "UPDATE kategori_barang SET nama_kategori = ? WHERE id_kategori = ?");
        mysqli_stmt_bind_param($stmt, "si", $namaKategori, $idKategori);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['sukses'] = "Kategori berhasil diperbarui!";
        } else {
            $_SESSION['gagal'] = "Gagal memperbarui kategori.";
        }
        mysqli_stmt_close($stmt);
    }

    header("Location: kategori_barang.php");
    exit();
}

// ---------- PROSES HAPUS KATEGORI ----------
if ($aksi === 'delete') {
    $idKategori = (int)($_GET['id'] ?? 0);

    if ($idKategori > 0) {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM kategori_barang WHERE id_kategori = ?");
        mysqli_stmt_bind_param($stmt, "i", $idKategori);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['sukses'] = "Kategori berhasil dihapus!";
        } else {
            $_SESSION['gagal'] = "Gagal menghapus kategori.";
        }
        mysqli_stmt_close($stmt);
    }

    header("Location: kategori_barang.php");
    exit();
}

header("Location: kategori_barang.php");
exit();