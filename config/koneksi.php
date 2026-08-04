<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "sapras";

$koneksi = new mysqli($hostname, $username, $password, $dbname);
if($koneksi->connect_error) {
    die('koneksi gagal' . $koneksi->connect_error);
}
?>