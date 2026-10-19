    </div> <!-- End .p-4 -->

    <footer class="bg-white border-top py-3 px-4 text-center text-muted small mt-auto">
        &copy; <?= date('Y') ?> TechTower Admin Panel </footer>
</div> <!-- End #main-content -->

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Responsive sidebar toggle
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
</script>

</body>
</html>
