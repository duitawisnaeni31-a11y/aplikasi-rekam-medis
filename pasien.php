<?php
include 'koneksi.php';

$is_edit       = false;
$id_pasien     = "";
$no_rm         = "";
$nama_pasien   = "";
$jenis_kelamin = "";
$tanggal_lahir = "";
$alamat        = "";
$pesan_sukses  = "";

// 1. KODE UNTUK AUTO GENERATE NO. RM OTOMATIS (MISAL: RM-004)
if (isset($_GET['edit'])) {
    $is_edit   = true;
    $id_pasien = $_GET['edit'];
    $data      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pasien WHERE id_pasien='$id_pasien'"));
    if ($data) {
        $no_rm         = $data['no_rm'];
        $nama_pasien   = $data['nama_pasien'];
        $jenis_kelamin = $data['jenis_kelamin'];
        $tanggal_lahir = $data['tanggal_lahir'];
        $alamat        = $data['alamat'];
    }
} else {
    // Cari No RM tertinggi/terakhir di database
    $query_max = mysqli_query($koneksi, "SELECT MAX(no_rm) as max_code FROM pasien");
    $data_max  = mysqli_fetch_assoc($query_max);
    $last_code = $data_max['max_code'];

    // Ambil angka dari format 'RM-003' -> 3
    $urutan = (int) substr($last_code, 3);
    $urutan++;

    // Format kembali menjadi RM-004, RM-005, dst.
    $no_rm = "RM-" . sprintf("%03s", $urutan);
}

// 2. PROSES SIMPAN / UPDATE DATA
if (isset($_POST['simpan'])) {
    $no_rm         = $_POST['no_rm'];
    $nama_pasien   = $_POST['nama_pasien'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat        = $_POST['alamat'];

    if (isset($_POST['id_pasien']) && $_POST['id_pasien'] != "") {
        $id_pasien = $_POST['id_pasien'];
        $query = "UPDATE pasien SET no_rm='$no_rm', nama_pasien='$nama_pasien', jenis_kelamin='$jenis_kelamin', tanggal_lahir='$tanggal_lahir', alamat='$alamat' WHERE id_pasien='$id_pasien'";
        $pesan_sukses = "Data Pasien Berhasil Diubah!";
    } else {
        $query = "INSERT INTO pasien (no_rm, nama_pasien, jenis_kelamin, tanggal_lahir, alamat) VALUES ('$no_rm', '$nama_pasien', '$jenis_kelamin', '$tanggal_lahir', '$alamat')";
        $pesan_sukses = "Data Pasien Berhasil Disimpan!";
    }

    if (mysqli_query($koneksi, $query)) {
        // Modal dipicu otomatis oleh PHP/JS
    }
}

if (isset($_GET['hapus'])) {
    $id_pasien = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM pasien WHERE id_pasien='$id_pasien'");
    header("Location: pasien.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pasien - Sistem Rekam Medis</title>
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
                <a href="pasien.php" class="nav-item active"><span>👥</span> Data Pasien</a>
                <a href="dokter.php" class="nav-item"><span>🩺</span> Data Dokter</a>
                <a href="obat.php" class="nav-item"><span>💊</span> Data Obat</a>
                <a href="rekam_medis.php" class="nav-item"><span>📋</span> Rekam Medis</a>
            </div>
        </div>
        <div class="sidebar-footer">Sistem Rekam Medis RS / Klinik</div>
    </div>

    <div class="main-content">
        <div class="header-title">
            <div>
                <h1>Data <span>Pasien</span></h1>
                <p>Kelola identitas dan riwayat pasien</p>
            </div>
        </div>

        <div class="row-grid">
            <div class="panel">
                <h3 class="panel-title"><?= $is_edit ? "Edit Data Pasien" : "Tambah Pasien"; ?></h3>
                <form action="pasien.php" method="POST">
                    <input type="hidden" name="id_pasien" value="<?= $id_pasien; ?>">
                    
                    <div class="form-group">
                        <label>No. RM (Otomatis)</label>
                        <!-- atribut readonly membuat kolom terisi otomatis dan tidak perlu diketik manual -->
                        <input type="text" name="no_rm" class="form-control" value="<?= $no_rm; ?>" readonly style="background-color: #e2e8f0; font-weight: bold; cursor: not-allowed;">
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Pasien</label>
                        <input type="text" name="nama_pasien" class="form-control" value="<?= $nama_pasien; ?>" required placeholder="Masukkan nama pasien...">
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="L" <?= ($jenis_kelamin == 'L') ? 'selected' : ''; ?>>Laki-laki (L)</option>
                            <option value="P" <?= ($jenis_kelamin == 'P') ? 'selected' : ''; ?>>Perempuan (P)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="<?= $tanggal_lahir; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" required placeholder="Masukkan alamat pasien..."><?= $alamat; ?></textarea>
                    </div>
                    <button type="submit" name="simpan" class="btn-submit"><?= $is_edit ? "Update Pasien" : "Simpan Pasien"; ?></button>
                    <?php if($is_edit): ?><a href="pasien.php" class="btn-cancel">Batal</a><?php endif; ?>
                </form>
            </div>

            <div class="panel">
                <h3 class="panel-title">Daftar Pasien</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>NO RM</th>
                                <th>NAMA PASIEN</th>
                                <th>JK</th>
                                <th>TGL LAHIR</th>
                                <th>ALAMAT</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $data = mysqli_query($koneksi, "SELECT * FROM pasien ORDER BY id_pasien DESC");
                            while ($d = mysqli_fetch_assoc($data)) {
                            ?>
                            <tr>
                                <td><b><?= $d['no_rm']; ?></b></td>
                                <td><b><?= $d['nama_pasien']; ?></b></td>
                                <td><span class="badge-jk <?= ($d['jenis_kelamin']=='L') ? 'badge-l':'badge-p'; ?>"><?= $d['jenis_kelamin']; ?></span></td>
                                <td><?= $d['tanggal_lahir']; ?></td>
                                <td><?= $d['alamat']; ?></td>
                                <td class="action-links">
                                    <a href="pasien.php?edit=<?= $d['id_pasien']; ?>" class="btn-action-edit">Edit</a>
                                    <a href="javascript:void(0)" onclick="konfirmasiHapus('pasien.php?hapus=<?= $d['id_pasien']; ?>')" class="btn-action-delete">Hapus</a>
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
                <a href="pasien.php" class="btn-submit" style="text-decoration:none; display:inline-block; width:120px; padding:10px;">OK</a>
            </div>
        </div>
    </div>

    <!-- POP-UP MODAL CONFIRMATION HAPUS -->
    <div id="modalHapus" class="modal-overlay">
        <div class="modal-content">
            <h4>Konfirmasi Hapus</h4>
            <p>Apakah Anda yakin ingin menghapus data pasien ini?</p>
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