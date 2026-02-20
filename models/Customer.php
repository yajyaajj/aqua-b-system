<?php
/**
 * Customer Model
 *
 * Handles all database operations for the customers table.
 */

require_once __DIR__ . '/../config/database.php';

class Customer
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all customers.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM customers ORDER BY full_name ASC');
        return $stmt->fetchAll();
    }

    /**
     * Find a customer by ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $customer = $stmt->fetch();
        return $customer ?: null;
    }

    /**
     * Create a new customer.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO customers (full_name, contact_number, address)
             VALUES (:full_name, :contact_number, :address)'
        );
        $stmt->execute([
            ':full_name'      => $data['full_name'],
            ':contact_number' => $data['contact_number'] ?? null,
            ':address'        => $data['address'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing customer.
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['full_name', 'contact_number', 'address'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = 'UPDATE customers SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete a customer by ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM customers WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Search customers by name or contact number.
     */
    public function search(string $term): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM customers
             WHERE full_name LIKE :term OR contact_number LIKE :term
             ORDER BY full_name ASC'
        );
        $stmt->execute([':term' => '%' . $term . '%']);
        return $stmt->fetchAll();
    }

    /**
     * Get the order history for a specific customer.
     */
    public function getOrderHistory(int $customerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, u.full_name AS cashier_name
             FROM orders o
             JOIN users u ON o.user_id = u.id
             WHERE o.customer_id = :customer_id
             ORDER BY o.created_at DESC'
        );
        $stmt->execute([':customer_id' => $customerId]);
        return $stmt->fetchAll();
    }
}
