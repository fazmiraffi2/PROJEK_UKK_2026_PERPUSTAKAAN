<?php
session_start();
require_once '../../config/init.php';
require_once '../../config/database.php';

// Proteksi akses hanya untuk siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: " . BASEURL);
    exit;
}

if (isset($_GET['id'])) {
    $id_pinjam = mysqli_real_escape_string($conn, $_GET['id']);
    // Cek ID User dari session (antisipasi perbedaan nama key)
    $id_user = $_SESSION['id'] ?? $_SESSION['user_id'];

    // Update status menjadi 'proses_kembali'
    // Pastikan status sebelumnya adalah 'dipinjam' agar tidak bisa disalahgunakan
    $query = "UPDATE peminjaman SET status = 'proses_kembali' 
              WHERE id = '$id_pinjam' AND user_id = '$id_user' AND status = 'dipinjam'";

    if (mysqli_query($conn, $query)) {
        if (mysqli_affected_rows($conn) > 0) {
            echo "<script>
                    alert('Permintaan pengembalian berhasil dikirim! Silakan serahkan buku ke admin perpustakaan.'); 
                    window.location='riwayat.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal! Transaksi tidak ditemukan atau status sudah berubah.'); 
                    window.location='riwayat.php';
                  </script>";
        }
    } else {
        // Jika masih error SQL, tampilkan pesan error yang lebih jelas (untuk didebug)
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: riwayat.php");
    exit;
}