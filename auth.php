<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['login'])) {
    // 1. Ambil input dan bersihkan (Gunakan trim untuk menghindari spasi tak sengaja)
    $username_input = mysqli_real_escape_string($conn, trim($_POST['username']));
    $pass_input     = mysqli_real_escape_string($conn, trim($_POST['password']));

    // 2. Query ke database
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username_input' AND password='$pass_input'");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        // 3. Bersihkan session lama untuk keamanan
        session_unset(); 
        session_regenerate_id();

        // 4. Simpan data baru ke Session
        // SINKRONISASI: Kita simpan ke 'id' DAN 'user_id' agar semua file kodingan kamu cocok
        $_SESSION['id']           = $data['id']; 
        $_SESSION['user_id']      = $data['id']; 
        $_SESSION['username']     = $data['username'];
        $_SESSION['role']         = $data['role'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap']; 

        // 5. Redirect berdasarkan Role
        if ($data['role'] == 'admin') {
            header('Location: views/admin/dashboard.php');
        } else {
            header('Location: views/siswa/dashboard.php');
        }
        exit();
    } else {
        echo "<script>
                alert('Username atau Password Salah!'); 
                window.location='index.php';
              </script>";
        exit();
    }
}