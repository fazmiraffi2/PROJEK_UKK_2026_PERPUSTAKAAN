<?php
require_once 'config/init.php';

if (isset($_POST['register'])) {
    $model = new PerpusModel();
    
    // Ambil data dari form
    $data = [
        'nama_lengkap' => $_POST['nama_lengkap'],
        'username'     => $_POST['username'],
        'password'     => $_POST['password'], // Sebaiknya gunakan password_hash jika sudah tahap mahir
        'kelas'        => $_POST['kelas'],
        'jurusan'      => $_POST['jurusan']
    ];

    // Cek apakah username sudah dipakai
    $username = mysqli_real_escape_string($conn, $data['username']);
    $cek_user = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Username sudah terdaftar! Gunakan username lain.'); window.history.back();</script>";
    } else {
        // Gunakan fungsi tambahSiswa yang sudah ada di model
        if ($model->tambahSiswa($data)) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat mendaftar!'); window.history.back();</script>";
        }
    }
} else {
    header("Location: register.php");
}