<?php 
session_start();
require_once '../../config/init.php';
require_once '../../config/database.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASEURL);
    exit;
}

// ==================================================
// LOGIKA HAPUS ANGGOTA
// ==================================================
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['id']);
    $query_hapus = mysqli_query($conn, "DELETE FROM users WHERE id = '$id_hapus' AND role = 'siswa'");
    
    if ($query_hapus) {
        echo "<script>alert('Anggota berhasil dihapus!'); window.location='tambah_anggota.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus anggota!'); window.location='tambah_anggota.php';</script>";
    }
}

// ==================================================
// LOGIKA PENCARIAN (FITUR BARU)
// ==================================================
$keyword = "";
$query_kondisi = "WHERE role = 'siswa'";

if (isset($_POST['cari'])) {
    $keyword = mysqli_real_escape_string($conn, $_POST['keyword']);
    $query_kondisi .= " AND (username LIKE '%$keyword%' OR nama_lengkap LIKE '%$keyword%' OR jurusan LIKE '%$keyword%')";
}

// Ambil data siswa berdasarkan kondisi pencarian
$query_siswa = mysqli_query($conn, "SELECT * FROM users $query_kondisi ORDER BY id DESC");

include '../layout/header.php'; 
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Manajemen Data Anggota 👥</h2>
            <p class="text-muted">Kelola informasi siswa yang terdaftar di sistem.</p>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-user-plus me-2"></i> Tambah Anggota Baru
        </button>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h5 class="fw-bold text-primary mb-0">Daftar Anggota (Siswa)</h5>
                    <span class="badge bg-soft-primary text-primary px-3"><?= mysqli_num_rows($query_siswa); ?> Siswa</span>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <form action="" method="POST" class="d-flex gap-2">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="keyword" class="form-control bg-light border-0" placeholder="Cari nama, username, atau jurusan..." value="<?= $keyword; ?>">
                        </div>
                        <button type="submit" name="cari" class="btn btn-primary px-4 rounded-3">Cari</button>
                        <?php if($keyword != ""): ?>
                            <a href="tambah_anggota.php" class="btn btn-outline-secondary rounded-3"><i class="fas fa-sync-alt"></i></a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="50">NO</th>
                            <th>USERNAME & NAMA</th>
                            <th>KELAS / JURUSAN</th>
                            <th>LEVEL</th>
                            <th class="text-center">TINDAKAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($query_siswa) > 0) : ?>
                            <?php $no = 1; while($row = mysqli_fetch_assoc($query_siswa)) : ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-primary rounded text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <?= strtoupper(substr($row['username'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block text-dark"><?= htmlspecialchars($row['username']); ?></span>
                                            <small class="text-muted"><?= htmlspecialchars($row['nama_lengkap']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary small fw-bold">
                                        <?= htmlspecialchars($row['kelas']); ?> - <?= htmlspecialchars($row['jurusan']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">SISWA</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-light btn-sm text-primary border rounded me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?aksi=hapus&id=<?= $row['id']; ?>" class="btn btn-light btn-sm text-danger border rounded" onclick="return confirm('Hapus data <?= $row['username']; ?>?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted fst-italic">Data siswa tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Input Data Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses_tambah_anggota.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control bg-light border-0" placeholder="Contoh: raffa12" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control bg-light border-0" placeholder="Nama sesuai ijazah" required>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label small fw-bold">Kelas</label>
                            <select name="kelas" class="form-select bg-light border-0">
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                        <div class="col-8">
                            <label class="form-label small fw-bold">Jurusan</label>
                            <input type="text" name="jurusan" class="form-control bg-light border-0" placeholder="Contoh: RPL 1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
mysqli_data_seek($query_siswa, 0); 
while($edit = mysqli_fetch_assoc($query_siswa)) : 
?>
<div class="modal fade" id="modalEdit<?= $edit['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold"><i class="fas fa-user-edit me-2 text-primary"></i>Edit Data Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses_edit_anggota.php" method="POST">
                <input type="hidden" name="id" value="<?= $edit['id']; ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control bg-light border-0" value="<?= htmlspecialchars($edit['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Baru <span class="text-muted" style="font-weight: normal;">(Kosongkan jika tak diubah)</span></label>
                        <input type="password" name="password" class="form-control bg-light border-0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control bg-light border-0" value="<?= htmlspecialchars($edit['nama_lengkap']); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label small fw-bold">Kelas</label>
                            <select name="kelas" class="form-select bg-light border-0">
                                <option value="X" <?= ($edit['kelas'] == 'X') ? 'selected' : ''; ?>>X</option>
                                <option value="XI" <?= ($edit['kelas'] == 'XI') ? 'selected' : ''; ?>>XI</option>
                                <option value="XII" <?= ($edit['kelas'] == 'XII') ? 'selected' : ''; ?>>XII</option>
                            </select>
                        </div>
                        <div class="col-8">
                            <label class="form-label small fw-bold">Jurusan</label>
                            <input type="text" name="jurusan" class="form-control bg-light border-0" value="<?= htmlspecialchars($edit['jurusan']); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update" class="btn btn-primary rounded-pill px-4 shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endwhile; ?>

<?php include '../layout/footer.php'; ?>