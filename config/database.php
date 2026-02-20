<?php
/**
 * Database Configuration
 * 
 * Singleton PDO connection for the Aqua B Water Refilling Station system.
 * Uses XAMPP default credentials (localhost, root, no password).
 */

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private string $host = 'localhost';
    private string $dbName = 'aqua_b_system';
    private string $username = 'root';
    private string $password = '';

    /**
     * Private constructor — use getInstance() instead.
     */
    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    /** Prevent cloning of the singleton. */
    private function __clone() {}

    /** Prevent unserialization of the singleton. */
    public function __wakeup()
    {
        throw new \RuntimeException('Cannot unserialize singleton');
    }

    /**
     * Get the singleton Database instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the underlying PDO connection.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
