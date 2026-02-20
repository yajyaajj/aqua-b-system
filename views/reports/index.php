<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4">
    <h2><i class="bi bi-bar-chart me-2"></i>Reports</h2>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <a href="<?php echo BASE_URL; ?>/index.php?page=reports&action=daily_sales" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-calendar-day text-primary" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-dark">Daily Sales Report</h4>
                    <p class="text-muted mb-0">View sales for a specific day</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="<?php echo BASE_URL; ?>/index.php?page=reports&action=monthly_sales" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-calendar-month text-success" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-dark">Monthly Sales Report</h4>
                    <p class="text-muted mb-0">View sales for a specific month</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="<?php echo BASE_URL; ?>/index.php?page=reports&action=inventory" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-box-seam text-info" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-dark">Inventory Report</h4>
                    <p class="text-muted mb-0">View current inventory status</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="<?php echo BASE_URL; ?>/index.php?page=reports&action=low_stock" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-dark">Low Stock Report</h4>
                    <p class="text-muted mb-0">View items that need restocking</p>
                </div>
            </div>
        </a>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
