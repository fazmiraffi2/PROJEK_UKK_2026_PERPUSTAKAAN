<?php
class Controller {
    // Fungsi untuk memanggil view (tampilan)
    public function view($folder, $file, $data = []) {
        extract($data);
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/' . $folder . '/' . $file . '.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }
}