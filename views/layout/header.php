<?php 
// 1. Inisialisasi Session & Keamanan Cache Browser
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Cegah Cache: Agar tombol Back tidak bisa menampilkan dashboard setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// 3. Proteksi Halaman: Jika tidak ada session user_id, tendang ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// 4. Ambil Data Foto Profil Secara Real-time dari Database
$header_user_id = $_SESSION['user_id'];
$header_query = mysqli_query($conn, "SELECT foto FROM users WHERE id = '$header_user_id'");
$header_data = mysqli_fetch_assoc($header_query);

// Tentukan path foto
$foto_tampil = (!empty($header_data['foto'])) ? $header_data['foto'] : 'default.png';

// 5. Logika Notifikasi Sidebar (Khusus Admin)
if ($_SESSION['role'] == 'admin') {
    $query_notif_sidebar = mysqli_query($conn, "SELECT COUNT(*) as jml FROM peminjaman WHERE status = 'proses_kembali'");
    $notif_sidebar = mysqli_fetch_assoc($query_notif_sidebar)['jml'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIB-APP - Digital Library</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <link rel="stylesheet" href="<?= BASEURL; ?>assets/css/style.css">
    
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --sidebar-width: 260px;
            --glass-bg: rgba(255, 255, 255, 0.15);
            --transition-speed: 0.4s;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            overflow-x: hidden;
        }

        /* --- Sidebar Modern --- */
        #sidebar-wrapper {
            min-height: 100vh;
            width: var(--sidebar-width);
            margin-left: calc(-1 * var(--sidebar-width));
            transition: all var(--transition-speed) ease;
            background: var(--bg-gradient);
            position: fixed;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }

        #wrapper.toggled #sidebar-wrapper { margin-left: 0; }

        .sidebar-heading {
            padding: 2rem 1.25rem;
            font-size: 1.4rem;
            font-weight: 700;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .list-group-item {
            border: none !important;
            padding: 0.9rem 1.5rem !important;
            margin: 0.3rem 1rem;
            border-radius: 12px !important;
            background: transparent !important;
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .list-group-item:hover {
            color: #ffffff !important;
            background: var(--glass-bg) !important;
            transform: translateX(8px);
        }

        .list-group-item.active {
            background: #ffffff !important;
            color: #764ba2 !important;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .sidebar-badge {
            font-size: 0.7rem;
            padding: 2px 8px;
            margin-left: auto;
            border-radius: 50px;
            font-weight: 600;
        }

        /* --- Page Content Wrapper --- */
        #page-content-wrapper {
            width: 100%;
            transition: padding-left var(--transition-speed) ease;
        }

        @media (min-width: 768px) {
            #sidebar-wrapper { margin-left: 0; }
            #page-content-wrapper { padding-left: var(--sidebar-width); }
            #wrapper.toggled #sidebar-wrapper { margin-left: calc(-1 * var(--sidebar-width)); }
            #wrapper.toggled #page-content-wrapper { padding-left: 0; }
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
            margin: 15px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        #menu-toggle {
            background: var(--bg-gradient);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
        }

        .container-fluid {
            animation: fadeInPage 0.8s ease-in-out;
        }

        @keyframes fadeInPage {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bg-soft-primary {
            background-color: rgba(102, 126, 234, 0.1);
        }
    </style>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <div id="sidebar-wrapper">
            <div class="sidebar-heading text-center">
                <i class="fas fa-book-reader me-2"></i>LIB-APP
                <hr class="text-white-50">
            </div>
            <div class="list-group list-group-flush h-100">
                
                <?php if($_SESSION['role'] == 'admin') : ?>
                    <a href="../admin/dashboard.php" class="list-group-item <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-pie me-3"></i> Dashboard
                    </a>
                    <a href="../admin/data_buku.php" class="list-group-item <?= (basename($_SERVER['PHP_SELF']) == 'data_buku.php') ? 'active' : ''; ?>">
                        <i class="fas fa-book-open me-3"></i> Kelola Buku
                    </a>
                    <a href="../admin/tambah_anggota.php" class="list-group-item <?= (basename($_SERVER['PHP_SELF']) == 'tambah_anggota.php') ? 'active' : ''; ?>">
                        <i class="fas fa-user-graduate me-3"></i> Data Siswa
                    </a>
                    <a href="../admin/admin_transaksi.php" class="list-group-item <?= (basename($_SERVER['PHP_SELF']) == 'admin_transaksi.php') ? 'active' : ''; ?>">
                        <i class="fas fa-sync-alt me-3"></i> Peminjaman & Pengembalian
                        <?php if($notif_sidebar > 0) : ?>
                            <span class="badge bg-danger sidebar-badge animate__animated animate__pulse animate__infinite">
                                <?= $notif_sidebar; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="../admin/laporan.php" class="list-group-item <?= (basename($_SERVER['PHP_SELF']) == 'laporan.php') ? 'active' : ''; ?>">
                        <i class="fas fa-file-invoice me-3"></i> Laporan Riwayat
                    </a>
                
                <?php else : ?>
                    <a href="../siswa/dashboard.php" class="list-group-item <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                        <i class="fas fa-home me-3"></i> Beranda
                    </a>
                    <a href="../siswa/profil.php" class="list-group-item <?= (basename($_SERVER['PHP_SELF']) == 'profil.php') ? 'active' : ''; ?>">
                        <i class="fas fa-user-circle me-3"></i> Profil Saya
                    </a>
                    <a href="../siswa/daftar_buku.php" class="list-group-item <?= (basename($_SERVER['PHP_SELF']) == 'daftar_buku.php') ? 'active' : ''; ?>">
                        <i class="fas fa-search me-3"></i> Jelajah Buku
                    </a>
                    <a href="../siswa/riwayat.php" class="list-group-item <?= (basename($_SERVER['PHP_SELF']) == 'riwayat.php') ? 'active' : ''; ?>">
                        <i class="fas fa-history me-3"></i> Riwayat Pinjam
                    </a>
                <?php endif; ?>

                <div class="mt-auto mb-5 px-3">
                    <a href="../../logout.php" class="btn btn-light w-100 rounded-pill text-danger fw-bold shadow-sm py-2" onclick="return confirm('Yakin ingin keluar?')">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light navbar-custom py-2 px-4">
                <div class="container-fluid px-0">
                    <button class="btn rounded-circle d-flex align-items-center justify-content-center shadow-sm" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center bg-white px-3 py-1 rounded-pill shadow-sm border">
                        <div class="text-end me-3 d-none d-md-block">
                            <p class="mb-0 small fw-bold text-dark">
                                <?= $_SESSION['nama_lengkap'] ?? $_SESSION['username']; ?>
                            </p>
                            <span class="badge bg-soft-primary text-primary" style="font-size: 9px;"><?= strtoupper($_SESSION['role']); ?></span>
                        </div>
                        
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm overflow-hidden" 
                             style="width: 40px; height: 40px; background: #eee; border: 2px solid #fff;">
                            <?php if($foto_tampil == 'default.png') : ?>
                                <i class="fas fa-user-circle fa-2x text-secondary"></i>
                            <?php else : ?>
                                <img src="../../public/img/profile/<?= $foto_tampil; ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="container-fluid p-4">