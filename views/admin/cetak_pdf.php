<?php 
session_start();
require_once '../../config/database.php';

// 1. Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Akses Ditolak");
}

// 2. Ambil & Bersihkan Data Filter
$nama      = isset($_GET['cari_nama']) ? mysqli_real_escape_string($conn, $_GET['cari_nama']) : '';
$tgl_awal  = isset($_GET['tgl_awal']) ? mysqli_real_escape_string($conn, $_GET['tgl_awal']) : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? mysqli_real_escape_string($conn, $_GET['tgl_akhir']) : '';

// 3. Query SQL (Hanya mengambil status 'kembali')
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

$query .= " ORDER BY p.tanggal_kembali_aktual DESC";
$res = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Perpustakaan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; background-color: white; }
        .line-header { border-bottom: 3px solid black; border-top: 1px solid black; height: 5px; margin-top: 5px; }
        .table th { background-color: #f8f9fa !important; border-color: black !important; }
        .table td { border-color: black !important; }
        
        @media print { 
            .no-print { display: none !important; }
            @page { margin: 1.5cm; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="container mt-3 no-print">
        <div class="d-flex justify-content-between align-items-center p-3 bg-light border rounded shadow-sm">
            <span class="text-muted small"><i class="fas fa-eye me-2"></i> Mode Pratinjau Laporan</span>
            <div>
                <a href="laporan.php" class="btn btn-danger btn-sm px-4 fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> KEMBALI
                </a>
                <button onclick="window.print()" class="btn btn-primary btn-sm px-4 fw-bold">
                    <i class="fas fa-print me-1"></i> CETAK SEKARANG
                </button>
            </div>
        </div>
        <hr>
    </div>

    <div class="container mt-4">
        <div class="text-center">
            <h2 class="mb-0 fw-bold text-uppercase">Laporan Transaksi Perpustakaan</h2>
            <p class="mb-0">Sistem Informasi Perpustakaan Digital</p>
            <p class="small text-muted">Periode: <?= ($tgl_awal) ? date('d/m/Y', strtotime($tgl_awal)) . " s/d " . date('d/m/Y', strtotime($tgl_akhir)) : "Semua Data Transaksi Selesai"; ?></p>
        </div>
        <div class="line-header mb-4"></div>
        
        <table class="table table-bordered border-dark">
            <thead class="text-center align-middle">
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Nama Siswa</th>
                    <th width="25%">Judul Buku</th>
                    <th width="15%">Tgl Pinjam</th>
                    <th width="15%">Tgl Kembali</th>
                    <th width="20%">Denda</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1; 
                $total_denda = 0;
                if(mysqli_num_rows($res) > 0) :
                    while($d = mysqli_fetch_assoc($res)) : 
                        $total_denda += $d['denda'];
                ?>
                <tr class="align-middle">
                    <td class="text-center"><?= $i++; ?></td>
                    <td><?= $d['nama_lengkap']; ?></td>
                    <td><?= $d['judul']; ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($d['tanggal_pinjam'])); ?></td>
                    <td class="text-center"><?= ($d['tanggal_kembali_aktual']) ? date('d/m/Y', strtotime($d['tanggal_kembali_aktual'])) : '-'; ?></td>
                    <td class="text-end">Rp <?= number_format($d['denda'], 0, ',', '.'); ?></td>
                </tr>
                <?php 
                    endwhile; 
                else :
                ?>
                <tr>
                    <td colspan="6" class="text-center py-4"><i>Tidak ada data transaksi yang ditemukan untuk kriteria ini.</i></td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="5" class="text-end bg-light">TOTAL PENDAPATAN DENDA:</td>
                    <td class="text-end bg-light">Rp <?= number_format($total_denda, 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
        
        <div class="mt-5 d-flex justify-content-end">
            <div class="text-center" style="width: 250px;">
                <p class="mb-5">Dicetak pada: <?= date('d F Y'); ?><br>Petugas Perpustakaan,</p>
                <br>
                <p class="fw-bold text-decoration-underline">( ____________________ )</p>
                <p class="small">Administrator Sistem</p>
            </div>
        </div>
    </div>
</body>
</html>