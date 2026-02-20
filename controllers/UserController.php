<?php
/**
 * User Controller
 *
 * Handles CRUD operations for user accounts. Admin only access.
 */

require_once __DIR__ . '/../models/User.php';

const MIN_PASSWORD_LENGTH = 6;

function handleUsers($action)
{
    $isAdmin = (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1);
    if (!$isAdmin) {
        $_SESSION['flash_message'] = 'Access denied. Admin only.';
        $_SESSION['flash_type'] = 'error';
        header('Location: ' . BASE_URL . '/index.php?page=dashboard');
        exit;
    }

    $userModel = new User();

    switch ($action) {
        case 'create':
            $pageTitle = 'Add User';
            include __DIR__ . '/../views/users/create.php';
            break;

        case 'store':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=users');
                exit;
            }

            $errors = validateUserInput($_POST, $userModel);
            if (!empty($errors)) {
                $_SESSION['flash_message'] = implode('<br>', $errors);
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=users&action=create');
                exit;
            }

            $userModel->create([
                'username'  => trim($_POST['username']),
                'password'  => $_POST['password'],
                'full_name' => trim($_POST['full_name']),
                'email'     => trim($_POST['email']),
                'role_id'   => (int)$_POST['role_id'],
                'status'    => $_POST['status'] ?? 'active',
            ]);

            $_SESSION['flash_message'] = 'User created successfully.';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . BASE_URL . '/index.php?page=users');
            exit;

        case 'edit':
            $id = (int)($_GET['id'] ?? 0);
            $user = $userModel->findById($id);
            if (!$user) {
                $_SESSION['flash_message'] = 'User not found.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=users');
                exit;
            }
            $pageTitle = 'Edit User';
            include __DIR__ . '/../views/users/edit.php';
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=users');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            $errors = validateUserInput($_POST, $userModel, $id);
            if (!empty($errors)) {
                $_SESSION['flash_message'] = implode('<br>', $errors);
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=users&action=edit&id=' . $id);
                exit;
            }

            $data = [
                'username'  => trim($_POST['username']),
                'full_name' => trim($_POST['full_name']),
                'email'     => trim($_POST['email']),
                'role_id'   => (int)$_POST['role_id'],
                'status'    => $_POST['status'] ?? 'active',
            ];

            // Only update password if provided
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }

            $userModel->update($id, $data);

            $_SESSION['flash_message'] = 'User updated successfully.';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . BASE_URL . '/index.php?page=users');
            exit;

        case 'delete':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=users');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);

            // Prevent self-deletion
            if ($id === (int)$_SESSION['user_id']) {
                $_SESSION['flash_message'] = 'You cannot delete your own account.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=users');
                exit;
            }

            try {
                $userModel->delete($id);
                $_SESSION['flash_message'] = 'User deleted successfully.';
                $_SESSION['flash_type'] = 'success';
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = 'Cannot delete user. They may have associated records.';
                $_SESSION['flash_type'] = 'error';
            }
            header('Location: ' . BASE_URL . '/index.php?page=users');
            exit;

        default:
            $users = $userModel->getAll();
            $pageTitle = 'Users';
            include __DIR__ . '/../views/users/index.php';
            break;
    }
}

/**
 * Validate user input fields.
 *
 * @param int $editId  Pass the user ID when updating to exclude from uniqueness check
 */
function validateUserInput(array $data, User $userModel, int $editId = 0): array
{
    $errors = [];

    $username = trim($data['username'] ?? '');
    if ($username === '') {
        $errors[] = 'Username is required.';
    } else {
        $existing = $userModel->findByUsername($username);
        if ($existing && (int)$existing['id'] !== $editId) {
            $errors[] = 'Username is already taken.';
        }
    }

    // Password required for new users, optional for updates
    $password = $data['password'] ?? '';
    if ($editId === 0 && strlen($password) < MIN_PASSWORD_LENGTH) {
        $errors[] = 'Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters.';
    } elseif ($editId > 0 && $password !== '' && strlen($password) < MIN_PASSWORD_LENGTH) {
        $errors[] = 'Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters.';
    }

    if (empty(trim($data['full_name'] ?? ''))) {
        $errors[] = 'Full name is required.';
    }

    $email = trim($data['email'] ?? '');
    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    $roleId = (int)($data['role_id'] ?? 0);
    if ($roleId <= 0) {
        $errors[] = 'Please select a valid role.';
    }

    return $errors;
}
