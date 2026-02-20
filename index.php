<?php
/**
 * Main Entry Point / Front Controller
 *
 * Routes requests to the appropriate controller based on
 * the ?page= and ?action= query-string parameters.
 */

// ----- Bootstrap -----
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

session_start();

// ----- Determine requested page and action -----
$page   = $_GET['page']   ?? '';
$action = $_GET['action'] ?? 'index';

// Allowed pages that map to controller files
$allowedPages = [
    'login',
    'logout',
    'dashboard',
    'products',
    'customers',
    'orders',
    'payments',
    'reports',
    'users',
];

// ----- Authentication check -----
$isLoggedIn = isset($_SESSION['user_id']);

// Pages accessible without authentication
$publicPages = ['login'];

if (!$isLoggedIn && !in_array($page, $publicPages, true)) {
    // Redirect unauthenticated users to the login page
    header('Location: ' . BASE_URL . '/index.php?page=login');
    exit;
}

// Redirect already-authenticated users away from login
if ($isLoggedIn && $page === 'login') {
    header('Location: ' . BASE_URL . '/index.php?page=dashboard');
    exit;
}

// Default page for authenticated users
if ($isLoggedIn && ($page === '' || !in_array($page, $allowedPages, true))) {
    header('Location: ' . BASE_URL . '/index.php?page=dashboard');
    exit;
}

// Default page for unauthenticated users hitting an invalid page
if (!$isLoggedIn && !in_array($page, $allowedPages, true)) {
    header('Location: ' . BASE_URL . '/index.php?page=login');
    exit;
}

// ----- Route to controller -----
$controllerFile = __DIR__ . '/controllers/' . $page . '_controller.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
} else {
    http_response_code(404);
    echo '<h1>404 — Page Not Found</h1>';
    echo '<p>The requested page could not be found.</p>';
}
