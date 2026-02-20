<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cart-plus me-2"></i>Create Order</h2>
    <a href="<?php echo BASE_URL; ?>/index.php?page=orders" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Orders
    </a>
</div>

<form action="<?php echo BASE_URL; ?>/index.php?page=orders&action=store" method="POST" id="orderForm">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light"><h5 class="mb-0">Customer</h5></div>
                <div class="card-body">
                    <select name="customer_id" id="customer_id" class="form-select" required>
                        <option value="">-- Select Customer --</option>
                        <?php if (!empty($customers)): ?>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo (int) $customer['id']; ?>">
                                    <?php echo htmlspecialchars($customer['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light"><h5 class="mb-0">Add Item</h5></div>
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="addProduct" class="form-label">Product</label>
                            <select id="addProduct" class="form-select">
                                <option value="">-- Select Product --</option>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo (int) $product['id']; ?>"
                                                data-price="<?php echo (float) $product['selling_price']; ?>"
                                                data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                data-stock="<?php echo (int) $product['quantity_in_stock']; ?>">
                                            <?php echo htmlspecialchars($product['product_name']); ?> (Stock: <?php echo (int) $product['quantity_in_stock']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="addQty" class="form-label">Quantity</label>
                            <input type="number" id="addQty" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-md-3">
                            <label for="addPrice" class="form-label">Unit Price</label>
                            <input type="text" id="addPrice" class="form-control" readonly>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="btnAddItem" class="btn btn-primary w-100">
                                <i class="bi bi-plus-lg me-1"></i>Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light"><h5 class="mb-0">Order Items</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr id="noItemsRow">
                                    <td colspan="5" class="text-center text-muted py-4">No items added yet.</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="3" class="text-end">Total Amount:</th>
                                    <th id="totalAmount">₱0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-success w-100 btn-lg" id="btnSubmit">
                        <i class="bi bi-check-lg me-1"></i>Create Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
(function() {
    const addProductEl = document.getElementById('addProduct');
    const addQtyEl     = document.getElementById('addQty');
    const addPriceEl   = document.getElementById('addPrice');
    const btnAddItem   = document.getElementById('btnAddItem');
    const itemsBody    = document.getElementById('itemsBody');
    const totalAmountEl = document.getElementById('totalAmount');
    const noItemsRow   = document.getElementById('noItemsRow');
    let orderTotal = 0;

    addProductEl.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        addPriceEl.value = opt.value ? parseFloat(opt.dataset.price).toFixed(2) : '';
    });

    btnAddItem.addEventListener('click', function() {
        const opt = addProductEl.options[addProductEl.selectedIndex];
        if (!opt.value) { alert('Please select a product.'); return; }

        const productId = opt.value;
        const productName = opt.dataset.name;
        const price = parseFloat(opt.dataset.price);
        const qty = parseInt(addQtyEl.value);

        if (isNaN(qty) || qty < 1) { alert('Please enter a valid quantity.'); return; }

        const existing = itemsBody.querySelector('tr[data-product-id="' + productId + '"]');
        if (existing) { alert('This product is already added. Remove it first to change quantity.'); return; }

        noItemsRow.style.display = 'none';
        const subtotal = price * qty;
        const row = document.createElement('tr');
        row.dataset.productId = productId;
        row.innerHTML =
            '<td>' + escapeHtml(productName) +
                '<input type="hidden" name="product_id[]" value="' + productId + '">' +
            '</td>' +
            '<td>' + qty +
                '<input type="hidden" name="quantity[]" value="' + qty + '">' +
            '</td>' +
            '<td>₱' + price.toFixed(2) +
                '<input type="hidden" name="unit_price[]" value="' + price.toFixed(2) + '">' +
            '</td>' +
            '<td>₱' + subtotal.toFixed(2) + '</td>' +
            '<td class="text-center">' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-remove">' +
                    '<i class="bi bi-trash"></i>' +
                '</button>' +
            '</td>';
        itemsBody.appendChild(row);
        orderTotal += subtotal;
        totalAmountEl.textContent = '₱' + orderTotal.toFixed(2);

        addProductEl.value = '';
        addQtyEl.value = 1;
        addPriceEl.value = '';
    });

    itemsBody.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-remove');
        if (!btn) return;
        const row = btn.closest('tr');
        const price = parseFloat(row.querySelector('input[name="unit_price[]"]').value);
        const qty = parseInt(row.querySelector('input[name="quantity[]"]').value);
        orderTotal -= price * qty;
        row.remove();
        totalAmountEl.textContent = '₱' + orderTotal.toFixed(2);
        if (!itemsBody.querySelector('tr[data-product-id]')) {
            noItemsRow.style.display = '';
        }
    });

    document.getElementById('orderForm').addEventListener('submit', function(e) {
        if (!itemsBody.querySelector('tr[data-product-id]')) {
            e.preventDefault();
            alert('Please add at least one item to the order.');
        }
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
})();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
