<?php 
session_start();
require_once '../../config/init.php';
require_once '../../config/database.php';

// 1. Proteksi & Sinkronisasi Session
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: " . BASEURL);
    exit;
}

$id_user = isset($_SESSION['id']) ? $_SESSION['id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);

if (!$id_user) {
    echo "<script>alert('Sesi login berakhir, silakan login kembali.'); window.location='" . BASEURL . "';</script>";
    exit;
}

// 2. AMBIL DATA RIWAYAT
$query = "SELECT p.*, b.judul 
          FROM peminjaman p 
          JOIN buku b ON p.buku_id = b.id 
          WHERE p.user_id = '$id_user' 
          ORDER BY FIELD(p.status, 'pending', 'dipinjam', 'proses_kembali', 'kembali', 'ditolak') ASC, p.id DESC";
$riwayat = mysqli_query($conn, $query);

include '../layout/header.php'; 
?>

<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-primary mb-1"><i class="fas fa-history me-2"></i> Riwayat & Status Pinjam</h4>
                <p class="text-muted small mb-0">Cek apakah permintaan pinjam Anda sudah disetujui admin.</p>
            </div>
            <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm">
                Total Transaksi: <?= mysqli_num_rows($riwayat); ?>
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="border-0 ps-3">Judul Buku</th>
                        <th class="border-0">Tgl Pinjam</th>
                        <th class="border-0">Batas Kembali</th>
                        <th class="border-0">Denda</th>
                        <th class="border-0 text-center">Status</th>
                        <th class="border-0 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($riwayat) > 0) : ?>
                        <?php while($row = mysqli_fetch_assoc($riwayat)) : ?>
                        <?php 
                            $tgl_p = ($row['tanggal_pinjam']) ? date('d M Y', strtotime($row['tanggal_pinjam'])) : '-';
                            $tgl_k = ($row['tanggal_kembali']) ? date('d M Y', strtotime($row['tanggal_kembali'])) : '-';
                            $is_late = (date('Y-m-d') > $row['tanggal_kembali'] && $row['status'] == 'dipinjam');
                        ?>
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark"><?= $row['judul']; ?></div>
                                <small class="text-muted">ID: #TR-<?= $row['id']; ?></small>
                            </td>
                            <td><small class="text-muted"><?= $tgl_p; ?></small></td>
                            <td>
                                <small class="fw-bold <?= ($is_late) ? 'text-danger' : ''; ?>">
                                    <?= $tgl_k; ?>
                                </small>
                            </td>
                            <td>
                                <?php if($row['denda'] > 0) : ?>
                                    <span class="text-danger fw-bold">Rp <?= number_format($row['denda'], 0, ',', '.'); ?></span>
                                <?php else : ?>
                                    <span class="text-muted small">Rp 0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['status'] == 'pending') : ?>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 border border-primary-subtle">
                                        <i class="fas fa-clock me-1"></i> Menunggu Persetujuan
                                    </span>
                                <?php elseif($row['status'] == 'dipinjam') : ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-3">
                                        <i class="fas fa-book-reader me-1"></i> Sedang Dipinjam
                                    </span>
                                <?php elseif($row['status'] == 'proses_kembali') : ?>
                                    <span class="badge bg-info text-white rounded-pill px-3">
                                        <i class="fas fa-hourglass-half me-1"></i> Dicek Admin
                                    </span>
                                <?php elseif($row['status'] == 'kembali') : ?>
                                    <span class="badge bg-success rounded-pill px-3">
                                        <i class="fas fa-check me-1"></i> Selesai
                                    </span>
                                <?php elseif($row['status'] == 'ditolak') : ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 border border-danger-subtle">
                                        <i class="fas fa-times me-1"></i> Ditolak
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['status'] == 'pending') : ?>
                                    <small class="text-muted italic">Silahkan ke meja perpus</small>
                                
                                <?php elseif($row['status'] == 'dipinjam') : ?>
                                    <a href="ajukan_kembali.php?id=<?= $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm"
                                       onclick="return confirm('Ajukan pengembalian buku ini?')">
                                       <i class="fas fa-undo me-1"></i> Kembalikan
                                    </a>

                                <?php elseif($row['status'] == 'ditolak') : ?>
                                    <i class="fas fa-exclamation-circle text-danger" title="Permintaan ditolak admin"></i>

                                <?php else : ?>
                                    <i class="fas fa-check-circle text-success" title="Sudah Beres"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted opacity-50">
                                    <i class="fas fa-book-open fa-3x mb-3"></i><br>
                                    Belum ada riwayat peminjaman.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>