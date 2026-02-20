<?php 
require_once 'config/init.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - LIB-APP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        .register-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .register-header {
            background: #ffffff;
            padding: 30px 30px 10px;
            border-radius: 20px 20px 0 0;
            text-align: center;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px;
            background: #f8f9fa;
        }
        .btn-register {
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card register-card">
                <div class="register-header">
                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">REGISTRASI SISWA</h3>
                    <p class="text-muted">Lengkapi data diri untuk meminjam buku</p>
                </div>
                <div class="card-body p-4 pt-0">
                    <form action="proses_register.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama sesuai absen" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Buat username unik" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password / NISN</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password kuat" required>
                        </div>

                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label small fw-bold">Kelas</label>
                                <select name="kelas" class="form-select" required>
                                    <option value="X">X</option>
                                    <option value="XI">XI</option>
                                    <option value="XII">XII</option>
                                </select>
                            </div>
                            <div class="col-md-7 mb-3">
                                <label class="form-label small fw-bold">Jurusan</label>
                                <input type="text" name="jurusan" class="form-control" placeholder="Contoh: RPL 1" required>
                            </div>
                        </div>

                        <button type="submit" name="register" class="btn btn-primary w-100 btn-register mt-2 mb-3">
                            DAFTAR SEKARANG
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="small text-muted">Sudah punya akun? <a href="index.php" class="fw-bold text-decoration-none text-primary">Login Kembali</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>