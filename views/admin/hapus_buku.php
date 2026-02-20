<?php
session_start();
require_once '../../config/init.php';

// Pastikan hanya admin yang bisa akses
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ' . BASEURL);
    exit;
}

// Tangkap keyword search supaya posisi pencarian tidak hilang setelah hapus
$keyword = isset($_GET['search']) ? $_GET['search'] : "";

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $model = new PerpusModel();

    // 1. Cek apakah buku sedang dipinjam oleh siswa
    if($model->cekBukuDipinjam($id)) {
        // Jika masih dipinjam, kirim pesan error
        $_SESSION['msg'] = "Gagal menghapus! Buku ini masih dalam status DIPINJAM oleh siswa.";
        $_SESSION['msg_type'] = "danger";
    } else {
        // 2. Jika aman, lakukan penghapusan
        if($model->hapusBuku($id)) {
            $_SESSION['msg'] = "Buku berhasil dihapus dari sistem.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['msg'] = "Terjadi kesalahan sistem saat menghapus data.";
            $_SESSION['msg_type'] = "danger";
        }
    }
}

// Kembali ke halaman data buku dengan membawa kembali keyword pencarian
header('Location: data_buku.php?search=' . urlencode($keyword));
exit;