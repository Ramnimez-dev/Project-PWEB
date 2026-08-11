<?php
session_start();
require '../config/koneksi.php';

session_destroy();
header("Location: login.php");
?>