<?php
session_start();
require_once '../../config/init.php';

// Proteksi: Hanya admin yang bisa akses proses ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASEURL);
    exit;
}

if (isset($_GET['id'])) {
    $id_pinjam = $_GET['id'];
    $model = new PerpusModel();
    
    // Kita panggil fungsi kembalikanBuku yang baru.
    // Fungsi ini sekarang mengembalikan nilai denda (angka) jika berhasil, atau false jika gagal.
    $hasil_denda = $model->kembalikanBuku($id_pinjam);

    if ($hasil_denda !== false) {
        // Jika denda lebih dari 0, buat pesan khusus keterlambatan
        if ($hasil_denda > 0) {
            $pesan = "Buku Berhasil Dikembalikan! Siswa terlambat, denda yang harus dibayar: Rp " . number_format($hasil_denda, 0, ',', '.');
        } else {
            $pesan = "Buku Berhasil Dikembalikan tepat waktu. Tanpa denda.";
        }
        
        echo "<script>alert('$pesan'); window.location='admin_transaksi.php';</script>";
    } else {
        echo "<script>alert('Gagal memproses pengembalian buku!'); window.history.back();</script>";
    }
} else {
    header("Location: admin_transaksi.php");
}