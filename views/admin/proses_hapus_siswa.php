<?php
session_start();
require_once '../../config/database.php'; // Pastikan path ke koneksi database benar

// Proteksi: Pastikan hanya admin yang bisa menghapus
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Cek apakah ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. Cek apakah siswa ini punya transaksi peminjaman yang masih aktif
    // Ini penting agar tidak terjadi error relasi database (Integrity Constraint)
    $cek_pinjam = mysqli_query($conn, "SELECT * FROM peminjaman WHERE user_id = '$id' AND status != 'kembali'");
    
    if (mysqli_num_rows($cek_pinjam) > 0) {
        echo "<script>
                alert('Gagal! Siswa masih memiliki tanggungan peminjaman buku.');
                window.location='admin_data_siswa.php';
              </script>";
        exit;
    }

    // 2. Proses Hapus
    $query = "DELETE FROM users WHERE id = '$id' AND role = 'siswa'";
    $hapus = mysqli_query($conn, $query);

    if ($hapus) {
        echo "<script>
                alert('Data siswa berhasil dihapus!');
                window.location='admin_data_siswa.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus data: " . mysqli_error($conn) . "');
                window.location='admin_data_siswa.php';
              </script>";
    }
} else {
    // Jika tidak ada ID, kembalikan ke halaman data siswa
    header("Location: admin_data_siswa.php");
    exit;
}