// Fungsi untuk konfirmasi peminjaman buku
function konfirmasiPinjam(judul) {
    return confirm("Apakah kamu yakin ingin meminjam buku: " + judul + "?");
}

// Fungsi Otomatis menghilangkan Alert setelah 3 detik
window.setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 3000);

// Efek halus saat scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});