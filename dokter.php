<?php
include 'koneksi.php';

$is_edit      = false;
$id_dokter    = "";
$nama_dokter  = "";
$spesialisasi = "";
$pesan_sukses = "";

if (isset($_POST['simpan'])) {
    $nama_dokter  = $_POST['nama_dokter'];
    $spesialisasi = $_POST['spesialisasi'];

    if (isset($_POST['id_dokter']) && $_POST['id_dokter'] != "") {
        $id_dokter = $_POST['id_dokter'];
        $query = "UPDATE dokter SET nama_dokter='$nama_dokter', spesialisasi='$spesialisasi' WHERE id_dokter='$id_dokter'";
        $pesan_sukses = "Data Dokter Berhasil Diubah!";
    } else {
        $query = "INSERT INTO dokter (nama_dokter, spesialisasi) VALUES ('$nama_dokter', '$spesialisasi')";
        $pesan_sukses = "Data Dokter Berhasil Disimpan!";
    }

    if (mysqli_query($koneksi, $query)) {
        // Modal akan dipicu oleh JavaScript di bawah
    }
}

if (isset($_GET['edit'])) {
    $is_edit   = true;
    $id_dokter = $_GET['edit'];
    $data      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM dokter WHERE id_dokter='$id_dokter'"));
    if ($data) {
        $nama_dokter  = $data['nama_dokter'];
        $spesialisasi = $data['spesialisasi'];
    }
}

if (isset($_GET['hapus'])) {
    $id_dokter = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM dokter WHERE id_dokter='$id_dokter'");
    header("Location: dokter.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Dokter - Sistem Rekam Medis</title>
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
                <a href="dokter.php" class="nav-item active"><span>🩺</span> Data Dokter</a>
                <a href="obat.php" class="nav-item"><span>💊</span> Data Obat</a>
                <a href="rekam_medis.php" class="nav-item"><span>📋</span> Rekam Medis</a>
            </div>
        </div>
        <div class="sidebar-footer">Sistem Rekam Medis RS / Klinik</div>
    </div>

    <div class="main-content">
        <div class="header-title">
            <div>
                <h1>Data <span>Dokter</span></h1>
                <p>Tenaga medis yang bertugas</p>
            </div>
        </div>

        <div class="row-grid">
            <div class="panel">
                <h3 class="panel-title"><?= $is_edit ? "Edit Data Dokter" : "Tambah Dokter"; ?></h3>
                <form action="dokter.php" method="POST">
                    <input type="hidden" name="id_dokter" value="<?= $id_dokter; ?>">
                    <div class="form-group">
                        <label>Nama Dokter</label>
                        <input type="text" name="nama_dokter" class="form-control" value="<?= $nama_dokter; ?>" placeholder="dr. ..." required>
                    </div>
                    <div class="form-group">
                        <label>Spesialisasi</label>
                        <input type="text" name="spesialisasi" class="form-control" value="<?= $spesialisasi; ?>" placeholder="Spesialis ..." required>
                    </div>
                    <button type="submit" name="simpan" class="btn-submit"><?= $is_edit ? "Update Dokter" : "Simpan Dokter"; ?></button>
                    <?php if($is_edit): ?><a href="dokter.php" class="btn-cancel">Batal</a><?php endif; ?>
                </form>
            </div>

            <div class="panel">
                <h3 class="panel-title">Daftar Dokter</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA DOKTER</th>
                                <th>SPESIALISASI</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $data = mysqli_query($koneksi, "SELECT * FROM dokter ORDER BY id_dokter DESC");
                            while ($d = mysqli_fetch_assoc($data)) {
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><b><?= $d['nama_dokter']; ?></b></td>
                                <td><span class="badge-spesialis"><?= $d['spesialisasi']; ?></span></td>
                                <td class="action-links">
                                    <a href="dokter.php?edit=<?= $d['id_dokter']; ?>" class="btn-action-edit">Edit</a>
                                    <a href="javascript:void(0)" onclick="konfirmasiHapus('dokter.php?hapus=<?= $d['id_dokter']; ?>')" class="btn-action-delete">Hapus</a>
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
                <a href="dokter.php" class="btn-submit" style="text-decoration:none; display:inline-block; width:120px; padding:10px;">OK</a>
            </div>
        </div>
    </div>

    <!-- POP-UP MODAL CONFIRMATION HAPUS -->
    <div id="modalHapus" class="modal-overlay">
        <div class="modal-content">
            <h4>Konfirmasi Hapus</h4>
            <p>Apakah Anda yakin ingin menghapus data dokter ini?</p>
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