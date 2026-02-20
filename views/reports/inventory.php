<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
@media print {
    .navbar, .no-print, footer { display: none !important; }
    .container { max-width: 100% !important; }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam me-2"></i>Inventory Report</h2>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-outline-primary me-2">
            <i class="bi bi-printer me-1"></i>Print Report
        </button>
        <a href="<?php echo BASE_URL; ?>/index.php?page=reports" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Reports
        </a>
    </div>
</div>

<p class="text-muted">Date Generated: <strong><?php echo date('F j, Y g:i A'); ?></strong></p>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th class="text-center">Current Stock</th>
                        <th class="text-center">Reorder Level</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Cost Price</th>
                        <th class="text-end">Selling Price</th>
                        <th class="text-end">Stock Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalStockValue = 0;
                    if (!empty($products)):
                        foreach ($products as $product):
                            $stockValue = (int) $product['quantity_in_stock'] * (float) $product['cost_price'];
                            $totalStockValue += $stockValue;
                            $isLow = (int) $product['quantity_in_stock'] <= (int) $product['reorder_level'];
                    ?>
                    <tr>
                        <td class="fw-semibold"><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td class="text-center"><?php echo (int) $product['quantity_in_stock']; ?></td>
                        <td class="text-center"><?php echo (int) $product['reorder_level']; ?></td>
                        <td class="text-center">
                            <?php if ($isLow): ?>
                                <span class="badge bg-danger">Low Stock</span>
                            <?php else: ?>
                                <span class="badge bg-success">In Stock</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">₱<?php echo number_format($product['cost_price'], 2); ?></td>
                        <td class="text-end">₱<?php echo number_format($product['selling_price'], 2); ?></td>
                        <td class="text-end">₱<?php echo number_format($stockValue, 2); ?></td>
                    </tr>
                    <?php
                        endforeach;
                    ?>
                    <tr class="table-dark fw-bold">
                        <td colspan="7" class="text-end">Total Stock Value:</td>
                        <td class="text-end">₱<?php echo number_format($totalStockValue, 2); ?></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No products found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
