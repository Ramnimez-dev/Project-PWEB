<?php
session_start();
require '../config/koneksi.php';

if(!isset($_SESSION['nama'])) {
    header("Location: ../auth/login.php");
    exit();
}

$aksi = $_GET['aksi'] ?? '';

// insert
if($aksi === 'tambah') {
    $nomor_induk = trim($_POST['nomor_induk'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $no_telp = trim($_POST['no_telp'] ?? '');
    $role = trim($_POST['role'] ?? 'user' ?? 'admin');

    $query = "INSERT INTO users(nomor_induk, nama, username, password, no_telp, role) VALUES('$nomor_induk', '$nama', '$username', '$password', '$no_telp', '$role')";
    
    if(mysqli_query($koneksi, $query)) {
        header("Location: data_pengguna.php?status=success_add");
    } else {
        header("Location: data_pengguna.php?status=failed");
    }
    exit();
}

// update & edit

// jika password diubah
if($aksi === 'edit') {
    $nomor_induk = trim($_POST['nomor_induk'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $no_telp = trim($_POST['no_telp'] ?? '');
    $role = trim($_POST['role'] ?? 'user' ?? 'admin');

    $query = "UPDATE users SET nama = '$nama', username = '$username', password = '$password', no_telp = '$no_telp', role = '$role' WHERE nomor_induk = '$nomor_induk'";

// jika password tidak diubah
} else {
    $nomor_induk = trim($_POST['nomor_induk'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $no_telp = trim($_POST['no_telp'] ?? '');
    $role = trim($_POST['role'] ?? 'user' ?? 'admin');

    $query = "UPDATE users SET nama = '$nama', username = '$username', no_telp = '$no_telp', role = '$role' WHERE nomor_induk = '$nomor_induk'";

    if(mysqli_query($koneksi, $query)) {
        header("Location: data_pengguna.php?status=success_update");
    }else {
        header("Locatioin: data_pengguna.php?status=failed");
    }
    exit();
}

// delete
if($aksi === 'delete') {
    $nomor_induk = $_GET['nomor_induk'] ?? '';

    $query = "DELETE FROM users nomor_induk='$nomor_induk'";

    if(mysqli_query($koneksi, $query)) {
        header("Location: data_pengguna.php?status=success_delete");
    }else {
        header("Location: data_pengguna.php?status=failed");
    }
    exit();
}
?>