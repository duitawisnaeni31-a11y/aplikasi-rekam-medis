<?php
include 'koneksi.php';

$is_edit         = false;
$id_rekam        = "";
$id_pasien       = "";
$id_dokter       = "";
$id_obat         = "";
$tanggal_periksa = "";
$keluhan         = "";
$diagnosa        = "";
$tindakan        = "";
$resep           = "";
$pesan_sukses     = "";

if (isset($_POST['simpan'])) {
    $id_pasien       = $_POST['id_pasien'];
    $id_dokter       = $_POST['id_dokter'];
    $id_obat         = $_POST['id_obat'];
    $tanggal_periksa = $_POST['tanggal_periksa'];
    $keluhan         = $_POST['keluhan'];
    $diagnosa        = $_POST['diagnosa'];
    $tindakan        = $_POST['tindakan'];
    $resep           = $_POST['resep'];

    if (isset($_POST['id_rekam']) && $_POST['id_rekam'] != "") {
        $id_rekam = $_POST['id_rekam'];
        $query = "UPDATE rekam_medis SET id_pasien='$id_pasien', id_dokter='$id_dokter', id_obat='$id_obat', tanggal_periksa='$tanggal_periksa', keluhan='$keluhan', diagnosa='$diagnosa', tindakan='$tindakan', resep='$resep' WHERE id_rekam='$id_rekam'";
        $pesan_sukses = "Catatan Rekam Medis Berhasil Diubah!";
    } else {
        $query = "INSERT INTO rekam_medis (id_pasien, id_dokter, id_obat, tanggal_periksa, keluhan, diagnosa, tindakan, resep) VALUES ('$id_pasien', '$id_dokter', '$id_obat', '$tanggal_periksa', '$keluhan', '$diagnosa', '$tindakan', '$resep')";
        $pesan_sukses = "Catatan Rekam Medis Berhasil Disimpan!";
    }

    if (mysqli_query($koneksi, $query)) {
        // Modal dipicu via JavaScript
    }
}

if (isset($_GET['edit'])) {
    $is_edit  = true;
    $id_rekam = $_GET['edit'];
    $data     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM rekam_medis WHERE id_rekam='$id_rekam'"));
    if ($data) {
        $id_pasien       = $data['id_pasien'];
        $id_dokter       = $data['id_dokter'];
        $id_obat         = $data['id_obat'];
        $tanggal_periksa = $data['tanggal_periksa'];
        $keluhan         = $data['keluhan'];
        $diagnosa        = $data['diagnosa'];
        $tindakan        = $data['tindakan'];
        $resep           = $data['resep'];
    }
}

if (isset($_GET['hapus'])) {
    $id_rekam = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM rekam_medis WHERE id_rekam='$id_rekam'");
    header("Location: rekam_medis.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekam Medis - Sistem Rekam Medis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <div>
            <div class="brand">
                <div class="brand-logo">R</div>
                <div class="brand-text"><h2>Rekam Medis</h2><span>RS / KLINIK</span></div>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="nav-item"><span>🏠</span> Dashboard</a>
                <a href="pasien.php" class="nav-item"><span>👥</span> Data Pasien</a>
                <a href="dokter.php" class="nav-item"><span>🩺</span> Data Dokter</a>
                <a href="obat.php" class="nav-item"><span>💊</span> Data Obat</a>
                <a href="rekam_medis.php" class="nav-item active"><span>📋</span> Rekam Medis</a>
            </div>
        </div>
        <div class="sidebar-footer">Sistem Rekam Medis RS / Klinik</div>
    </div>

    <div class="main-content">
        <div class="header-title">
            <div>
                <h1>Rekam <span>Medis</span></h1>
                <p>Riwayat pemeriksaan, diagnosa, dan resep</p>
            </div>
        </div>

        <div class="row-grid">
            <div class="panel">
                <h3 class="panel-title"><?= $is_edit ? "Edit Pemeriksaan" : "Tambah Pemeriksaan"; ?></h3>
                <form action="rekam_medis.php" method="POST">
                    <input type="hidden" name="id_rekam" value="<?= $id_rekam; ?>">
                    
                    <div class="form-group">
                        <label>Pilih Pasien</label>
                        <select name="id_pasien" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <?php
                            $pasien = mysqli_query($koneksi, "SELECT * FROM pasien");
                            while ($p = mysqli_fetch_assoc($pasien)) {
                                $selected = ($p['id_pasien'] == $id_pasien) ? "selected" : "";
                                echo "<option value='{$p['id_pasien']}' {$selected}>{$p['nama_pasien']} ({$p['no_rm']})</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Dokter Penanggung Jawab</label>
                        <select name="id_dokter" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <?php
                            $dokter = mysqli_query($koneksi, "SELECT * FROM dokter");
                            while ($d = mysqli_fetch_assoc($dokter)) {
                                $selected = ($d['id_dokter'] == $id_dokter) ? "selected" : "";
                                echo "<option value='{$d['id_dokter']}' {$selected}>{$d['nama_dokter']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Obat yang Diberikan</label>
                        <select name="id_obat" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <?php
                            $obat = mysqli_query($koneksi, "SELECT * FROM obat");
                            while ($o = mysqli_fetch_assoc($obat)) {
                                $selected = ($o['id_obat'] == $id_obat) ? "selected" : "";
                                echo "<option value='{$o['id_obat']}' {$selected}>{$o['nama_obat']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Periksa</label>
                        <input type="date" name="tanggal_periksa" class="form-control" value="<?= $tanggal_periksa; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Keluhan Pasien</label>
                        <textarea name="keluhan" class="form-control" placeholder="Keluhan yang dirasakan pasien..." required><?= $keluhan; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Hasil Diagnosa</label>
                        <textarea name="diagnosa" class="form-control" placeholder="Hasil analisa dokter..." required><?= $diagnosa; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Tindakan Medis</label>
                        <textarea name="tindakan" class="form-control" placeholder="Penanganan yang diberikan..." required><?= $tindakan; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Aturan Pakai / Dosis Resep</label>
                        <input type="text" name="resep" class="form-control" value="<?= $resep; ?>" placeholder="Contoh: 3x1 tablet sesudah makan" required>
                    </div>

                    <button type="submit" name="simpan" class="btn-submit"><?= $is_edit ? "Update Pemeriksaan" : "Simpan Pemeriksaan"; ?></button>
                    <?php if($is_edit): ?><a href="rekam_medis.php" class="btn-cancel">Batal</a><?php endif; ?>
                </form>
            </div>

            <div class="panel">
                <h3 class="panel-title">Daftar Riwayat Pemeriksaan Pasien</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>TGL</th>
                                <th>PASIEN</th>
                                <th>DOKTER</th>
                                <th>DIAGNOSA & TINDAKAN</th>
                                <th>OBAT & RESEP</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT rm.*, p.nama_pasien, p.no_rm, d.nama_dokter, o.nama_obat 
                                    FROM rekam_medis rm
                                    JOIN pasien p ON rm.id_pasien = p.id_pasien
                                    JOIN dokter d ON rm.id_dokter = d.id_dokter
                                    JOIN obat o ON rm.id_obat = o.id_obat
                                    ORDER BY rm.id_rekam DESC";
                            $query = mysqli_query($koneksi, $sql);
                            while ($row = mysqli_fetch_assoc($query)) {
                            ?>
                            <tr>
                                <td><b><?= $row['tanggal_periksa']; ?></b></td>
                                <td>
                                    <b><?= $row['nama_pasien']; ?></b><br>
                                    <span style="font-size:11px; color:#64748b;"><?= $row['no_rm']; ?></span>
                                </td>
                                <td><?= $row['nama_dokter']; ?></td>
                                <td>
                                    <b>Dx:</b> <?= $row['diagnosa']; ?><br>
                                    <span style="font-size:12px; color:#475569;"><b>Tx:</b> <?= $row['tindakan']; ?></span>
                                </td>
                                <td>
                                    <b style="color:#2563eb;"><?= $row['nama_obat']; ?></b><br>
                                    <i style="font-size:12px; color:#475569;"><?= $row['resep']; ?></i>
                                </td>
                                <td class="action-links">
                                    <a href="rekam_medis.php?edit=<?= $row['id_rekam']; ?>" class="btn-action-edit">Edit</a>
                                    <a href="javascript:void(0)" onclick="konfirmasiHapus('rekam_medis.php?hapus=<?= $row['id_rekam']; ?>')" class="btn-action-delete">Hapus</a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- POP-UP MODAL NOTIFIKASI SUKSES (SIMPAN/UPDATE) -->
    <div id="modalSukses" class="modal-overlay" style="<?= !empty($pesan_sukses) ? 'display:flex;' : 'display:none;'; ?>">
        <div class="modal-content">
            <div style="font-size:40px; color:#2563eb; margin-bottom:10px;">✓</div>
            <h4>Berhasil</h4>
            <p><?= $pesan_sukses; ?></p>
            <div class="modal-buttons">
                <a href="rekam_medis.php" class="btn-submit" style="text-decoration:none; display:inline-block; width:120px; padding:10px;">OK</a>
            </div>
        </div>
    </div>

    <!-- POP-UP MODAL CONFIRMATION HAPUS -->
    <div id="modalHapus" class="modal-overlay">
        <div class="modal-content">
            <h4>Konfirmasi Hapus</h4>
            <p>Apakah Anda yakin ingin menghapus catatan rekam medis ini?</p>
            <div class="modal-buttons">
                <a id="btnYaHapus" href="#" class="btn-confirm">Ya, Hapus</a>
                <button onclick="tutupModal()" class="btn-cancel-modal">Batal</button>
            </div>
        </div>
    </div>

    <script>
        function konfirmasiHapus(url) {
            document.getElementById('btnYaHapus').setAttribute('href', url);
            document.getElementById('modalHapus').style.display = 'flex';
        }
        function tutupModal() {
            document.getElementById('modalHapus').style.display = 'none';
        }
    </script>
</body>
</html>