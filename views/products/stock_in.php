<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-arrow-in-down me-2"></i>Stock In - <?php echo htmlspecialchars($product['product_name']); ?></h2>
    <a href="<?php echo BASE_URL; ?>/index.php?page=products" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Products
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Current Stock Information</h5>
                <p class="mb-1"><strong>Product:</strong> <?php echo htmlspecialchars($product['product_name']); ?></p>
                <p class="mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                <p class="mb-0">
                    <strong>Current Stock:</strong>
                    <?php if ((int) $product['quantity_in_stock'] <= (int) $product['reorder_level']): ?>
                        <span class="badge bg-danger"><?php echo (int) $product['quantity_in_stock']; ?></span>
                    <?php else: ?>
                        <span class="badge bg-success"><?php echo (int) $product['quantity_in_stock']; ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/index.php?page=products&action=stock_in" method="POST" novalidate>
                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity to Add <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                        <div class="invalid-feedback">Please enter a quantity of at least 1.</div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Optional notes about this stock entry"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-lg me-1"></i>Add Stock
                    </button>
                    <a href="<?php echo BASE_URL; ?>/index.php?page=products" class="btn btn-outline-secondary ms-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
