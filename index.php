<?php
include 'koneksi.php';

$total_pasien = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pasien"))['total'];
$total_dokter = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM dokter"))['total'];
$total_obat   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM obat"))['total'];
$total_rekam  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM rekam_medis"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Rekam Medis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <div>
            <div class="brand">
                <div class="brand-logo">R</div>
                <div class="brand-text">
                    <h2>Rekam Medis</h2>
                    <span>RS / KLINIK</span>
                </div>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="nav-item active"><span>🏠</span> Dashboard</a>
                <a href="pasien.php" class="nav-item"><span>👥</span> Data Pasien</a>
                <a href="dokter.php" class="nav-item"><span>🩺</span> Data Dokter</a>
                <a href="obat.php" class="nav-item"><span>💊</span> Data Obat</a>
                <a href="rekam_medis.php" class="nav-item"><span>📋</span> Rekam Medis</a>
            </div>
        </div>
        <div class="sidebar-footer">
            Sistem Rekam Medis 
        </div>
    </div>

    <div class="main-content">
        <div class="header-title">
            <div>
                <h1>Sistem Rekam Medis <span>RS / Klinik</span></h1>
                <p>Ringkasan aktivitas klinik hari ini</p>
            </div>
            <div class="date-badge">
                <?= date('l, d F Y'); ?>
            </div>
        </div>

        <div class="card-grid">
            <div class="stat-card card-pasien">
                <h4>TOTAL PASIEN</h4>
                <div class="value"><?= $total_pasien; ?></div>
            </div>
            <div class="stat-card card-dokter">
                <h4>TOTAL DOKTER</h4>
                <div class="value"><?= $total_dokter; ?></div>
            </div>
            <div class="stat-card card-obat">
                <h4>TOTAL OBAT</h4>
                <div class="value"><?= $total_obat; ?></div>
            </div>
            <div class="stat-card card-rekam">
                <h4>TOTAL PEMERIKSAAN</h4>
                <div class="value"><?= $total_rekam; ?></div>
            </div>
        </div>

        <div class="row-grid dashboard-grid">
            <div class="panel">
                <h3 class="panel-title"><?= ($total_rekam < 5) ?$total_rekam : 5; ?> Pemeriksaan Terakhir</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>TGL PERIKSA</th>
                                <th>PASIEN</th>
                                <th>DOKTER</th>
                                <th>DIAGNOSA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_terbaru = "SELECT rm.tanggal_periksa, p.nama_pasien, d.nama_dokter, rm.diagnosa 
                                            FROM rekam_medis rm
                                            JOIN pasien p ON rm.id_pasien = p.id_pasien
                                            JOIN dokter d ON rm.id_dokter = d.id_dokter
                                            ORDER BY rm.id_rekam DESC LIMIT 5";
                            $res_terbaru = mysqli_query($koneksi,$sql_terbaru);
                            while ($row = mysqli_fetch_assoc($res_terbaru)) {
                            ?>
                            <tr>
                                <td><b><?= $row['tanggal_periksa']; ?></b></td>
                                <td><b><?= $row['nama_pasien']; ?></b></td>
                                <td><?= $row['nama_dokter']; ?></td>
                                <td><span class="badge-diagnosa"><?= $row['diagnosa']; ?></span></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <h3 class="panel-title">Peringatan Stok Obat</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>OBAT</th>
                                <th>STOK</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_stok = "SELECT nama_obat, stok, satuan FROM obat ORDER BY stok ASC LIMIT 5";
                            $res_stok = mysqli_query($koneksi,$sql_stok);
                            while ($o = mysqli_fetch_assoc($res_stok)) {$pct = min(100, max(10, $o['stok']));$bar_color = ($o['stok'] < 40) ? '#e74c3c' : (($o['stok'] < 80) ? '#d4a359' : '#0e7c7b');
                            ?>
                            <tr>
                                <td><?= $o['nama_obat']; ?></td>
                                <td>
                                    <b><?= $o['stok']; ?></b> <?=$o['satuan']; ?>
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill" style="width: <?= $pct; ?>\%; background: <?=$bar_color; ?>;"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>