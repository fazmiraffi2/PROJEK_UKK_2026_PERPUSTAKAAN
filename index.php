<?php 
session_start();

// 1. ANTI-BACK & ANTI-CACHE (Penting!)
// Agar saat di halaman login, browser tidak menyimpan data dashboard sebelumnya
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 2. Jika sudah login, langsung lempar ke dashboard masing-masing
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: views/admin/dashboard.php");
    } else {
        header("Location: views/siswa/dashboard.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LIB-APP Digital Library</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        body {
            background: var(--primary-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        .login-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: fadeInUp 0.8s ease;
        }

        .login-header {
            background: #ffffff;
            padding: 40px 40px 10px;
            text-align: center;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            box-shadow: 0 10px 20px rgba(118, 75, 162, 0.3);
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 1px solid #eee;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #667eea;
            background: #fff;
        }

        .input-group-text {
            border-radius: 12px;
            background: #f8f9fa;
            border: 1px solid #eee;
            color: #764ba2;
        }

        .btn-login {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s;
            color: white;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(118, 75, 162, 0.4);
            color: white;
        }

        .register-link {
            color: #764ba2;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4 px-4">
            <div class="card login-card">
                <div class="login-header">
                    <div class="icon-box animate__animated animate__bounceIn">
                        <i class="fas fa-book-reader fa-3x"></i>
                    </div>
                    <h3 class="fw-bold mb-1">LIB-APP</h3>
                    <p class="text-muted small">Pustaka Digital dalam Genggaman</p>
                </div>
                
                <div class="card-body p-4 pt-2">
                    <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'gagal') : ?>
                        <div class="alert alert-danger border-0 small text-center rounded-3 animate__animated animate__shakeX" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i> Username atau Password Salah!
                        </div>
                    <?php endif; ?>

                    <form action="auth.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Username</label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0"><i class="fas fa-user"></i></span>
                                <input type="text" name="username" class="form-control border-start-0" placeholder="Username Anda" required autofocus>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Password / NISN</label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control border-start-0" placeholder="Password Anda" required>
                            </div>
                        </div>

                        <button type="submit" name="login" class="btn btn-login w-100 mb-3">
                            LOGIN SEKARANG <i class="fas fa-sign-in-alt ms-2"></i>
                        </button>
                    </form>
                    
                    <div class="text-center mt-2">
                        <p class="small text-muted mb-0">Belum memiliki akun?</p>
                        <a href="register.php" class="register-link small">Daftar Anggota Siswa</a>
                    </div>
                </div>
            </div>
            
            <p class="text-center text-white-50 mt-4 small">
                &copy; <?= date('Y'); ?> LIB-APP - All Rights Reserved
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>