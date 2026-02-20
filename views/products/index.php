<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam me-2"></i>Products</h2>
    <?php if (isset($_SESSION['role_id']) && (int) $_SESSION['role_id'] === 1): ?>
    <a href="<?php echo BASE_URL; ?>/index.php?page=products&action=create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Product
    </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Reorder Level</th>
                        <th class="text-end">Cost Price</th>
                        <th class="text-end">Selling Price</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $index => $product): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td class="text-center">
                                <?php if ((int) $product['quantity_in_stock'] <= (int) $product['reorder_level']): ?>
                                    <span class="badge bg-danger"><?php echo (int) $product['quantity_in_stock']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?php echo (int) $product['quantity_in_stock']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo (int) $product['reorder_level']; ?></td>
                            <td class="text-end">₱<?php echo number_format($product['cost_price'], 2); ?></td>
                            <td class="text-end">₱<?php echo number_format($product['selling_price'], 2); ?></td>
                            <td class="text-center">
                                <a href="<?php echo BASE_URL; ?>/index.php?page=products&action=edit&id=<?php echo (int) $product['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/index.php?page=products&action=stock_in&id=<?php echo (int) $product['id']; ?>" class="btn btn-sm btn-outline-success" title="Stock In">
                                    <i class="bi bi-box-arrow-in-down"></i>
                                </a>
                                <?php if (isset($_SESSION['role_id']) && (int) $_SESSION['role_id'] === 1): ?>
                                <a href="<?php echo BASE_URL; ?>/index.php?page=products&action=delete&id=<?php echo (int) $product['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this product?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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
