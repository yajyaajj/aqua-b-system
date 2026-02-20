-- =====================================================
-- Aqua B Water Refilling Station
-- Inventory Tracking System - Database Schema
-- =====================================================

DROP DATABASE IF EXISTS aqua_b_system;
CREATE DATABASE aqua_b_system
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE aqua_b_system;

-- -----------------------------------------------------
-- Table: roles
-- -----------------------------------------------------
CREATE TABLE roles (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    role_name   VARCHAR(50)     NOT NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_roles_role_name (role_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: users
-- -----------------------------------------------------
CREATE TABLE users (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    username    VARCHAR(50)     NOT NULL,
    password    VARCHAR(255)    NOT NULL,
    full_name   VARCHAR(100)    NOT NULL,
    email       VARCHAR(100)    NOT NULL,
    role_id     INT UNSIGNED    NOT NULL,
    status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_users_username (username),
    UNIQUE KEY uk_users_email (email),
    INDEX idx_users_role_id (role_id),
    INDEX idx_users_status (status),
    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES roles (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: products
-- -----------------------------------------------------
CREATE TABLE products (
    id                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    product_name      VARCHAR(100)    NOT NULL,
    category          VARCHAR(50)     NOT NULL,
    quantity_in_stock INT             NOT NULL DEFAULT 0,
    reorder_level     INT             NOT NULL DEFAULT 0,
    cost_price        DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    selling_price     DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    created_at        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_products_category (category),
    INDEX idx_products_product_name (product_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: customers
-- -----------------------------------------------------
CREATE TABLE customers (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    full_name       VARCHAR(100)    NOT NULL,
    contact_number  VARCHAR(20)     NULL,
    address         TEXT            NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_customers_full_name (full_name),
    INDEX idx_customers_contact_number (contact_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: orders
-- -----------------------------------------------------
CREATE TABLE orders (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    order_number    VARCHAR(30)     NOT NULL,
    customer_id     INT UNSIGNED    NOT NULL,
    user_id         INT UNSIGNED    NOT NULL,
    total_amount    DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    status          ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_orders_order_number (order_number),
    INDEX idx_orders_customer_id (customer_id),
    INDEX idx_orders_user_id (user_id),
    INDEX idx_orders_status (status),
    INDEX idx_orders_created_at (created_at),
    CONSTRAINT fk_orders_customer
        FOREIGN KEY (customer_id) REFERENCES customers (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: order_items
-- -----------------------------------------------------
CREATE TABLE order_items (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    order_id    INT UNSIGNED    NOT NULL,
    product_id  INT UNSIGNED    NOT NULL,
    quantity    INT             NOT NULL,
    unit_price  DECIMAL(10,2)   NOT NULL,
    subtotal    DECIMAL(10,2)   NOT NULL,
    PRIMARY KEY (id),
    INDEX idx_order_items_order_id (order_id),
    INDEX idx_order_items_product_id (product_id),
    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id) REFERENCES orders (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_order_items_product
        FOREIGN KEY (product_id) REFERENCES products (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: payments
-- -----------------------------------------------------
CREATE TABLE payments (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    order_id        INT UNSIGNED    NOT NULL,
    amount          DECIMAL(10,2)   NOT NULL,
    payment_method  ENUM('cash','gcash','bank_transfer') NOT NULL DEFAULT 'cash',
    payment_date    DATETIME        NOT NULL,
    received_by     INT UNSIGNED    NOT NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_payments_order_id (order_id),
    INDEX idx_payments_received_by (received_by),
    INDEX idx_payments_payment_date (payment_date),
    INDEX idx_payments_payment_method (payment_method),
    CONSTRAINT fk_payments_order
        FOREIGN KEY (order_id) REFERENCES orders (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_payments_received_by
        FOREIGN KEY (received_by) REFERENCES users (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: stock_movements
-- -----------------------------------------------------
CREATE TABLE stock_movements (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    product_id  INT UNSIGNED    NOT NULL,
    type        ENUM('stock_in','stock_out') NOT NULL,
    quantity    INT             NOT NULL,
    reference   VARCHAR(100)    NULL,
    notes       TEXT            NULL,
    created_by  INT UNSIGNED    NOT NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_stock_movements_product_id (product_id),
    INDEX idx_stock_movements_type (type),
    INDEX idx_stock_movements_created_by (created_by),
    INDEX idx_stock_movements_created_at (created_at),
    CONSTRAINT fk_stock_movements_product
        FOREIGN KEY (product_id) REFERENCES products (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_stock_movements_created_by
        FOREIGN KEY (created_by) REFERENCES users (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Seed Data
-- =====================================================

-- Roles
INSERT INTO roles (id, role_name) VALUES
    (1, 'Admin'),
    (2, 'Staff');

-- Users (password: 'password' hashed with bcrypt)
INSERT INTO users (id, username, password, full_name, email, role_id, status) VALUES
    (1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@aquab.com', 1, 'active');

-- Products (water refilling station items)
INSERT INTO products (id, product_name, category, quantity_in_stock, reorder_level, cost_price, selling_price) VALUES
    (1, 'Round Slim Gallon (5 gallons)',  'Water Container', 150, 30, 15.00, 25.00),
    (2, 'Round Regular Gallon (5 gallons)', 'Water Container', 200, 50, 15.00, 25.00),
    (3, '1-Liter Bottle',                 'Bottled Water',   500, 100, 3.00, 10.00),
    (4, '500mL Bottle',                   'Bottled Water',   600, 150, 2.00,  8.00),
    (5, 'Mineral Water Gallon (5 gallons)', 'Water Container', 100, 20, 20.00, 35.00);

-- Customers
INSERT INTO customers (id, full_name, contact_number, address) VALUES
    (1, 'Juan Dela Cruz',   '09171234567', '123 Rizal St, Barangay 1, Manila'),
    (2, 'Maria Santos',     '09289876543', '456 Mabini Ave, Barangay 2, Manila'),
    (3, 'Pedro Reyes',      '09351112233', '789 Bonifacio Rd, Barangay 3, Manila');
