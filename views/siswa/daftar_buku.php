<?php 
session_start();
require_once '../../config/init.php';
require_once '../../config/database.php';

// Proteksi akses siswa
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header('Location: '.BASEURL);
    exit;
}

$id_user = $_SESSION['id'] ?? $_SESSION['user_id'];
$model = new PerpusModel();

// --- LOGIKA FILTER & SEARCH ---
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_kelas = isset($_GET['kelas']) ? mysqli_real_escape_string($conn, $_GET['kelas']) : '';

// Query dasar
$sql = "SELECT * FROM buku WHERE 1=1";

// Tambahkan kondisi search jika ada
if (!empty($search)) {
    $sql .= " AND (judul LIKE '%$search%' OR penulis LIKE '%$search%')";
}

// Tambahkan kondisi filter kelas/kategori jika ada
if (!empty($filter_kelas)) {
    if ($filter_kelas == 'Umum') {
        $sql .= " AND (kelas_buku IS NULL OR kelas_buku = '' OR kelas_buku = 'Umum')";
    } else {
        // Ini akan menangani 'Kelas 10', 'Kelas 11', 'Kelas 12', dan 'Cerita'
        $sql .= " AND kelas_buku = '$filter_kelas'";
    }
}

$sql .= " ORDER BY id DESC";
$buku = mysqli_query($conn, $sql);

// HITUNG JUMLAH PINJAMAN AKTIF UNTUK LIMIT 3 BUKU
$query_limit = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman 
                                    WHERE user_id = '$id_user' 
                                    AND (status = 'pending' OR status = 'dipinjam')");
$data_limit = mysqli_fetch_assoc($query_limit);
$jumlah_aktif = $data_limit['total'];

include '../layout/header.php'; 
?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h3 class="fw-bold text-dark mb-1">Halo, <?= $_SESSION['nama_lengkap']; ?>! 👋</h3>
        <p class="text-muted">Cari koleksi buku terbaikmu di sini.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <div class="bg-white p-3 shadow-sm rounded-4 d-inline-block border">
            <span class="text-muted small d-block">Status Pinjam Anda</span>
            <span class="fw-bold <?= ($jumlah_aktif >= 3) ? 'text-danger' : 'text-primary'; ?> h5 mb-0">
                <?= $jumlah_aktif; ?> / 3 Buku
            </span>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-5 mb-3 mb-lg-0">
        <form action="" method="GET" class="d-flex shadow-sm rounded-pill overflow-hidden border bg-white">
            <input type="text" name="search" class="form-control border-0 px-4 py-2" placeholder="Cari judul atau penulis..." value="<?= $search; ?>">
            <?php if(!empty($filter_kelas)) : ?>
                <input type="hidden" name="kelas" value="<?= $filter_kelas; ?>">
            <?php endif; ?>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    <div class="col-lg-7">
        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
            <a href="daftar_buku.php" class="btn btn-sm rounded-pill px-3 <?= empty($filter_kelas) ? 'btn-primary' : 'btn-outline-primary'; ?>">Semua</a>
            <a href="?kelas=Kelas 10<?= !empty($search) ? '&search='.$search : ''; ?>" class="btn btn-sm rounded-pill px-3 <?= ($filter_kelas == 'Kelas 10') ? 'btn-primary' : 'btn-outline-primary'; ?>">Kelas 10</a>
            <a href="?kelas=Kelas 11<?= !empty($search) ? '&search='.$search : ''; ?>" class="btn btn-sm rounded-pill px-3 <?= ($filter_kelas == 'Kelas 11') ? 'btn-primary' : 'btn-outline-primary'; ?>">Kelas 11</a>
            <a href="?kelas=Kelas 12<?= !empty($search) ? '&search='.$search : ''; ?>" class="btn btn-sm rounded-pill px-3 <?= ($filter_kelas == 'Kelas 12') ? 'btn-primary' : 'btn-outline-primary'; ?>">Kelas 12</a>
            <a href="?kelas=Cerita<?= !empty($search) ? '&search='.$search : ''; ?>" class="btn btn-sm rounded-pill px-3 <?= ($filter_kelas == 'Cerita') ? 'btn-primary' : 'btn-outline-primary'; ?>">Cerita</a>
            <a href="?kelas=Umum<?= !empty($search) ? '&search='.$search : ''; ?>" class="btn btn-sm rounded-pill px-3 <?= ($filter_kelas == 'Umum') ? 'btn-primary' : 'btn-outline-primary'; ?>">Umum</a>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background-color: #f8f9fc; border-left: 5px solid #4e73df !important;">
    <div class="card-body d-flex align-items-center p-3">
        <div class="me-3 bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
            <i class="fas fa-info-circle text-white"></i>
        </div>
        <div>
            <h6 class="fw-bold text-dark mb-1">Informasi Peminjaman:</h6>
            <div class="d-flex flex-wrap gap-2">
                <span class="small text-muted"><i class="fas fa-check-circle text-success me-1"></i>Maks 3 Buku</span>
                <span class="small text-muted"><i class="fas fa-clock text-warning me-1"></i>Durasi 7 Hari</span>
                <span class="small text-muted"><i class="fas fa-coins text-danger me-1"></i>Denda Rp 1.000/hari</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php if(mysqli_num_rows($buku) > 0) : ?>
        <?php while($row = mysqli_fetch_assoc($buku)) : ?>
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm card-buku-modern">
                <div class="position-absolute top-0 start-0 m-2 z-index-1">
                    <?php 
                    $label_class = 'bg-secondary';
                    if($row['kelas_buku'] == 'Kelas 10' || $row['kelas_buku'] == 'Kelas 11' || $row['kelas_buku'] == 'Kelas 12') $label_class = 'bg-primary';
                    if($row['kelas_buku'] == 'Cerita') $label_class = 'bg-success';
                    ?>
                    <span class="badge <?= $label_class; ?> shadow-sm">
                        <?= !empty($row['kelas_buku']) ? $row['kelas_buku'] : 'Umum'; ?>
                    </span>
                </div>

                <div class="container-cover">
                    <?php $cover = !empty($row['foto']) ? $row['foto'] : 'default.png'; ?>
                    <img src="../../public/img/buku/<?= $cover; ?>" class="card-img-top img-cover" alt="Cover">
                </div>

                <div class="card-body d-flex flex-column p-3">
                    <div class="mb-2">
                        <small class="text-primary fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">
                            <?php 
                                if($row['kelas_buku'] == 'Cerita') echo 'Koleksi Fiksi';
                                elseif(strpos($row['kelas_buku'], 'Kelas') !== false) echo 'Buku Pelajaran';
                                else echo 'Referensi Umum';
                            ?>
                        </small>
                    </div>

                    <h6 class="card-title fw-bold text-dark mb-1 text-truncate-2" title="<?= $row['judul']; ?>">
                        <?= $row['judul']; ?>
                    </h6>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-pen-nib me-1 small"></i> <?= $row['penulis']; ?>
                    </p>

                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted d-block" style="font-size: 0.7rem;">Sisa Stok</span>
                            <span class="fw-bold <?= ($row['stok'] > 0) ? 'text-dark' : 'text-danger'; ?>">
                                <?= $row['stok']; ?> <small>Eks</small>
                            </span>
                        </div>

                        <?php if($row['stok'] <= 0) : ?>
                            <button class="btn btn-light disabled rounded-pill px-3 py-2 btn-sm fw-bold border">Habis</button>
                        <?php elseif($jumlah_aktif >= 3) : ?>
                            <button class="btn btn-secondary disabled rounded-pill px-3 py-2 btn-sm fw-bold shadow-sm">Limit Penuh</button>
                        <?php else : ?>
                            <a href="proses_pinjam.php?id=<?= $row['id']; ?>" 
                               class="btn btn-primary rounded-pill px-3 py-2 btn-sm fw-bold shadow-sm"
                               onclick="return confirm('Ingin meminjam buku <?= $row['judul']; ?>?')">
                                 Pinjam <i class="fas fa-arrow-right ms-1" style="font-size: 0.7rem;"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else : ?>
        <div class="col-12 text-center py-5">
            <p class="text-muted mt-3">Ups! Buku tidak ditemukan.</p>
            <a href="daftar_buku.php" class="btn btn-primary rounded-pill px-4">Lihat Semua Buku</a>
        </div>
    <?php endif; ?>
</div>

<style>
.container-cover { width: 100%; height: 280px; overflow: hidden; background-color: #f8f9fa; border-radius: 15px 15px 0 0; }
.img-cover { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.card-buku-modern { border-radius: 15px !important; transition: all 0.3s ease; overflow: hidden; border: 1px solid #eee !important; }
.card-buku-modern:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
.card-buku-modern:hover .img-cover { transform: scale(1.08); }
.text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.4em; }
.z-index-1 { z-index: 1; }
</style>

<?php include '../layout/footer.php'; ?>