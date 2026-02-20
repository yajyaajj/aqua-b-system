<?php
/**
 * Order Controller
 *
 * Handles listing, creating, viewing, and cancelling orders.
 */

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Product.php';

function handleOrders($action)
{
    $orderModel    = new Order();
    $isAdmin = (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1);

    switch ($action) {
        case 'create':
            $customerModel = new Customer();
            $productModel  = new Product();
            $customers = $customerModel->getAll();
            $products  = $productModel->getAll();
            $pageTitle = 'Create Order';
            include __DIR__ . '/../views/orders/create.php';
            break;

        case 'store':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=orders');
                exit;
            }

            $customerId = (int)($_POST['customer_id'] ?? 0);
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];

            if ($customerId <= 0) {
                $_SESSION['flash_message'] = 'Please select a customer.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=orders&action=create');
                exit;
            }

            if (empty($productIds) || !is_array($productIds)) {
                $_SESSION['flash_message'] = 'Please add at least one product to the order.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=orders&action=create');
                exit;
            }

            $productModel = new Product();
            $items = [];
            $totalAmount = 0;
            $errors = [];

            foreach ($productIds as $index => $pid) {
                $pid = (int)$pid;
                $qty = (int)($quantities[$index] ?? 0);

                if ($pid <= 0 || $qty <= 0) {
                    continue;
                }

                $product = $productModel->findById($pid);
                if (!$product) {
                    $errors[] = 'Product ID ' . $pid . ' not found.';
                    continue;
                }

                if ($product['quantity_in_stock'] < $qty) {
                    $errors[] = htmlspecialchars($product['product_name']) . ' has insufficient stock (available: ' . $product['quantity_in_stock'] . ').';
                    continue;
                }

                $subtotal = $qty * (float)$product['selling_price'];
                $items[] = [
                    'product_id' => $pid,
                    'quantity'   => $qty,
                    'unit_price' => (float)$product['selling_price'],
                    'subtotal'   => $subtotal,
                ];
                $totalAmount += $subtotal;
            }

            if (!empty($errors)) {
                $_SESSION['flash_message'] = implode('<br>', $errors);
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=orders&action=create');
                exit;
            }

            if (empty($items)) {
                $_SESSION['flash_message'] = 'No valid items in the order.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=orders&action=create');
                exit;
            }

            try {
                $orderId = $orderModel->create(
                    [
                        'customer_id'  => $customerId,
                        'user_id'      => $_SESSION['user_id'],
                        'total_amount' => $totalAmount,
                        'status'       => 'pending',
                    ],
                    $items
                );

                $_SESSION['flash_message'] = 'Order created successfully.';
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . BASE_URL . '/index.php?page=orders&action=view&id=' . $orderId);
                exit;
            } catch (\RuntimeException $e) {
                $_SESSION['flash_message'] = 'Failed to create order: ' . htmlspecialchars($e->getMessage());
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=orders&action=create');
                exit;
            }

        case 'view':
            $id = (int)($_GET['id'] ?? 0);
            $order = $orderModel->getOrderWithItems($id);
            if (!$order) {
                $_SESSION['flash_message'] = 'Order not found.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=orders');
                exit;
            }
            $pageTitle = 'Order Details';
            include __DIR__ . '/../views/orders/view.php';
            break;

        case 'cancel':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Access denied. Admin only.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=orders');
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=orders');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            try {
                $orderModel->updateStatus($id, 'cancelled');
                $_SESSION['flash_message'] = 'Order cancelled successfully.';
                $_SESSION['flash_type'] = 'success';
            } catch (\RuntimeException $e) {
                $_SESSION['flash_message'] = 'Failed to cancel order: ' . htmlspecialchars($e->getMessage());
                $_SESSION['flash_type'] = 'error';
            }
            header('Location: ' . BASE_URL . '/index.php?page=orders');
            exit;

        default:
            $orders = $orderModel->getAll();
            $pageTitle = 'Orders';
            include __DIR__ . '/../views/orders/index.php';
            break;
    }
}
