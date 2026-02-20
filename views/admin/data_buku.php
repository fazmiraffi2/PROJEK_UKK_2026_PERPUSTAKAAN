<?php 
session_start();
require_once '../../config/init.php';

// Proteksi akses admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: '.BASEURL);
    exit;
}

$model = new PerpusModel();

// Ambil keyword dari URL jika ada
$keyword = isset($_GET['search']) ? $_GET['search'] : "";
$buku = $model->getAllBuku($keyword);

// Logika Proses Tambah
if(isset($_POST['tambah'])) {
    if($model->tambahBuku($_POST, $_FILES)) {
        $_SESSION['msg'] = "Buku baru berhasil ditambahkan!";
        $_SESSION['msg_type'] = "success";
        header('Location: data_buku.php?search=' . $keyword); // Kembali ke posisi search semula
        exit;
    } else {
        $_SESSION['msg'] = "Gagal menambah buku. Cek format gambar!";
        $_SESSION['msg_type'] = "danger";
    }
}

// Logika Proses Edit
if(isset($_POST['edit'])) {
    if($model->updateBuku($_POST, $_FILES)) {
        $_SESSION['msg'] = "Data buku berhasil diperbarui!";
        $_SESSION['msg_type'] = "success";
        header('Location: data_buku.php?search=' . $keyword); // Kembali ke posisi search semula
        exit;
    } else {
        $_SESSION['msg'] = "Gagal memperbarui data buku!";
        $_SESSION['msg_type'] = "danger";
    }
}

include '../layout/header.php'; 
?>

<?php if(isset($_SESSION['msg'])) : ?>
    <div class="alert alert-<?= $_SESSION['msg_type']; ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
        <div class="d-flex align-items-center">
            <i class="fas <?= $_SESSION['msg_type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
            <div><?= $_SESSION['msg']; ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['msg']); unset($_SESSION['msg_type']); ?>
<?php endif; ?>

<div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold text-primary mb-1"><i class="fas fa-book me-2"></i> Kelola Koleksi Buku</h4>
            <p class="text-muted small mb-0">Total koleksi: <?= mysqli_num_rows($buku); ?> Judul Buku</p>
        </div>
        
        <div class="d-flex gap-2 flex-grow-1 justify-content-end">
            <form action="" method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" name="search" class="form-control rounded-start-pill border-primary ps-3" placeholder="Cari buku/penulis..." value="<?= htmlspecialchars($keyword); ?>">
                    <button class="btn btn-primary rounded-end-pill px-3" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <?php if($keyword != "") : ?>
                    <a href="data_buku.php" class="btn btn-outline-secondary rounded-pill ms-2"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </form>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i> Tambah
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th width="100">Sampul</th>
                    <th>Informasi Buku</th>
                    <th>Penulis</th>
                    <th>Kategori/Kelas</th>
                    <th>Stok</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($buku) == 0) : ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">Data buku tidak ditemukan.</td></tr>
                <?php endif; ?>
                
                <?php $no=1; while($row = mysqli_fetch_assoc($buku)) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td>
                        <?php 
                        $cover = !empty($row['foto']) ? $row['foto'] : 'default.png'; 
                        $pathCover = "../../public/img/buku/" . $cover . "?t=" . time();
                        ?>
                        <img src="<?= $pathCover; ?>" alt="Cover" class="rounded shadow-sm" style="width: 50px; height: 70px; object-fit: cover; border: 1px solid #eee;">
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= $row['judul']; ?></div>
                        <span class="badge bg-light text-primary fw-normal" style="font-size: 0.7rem;">ID: #<?= $row['id']; ?></span>
                    </td>
                    <td><?= $row['penulis']; ?></td>
                    <td>
                        <?php 
                        $kat = $row['kelas_buku'];
                        $color = ($kat == 'Cerita') ? 'bg-warning' : (($kat == 'Umum' || empty($kat)) ? 'bg-secondary' : 'bg-info');
                        ?>
                        <span class="badge <?= $color; ?> text-white"><?= !empty($kat) ? $kat : 'Umum'; ?></span>
                    </td>
                    <td>
                        <span class="badge <?= ($row['stok'] > 0) ? 'bg-soft-primary text-primary' : 'bg-soft-danger text-danger'; ?> px-3">
                            <?= $row['stok']; ?> Eks
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-warning rounded-pill px-3 me-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEdit<?= $row['id']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="hapus_buku.php?id=<?= $row['id']; ?>&search=<?= urlencode($keyword); ?>" 
                               class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>

                <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">Update Data Buku</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="?search=<?= urlencode($keyword); ?>" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <input type="hidden" name="foto_lama" value="<?= $row['foto']; ?>">
                                
                                <div class="modal-body py-4">
                                    <div class="mb-3 bg-light p-3 rounded-3 d-flex align-items-center">
                                         <img src="<?= $pathCover; ?>" class="rounded shadow-sm me-3" style="width: 50px; height: 70px; object-fit: cover;">
                                         <div class="flex-grow-1">
                                             <label class="form-label fw-bold small mb-1">Ganti Sampul</label>
                                             <input type="file" name="foto" class="form-control form-control-sm">
                                         </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Judul Buku</label>
                                        <input type="text" name="judul" class="form-control" value="<?= $row['judul']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Penulis</label>
                                        <input type="text" name="penulis" class="form-control" value="<?= $row['penulis']; ?>" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold small">Kategori / Kelas</label>
                                            <select name="kelas_buku" class="form-select shadow-none">
                                                <option value="Umum" <?= ($row['kelas_buku'] == 'Umum') ? 'selected' : ''; ?>>Umum</option>
                                                <option value="Cerita" <?= ($row['kelas_buku'] == 'Cerita') ? 'selected' : ''; ?>>Cerita</option>
                                                <option value="Kelas 10" <?= ($row['kelas_buku'] == 'Kelas 10') ? 'selected' : ''; ?>>Kelas 10</option>
                                                <option value="Kelas 11" <?= ($row['kelas_buku'] == 'Kelas 11') ? 'selected' : ''; ?>>Kelas 11</option>
                                                <option value="Kelas 12" <?= ($row['kelas_buku'] == 'Kelas 12') ? 'selected' : ''; ?>>Kelas 12</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold small">Jumlah Stok</label>
                                            <input type="number" name="stok" class="form-control" value="<?= $row['stok']; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pb-4">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" name="edit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Koleksi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="?search=<?= urlencode($keyword); ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Sampul Buku</label>
                        <input type="file" name="foto" class="form-control shadow-none" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Judul Buku</label>
                        <input type="text" name="judul" class="form-control shadow-none" placeholder="Masukkan judul buku" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Penulis</label>
                        <input type="text" name="penulis" class="form-control shadow-none" placeholder="Nama penulis" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Kategori / Kelas</label>
                            <select name="kelas_buku" class="form-select shadow-none">
                                <option value="Umum">Umum</option>
                                <option value="Cerita">Cerita</option>
                                <option value="Kelas 10">Kelas 10</option>
                                <option value="Kelas 11">Kelas 11</option>
                                <option value="Kelas 12">Kelas 12</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Jumlah Stok</label>
                            <input type="number" name="stok" class="form-control shadow-none" placeholder="Contoh: 10" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary rounded-pill px-4">Simpan Buku</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-soft-primary { background-color: #e3f2fd; color: #0d6efd; }
.bg-soft-danger { background-color: #fbe9e7; color: #dc3545; }
.table thead th { 
    font-size: 0.8rem; 
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
    border-bottom: 2px solid #f8f9fa;
}
.table-hover tbody tr:hover {
    background-color: rgba(78, 115, 223, 0.03);
    transition: background-color 0.2s ease;
}
.form-control:focus, .form-select:focus { 
    border-color: #4e73df; 
    box-shadow: none; 
}
.btn { transition: all 0.2s ease; }
</style>

<?php include '../layout/footer.php'; ?>