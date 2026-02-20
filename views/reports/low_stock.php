<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
@media print {
    .navbar, .no-print, footer { display: none !important; }
    .container { max-width: 100% !important; }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Report</h2>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-outline-primary me-2">
            <i class="bi bi-printer me-1"></i>Print Report
        </button>
        <a href="<?php echo BASE_URL; ?>/index.php?page=reports" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Reports
        </a>
    </div>
</div>

<?php if (!empty($lowStockProducts)): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong><?php echo count($lowStockProducts); ?></strong> item(s) need restocking.
</div>
<?php endif; ?>

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
                        <th class="text-center">Deficit</th>
                        <th class="text-center no-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lowStockProducts)): ?>
                        <?php foreach ($lowStockProducts as $product): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td class="text-center">
                                <span class="badge bg-danger"><?php echo (int) $product['quantity_in_stock']; ?></span>
                            </td>
                            <td class="text-center"><?php echo (int) $product['reorder_level']; ?></td>
                            <td class="text-center">
                                <span class="text-danger fw-bold"><?php echo (int) $product['reorder_level'] - (int) $product['quantity_in_stock']; ?></span>
                            </td>
                            <td class="text-center no-print">
                                <a href="<?php echo BASE_URL; ?>/index.php?page=products&action=stock_in&id=<?php echo (int) $product['id']; ?>" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-box-arrow-in-down me-1"></i>Stock In
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">All products are adequately stocked.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
