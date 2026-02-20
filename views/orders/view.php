<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cart me-2"></i>Order Details</h2>
    <a href="<?php echo BASE_URL; ?>/index.php?page=orders" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Orders
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light"><h5 class="mb-0">Order Information</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Order Number:</strong><br>
                        <?php echo htmlspecialchars($order['order_number']); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Customer:</strong><br>
                        <?php echo htmlspecialchars($order['customer_name'] ?? $order['full_name'] ?? ''); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Date:</strong><br>
                        <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Status:</strong><br>
                        <?php
                            $status = strtolower($order['status']);
                            $badgeClass = match ($status) {
                                'pending'   => 'warning',
                                'paid'      => 'success',
                                'cancelled' => 'danger',
                                default     => 'secondary',
                            };
                        ?>
                        <span class="badge bg-<?php echo $badgeClass; ?> fs-6"><?php echo htmlspecialchars(ucfirst($order['status'])); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light"><h5 class="mb-0">Order Items</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orderItems)): ?>
                                <?php foreach ($orderItems as $index => $item): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo (int) $item['quantity']; ?></td>
                                    <td>₱<?php echo number_format((float) $item['unit_price'], 2); ?></td>
                                    <td>₱<?php echo number_format((float) $item['unit_price'] * (int) $item['quantity'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No items found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="4" class="text-end">Total Amount:</th>
                                <th>₱<?php echo number_format((float) $order['total_amount'], 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <?php if (!empty($payment)): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light"><h5 class="mb-0">Payment Information</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Payment Method:</strong><br>
                        <?php echo htmlspecialchars($payment['payment_method']); ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Amount:</strong><br>
                        ₱<?php echo number_format((float) $payment['amount'], 2); ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Date:</strong><br>
                        <?php echo date('M j, Y g:i A', strtotime($payment['created_at'])); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light"><h5 class="mb-0">Actions</h5></div>
            <div class="card-body d-grid gap-2">
                <?php if ($status === 'pending'): ?>
                    <a href="<?php echo BASE_URL; ?>/index.php?page=payments&action=create&order_id=<?php echo (int) $order['id']; ?>" class="btn btn-success">
                        <i class="bi bi-cash me-1"></i>Make Payment
                    </a>
                    <?php if (isset($_SESSION['role_id']) && (int) $_SESSION['role_id'] === 1): ?>
                        <form action="<?php echo BASE_URL; ?>/index.php?page=orders&action=cancel" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            <input type="hidden" name="id" value="<?php echo (int) $order['id']; ?>">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-circle me-1"></i>Cancel Order
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($status === 'paid' && !empty($payment)): ?>
                    <a href="<?php echo BASE_URL; ?>/index.php?page=payments&action=receipt&id=<?php echo (int) $payment['id']; ?>" class="btn btn-outline-primary" target="_blank">
                        <i class="bi bi-printer me-1"></i>Print Receipt
                    </a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/index.php?page=orders" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
