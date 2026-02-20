<?php
session_start();
require_once '../../config/init.php';

if (isset($_POST['update'])) {
    $model = new PerpusModel();
    
    if ($model->updateSiswa($_POST)) {
        echo "<script>alert('Data anggota berhasil diperbarui!'); window.location='tambah_anggota.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!'); window.history.back();</script>";
    }
} else {
    header("Location: tambah_anggota.php");
}