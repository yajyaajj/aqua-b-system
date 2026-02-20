<?php
/**
 * StockMovement Model
 *
 * Handles all database operations for the stock_movements table.
 */

require_once __DIR__ . '/../config/database.php';

class StockMovement
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all stock movements with product and user details.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT sm.*, p.product_name, u.full_name AS created_by_name
             FROM stock_movements sm
             JOIN products p ON sm.product_id = p.id
             JOIN users u ON sm.created_by = u.id
             ORDER BY sm.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Create a new stock movement record.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO stock_movements (product_id, type, quantity, reference, notes, created_by)
             VALUES (:product_id, :type, :quantity, :reference, :notes, :created_by)'
        );
        $stmt->execute([
            ':product_id' => $data['product_id'],
            ':type'       => $data['type'],
            ':quantity'   => $data['quantity'],
            ':reference'  => $data['reference'] ?? null,
            ':notes'      => $data['notes'] ?? null,
            ':created_by' => $data['created_by'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Get all stock movements for a specific product.
     */
    public function getByProduct(int $productId): array
    {
        $stmt = $this->db->prepare(
            'SELECT sm.*, p.product_name, u.full_name AS created_by_name
             FROM stock_movements sm
             JOIN products p ON sm.product_id = p.id
             JOIN users u ON sm.created_by = u.id
             WHERE sm.product_id = :product_id
             ORDER BY sm.created_at DESC'
        );
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }

    /**
     * Get the most recent stock movements.
     */
    public function getRecent(int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            'SELECT sm.*, p.product_name, u.full_name AS created_by_name
             FROM stock_movements sm
             JOIN products p ON sm.product_id = p.id
             JOIN users u ON sm.created_by = u.id
             ORDER BY sm.created_at DESC
             LIMIT :lim'
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
