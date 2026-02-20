<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-credit-card me-2"></i>Payments</h2>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo BASE_URL; ?>/index.php" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="payments">
            <div class="col-auto">
                <label for="start_date" class="form-label mb-0">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
            </div>
            <div class="col-auto">
                <label for="end_date" class="form-label mb-0">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Order Number</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $index => $payment): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($payment['order_number'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($payment['customer_name'] ?? $payment['full_name'] ?? ''); ?></td>
                            <td>₱<?php echo number_format((float) $payment['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($payment['created_at'])); ?></td>
                            <td class="text-center">
                                <a href="<?php echo BASE_URL; ?>/index.php?page=payments&action=receipt&id=<?php echo (int) $payment['id']; ?>" class="btn btn-sm btn-outline-primary" title="View Receipt" target="_blank">
                                    <i class="bi bi-receipt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No payments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
