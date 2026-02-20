<?php 
session_start();
require_once '../../config/init.php';
require_once '../../config/database.php';

// 1. Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASEURL);
    exit;
}

$model = new PerpusModel();

// 2. LOGIKA PROSES
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_pinjam = mysqli_real_escape_string($conn, $_GET['id']);
    $aksi = $_GET['aksi'];

    if ($aksi == 'setujui' || $aksi == 'tolak') {
        if ($model->konfirmasiPeminjaman($id_pinjam, $aksi)) {
            $pesan = ($aksi == 'setujui') ? 'Peminjaman Disetujui!' : 'Peminjaman Ditolak!';
            echo "<script>alert('$pesan'); window.location='admin_transaksi.php';</script>";
        }
    } 
    
    // Aksi untuk menerima pengembalian buku dari siswa
    if ($aksi == 'terima') {
        if ($model->kembalikanBuku($id_pinjam)) {
            echo "<script>alert('Buku Berhasil Diterima & Stok Bertambah!'); window.location='admin_transaksi.php';</script>";
        } else {
            echo "<script>alert('Gagal memproses pengembalian.'); window.location='admin_transaksi.php';</script>";
        }
    }
}

// 3. AMBIL DATA TRANSAKSI
// Query ini menggabungkan data peminjaman, user, dan buku
$query = "SELECT p.*, u.nama_lengkap, b.judul 
          FROM peminjaman p 
          JOIN users u ON p.user_id = u.id 
          JOIN buku b ON p.buku_id = b.id 
          ORDER BY FIELD(p.status, 'pending', 'proses_kembali', 'dipinjam', 'kembali', 'ditolak') ASC, p.id DESC";
$transaksi = mysqli_query($conn, $query);

include '../layout/header.php'; 
?>

<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.9);
        --primary-grad: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        --danger-grad: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
        --success-grad: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        --warning-grad: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        --info-grad: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
    }

    .main-content { background: #f0f2f5; min-height: 100vh; }
    
    .card-gradient {
        border: none;
        border-radius: 20px;
        color: white;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
        position: relative;
    }
    .card-gradient:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }
    .card-gradient i { position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.2; }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border-radius: 25px;
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .tr-modern { transition: 0.3s; }
    .tr-modern:hover { background: rgba(78, 115, 223, 0.05) !important; }

    .badge-pill-custom {
        padding: 6px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
    }

    .btn-action {
        border-radius: 12px;
        padding: 8px 15px;
        font-weight: 600;
        transition: 0.3s;
    }
</style>

<div class="container-fluid py-4 main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h2 class="fw-bold text-dark mb-1">Pusat Transaksi 📚</h2>
            <p class="text-muted">Monitor dan kelola alur sirkulasi buku secara real-time.</p>
        </div>
        <button onclick="window.location.reload()" class="btn btn-white shadow-sm rounded-pill px-4 fw-bold">
            <i class="fas fa-sync-alt me-2 text-primary"></i> Refresh Data
        </button>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card-gradient p-4 h-100 shadow-sm" style="background: var(--primary-grad);">
                <i class="fas fa-bell"></i>
                <small class="opacity-75 fw-bold">PERMINTAAN BARU</small>
                <h2 class="fw-bold mt-2 mb-0"><?= $model->hitungData('peminjaman', "WHERE status='pending'"); ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-gradient p-4 h-100 shadow-sm" style="background: var(--info-grad);">
                <i class="fas fa-undo"></i>
                <small class="opacity-75 fw-bold">PROSES KEMBALI</small>
                <h2 class="fw-bold mt-2 mb-0"><?= $model->hitungData('peminjaman', "WHERE status='proses_kembali'"); ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-gradient p-4 h-100 shadow-sm" style="background: var(--warning-grad);">
                <i class="fas fa-book-reader"></i>
                <small class="opacity-75 fw-bold">DIPINJAM SISWA</small>
                <h2 class="fw-bold mt-2 mb-0"><?= $model->hitungData('peminjaman', "WHERE status='dipinjam'"); ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-gradient p-4 h-100 shadow-sm" style="background: var(--success-grad);">
                <i class="fas fa-check-double"></i>
                <small class="opacity-75 fw-bold">TOTAL SELESAI</small>
                <h2 class="fw-bold mt-2 mb-0"><?= $model->hitungData('peminjaman', "WHERE status='kembali'"); ?></h2>
            </div>
        </div>
    </div>

    <div class="glass-card p-4 border-0">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Data Siswa</th>
                        <th>Info Buku</th>
                        <th class="text-center">Tgl Pinjam</th>
                        <th class="text-center">Denda</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4">Aksi Kontrol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($transaksi) > 0) : ?>
                        <?php while($row = mysqli_fetch_assoc($transaksi)) : ?>
                        <tr class="tr-modern">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-weight: bold;">
                                        <?= strtoupper(substr($row['nama_lengkap'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0"><?= $row['nama_lengkap']; ?></div>
                                        <code class="small text-primary">#TR-<?= $row['id']; ?></code>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-secondary"><?= $row['judul']; ?></div>
                                <small class="text-muted"><i class="fas fa-bookmark me-1"></i> Koleksi Perpus</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-3"><?= date('d M Y', strtotime($row['tanggal_pinjam'])); ?></span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold <?= ($row['denda'] > 0) ? 'text-danger' : 'text-success'; ?>">
                                    Rp <?= number_format($row['denda'], 0, ',', '.'); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php 
                                    $status_config = [
                                        'pending' => ['bg-primary text-white', 'Menunggu', 'fa-clock'],
                                        'dipinjam' => ['bg-warning text-dark', 'Aktif', 'fa-book'],
                                        'proses_kembali' => ['bg-info text-white', 'Verif Kembali', 'fa-redo'],
                                        'kembali' => ['bg-success text-white', 'Selesai', 'fa-check-circle'],
                                        'ditolak' => ['bg-danger text-white', 'Ditolak', 'fa-times-circle']
                                    ];
                                    $conf = $status_config[$row['status']];
                                ?>
                                <span class="badge-pill-custom <?= $conf[0]; ?>">
                                    <i class="fas <?= $conf[2]; ?> me-1"></i> <?= $conf[1]; ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <?php if($row['status'] == 'pending') : ?>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="?aksi=setujui&id=<?= $row['id']; ?>" class="btn btn-success btn-action shadow-sm" onclick="return confirm('Setujui peminjaman ini?')">Setujui</a>
                                        <a href="?aksi=tolak&id=<?= $row['id']; ?>" class="btn btn-outline-danger btn-action" onclick="return confirm('Tolak peminjaman ini?')"><i class="fas fa-times"></i></a>
                                    </div>
                                <?php elseif($row['status'] == 'proses_kembali') : ?>
                                    <a href="?aksi=terima&id=<?= $row['id']; ?>" class="btn btn-info btn-action text-white shadow-sm px-4" onclick="return confirm('Apakah buku sudah diterima dalam keadaan baik?')">
                                        Terima Buku <i class="fas fa-check ms-1"></i>
                                    </a>
                                <?php elseif($row['status'] == 'dipinjam') : ?>
                                    <span class="text-muted small">Sedang Dipinjam</span>
                                <?php elseif($row['status'] == 'kembali') : ?>
                                    <span class="text-success small fw-bold">Selesai</span>
                                <?php else : ?>
                                    <span class="text-danger small">Dibatalkan</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr><td colspan="6" class="text-center py-5">
                            <p class="text-muted mt-3">Tidak ada transaksi yang ditemukan.</p>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>