<?php
session_start();
require_once '../../config/init.php';

// Pastikan hanya admin yang bisa memproses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASEURL);
    exit;
}

// Cek apakah ada data yang dikirim (bisa cek salah satu input unik seperti username)
if (isset($_POST['username'])) {
    $model = new PerpusModel();
    
    // Pastikan password di-hash atau diproses di dalam Model nanti
    // Kita kirim seluruh array $_POST ke method tambahSiswa di Model
    if ($model->tambahSiswa($_POST)) {
        echo "<script>
                alert('Anggota baru berhasil ditambahkan!'); 
                window.location='tambah_anggota.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan anggota! Periksa apakah username sudah terdaftar.'); 
                window.history.back();
              </script>";
    }
} else {
    // Jika diakses tanpa kirim form, balikkan ke halaman utama
    header("Location: tambah_anggota.php");
    exit;
}