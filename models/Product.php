<?php
/**
 * Product Model
 *
 * Handles all database operations for the products table.
 */

require_once __DIR__ . '/../config/database.php';

class Product
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all products.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM products ORDER BY product_name ASC');
        return $stmt->fetchAll();
    }

    /**
     * Find a product by ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    /**
     * Create a new product.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products (product_name, category, quantity_in_stock, reorder_level, cost_price, selling_price)
             VALUES (:product_name, :category, :quantity_in_stock, :reorder_level, :cost_price, :selling_price)'
        );
        $stmt->execute([
            ':product_name'      => $data['product_name'],
            ':category'          => $data['category'],
            ':quantity_in_stock' => $data['quantity_in_stock'] ?? 0,
            ':reorder_level'     => $data['reorder_level'] ?? 0,
            ':cost_price'        => $data['cost_price'] ?? 0.00,
            ':selling_price'     => $data['selling_price'] ?? 0.00,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing product.
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowed = ['product_name', 'category', 'quantity_in_stock', 'reorder_level', 'cost_price', 'selling_price'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = 'UPDATE products SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete a product by ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get products whose stock is at or below the reorder level.
     */
    public function getLowStock(): array
    {
        $stmt = $this->db->query('SELECT * FROM products WHERE quantity_in_stock <= reorder_level ORDER BY quantity_in_stock ASC');
        return $stmt->fetchAll();
    }

    /**
     * Update stock quantity (add or subtract).
     *
     * @param string $type 'subtract' to decrease stock, 'add' to increase stock
     */
    public function updateStock(int $id, int $quantity, string $type = 'subtract'): bool
    {
        if ($type === 'subtract') {
            $stmt = $this->db->prepare('UPDATE products SET quantity_in_stock = quantity_in_stock - :quantity WHERE id = :id');
        } else {
            $stmt = $this->db->prepare('UPDATE products SET quantity_in_stock = quantity_in_stock + :quantity WHERE id = :id');
        }
        return $stmt->execute([':quantity' => $quantity, ':id' => $id]);
    }

    /**
     * Search products by name or category.
     */
    public function search(string $term): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM products WHERE product_name LIKE :term OR category LIKE :term ORDER BY product_name ASC'
        );
        $stmt->execute([':term' => '%' . $term . '%']);
        return $stmt->fetchAll();
    }

    /**
     * Get products by category.
     */
    public function getByCategory(string $category): array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE category = :category ORDER BY product_name ASC');
        $stmt->execute([':category' => $category]);
        return $stmt->fetchAll();
    }

    /**
     * Get all distinct product categories.
     */
    public function getCategories(): array
    {
        $stmt = $this->db->query('SELECT DISTINCT category FROM products ORDER BY category ASC');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
