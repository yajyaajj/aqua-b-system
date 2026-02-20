<?php
/**
 * Order Model
 *
 * Handles all database operations for orders and order_items tables.
 */

require_once __DIR__ . '/../config/database.php';

class Order
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all orders with customer names.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT o.*, c.full_name AS customer_name, u.full_name AS cashier_name
             FROM orders o
             JOIN customers c ON o.customer_id = c.id
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Find an order by ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, c.full_name AS customer_name, u.full_name AS cashier_name
             FROM orders o
             JOIN customers c ON o.customer_id = c.id
             JOIN users u ON o.user_id = u.id
             WHERE o.id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch();
        return $order ?: null;
    }

    /**
     * Create an order with its items in a single transaction.
     * Deducts product stock for each item.
     *
     * @param array $orderData  Keys: customer_id, user_id, total_amount, status
     * @param array $items      Each item: product_id, quantity, unit_price, subtotal
     * @return int  The new order ID
     * @throws \RuntimeException on failure
     */
    public function create(array $orderData, array $items): int
    {
        $this->db->beginTransaction();

        try {
            $orderNumber = $this->generateOrderNumber();

            $stmt = $this->db->prepare(
                'INSERT INTO orders (order_number, customer_id, user_id, total_amount, status)
                 VALUES (:order_number, :customer_id, :user_id, :total_amount, :status)'
            );
            $stmt->execute([
                ':order_number' => $orderNumber,
                ':customer_id'  => $orderData['customer_id'],
                ':user_id'      => $orderData['user_id'],
                ':total_amount' => $orderData['total_amount'],
                ':status'       => $orderData['status'] ?? 'pending',
            ]);
            $orderId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
                 VALUES (:order_id, :product_id, :quantity, :unit_price, :subtotal)'
            );
            $stockStmt = $this->db->prepare(
                'UPDATE products SET quantity_in_stock = quantity_in_stock - :quantity
                 WHERE id = :id AND quantity_in_stock >= :qty_check'
            );

            foreach ($items as $item) {
                $itemStmt->execute([
                    ':order_id'   => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity'   => $item['quantity'],
                    ':unit_price' => $item['unit_price'],
                    ':subtotal'   => $item['subtotal'],
                ]);

                $stockStmt->execute([
                    ':quantity'  => $item['quantity'],
                    ':id'        => $item['product_id'],
                    ':qty_check' => $item['quantity'],
                ]);
                if ($stockStmt->rowCount() === 0) {
                    throw new \RuntimeException('Insufficient stock for product ID ' . $item['product_id']);
                }
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \RuntimeException('Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of an order.
     * If cancelled, restore product stock quantities.
     */
    public function updateStatus(int $id, string $status): bool
    {
        if ($status === 'cancelled') {
            $this->db->beginTransaction();

            try {
                $items = $this->getOrderItems($id);

                $stockStmt = $this->db->prepare(
                    'UPDATE products SET quantity_in_stock = quantity_in_stock + :quantity WHERE id = :id'
                );
                foreach ($items as $item) {
                    $stockStmt->execute([
                        ':quantity' => $item['quantity'],
                        ':id'      => $item['product_id'],
                    ]);
                }

                $stmt = $this->db->prepare('UPDATE orders SET status = :status WHERE id = :id');
                $stmt->execute([':status' => $status, ':id' => $id]);

                $this->db->commit();
                return true;
            } catch (\Exception $e) {
                $this->db->rollBack();
                throw new \RuntimeException('Failed to cancel order: ' . $e->getMessage());
            }
        }

        $stmt = $this->db->prepare('UPDATE orders SET status = :status WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    /**
     * Get orders by customer ID.
     */
    public function getByCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, c.full_name AS customer_name
             FROM orders o
             JOIN customers c ON o.customer_id = c.id
             WHERE o.customer_id = :customer_id
             ORDER BY o.created_at DESC'
        );
        $stmt->execute([':customer_id' => $customerId]);
        return $stmt->fetchAll();
    }

    /**
     * Get orders by status.
     */
    public function getByStatus(string $status): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, c.full_name AS customer_name
             FROM orders o
             JOIN customers c ON o.customer_id = c.id
             WHERE o.status = :status
             ORDER BY o.created_at DESC'
        );
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    /**
     * Get all orders created today.
     */
    public function getToday(): array
    {
        $stmt = $this->db->query(
            "SELECT o.*, c.full_name AS customer_name
             FROM orders o
             JOIN customers c ON o.customer_id = c.id
             WHERE DATE(o.created_at) = CURDATE()
             ORDER BY o.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Generate a unique order number in format ORD-YYYYMMDD-NNN.
     */
    public function generateOrderNumber(): string
    {
        $date = date('Ymd');
        $prefix = 'ORD-' . $date . '-';

        $stmt = $this->db->prepare(
            "SELECT order_number FROM orders
             WHERE order_number LIKE :prefix
             ORDER BY order_number DESC LIMIT 1"
        );
        $stmt->execute([':prefix' => $prefix . '%']);
        $last = $stmt->fetch();

        if ($last) {
            $seq = substr($last['order_number'], -3);
            $lastSeq = is_numeric($seq) ? (int) $seq : 0;
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad((string) $nextSeq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get an order with its line items (includes product names).
     */
    public function getOrderWithItems(int $id): ?array
    {
        $order = $this->findById($id);
        if (!$order) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT oi.*, p.product_name
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = :order_id'
        );
        $stmt->execute([':order_id' => $id]);
        $order['items'] = $stmt->fetchAll();

        return $order;
    }

    /**
     * Get recent orders.
     */
    public function getRecentOrders(int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, c.full_name AS customer_name
             FROM orders o
             JOIN customers c ON o.customer_id = c.id
             ORDER BY o.created_at DESC
             LIMIT :lim'
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get total sales amount for today (paid orders only).
     */
    public function getTotalSalesToday(): float
    {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(total_amount), 0) AS total
             FROM orders
             WHERE DATE(created_at) = CURDATE() AND status = 'paid'"
        );
        return (float) $stmt->fetch()['total'];
    }

    /**
     * Get monthly revenue for the current year (paid orders).
     * Returns an array keyed by month number (1–12).
     */
    public function getMonthlyRevenue(): array
    {
        $stmt = $this->db->query(
            "SELECT MONTH(created_at) AS month, COALESCE(SUM(total_amount), 0) AS revenue
             FROM orders
             WHERE YEAR(created_at) = YEAR(CURDATE()) AND status = 'paid'
             GROUP BY MONTH(created_at)
             ORDER BY month ASC"
        );
        $rows = $stmt->fetchAll();

        $revenue = array_fill(1, 12, 0.00);
        foreach ($rows as $row) {
            $revenue[(int) $row['month']] = (float) $row['revenue'];
        }

        return $revenue;
    }

    /**
     * Get the total number of orders.
     */
    public function getTotalOrders(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) AS total FROM orders');
        return (int) $stmt->fetch()['total'];
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    /**
     * Get raw order items for an order (used internally for stock restoration).
     */
    private function getOrderItems(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = :order_id');
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }
}
