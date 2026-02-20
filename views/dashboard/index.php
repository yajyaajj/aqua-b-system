<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
    <span class="text-muted"><?php echo date('F j, Y'); ?></span>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Total Sales Today</p>
                        <h3 class="fw-bold text-success mb-0">₱<?php echo number_format($totalSalesToday, 2); ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-currency-dollar text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Monthly Revenue</p>
                        <h3 class="fw-bold text-primary mb-0">₱<?php echo number_format($currentMonthRevenue, 2); ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-graph-up text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Total Orders</p>
                        <h3 class="fw-bold text-warning mb-0"><?php echo number_format($totalOrders); ?></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-cart-check text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Low Stock Items</p>
                        <h3 class="fw-bold text-danger mb-0"><?php echo count($lowStockProducts); ?></h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Low Stock Alerts -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Low Stock Alerts</h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($lowStockProducts)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th class="text-center">Current Stock</th>
                                <th class="text-center">Reorder Level</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockProducts as $product): ?>
                            <tr>
                                <td class="fw-semibold"><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td class="text-center"><?php echo (int) $product['quantity_in_stock']; ?></td>
                                <td class="text-center"><?php echo (int) $product['reorder_level']; ?></td>
                                <td class="text-center">
                                    <?php if ((int) $product['quantity_in_stock'] === 0): ?>
                                        <span class="badge bg-danger">Out of Stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">All products are well stocked.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Recent Orders</h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentOrders)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td class="fw-semibold">#<?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
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
                                <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No recent orders.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
