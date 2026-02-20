<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cart me-2"></i>Orders</h2>
    <a href="<?php echo BASE_URL; ?>/index.php?page=orders&action=create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Create Order
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo BASE_URL; ?>/index.php" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="orders">
            <div class="col-auto">
                <label for="status" class="form-label mb-0">Filter by Status</label>
                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="paid" <?php echo (isset($_GET['status']) && $_GET['status'] === 'paid') ? 'selected' : ''; ?>>Paid</option>
                    <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
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
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($order['order_number']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name'] ?? $order['full_name'] ?? ''); ?></td>
                            <td>₱<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                            <td>
                                <?php
                                    $status = strtolower($order['status']);
                                    $badgeClass = match ($status) {
                                        'pending'   => 'warning',
                                        'paid'      => 'success',
                                        'cancelled' => 'danger',
                                        default     => 'secondary',
                                    };
                                ?>
                                <span class="badge bg-<?php echo $badgeClass; ?>"><?php echo htmlspecialchars(ucfirst($order['status'])); ?></span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                            <td class="text-center">
                                <a href="<?php echo BASE_URL; ?>/index.php?page=orders&action=view&id=<?php echo (int) $order['id']; ?>" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if ($status === 'pending'): ?>
                                    <a href="<?php echo BASE_URL; ?>/index.php?page=payments&action=create&order_id=<?php echo (int) $order['id']; ?>" class="btn btn-sm btn-outline-success" title="Make Payment">
                                        <i class="bi bi-cash"></i>
                                    </a>
                                    <?php if (isset($_SESSION['role_id']) && (int) $_SESSION['role_id'] === 1): ?>
                                        <form action="<?php echo BASE_URL; ?>/index.php?page=orders&action=cancel" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                            <input type="hidden" name="id" value="<?php echo (int) $order['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
