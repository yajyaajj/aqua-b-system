<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cash me-2"></i>Record Payment</h2>
    <a href="<?php echo BASE_URL; ?>/index.php?page=orders&action=view&id=<?php echo (int) $order['id']; ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Order
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light"><h5 class="mb-0">Order Details</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <strong>Order Number:</strong><br>
                        <?php echo htmlspecialchars($order['order_number']); ?>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <strong>Customer:</strong><br>
                        <?php echo htmlspecialchars($order['customer_name'] ?? $order['full_name'] ?? ''); ?>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <strong>Total Amount:</strong><br>
                        <span class="fs-5 fw-bold text-success">₱<?php echo number_format((float) $order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light"><h5 class="mb-0">Payment Details</h5></div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/index.php?page=payments&action=store" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0" value="<?php echo number_format((float) $order['total_amount'], 2, '.', ''); ?>" readonly required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="">-- Select Method --</option>
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-lg me-1"></i>Record Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
