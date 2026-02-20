<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
@media print {
    .navbar, .no-print, footer { display: none !important; }
    .container { max-width: 100% !important; }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calendar-month me-2"></i>Monthly Sales Report</h2>
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
            <input type="hidden" name="action" value="monthly_sales">
            <div class="col-auto">
                <label for="report_month" class="form-label">Month</label>
                <select class="form-select" id="report_month" name="report_month">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php echo ((int)($reportMonth ?? date('n')) === $m) ? 'selected' : ''; ?>>
                        <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <label for="report_year" class="form-label">Year</label>
                <select class="form-select" id="report_year" name="report_year">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo ((int)($reportYear ?? date('Y')) === $y) ? 'selected' : ''; ?>>
                        <?php echo $y; ?>
                    </option>
                    <?php endfor; ?>
                </select>
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
                <h6 class="text-uppercase">Total Revenue</h6>
                <h3 class="mb-0">₱<?php echo number_format($totalRevenue ?? 0, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body text-center">
                <h6 class="text-uppercase">Total Transactions</h6>
                <h3 class="mb-0"><?php echo (int) ($totalTransactions ?? 0); ?></h3>
            </div>
        </div>
    </div>
</div>

<p class="text-muted">Report Period: <strong><?php echo htmlspecialchars(date('F', mktime(0, 0, 0, (int)($reportMonth ?? date('n')), 1)) . ' ' . (int)($reportYear ?? date('Y'))); ?></strong></p>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th class="text-center">Number of Orders</th>
                        <th class="text-end">Daily Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($salesData)): ?>
                        <?php foreach ($salesData as $day): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('F j, Y', strtotime($day['date']))); ?></td>
                            <td class="text-center"><?php echo (int) $day['order_count']; ?></td>
                            <td class="text-end">₱<?php echo number_format($day['daily_total'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-dark fw-bold">
                            <td>Grand Total</td>
                            <td class="text-center"><?php echo (int) ($totalTransactions ?? 0); ?></td>
                            <td class="text-end">₱<?php echo number_format($totalRevenue ?? 0, 2); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No sales found for this period.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
