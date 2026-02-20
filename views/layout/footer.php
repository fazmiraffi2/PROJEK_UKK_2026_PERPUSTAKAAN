</div> </div> </div> <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // 1. Logika Toggle Sidebar (Hamburger Menu)
        // Kita gunakan jQuery agar konsisten dan menghindari bug "null selector"
        const $wrapper = $("#wrapper");
        const $menuToggle = $("#menu-toggle");

        $menuToggle.on("click", function(e) {
            e.preventDefault();
            $wrapper.toggleClass("toggled");
            
            // Simpan status sidebar di localStorage agar tidak reset saat pindah halaman
            const isOpen = $wrapper.hasClass("toggled");
            localStorage.setItem("sidebarStatus", isOpen ? "open" : "closed");
        });

        // 2. Load status sidebar saat halaman dimuat ulang
        const sidebarStatus = localStorage.getItem("sidebarStatus");
        if (sidebarStatus === "open") {
            $wrapper.addClass("toggled");
        }

        // 3. Inisialisasi Bootstrap Tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // 4. Efek Menghilangkan Alert Otomatis (Auto-close)
        // Alert akan menghilang halus setelah 3 detik
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 3000);
        
        // 5. Active State Fix (Opsional)
        // Memastikan link yang diklik mendapatkan class active jika belum ter-render dari server
        const currentUrl = window.location.href;
        $('.list-group-item').each(function() {
            if (this.href === currentUrl) {
                $(this).addClass('active');
            }
        });
    });
</script>

</body>
</html>