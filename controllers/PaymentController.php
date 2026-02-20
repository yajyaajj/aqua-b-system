<?php
/**
 * Payment Controller
 *
 * Handles listing, creating, and receipt generation for payments.
 */

require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Order.php';

function handlePayments($action)
{
    $paymentModel = new Payment();
    $orderModel   = new Order();

    switch ($action) {
        case 'create':
            $orderId = (int)($_GET['order_id'] ?? 0);
            $order = $orderModel->getOrderWithItems($orderId);
            if (!$order) {
                $_SESSION['flash_message'] = 'Order not found.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=orders');
                exit;
            }

            if ($order['status'] === 'paid') {
                $_SESSION['flash_message'] = 'This order has already been paid.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=orders&action=view&id=' . $orderId);
                exit;
            }

            if ($order['status'] === 'cancelled') {
                $_SESSION['flash_message'] = 'Cannot process payment for a cancelled order.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=orders&action=view&id=' . $orderId);
                exit;
            }

            $pageTitle = 'Record Payment';
            include __DIR__ . '/../views/payments/create.php';
            break;

        case 'store':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=payments');
                exit;
            }

            $orderId       = (int)($_POST['order_id'] ?? 0);
            $amount        = (float)($_POST['amount'] ?? 0);
            $paymentMethod = trim($_POST['payment_method'] ?? 'cash');

            if ($orderId <= 0) {
                $_SESSION['flash_message'] = 'Invalid order.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=payments');
                exit;
            }

            $order = $orderModel->findById($orderId);
            if (!$order) {
                $_SESSION['flash_message'] = 'Order not found.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=payments');
                exit;
            }

            if ($order['status'] !== 'pending') {
                $_SESSION['flash_message'] = 'Payment can only be recorded for pending orders.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=orders&action=view&id=' . $orderId);
                exit;
            }

            if ($amount <= 0) {
                $_SESSION['flash_message'] = 'Payment amount must be greater than zero.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=payments&action=create&order_id=' . $orderId);
                exit;
            }

            $allowedMethods = ['cash', 'gcash', 'bank_transfer'];
            if (!in_array($paymentMethod, $allowedMethods, true)) {
                $paymentMethod = 'cash';
            }

            $paymentId = $paymentModel->create([
                'order_id'       => $orderId,
                'amount'         => $amount,
                'payment_method' => $paymentMethod,
                'payment_date'   => date('Y-m-d H:i:s'),
                'received_by'    => $_SESSION['user_id'],
            ]);

            $orderModel->updateStatus($orderId, 'paid');

            $_SESSION['flash_message'] = 'Payment recorded successfully.';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . BASE_URL . '/index.php?page=payments&action=receipt&id=' . $paymentId);
            exit;

        case 'receipt':
            $id = (int)($_GET['id'] ?? 0);
            $payment = $paymentModel->findById($id);
            if (!$payment) {
                $_SESSION['flash_message'] = 'Payment not found.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=payments');
                exit;
            }

            $order = $orderModel->getOrderWithItems($payment['order_id']);
            $pageTitle = 'Payment Receipt';
            include __DIR__ . '/../views/payments/receipt.php';
            break;

        default:
            $payments = $paymentModel->getAll();
            $pageTitle = 'Payments';
            include __DIR__ . '/../views/payments/index.php';
            break;
    }
}
