<?php
/**
 * User Model
 *
 * Handles all database operations for the users table.
 */

require_once __DIR__ . '/../config/database.php';

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find a user by username.
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Get all users with their role names.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC');
        return $stmt->fetchAll();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, password, full_name, email, role_id, status)
             VALUES (:username, :password, :full_name, :email, :role_id, :status)'
        );
        $stmt->execute([
            ':username'  => $data['username'],
            ':password'  => password_hash($data['password'], PASSWORD_DEFAULT),
            ':full_name' => $data['full_name'],
            ':email'     => $data['email'],
            ':role_id'   => $data['role_id'],
            ':status'    => $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing user. Only hashes password if provided.
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['username', 'full_name', 'email', 'role_id', 'status'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (!empty($data['password'])) {
            $fields[] = 'password = :password';
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($fields)) {
            return false;
        }

        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete a user by ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Authenticate a user by username and password.
     * Returns the user row on success, null on failure.
     */
    public function authenticate(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);

        if ($user && $user['status'] === 'active' && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    /**
     * Get a role name by role ID.
     */
    public function getRoleName(int $roleId): ?string
    {
        $stmt = $this->db->prepare('SELECT role_name FROM roles WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $roleId]);
        $role = $stmt->fetch();
        return $role ? $role['role_name'] : null;
    }
}
