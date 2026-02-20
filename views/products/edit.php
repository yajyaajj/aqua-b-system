<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square me-2"></i>Edit Product</h2>
    <a href="<?php echo BASE_URL; ?>/index.php?page=products" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Products
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/index.php?page=products&action=update" method="POST" novalidate>
            <input type="hidden" name="id" value="<?php echo (int) $product['id']; ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                    <div class="invalid-feedback">Please enter a product name.</div>
                </div>

                <div class="col-md-6">
                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="" disabled>Select category</option>
                        <?php
                        $categories = ['Water Container', 'Water Refill', 'Accessories', 'Other'];
                        foreach ($categories as $cat):
                        ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($product['category'] === $cat) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a category.</div>
                </div>

                <div class="col-md-6">
                    <label for="quantity_in_stock" class="form-label">Quantity in Stock <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="quantity_in_stock" name="quantity_in_stock" min="0" value="<?php echo (int) $product['quantity_in_stock']; ?>" required>
                    <div class="invalid-feedback">Please enter a valid quantity.</div>
                </div>

                <div class="col-md-6">
                    <label for="reorder_level" class="form-label">Reorder Level <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="reorder_level" name="reorder_level" min="0" value="<?php echo (int) $product['reorder_level']; ?>" required>
                    <div class="invalid-feedback">Please enter a valid reorder level.</div>
                </div>

                <div class="col-md-6">
                    <label for="cost_price" class="form-label">Cost Price (₱) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="cost_price" name="cost_price" min="0" step="0.01" value="<?php echo number_format($product['cost_price'], 2, '.', ''); ?>" required>
                    <div class="invalid-feedback">Please enter a valid cost price.</div>
                </div>

                <div class="col-md-6">
                    <label for="selling_price" class="form-label">Selling Price (₱) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="selling_price" name="selling_price" min="0" step="0.01" value="<?php echo number_format($product['selling_price'], 2, '.', ''); ?>" required>
                    <div class="invalid-feedback">Please enter a valid selling price.</div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Update Product
                </button>
                <a href="<?php echo BASE_URL; ?>/index.php?page=products" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
