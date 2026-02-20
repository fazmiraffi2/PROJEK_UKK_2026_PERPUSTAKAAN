<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "projek_ukk"; // Pastikan nama database di phpMyAdmin sesuai

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

define('BASEURL', 'http://localhost/library1/');