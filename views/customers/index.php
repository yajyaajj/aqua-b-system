<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people me-2"></i>Customers</h2>
    <a href="<?php echo BASE_URL; ?>/index.php?page=customers&action=create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Customer
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Date Added</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $index => $customer): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($customer['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['contact_number'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($customer['address'] ?? ''); ?></td>
                            <td><?php echo date('M j, Y', strtotime($customer['created_at'])); ?></td>
                            <td class="text-center">
                                <a href="<?php echo BASE_URL; ?>/index.php?page=customers&action=view&id=<?php echo (int) $customer['id']; ?>" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/index.php?page=customers&action=edit&id=<?php echo (int) $customer['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/index.php?page=customers&action=delete&id=<?php echo (int) $customer['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this customer?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No customers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
