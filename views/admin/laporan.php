<?php 
session_start();
require_once '../../config/init.php';
require_once '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASEURL);
    exit;
}

// Ambil parameter filter jika ada
$nama   = isset($_GET['cari_nama']) ? mysqli_real_escape_string($conn, $_GET['cari_nama']) : '';
$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : '';

// --- PERUBAHAN LOGIKA DI SINI ---
// Kita tambahkan "AND p.status = 'kembali'" agar yang masih dipinjam tidak muncul
$query = "SELECT p.*, u.nama_lengkap, b.judul 
          FROM peminjaman p 
          JOIN users u ON p.user_id = u.id 
          JOIN buku b ON p.buku_id = b.id 
          WHERE p.status = 'kembali'"; 

if ($nama != '') {
    $query .= " AND u.nama_lengkap LIKE '%$nama%'";
}
if ($tgl_awal != '' && $tgl_akhir != '') {
    $query .= " AND p.tanggal_pinjam BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$query .= " ORDER BY p.id DESC";
$data_laporan = mysqli_query($conn, $query);

include '../layout/header.php'; 
?>

<style>
    /* Desain Tabel Custom */
    .table-custom {
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    .table-custom thead th {
        background: var(--bg-gradient);
        color: white;
        border: none;
        padding: 15px;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
    }
    .table-custom thead th:first-child { border-radius: 12px 0 0 12px; }
    .table-custom thead th:last-child { border-radius: 0 12px 12px 0; }

    .table-custom tbody tr {
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }
    .table-custom tbody tr:hover {
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        background-color: #f8f9ff;
    }
    .table-custom tbody td {
        padding: 15px;
        border: none;
        color: #555;
    }
    .table-custom tbody td:first-child { border-radius: 12px 0 0 12px; }
    .table-custom tbody td:last-child { border-radius: 0 12px 12px 0; }

    .badge-status {
        padding: 6px 16px;
        font-weight: 500;
        border-radius: 30px;
    }
</style>

<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: rgba(255,255,255,0.9);">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-primary mb-1"><i class="fas fa-file-invoice me-2"></i> Laporan Transaksi Buku</h4>
                <p class="text-muted small mb-0">Menampilkan data buku yang sudah dikembalikan & diverifikasi</p>
            </div>
            <a href="cetak_pdf.php?cari_nama=<?= $nama ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&status=kembali" 
               target="_blank" class="btn btn-danger rounded-pill px-4 shadow-sm">
                <i class="fas fa-file-pdf me-2"></i> Cetak PDF
            </a>
        </div>

        <form method="GET" class="row g-3 mb-4 p-3 border-0 shadow-sm" style="border-radius: 15px; background: #fcfcfd;">
            <div class="col-md-3">
                <label class="small fw-bold text-secondary">Cari Nama Siswa</label>
                <div class="input-group mt-1">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted small"></i></span>
                    <input type="text" name="cari_nama" class="form-control border-start-0 ps-0" placeholder="Ketik nama..." value="<?= $nama; ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-secondary">Dari Tanggal</label>
                <input type="date" name="tgl_awal" class="form-control mt-1" value="<?= $tgl_awal; ?>">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-secondary">Sampai Tanggal</label>
                <input type="date" name="tgl_akhir" class="form-control mt-1" value="<?= $tgl_akhir; ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm"><i class="fas fa-filter me-1"></i> Cari</button>
                <a href="laporan.php" class="btn btn-outline-secondary w-100 rounded-pill">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th class="text-start">Nama Siswa</th>
                        <th class="text-start">Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Denda</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($data_laporan) > 0) : ?>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($data_laporan)) : ?>
                        <tr class="text-center">
                            <td class="fw-bold text-primary"><?= $no++; ?></td>
                            <td class="text-start">
                                <div class="fw-bold text-dark"><?= $row['nama_lengkap']; ?></div>
                            </td>
                            <td class="text-start">
                                <span class="text-secondary"><?= $row['judul']; ?></span>
                            </td>
                            <td><small class="text-muted"><i class="far fa-calendar-alt me-1"></i> <?= date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></small></td>
                            <td><small class="text-muted"><i class="far fa-calendar-check me-1"></i> <?= date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></small></td>
                            <td>
                                <span class="fw-bold <?= ($row['denda'] > 0) ? 'text-danger' : 'text-success'; ?>">
                                    <?= ($row['denda'] > 0) ? 'Rp ' . number_format($row['denda'], 0, ',', '.') : 'Rp 0'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-status shadow-sm" style="background: #e6fffa; color: #2d3748; border-left: 4px solid #38a169;">
                                    <i class="fas fa-check-circle text-success me-1"></i> Selesai
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 bg-white" style="border-radius: 12px;">
                                <img src="https://illustrations.popsy.co/purple/data-report.svg" alt="no-data" style="width: 150px;" class="mb-3 opacity-50">
                                <p class="text-muted">Tidak ada riwayat pengembalian yang ditemukan.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>