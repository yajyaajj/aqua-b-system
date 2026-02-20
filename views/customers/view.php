<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person me-2"></i><?php echo htmlspecialchars($customer['full_name']); ?></h2>
    <a href="<?php echo BASE_URL; ?>/index.php?page=customers" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Customers
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Customer Details</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Full Name:</strong><br><?php echo htmlspecialchars($customer['full_name']); ?></p>
                <p class="mb-2"><strong>Contact Number:</strong><br><?php echo htmlspecialchars($customer['contact_number'] ?? 'N/A'); ?></p>
                <p class="mb-0"><strong>Address:</strong><br><?php echo htmlspecialchars($customer['address'] ?? 'N/A'); ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Order History</h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($orderHistory)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderHistory as $order): ?>
                            <tr>
                                <td class="fw-semibold">#<?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                <td class="text-end">₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td class="text-center">
                                    <?php
                                    $statusClass = match($order['status'] ?? '') {
                                        'completed' => 'bg-success',
                                        'pending'   => 'bg-warning text-dark',
                                        'cancelled' => 'bg-danger',
                                        default     => 'bg-secondary',
                                    };
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst(htmlspecialchars($order['status'])); ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo BASE_URL; ?>/index.php?page=orders&action=view&id=<?php echo (int) $order['id']; ?>" class="btn btn-sm btn-outline-info" title="View Order">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No orders found for this customer.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
