<?php
/**
 * Payment Model
 *
 * Handles all database operations for the payments table.
 */

require_once __DIR__ . '/../config/database.php';

class Payment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all payments with order and user details.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT p.*, o.order_number, u.full_name AS received_by_name
             FROM payments p
             JOIN orders o ON p.order_id = o.id
             JOIN users u ON p.received_by = u.id
             ORDER BY p.payment_date DESC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Find a payment by ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, o.order_number, u.full_name AS received_by_name
             FROM payments p
             JOIN orders o ON p.order_id = o.id
             JOIN users u ON p.received_by = u.id
             WHERE p.id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $payment = $stmt->fetch();
        return $payment ?: null;
    }

    /**
     * Create a new payment record.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payments (order_id, amount, payment_method, payment_date, received_by)
             VALUES (:order_id, :amount, :payment_method, :payment_date, :received_by)'
        );
        $stmt->execute([
            ':order_id'       => $data['order_id'],
            ':amount'         => $data['amount'],
            ':payment_method' => $data['payment_method'] ?? 'cash',
            ':payment_date'   => $data['payment_date'] ?? date('Y-m-d H:i:s'),
            ':received_by'    => $data['received_by'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Get all payments for a specific order.
     */
    public function getByOrder(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, u.full_name AS received_by_name
             FROM payments p
             JOIN users u ON p.received_by = u.id
             WHERE p.order_id = :order_id
             ORDER BY p.payment_date DESC'
        );
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    /**
     * Get payments within a date range (inclusive).
     */
    public function getByDateRange(string $startDate, string $endDate): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, o.order_number, u.full_name AS received_by_name
             FROM payments p
             JOIN orders o ON p.order_id = o.id
             JOIN users u ON p.received_by = u.id
             WHERE DATE(p.payment_date) BETWEEN :start_date AND :end_date
             ORDER BY p.payment_date DESC'
        );
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date'   => $endDate,
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Get total sales for a specific date.
     */
    public function getDailySales(string $date): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(amount), 0) AS total FROM payments WHERE DATE(payment_date) = :date'
        );
        $stmt->execute([':date' => $date]);
        return (float) $stmt->fetch()['total'];
    }

    /**
     * Get total sales for a specific month.
     */
    public function getMonthlySales(int $year, int $month): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(amount), 0) AS total
             FROM payments
             WHERE YEAR(payment_date) = :year AND MONTH(payment_date) = :month'
        );
        $stmt->execute([':year' => $year, ':month' => $month]);
        return (float) $stmt->fetch()['total'];
    }

    /**
     * Get all distinct payment methods.
     */
    public function getPaymentMethods(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT payment_method FROM payments ORDER BY payment_method ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
