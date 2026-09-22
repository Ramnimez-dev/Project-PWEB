<?php
session_start();
require '../config/koneksi.php';

// =====================================================
// CEK LOGIN USER
// =====================================================
if (!isset($_SESSION['nama']) || ($_SESSION['role'] ?? '') !== 'user') {
    header('Location: ../auth/login.php');
    exit;
}

$userName = $_SESSION['nama'];
$userId   = $_SESSION['id_user'];

// =====================================================
// AMBIL ID ADUAN
// =====================================================
$idAduan = (int)($_GET['id'] ?? 0);

if ($idAduan <= 0) {
    die('ID aduan tidak valid.');
}

// =====================================================
// AMBIL DATA ADUAN
// Hanya aduan milik user yang sedang login
// =====================================================
$stmt = mysqli_prepare($koneksi, "
    SELECT 
        a.id_aduan,
        a.barang_aduan,
        a.jumlah_barang,
        a.lokasi,
        a.isi_keluhan,
        a.status,
        a.tanggal,
        k.nama_kategori
    FROM aduan a
    LEFT JOIN kategori_barang k 
        ON k.id_kategori = a.kategori_id
    WHERE a.id_aduan = ?
      AND a.user_id = ?
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, 'ii', $idAduan, $userId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$aduan  = mysqli_fetch_assoc($result);

// Jika aduan tidak ditemukan
if (!$aduan) {
    die('Aduan tidak ditemukan atau bukan milik akun Anda.');
}

// =====================================================
// FORMAT TANGGAL
// =====================================================
$bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$timestamp = strtotime($aduan['tanggal']);

$tanggalCetak =
    date('d', $timestamp) . ' ' .
    $bulan[(int)date('m', $timestamp)] . ' ' .
    date('Y', $timestamp);

$jamCetak = date('H:i', $timestamp);

// =====================================================
// STATUS
// =====================================================
$status = $aduan['status'];

$statusClass = match ($status) {
    'Belum Dikerjakan' => 'status-belum',
    'Sedang Dikerjakan' => 'status-proses',
    'Selesai' => 'status-selesai',
    default => 'status-belum'
};

// =====================================================
// ID FORMAT
// Contoh: #00001
// =====================================================
$nomorAduan = '#' . str_pad($aduan['id_aduan'], 5, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Cetak Aduan <?= htmlspecialchars($nomorAduan) ?> - Halo Sarpras
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #eeeeee;
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
        }

        /* =====================================================
           AREA STRUK
        ===================================================== */

        .receipt {
            width: 80mm;
            min-height: 100mm;
            margin: 20px auto;
            padding: 7mm 5mm;

            background: #ffffff;

            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.12);

            font-size: 12px;
            line-height: 1.45;
        }

        /* =====================================================
           HEADER
        ===================================================== */

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo {
            width: 45px;
            height: 45px;
            object-fit: contain;
            margin-bottom: 4px;
        }

        .brand {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .subtitle {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }

        .line {
            border-top: 1px dashed #555;
            margin: 8px 0;
        }

        /* =====================================================
           INFORMASI
        ===================================================== */

        .info {
            margin-top: 5px;
        }

        .row {
            display: flex;
            align-items: flex-start;
            margin: 5px 0;
        }

        .label {
            width: 34%;
            color: #555;
            flex-shrink: 0;
        }

        .colon {
            width: 5%;
            text-align: center;
        }

        .value {
            width: 61%;
            font-weight: 600;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }

        /* =====================================================
           JUDUL SECTION
        ===================================================== */

        .section-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        /* =====================================================
           ISI KELUHAN
        ===================================================== */

        .complaint {
            border: 1px dashed #999;
            padding: 8px;
            margin-top: 5px;
            border-radius: 4px;

            font-size: 11px;
            line-height: 1.5;

            word-wrap: break-word;
            overflow-wrap: anywhere;
        }

        /* =====================================================
           STATUS
        ===================================================== */

        .status-box {
            text-align: center;
            margin: 10px 0;
            padding: 7px;

            border: 1px solid #ddd;
            border-radius: 4px;

            font-weight: 700;
            font-size: 11px;
        }

        .status-belum {
            color: #dc2626;
        }

        .status-proses {
            color: #d97706;
        }

        .status-selesai {
            color: #16a34a;
        }

        /* =====================================================
           FOOTER
        ===================================================== */

        .footer {
            text-align: center;
            margin-top: 12px;
        }

        .thank-you {
            font-size: 10px;
            color: #555;
            line-height: 1.5;
        }

        /*
         * ID ADUAN DILETAKKAN DI BAGIAN PALING BAWAH
         */
        .complaint-id {
            margin-top: 10px;

            padding-top: 8px;

            border-top: 1px dashed #555;

            text-align: center;

            font-size: 16px;
            font-weight: 800;

            letter-spacing: 1px;
        }

        .id-label {
            display: block;

            font-size: 9px;
            font-weight: normal;

            color: #777;

            letter-spacing: 0.5px;

            margin-bottom: 2px;
        }

        /* =====================================================
           TOMBOL
        ===================================================== */

        .print-area {
            width: 80mm;
            margin: 10px auto 30px;
            text-align: center;
        }

        .btn-print {
            border: none;
            background: #166534;
            color: white;

            padding: 10px 18px;

            border-radius: 7px;

            font-size: 13px;
            font-weight: 600;

            cursor: pointer;
        }

        .btn-back {
            display: inline-block;

            margin-left: 5px;

            padding: 9px 15px;

            border-radius: 7px;

            background: #e5e7eb;
            color: #333;

            text-decoration: none;

            font-size: 13px;
        }

        .btn-print:hover {
            background: #14532d;
        }

        .btn-back:hover {
            background: #d1d5db;
        }

        /* =====================================================
           MODE PRINT
        ===================================================== */

        @media print {

            @page {
                size: 80mm auto;
                margin: 0;
            }

            html,
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
                background: white;
            }

            .receipt {
                width: 80mm;
                min-height: auto;

                margin: 0;
                padding: 5mm 5mm;

                box-shadow: none;
            }

            .print-area {
                display: none;
            }

        }

    </style>
</head>

<body>

    <!-- =====================================================
         STRUK
    ===================================================== -->

    <div class="receipt">

        <!-- HEADER -->
        <div class="header">

            <img
                src="../img/haloLogo.png"
                alt="Logo Halo Sarpras"
                class="logo"
            >

            <div class="brand">
                HALO SARPRAS
            </div>

            <div class="subtitle">
                BUKTI PENGADUAN SARANA & PRASARANA
            </div>

        </div>


        <div class="line"></div>


        <!-- INFORMASI ADUAN -->
        <div class="info">

            <div class="row">
                <div class="label">
                    Tanggal
                </div>

                <div class="colon">
                    :
                </div>

                <div class="value">
                    <?= htmlspecialchars($tanggalCetak) ?>
                    <?= htmlspecialchars($jamCetak) ?>
                </div>
            </div>


            <div class="row">
                <div class="label">
                    Pelapor
                </div>

                <div class="colon">
                    :
                </div>

                <div class="value">
                    <?= htmlspecialchars($userName) ?>
                </div>
            </div>


            <div class="row">
                <div class="label">
                    Kategori
                </div>

                <div class="colon">
                    :
                </div>

                <div class="value">
                    <?= htmlspecialchars($aduan['nama_kategori'] ?? '-') ?>
                </div>
            </div>


            <div class="row">
                <div class="label">
                    Barang
                </div>

                <div class="colon">
                    :
                </div>

                <div class="value">
                    <?= htmlspecialchars($aduan['barang_aduan']) ?>
                </div>
            </div>


            <div class="row">
                <div class="label">
                    Jumlah
                </div>

                <div class="colon">
                    :
                </div>

                <div class="value">
                    <?= (int)$aduan['jumlah_barang'] ?> unit
                </div>
            </div>


            <div class="row">
                <div class="label">
                    Lokasi
                </div>

                <div class="colon">
                    :
                </div>

                <div class="value">
                    <?= htmlspecialchars($aduan['lokasi']) ?>
                </div>
            </div>

        </div>


        <div class="line"></div>


        <!-- ISI KELUHAN -->
        <div>

            <div class="section-title">
                Isi Keluhan
            </div>

            <div class="complaint">
                <?= nl2br(htmlspecialchars($aduan['isi_keluhan'])) ?>
            </div>

        </div>


        <!-- STATUS -->
        <div class="status-box">

            STATUS ADUAN

            <br>

            <span class="<?= $statusClass ?>">
                <?= htmlspecialchars($status) ?>
            </span>

        </div>


        <div class="line"></div>


        <!-- FOOTER -->
        <div class="footer">

            <div class="thank-you">
                Terima kasih telah menggunakan
                <br>
                layanan Halo Sarpras.
            </div>


            <!-- ID DI PALING BAWAH -->
            <div class="complaint-id">

                <span class="id-label">
                    ID PENGADUAN
                </span>

                <?= htmlspecialchars($nomorAduan) ?>

            </div>

        </div>

    </div>


    <!-- =====================================================
         TOMBOL
    ===================================================== -->

    <div class="print-area">

        <button
            type="button"
            class="btn-print"
            onclick="window.print()"
        >
            🖨 Cetak Struk
        </button>


        <a
            href="riwayat.php?id=<?= (int)$aduan['id_aduan'] ?>"
            class="btn-back"
        >
            Kembali
        </a>

    </div>


    <!-- =====================================================
         AUTO FOCUS / PRINT OPTIONAL
    ===================================================== -->

    <script>
        window.addEventListener('load', function () {

            // Tidak langsung membuka dialog print.
            // User bisa melihat struk terlebih dahulu.

        });
    </script>

</body>
</html>