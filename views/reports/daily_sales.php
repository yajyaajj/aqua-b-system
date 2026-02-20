<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
@media print {
    .navbar, .no-print, footer { display: none !important; }
    .container { max-width: 100% !important; }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calendar-day me-2"></i>Daily Sales Report</h2>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-outline-primary me-2">
            <i class="bi bi-printer me-1"></i>Print Report
        </button>
        <a href="<?php echo BASE_URL; ?>/index.php?page=reports" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Reports
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4 no-print">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="reports">
            <input type="hidden" name="action" value="daily_sales">
            <div class="col-auto">
                <label for="report_date" class="form-label">Select Date</label>
                <input type="date" class="form-control" id="report_date" name="report_date" value="<?php echo htmlspecialchars($reportDate ?? date('Y-m-d')); ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body text-center">
                <h6 class="text-uppercase">Total Sales</h6>
                <h3 class="mb-0">₱<?php echo number_format($totalSales ?? 0, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body text-center">
                <h6 class="text-uppercase">Number of Transactions</h6>
                <h3 class="mb-0"><?php echo (int) ($totalTransactions ?? 0); ?></h3>
            </div>
        </div>
    </div>
</div>

<p class="text-muted">Report Date: <strong><?php echo htmlspecialchars(date('F j, Y', strtotime($reportDate ?? date('Y-m-d')))); ?></strong></p>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th class="text-end">Total Amount</th>
                        <th>Payment Method</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($salesData)): ?>
                        <?php foreach ($salesData as $sale): ?>
                        <tr>
                            <td><?php echo (int) $sale['id']; ?></td>
                            <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in'); ?></td>
                            <td><?php echo htmlspecialchars($sale['items'] ?? ''); ?></td>
                            <td class="text-end">₱<?php echo number_format($sale['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($sale['payment_method'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(date('h:i A', strtotime($sale['created_at'] ?? ''))); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No sales found for this date.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
