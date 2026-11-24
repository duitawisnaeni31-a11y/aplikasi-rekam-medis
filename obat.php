<?php
include 'koneksi.php';

$is_edit      = false;
$id_obat      = "";
$nama_obat    = "";
$satuan       = "";
$stok         = "";
$pesan_sukses = "";

if (isset($_POST['simpan'])) {
    $nama_obat = $_POST['nama_obat'];
    $satuan    = $_POST['satuan'];
    $stok      = $_POST['stok'];

    if (isset($_POST['id_obat']) && $_POST['id_obat'] != "") {
        $id_obat = $_POST['id_obat'];
        $query = "UPDATE obat SET nama_obat='$nama_obat', satuan='$satuan', stok='$stok' WHERE id_obat='$id_obat'";
        $pesan_sukses = "Data Obat Berhasil Diubah!";
    } else {
        $query = "INSERT INTO obat (nama_obat, satuan, stok) VALUES ('$nama_obat', '$satuan', '$stok')";
        $pesan_sukses = "Data Obat Berhasil Disimpan!";
    }

    if (mysqli_query($koneksi, $query)) {
        // Modal dipicu via JavaScript
    }
}

if (isset($_GET['edit'])) {
    $is_edit = true;
    $id_obat = $_GET['edit'];
    $data    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM obat WHERE id_obat='$id_obat'"));
    if ($data) {
        $nama_obat = $data['nama_obat'];
        $satuan    = $data['satuan'];
        $stok      = $data['stok'];
    }
}

if (isset($_GET['hapus'])) {
    $id_obat = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM obat WHERE id_obat='$id_obat'");
    header("Location: obat.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Obat - Sistem Rekam Medis</title>
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
                <a href="obat.php" class="nav-item active"><span>💊</span> Data Obat</a>
                <a href="rekam_medis.php" class="nav-item"><span>📋</span> Rekam Medis</a>
            </div>
        </div>
        <div class="sidebar-footer">Sistem Rekam Medis RS / Klinik</div>
    </div>

    <div class="main-content">
        <div class="header-title">
            <div>
                <h1>Data <span>Obat</span></h1>
                <p>Pantau ketersediaan stok obat</p>
            </div>
        </div>

        <div class="row-grid">
            <div class="panel">
                <h3 class="panel-title"><?= $is_edit ? "Edit Data Obat" : "Tambah Obat"; ?></h3>
                <form action="obat.php" method="POST">
                    <input type="hidden" name="id_obat" value="<?= $id_obat; ?>">
                    <div class="form-group">
                        <label>Nama Obat</label>
                        <input type="text" name="nama_obat" class="form-control" value="<?= $nama_obat; ?>" placeholder="Masukkan nama obat..." required>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text" name="satuan" class="form-control" value="<?= $satuan; ?>" placeholder="Tablet / Kapsul / Sirup" required>
                    </div>
                    <div class="form-group">
                        <label>Stok Obat</label>
                        <input type="number" name="stok" class="form-control" value="<?= $stok; ?>" min="0" placeholder="Jumlah stok" required>
                    </div>
                    <button type="submit" name="simpan" class="btn-submit"><?= $is_edit ? "Update Obat" : "Simpan Obat"; ?></button>
                    <?php if($is_edit): ?><a href="obat.php" class="btn-cancel">Batal</a><?php endif; ?>
                </form>
            </div>

            <div class="panel">
                <h3 class="panel-title">Daftar Obat</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA OBAT</th>
                                <th>SATUAN</th>
                                <th>STOK</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $data = mysqli_query($koneksi, "SELECT * FROM obat ORDER BY id_obat DESC");
                            while ($d = mysqli_fetch_assoc($data)) {
                                $pct = min(100, max(10, $d['stok']));
                                $bar_color = ($d['stok'] < 40) ? '#ef4444' : (($d['stok'] < 80) ? '#d97706' : '#2563eb');
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><b><?= $d['nama_obat']; ?></b></td>
                                <td><?= $d['satuan']; ?></td>
                                <td>
                                    <b><?= $d['stok']; ?></b>
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill" style="width: <?= $pct; ?>%; background: <?= $bar_color; ?>;"></div>
                                    </div>
                                </td>
                                <td class="action-links">
                                    <a href="obat.php?edit=<?= $d['id_obat']; ?>" class="btn-action-edit">Edit</a>
                                    <a href="javascript:void(0)" onclick="konfirmasiHapus('obat.php?hapus=<?= $d['id_obat']; ?>')" class="btn-action-delete">Hapus</a>
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
                <a href="obat.php" class="btn-submit" style="text-decoration:none; display:inline-block; width:120px; padding:10px;">OK</a>
            </div>
        </div>
    </div>

    <!-- POP-UP MODAL CONFIRMATION HAPUS -->
    <div id="modalHapus" class="modal-overlay">
        <div class="modal-content">
            <h4>Konfirmasi Hapus</h4>
            <p>Apakah Anda yakin ingin menghapus data obat ini?</p>
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