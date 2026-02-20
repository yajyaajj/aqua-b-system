<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-plus me-2"></i>Add New Customer</h2>
    <a href="<?php echo BASE_URL; ?>/index.php?page=customers" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Customers
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/index.php?page=customers&action=store" method="POST" novalidate>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                    <div class="invalid-feedback">Please enter the customer's full name.</div>
                </div>

                <div class="col-md-6">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number">
                </div>

                <div class="col-12">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Save Customer
                </button>
                <a href="<?php echo BASE_URL; ?>/index.php?page=customers" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
