<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square me-2"></i>Edit User</h2>
    <a href="<?php echo BASE_URL; ?>/index.php?page=users" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Users
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/index.php?page=users&action=update" method="POST" novalidate>
            <input type="hidden" name="id" value="<?php echo (int) $user['id']; ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    <div class="invalid-feedback">Please enter a username.</div>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" minlength="6">
                    <div class="form-text">Leave blank to keep current password. Minimum 6 characters.</div>
                    <div class="invalid-feedback">Password must be at least 6 characters.</div>
                </div>

                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    <div class="invalid-feedback">Please enter a full name.</div>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>

                <div class="col-md-6">
                    <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <option value="" disabled>Select role</option>
                        <option value="1" <?php echo ((int) $user['role_id'] === 1) ? 'selected' : ''; ?>>Admin</option>
                        <option value="2" <?php echo ((int) $user['role_id'] === 2) ? 'selected' : ''; ?>>Staff</option>
                    </select>
                    <div class="invalid-feedback">Please select a role.</div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Update User
                </button>
                <a href="<?php echo BASE_URL; ?>/index.php?page=users" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
