<?php 
session_start();
require_once '../../config/init.php';
require_once '../../config/database.php';

// 1. Cek Login & Session
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: " . BASEURL);
    exit;
}

$id_user = isset($_SESSION['id']) ? $_SESSION['id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);

if (!$id_user) {
    echo "<script>alert('Sesi login hilang, silakan login kembali.'); window.location='" . BASEURL . "';</script>";
    exit;
}

if (isset($_GET['id'])) {
    $id_buku = mysqli_real_escape_string($conn, $_GET['id']);
    
    // --- FITUR BARU: CEK BATAS MAKSIMAL 3 BUKU (SERVER-SIDE VALIDATION) ---
    // Menghitung buku yang statusnya masih 'pending' atau 'dipinjam'
    $cek_limit = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman 
                                      WHERE user_id = '$id_user' 
                                      AND (status = 'pending' OR status = 'dipinjam')");
    $data_limit = mysqli_fetch_assoc($cek_limit);

    if ($data_limit['total'] >= 3) {
        echo "<script>
                alert('Gagal! Anda sudah mencapai batas maksimal peminjaman (3 buku). Silakan kembalikan buku yang ada terlebih dahulu.'); 
                window.location='riwayat.php';
              </script>";
        exit;
    }
    // ---------------------------------------------------------------------

    // 2. Tentukan Tanggal (Status Pending)
    $tanggal_pinjam  = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime('+7 days')); 
    
    // 3. Cek stok buku
    $cek_stok = mysqli_query($conn, "SELECT stok FROM buku WHERE id = '$id_buku'");
    $buku = mysqli_fetch_assoc($cek_stok);

    if ($buku && $buku['stok'] > 0) {
        
        // Cek apakah siswa sudah meminjam buku yang sama sebelumnya
        $cek_double = mysqli_query($conn, "SELECT id FROM peminjaman 
                                           WHERE user_id = '$id_user' 
                                           AND buku_id = '$id_buku' 
                                           AND (status = 'pending' OR status = 'dipinjam')");
        
        if (mysqli_num_rows($cek_double) > 0) {
            echo "<script>alert('Anda sudah meminta/meminjam buku ini. Silakan cek riwayat.'); window.location='riwayat.php';</script>";
            exit;
        }

        // 4. Masukkan data dengan status 'pending'
        // Ingat: Stok baru berkurang setelah ADMIN menyetujui di halaman admin_transaksi
        $query_pinjam = "INSERT INTO peminjaman (user_id, buku_id, tanggal_pinjam, tanggal_kembali, denda, status) 
                         VALUES ('$id_user', '$id_buku', '$tanggal_pinjam', '$tanggal_kembali', 0, 'pending')";
        
        if (mysqli_query($conn, $query_pinjam)) {
            echo "<script>
                    alert('Permintaan pinjam berhasil dikirim! Silakan menemui petugas perpustakaan untuk pengambilan buku.'); 
                    window.location='riwayat.php';
                  </script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Maaf, stok buku sedang habis!'); window.history.back();</script>";
    }
} else {
    header("Location: daftar_buku.php");
    exit;
}