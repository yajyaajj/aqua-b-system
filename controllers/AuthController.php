<?php
/**
 * Auth Controller
 *
 * Handles login and logout actions.
 */

require_once __DIR__ . '/../models/User.php';

function handleAuth($action)
{
    switch ($action) {
        case 'logout':
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;

        case 'login':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=login');
                exit;
            }

            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $password === '') {
                $_SESSION['flash_message'] = 'Please enter both username and password.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=login');
                exit;
            }

            $userModel = new User();
            $user = $userModel->authenticate($username, $password);

            if (!$user) {
                $_SESSION['flash_message'] = 'Invalid username or password.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=login');
                exit;
            }

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role_id']   = $user['role_id'];
            $_SESSION['full_name'] = $user['full_name'];

            $_SESSION['flash_message'] = 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . BASE_URL . '/index.php?page=dashboard');
            exit;

        default:
            // Show login form
            $pageTitle = 'Login';
            include __DIR__ . '/../views/auth/login.php';
            break;
    }
}
