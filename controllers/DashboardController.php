<?php
/**
 * Dashboard Controller
 *
 * Gathers summary statistics and displays the dashboard.
 */

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';

function handleDashboard($action)
{
    $orderModel   = new Order();
    $productModel = new Product();

    $totalSalesToday = $orderModel->getTotalSalesToday();
    $monthlyRevenue  = $orderModel->getMonthlyRevenue();
    $totalOrders     = $orderModel->getTotalOrders();
    $lowStockProducts = $productModel->getLowStock();
    $recentOrders    = $orderModel->getRecentOrders(10);

    $currentMonth = (int) date('n');
    $currentMonthRevenue = $monthlyRevenue[$currentMonth] ?? 0.00;

    $pageTitle = 'Dashboard';
    include __DIR__ . '/../views/dashboard/index.php';
}
