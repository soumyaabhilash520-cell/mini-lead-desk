<?php
/**
 * LeadDesk Mini - Footer Template
 * Mandatory footer credit per Task A / Task B specs.
 */
?>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Main Script -->
    <script src="assets/js/main.js"></script>

    <?php if (isset($includeDashboardJs) && $includeDashboardJs): ?>
        <script src="assets/js/dashboard.js"></script>
    <?php endif; ?>
</body>
</html>
