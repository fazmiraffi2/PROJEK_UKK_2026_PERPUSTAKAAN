<?php 
session_start();
require_once '../../config/init.php';
require_once '../../config/database.php';

// Proteksi Admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: '.BASEURL);
    exit;
}

$model = new PerpusModel();

// --- 1. AMBIL DATA STATISTIK UNTUK CARD ---
$jml_buku    = $model->hitungData('buku');
$jml_siswa   = $model->hitungData('users', "WHERE role = 'siswa'");
$jml_pinjam  = $model->hitungData('peminjaman', "WHERE status = 'dipinjam'");

// Hitung khusus untuk angka notifikasi pengembalian (Badge)
$query_notif_kembali = mysqli_query($conn, "SELECT COUNT(*) as jml FROM peminjaman WHERE status = 'proses_kembali'");
$notif_kembali = mysqli_fetch_assoc($query_notif_kembali)['jml'];

// Hitung Total Kas Denda
$query_kas = mysqli_query($conn, "SELECT SUM(denda) as total_kas FROM peminjaman WHERE status = 'kembali'");
$data_kas  = mysqli_fetch_assoc($query_kas);
$total_kas = $data_kas['total_kas'] ?? 0;

include '../layout/header.php'; 
?>

<div class="mb-4">
    <div class="card border-0 shadow-lg p-5 text-white position-relative overflow-hidden" 
         style="border-radius: 25px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
        
        <i class="fas fa-user-shield position-absolute" style="font-size: 150px; right: -30px; bottom: -30px; opacity: 0.1;"></i>
        
        <div class="position-relative">
            <h2 class="fw-bold mb-1">Administrator Panel 🛡️</h2>
            <p class="opacity-75 mb-0">Selamat datang kembali, <strong><?= $_SESSION['nama_lengkap'] ?? $_SESSION['username']; ?></strong>. Pantau kesehatan sirkulasi buku hari ini.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 5px solid #4e73df; border-radius: 15px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1" style="font-size: 0.75rem;">Koleksi Buku</div>
                        <div class="h3 mb-0 fw-bold text-gray-800"><?= $jml_buku; ?></div>
                        <small class="text-muted">Judul terdaftar</small>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                        <i class="fas fa-book fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 5px solid #1cc88a; border-radius: 15px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-success text-uppercase mb-1" style="font-size: 0.75rem;">Total Anggota</div>
                        <div class="h3 mb-0 fw-bold text-gray-800"><?= $jml_siswa; ?></div>
                        <small class="text-muted">Siswa aktif</small>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-circle p-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 5px solid #f6c23e; border-radius: 15px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1" style="font-size: 0.75rem;">Pinjam Aktif</div>
                        <div class="h3 mb-0 fw-bold text-gray-800"><?= $jml_pinjam; ?></div>
                        <small class="text-muted">Belum kembali</small>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                        <i class="fas fa-hand-holding fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 5px solid #e74a3b; border-radius: 15px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1" style="font-size: 0.75rem;">Kas Denda</div>
                        <div class="h3 mb-0 fw-bold text-gray-800">Rp <?= number_format($total_kas, 0, ',', '.'); ?></div>
                        <small class="text-muted">Terkumpul</small>
                    </div>
                    <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
            <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-rocket me-2 text-primary"></i>Navigasi Cepat</h5>
            <div class="row g-3 text-center">
                <div class="col-6 col-md-3">
                    <a href="data_buku.php" class="btn btn-light w-100 py-4 border-0 shadow-sm rounded-4 bg-white border">
                        <i class="fas fa-plus-circle fa-2x d-block mb-3 text-primary"></i>
                        <span class="fw-semibold">Kelola Buku</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="tambah_anggota.php" class="btn btn-light w-100 py-4 border-0 shadow-sm rounded-4 bg-white border">
                        <i class="fas fa-user-plus fa-2x d-block mb-3 text-success"></i>
                        <span class="fw-semibold">Daftar Siswa</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="admin_transaksi.php" class="btn btn-light w-100 py-4 border-0 shadow-sm rounded-4 bg-white border position-relative">
                        <i class="fas fa-sync-alt fa-2x d-block mb-3 text-warning"></i>
                        <span class="fw-semibold">Pengembalian</span>
                        <?php if($notif_kembali > 0) : ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger animate__animated animate__pulse animate__infinite" style="font-size: 0.8rem;">
                            <?= $notif_kembali; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="laporan.php" class="btn btn-light w-100 py-4 border-0 shadow-sm rounded-4 bg-white border">
                        <i class="fas fa-file-pdf fa-2x d-block mb-3 text-danger"></i>
                        <span class="fw-semibold">Laporan PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>