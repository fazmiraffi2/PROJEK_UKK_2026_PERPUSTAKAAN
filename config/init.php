<?php
// config/init.php

// 1. Panggil file-file core
require_once 'database.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/PerpusModel.php';

// 2. Settingan Database (SESUAIKAN DENGAN DASHBOARD INFINITYFREE)
// Jangan pakai 'localhost' atau 'root' kalau sudah di hosting!

define('DB_HOST', 'sql311.infinityfree.com'); // Lihat di menu MySQL Databases InfinityFree
define('DB_USER', 'if0_382xxxxx');            // MySQL Username lo
define('DB_PASS', 'PasswordHostingLo');       // Password akun hosting (biasanya sama dengan password login)
define('DB_NAME', 'if0_382xxxxx_db_perpus');  // Nama database yang lo buat di hosting

// 3. Inisialisasi Koneksi (Biar bisa dipake di Model)
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Opsional: Base URL (Sesuaikan dengan domain infinityfree lo)
define('BASEURL', 'http://perpus-fazmi.infy.uk/');