<?php
/**
 * Report Controller
 *
 * Generates various reports: daily sales, monthly sales, inventory, low stock.
 * Admin only access.
 */

require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';

function handleReports($action)
{
    $isAdmin = (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1);
    if (!$isAdmin) {
        $_SESSION['flash_message'] = 'Access denied. Admin only.';
        $_SESSION['flash_type'] = 'error';
        header('Location: ' . BASE_URL . '/index.php?page=dashboard');
        exit;
    }

    switch ($action) {
        case 'daily_sales':
            $date = $_GET['date'] ?? date('Y-m-d');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $date = date('Y-m-d');
            }

            $paymentModel = new Payment();
            $dailyTotal = $paymentModel->getDailySales($date);
            $payments   = $paymentModel->getByDateRange($date, $date);

            $pageTitle = 'Daily Sales Report';
            include __DIR__ . '/../views/reports/daily_sales.php';
            break;

        case 'monthly_sales':
            $year  = (int)($_GET['year'] ?? date('Y'));
            $month = (int)($_GET['month'] ?? date('n'));

            if ($year < 2000 || $year > 2100) {
                $year = (int)date('Y');
            }
            if ($month < 1 || $month > 12) {
                $month = (int)date('n');
            }

            $paymentModel = new Payment();
            $monthlyTotal = $paymentModel->getMonthlySales($year, $month);

            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate   = date('Y-m-t', strtotime($startDate));
            $payments  = $paymentModel->getByDateRange($startDate, $endDate);

            $orderModel = new Order();
            $monthlyRevenue = $orderModel->getMonthlyRevenue();

            $pageTitle = 'Monthly Sales Report';
            include __DIR__ . '/../views/reports/monthly_sales.php';
            break;

        case 'inventory':
            $productModel = new Product();
            $products = $productModel->getAll();
            $categories = $productModel->getCategories();

            $pageTitle = 'Inventory Report';
            include __DIR__ . '/../views/reports/inventory.php';
            break;

        case 'low_stock':
            $productModel = new Product();
            $products = $productModel->getLowStock();

            $pageTitle = 'Low Stock Report';
            include __DIR__ . '/../views/reports/low_stock.php';
            break;

        default:
            $pageTitle = 'Reports';
            include __DIR__ . '/../views/reports/index.php';
            break;
    }
}
