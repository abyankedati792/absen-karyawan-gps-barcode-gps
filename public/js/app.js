/**
 * Fungsi umum halaman aplikasi.
 */
document.addEventListener('DOMContentLoaded', function () {
    // Tutup tombol sidebar di layar kecil
    const btnSidebar = document.getElementById('btnSidebar');
    const sidebar = document.getElementById('sidebar');
    if (btnSidebar && sidebar) {
        btnSidebar.addEventListener('click', function () {
            sidebar.classList.toggle('sidebar-open');
        });
        document.addEventListener('click', function (e) {
            if (
                sidebar.classList.contains('sidebar-open') &&
                !sidebar.contains(e.target) &&
                e.target !== btnSidebar
            ) {
                sidebar.classList.remove('sidebar-open');
            }
        });
    }

    // Auto-dismiss alert
    setTimeout(function () {
        document.querySelectorAll('.alert-dismissible').forEach(function (el) {
            try {
                bootstrap.Alert.getOrCreateInstance(el).close();
            } catch (e) { /* abaikan */ }
        });
    }, 6000);

    // Konfirmasi hapus
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});
