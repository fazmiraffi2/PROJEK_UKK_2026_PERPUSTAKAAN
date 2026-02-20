<?php
// models/PerpusModel.php
class PerpusModel extends Model {

    // ==========================================
    // BAGIAN GLOBAL & STATISTIK
    // ==========================================

    public function hitungData($tabel, $where = "") {
        $query = "SELECT COUNT(*) as total FROM $tabel $where";
        $result = mysqli_query($this->db, $query);
        $data = mysqli_fetch_assoc($result);
        return $data['total'];
    }

    // ==========================================
    // BAGIAN BUKU (CRUD + FOTO + KELAS)
    // ==========================================

    private function uploadFotoBuku($file) {
        if (!isset($file['foto']) || $file['foto']['error'] === 4) return 'default.png';

        $namaFile = $file['foto']['name'];
        $ukuranFile = $file['foto']['size'];
        $tmpName = $file['foto']['tmp_name'];

        $ekstensiValid = ['jpg', 'jpeg', 'png'];
        $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

        if (!in_array($ekstensi, $ekstensiValid)) return false;
        if ($ukuranFile > 2000000) return false;

        $namaFileBaru = uniqid() . '.' . $ekstensi;
        $tujuan = '../../public/img/buku/' . $namaFileBaru;

        if (move_uploaded_file($tmpName, $tujuan)) {
            return $namaFileBaru;
        }
        return false;
    }

    // UPDATE: Hanya tambah parameter keyword tanpa hapus logika lama
    public function getAllBuku($keyword = "") {
        if ($keyword != "") {
            $keyword = mysqli_real_escape_string($this->db, $keyword);
            $query = "SELECT * FROM buku WHERE 
                      judul LIKE '%$keyword%' OR 
                      penulis LIKE '%$keyword%' OR 
                      kelas_buku LIKE '%$keyword%' 
                      ORDER BY id DESC";
        } else {
            $query = "SELECT * FROM buku ORDER BY id DESC";
        }
        return mysqli_query($this->db, $query);
    }

    public function getBukuById($id) {
        $id = mysqli_real_escape_string($this->db, $id);
        $query = "SELECT * FROM buku WHERE id = '$id'";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_assoc($result);
    }

    public function cekBukuDipinjam($id) {
        $id = mysqli_real_escape_string($this->db, $id);
        $query = "SELECT id FROM peminjaman WHERE buku_id = '$id' AND (status = 'dipinjam' OR status = 'pending' OR status = 'proses_kembali')";
        $result = mysqli_query($this->db, $query);
        return mysqli_num_rows($result) > 0;
    }

    public function tambahBuku($data, $file) {
        $judul = mysqli_real_escape_string($this->db, htmlspecialchars($data['judul']));
        $penulis = mysqli_real_escape_string($this->db, htmlspecialchars($data['penulis']));
        $stok = mysqli_real_escape_string($this->db, htmlspecialchars($data['stok']));
        $kelas_buku = mysqli_real_escape_string($this->db, htmlspecialchars($data['kelas_buku']));
        
        $foto = $this->uploadFotoBuku($file);
        if (!$foto) return false;

        $query = "INSERT INTO buku (judul, penulis, kelas_buku, stok, foto) 
                  VALUES ('$judul', '$penulis', '$kelas_buku', '$stok', '$foto')";
        return mysqli_query($this->db, $query);
    }

    public function updateBuku($data, $file) {
        $id = mysqli_real_escape_string($this->db, $data['id']);
        $judul = mysqli_real_escape_string($this->db, htmlspecialchars($data['judul']));
        $penulis = mysqli_real_escape_string($this->db, htmlspecialchars($data['penulis']));
        $stok = mysqli_real_escape_string($this->db, htmlspecialchars($data['stok']));
        $fotoLama = mysqli_real_escape_string($this->db, $data['foto_lama']);
        $kelas_buku = mysqli_real_escape_string($this->db, htmlspecialchars($data['kelas_buku']));

        if (isset($file['foto']) && $file['foto']['error'] === 0) {
            $foto = $this->uploadFotoBuku($file);
            if ($foto) {
                if ($fotoLama != 'default.png' && file_exists('../../public/img/buku/' . $fotoLama)) {
                    unlink('../../public/img/buku/' . $fotoLama);
                }
            } else {
                return false; 
            }
        } else {
            $foto = $fotoLama;
        }
        
        $query = "UPDATE buku SET 
                    judul = '$judul', 
                    penulis = '$penulis', 
                    kelas_buku = '$kelas_buku',
                    stok = '$stok', 
                    foto = '$foto' 
                  WHERE id = '$id'";
                  
        return mysqli_query($this->db, $query);
    }

    public function hapusBuku($id) {
        $id = mysqli_real_escape_string($this->db, $id);
        if($this->cekBukuDipinjam($id)) return false;

        $buku = $this->getBukuById($id);
        if ($buku && $buku['foto'] != 'default.png' && file_exists('../../public/img/buku/' . $buku['foto'])) {
            unlink('../../public/img/buku/' . $buku['foto']);
        }
        return mysqli_query($this->db, "DELETE FROM buku WHERE id = '$id'");
    }

    // ==========================================
    // BAGIAN ANGGOTA/USER (Siswa) 
    // ==========================================

    public function getAllSiswa() {
        return mysqli_query($this->db, "SELECT * FROM users WHERE role = 'siswa' ORDER BY id DESC");
    }

    public function tambahSiswa($data) {
        $username = mysqli_real_escape_string($this->db, htmlspecialchars($data['username']));
        $password = mysqli_real_escape_string($this->db, $data['password']); 
        $nama     = mysqli_real_escape_string($this->db, htmlspecialchars($data['nama_lengkap']));
        $kelas    = mysqli_real_escape_string($this->db, htmlspecialchars($data['kelas']));
        $jurusan  = mysqli_real_escape_string($this->db, htmlspecialchars($data['jurusan']));
        $role     = 'siswa';

        $cek = mysqli_query($this->db, "SELECT id FROM users WHERE username = '$username'");
        if(mysqli_num_rows($cek) > 0) return false;

        $query = "INSERT INTO users (username, password, nama_lengkap, kelas, jurusan, role) 
                  VALUES ('$username', '$password', '$nama', '$kelas', '$jurusan', '$role')";
        return mysqli_query($this->db, $query);
    }

    // ==========================================
    // BAGIAN TRANSAKSI (Sistem Approval)
    // ==========================================

    public function konfirmasiPeminjaman($id_peminjaman, $aksi) {
        $id_peminjaman = mysqli_real_escape_string($this->db, $id_peminjaman);
        
        $sql = "SELECT buku_id FROM peminjaman WHERE id = '$id_peminjaman'";
        $res = mysqli_query($this->db, $sql);
        $data = mysqli_fetch_assoc($res);
        $id_buku = $data['buku_id'];

        if ($aksi == 'setujui') {
            $query = "UPDATE peminjaman SET status = 'dipinjam' WHERE id = '$id_peminjaman'";
            if(mysqli_query($this->db, $query)) {
                return mysqli_query($this->db, "UPDATE buku SET stok = stok - 1 WHERE id = '$id_buku'");
            }
        } elseif ($aksi == 'tolak') {
            return mysqli_query($this->db, "UPDATE peminjaman SET status = 'ditolak' WHERE id = '$id_peminjaman'");
        }
        return false;
    }

    public function kembalikanBuku($id_peminjaman) {
        $id_peminjaman = mysqli_real_escape_string($this->db, $id_peminjaman);
        $tgl_sekarang = date('Y-m-d');

        $sql = "SELECT buku_id, tanggal_kembali FROM peminjaman WHERE id = '$id_peminjaman'";
        $res = mysqli_query($this->db, $sql);
        $data = mysqli_fetch_assoc($res);
        if (!$data) return false;

        $id_buku = $data['buku_id'];
        
        $deadline = new DateTime($data['tanggal_kembali']);
        $today = new DateTime($tgl_sekarang);
        $denda = 0;

        if ($today > $deadline) {
            $selisih = $today->diff($deadline)->days;
            $denda = $selisih * 1000; 
        }

        $query = "UPDATE peminjaman SET 
                    status = 'kembali', 
                    denda = '$denda', 
                    tanggal_kembali_aktual = '$tgl_sekarang' 
                  WHERE id = '$id_peminjaman'";
        
        if(mysqli_query($this->db, $query)) {
            return mysqli_query($this->db, "UPDATE buku SET stok = stok + 1 WHERE id = '$id_buku'");
        }
        return false;
    }
}