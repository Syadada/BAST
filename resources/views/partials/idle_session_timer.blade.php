@auth
<!-- Pengatur Waktu Sesi Aktif 90 Detik (Auto Logout ke Login Biasa jika Idle) -->
<script>
(function() {
    var maxIdleSeconds = 90;
    var idleSeconds = 0;
    var logoutUrl = "{{ route('logout') }}";
    var csrfToken = "{{ csrf_token() }}";
    var isLoggingOut = false;

    function resetIdleTimer() {
        idleSeconds = 0;
    }

    // Pantau setiap aktivitas interaksi pengguna
    var events = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click'];
    events.forEach(function(evt) {
        window.addEventListener(evt, resetIdleTimer, { passive: true });
    });

    // Hitung mundur idle setiap 1 detik
    setInterval(function() {
        idleSeconds++;
        if (idleSeconds >= maxIdleSeconds && !isLoggingOut) {
            isLoggingOut = true;
            
            // Arahkan langsung ke URL logout (mendukung GET tanpa ketergantungan token CSRF basi)
            window.location.href = logoutUrl;
        }
    }, 1000);
})();
</script>
@endauth
