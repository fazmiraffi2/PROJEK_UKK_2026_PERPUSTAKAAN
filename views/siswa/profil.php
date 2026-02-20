<?php 
session_start();
require_once '../../config/init.php';
require_once '../../config/database.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header('Location: '.BASEURL);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { header('Location: ../../login.php'); exit; }

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

// Set foto default jika kosong
$foto_profil = (!empty($user['foto'])) ? $user['foto'] : 'default.png';

include '../layout/header.php'; 
?>

<div class="row justify-content-center animate__animated animate__fadeIn">
    <div class="col-md-11 col-lg-10">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 25px;">
            
            <div class="p-5 text-white text-center" style="background: var(--bg-gradient);">
                <div class="mb-3">
                    <div class="rounded-circle bg-white d-inline-flex align-items-center justify-content-center shadow-lg overflow-hidden" style="width: 150px; height: 150px; border: 5px solid rgba(255,255,255,0.3);">
                        <?php if($foto_profil == 'default.png') : ?>
                            <i class="fas fa-user-graduate fa-5x text-primary"></i>
                        <?php else : ?>
                            <img src="../../public/img/profile/<?= $foto_profil; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= htmlspecialchars($user['nama_lengkap']); ?></h3>
                <span class="badge bg-white text-primary rounded-pill px-3 py-2 shadow-sm fw-bold">
                    <i class="fas fa-check-circle me-1"></i> SISWA AKTIF
                </span>
            </div>
            
            <div class="card-body p-4 p-md-5 bg-white">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-2 d-block text-uppercase">Username</label>
                        <div class="p-3 rounded-4 bg-light border-start border-info border-4 shadow-sm">
                            <i class="fas fa-at me-2 text-info"></i>
                            <span class="text-dark fw-bold"><?= htmlspecialchars($user['username']); ?></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-2 d-block text-uppercase">Status Akun</label>
                        <div class="p-3 rounded-4 bg-light border-start border-success border-4 shadow-sm">
                            <i class="fas fa-user-check me-2 text-success"></i>
                            <span class="text-dark fw-bold">Terverifikasi</span>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="small text-muted fw-bold mb-2 d-block text-uppercase">Nama Lengkap</label>
                        <div class="p-3 rounded-4 bg-light border-start border-primary border-4 shadow-sm">
                            <i class="fas fa-id-card me-2 text-primary"></i>
                            <span class="text-dark fw-bold"><?= htmlspecialchars($user['nama_lengkap']); ?></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-2 d-block text-uppercase">Kelas</label>
                        <div class="p-3 rounded-4 bg-light border-start border-warning border-4 shadow-sm">
                            <i class="fas fa-chalkboard me-2 text-warning"></i>
                            <span class="text-dark fw-bold"><?= htmlspecialchars($user['kelas'] ?? 'Belum Diisi'); ?></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-2 d-block text-uppercase">Jurusan</label>
                        <div class="p-3 rounded-4 bg-light border-start border-success border-4 shadow-sm">
                            <i class="fas fa-microchip me-2 text-success"></i>
                            <span class="text-dark fw-bold"><?= htmlspecialchars($user['jurusan'] ?? 'Belum Diisi'); ?></span>
                        </div>
                    </div>
                </div>

                <hr class="my-5 opacity-25">

                <div class="d-flex justify-content-between align-items-center">
                    <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                    <button class="btn btn-primary rounded-pill px-4 py-2 shadow" data-bs-toggle="modal" data-bs-target="#modalEditProfil">
                        <i class="fas fa-edit me-2"></i> Edit Profil
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditProfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <form action="proses_edit_profil.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header border-0 p-4">
                    <h5 class="fw-bold mb-0">Perbarui Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <div class="mb-3 text-center">
                        <label class="form-label d-block small fw-bold">Ganti Foto Profil</label>
                        <input type="file" name="foto" class="form-control form-control-sm rounded-pill">
                        <small class="text-muted">JPG/PNG, Max 2MB (Kosongkan jika tidak diganti)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control rounded-pill" value="<?= $user['nama_lengkap']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" class="form-control rounded-pill bg-light" value="<?= $user['username']; ?>" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Kelas</label>
                            <input type="text" name="kelas" class="form-control rounded-pill" value="<?= $user['kelas']; ?>" placeholder="Contoh: XII">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Jurusan</label>
                            <input type="text" name="jurusan" class="form-control rounded-pill" value="<?= $user['jurusan']; ?>" placeholder="Contoh: RPL">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" name="update" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>