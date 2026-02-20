<?php 
session_start();
require_once '../../config/init.php';

// Proteksi: Hanya Siswa yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: " . BASEURL);
    exit;
}

// Safety Net: Jika nama_lengkap tidak ada di session, gunakan username agar tidak error
$nama_tampil = $_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Siswa';

include '../layout/header.php'; 
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card card-modern border-0 shadow-lg p-5 text-white" 
             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 25px; position: relative; overflow: hidden;">
            
            <div style="position: absolute; right: -50px; top: -50px; opacity: 0.2;">
                <i class="fas fa-book-open fa-10x"></i>
            </div>

            <div class="position-relative">
                <h1 class="fw-bold display-5">Halo, <?= htmlspecialchars($nama_tampil); ?>! 👋</h1>
                <p class="lead opacity-75">Senang melihatmu kembali. Jelajahi ribuan ilmu pengetahuan dalam genggamanmu.</p>
                <hr class="w-25 border-2 opacity-50">
                <div class="d-flex gap-2 mt-4">
                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-user-graduate me-1"></i> Siswa Aktif
                    </span>
                    <span class="badge bg-soft-light px-3 py-2 rounded-pill shadow-sm" style="background: rgba(255,255,255,0.2)">
                        <i class="fas fa-calendar-alt me-1"></i> <?= date('d M Y'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-4">
        <div class="card card-modern border-0 shadow-sm h-100 p-4 text-center">
            <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="fas fa-search fa-2x"></i>
            </div>
            <h5 class="fw-bold">Eksplorasi Buku</h5>
            <p class="text-muted small">Temukan referensi tugas atau novel favoritmu di sini.</p>
            <a href="daftar_buku.php" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm mt-auto">
                Cari Koleksi <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-modern border-0 shadow-sm h-100 p-4 text-center">
            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="fas fa-history fa-2x"></i>
            </div>
            <h5 class="fw-bold">Riwayat Pinjam</h5>
            <p class="text-muted small">Pantau status pengembalian dan denda buku kamu.</p>
            <a href="riwayat.php" class="btn btn-success w-100 rounded-pill py-2 shadow-sm mt-auto">
                Cek Riwayat <i class="fas fa-clock ms-2"></i>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-modern border-0 shadow-sm h-100 p-4 text-center border-start border-4 border-warning">
            <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="fas fa-info-circle fa-2x"></i>
            </div>
            <h5 class="fw-bold">Aturan Pinjam</h5>
            <p class="text-muted small">Batas pinjam 7 hari. Telat kembalikan? Denda Rp 1.000/hari.</p>
            <button class="btn btn-outline-warning w-100 rounded-pill py-2 mt-auto" data-bs-toggle="modal" data-bs-target="#modalAturan">
                Baca Aturan
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAturan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Aturan Perpustakaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4 text-muted">
                <ol>
                    <li>Maksimal peminjaman adalah <strong>3 buku</strong> sekaligus.</li>
                    <li>Lama waktu peminjaman maksimal <strong>7 hari</strong>.</li>
                    <li>Keterlambatan dikenakan denda <strong>Rp 1.000,- / hari / buku</strong>.</li>
                    <li>Wajib menjaga kebersihan dan keutuhan buku yang dipinjam.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>