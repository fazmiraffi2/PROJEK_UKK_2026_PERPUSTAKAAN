<?php
session_start();
require_once '../../config/database.php';

if (isset($_POST['update'])) {
    $id = $_SESSION['user_id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);

    // LOGIKA UPLOAD FOTO
    $foto_nama = $_FILES['foto']['name'];
    $foto_size = $_FILES['foto']['size'];
    $foto_tmp = $_FILES['foto']['tmp_name'];

    if (!empty($foto_nama)) {
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi = strtolower(pathinfo($foto_nama, PATHINFO_EXTENSION));

        if (!in_array($ekstensi, $ekstensi_valid)) {
            echo "<script>alert('Format foto harus JPG atau PNG!'); window.location='profil.php';</script>";
            exit;
        }

        if ($foto_size > 2000000) {
            echo "<script>alert('Ukuran foto terlalu besar (Max 2MB)!'); window.location='profil.php';</script>";
            exit;
        }

        // Buat nama unik untuk foto
        $nama_foto_baru = uniqid() . '.' . $ekstensi;
        $tujuan = '../../public/img/profile/' . $nama_foto_baru;

        // Buat folder jika belum ada
        if (!is_dir('../../public/img/profile/')) {
            mkdir('../../public/img/profile/', 0777, true);
        }

        if (move_uploaded_file($foto_tmp, $tujuan)) {
            // Hapus foto lama jika bukan default
            $query_lama = mysqli_query($conn, "SELECT foto FROM users WHERE id = '$id'");
            $data_lama = mysqli_fetch_assoc($query_lama);
            if ($data_lama['foto'] != 'default.png' && !empty($data_lama['foto'])) {
                @unlink('../../public/img/profile/' . $data_lama['foto']);
            }
            $update_foto = ", foto = '$nama_foto_baru'";
        }
    } else {
        $update_foto = "";
    }

    $sql = "UPDATE users SET nama_lengkap = '$nama', kelas = '$kelas', jurusan = '$jurusan' $update_foto WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil!'); window.location='profil.php';</script>";
    }
}