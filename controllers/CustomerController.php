<?php
/**
 * Customer Controller
 *
 * Handles CRUD operations and view (with order history) for customers.
 */

require_once __DIR__ . '/../models/Customer.php';

function handleCustomers($action)
{
    $customerModel = new Customer();

    switch ($action) {
        case 'create':
            $pageTitle = 'Add Customer';
            include __DIR__ . '/../views/customers/create.php';
            break;

        case 'store':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=customers');
                exit;
            }

            $errors = validateCustomerInput($_POST);
            if (!empty($errors)) {
                $_SESSION['flash_message'] = implode('<br>', $errors);
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=customers&action=create');
                exit;
            }

            $customerModel->create([
                'full_name'      => trim($_POST['full_name']),
                'contact_number' => trim($_POST['contact_number'] ?? ''),
                'address'        => trim($_POST['address'] ?? ''),
            ]);

            $_SESSION['flash_message'] = 'Customer added successfully.';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . BASE_URL . '/index.php?page=customers');
            exit;

        case 'edit':
            $id = (int)($_GET['id'] ?? 0);
            $customer = $customerModel->findById($id);
            if (!$customer) {
                $_SESSION['flash_message'] = 'Customer not found.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=customers');
                exit;
            }
            $pageTitle = 'Edit Customer';
            include __DIR__ . '/../views/customers/edit.php';
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=customers');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            $errors = validateCustomerInput($_POST);
            if (!empty($errors)) {
                $_SESSION['flash_message'] = implode('<br>', $errors);
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=customers&action=edit&id=' . $id);
                exit;
            }

            $customerModel->update($id, [
                'full_name'      => trim($_POST['full_name']),
                'contact_number' => trim($_POST['contact_number'] ?? ''),
                'address'        => trim($_POST['address'] ?? ''),
            ]);

            $_SESSION['flash_message'] = 'Customer updated successfully.';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . BASE_URL . '/index.php?page=customers');
            exit;

        case 'delete':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=customers');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            try {
                $customerModel->delete($id);
                $_SESSION['flash_message'] = 'Customer deleted successfully.';
                $_SESSION['flash_type'] = 'success';
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = 'Cannot delete customer. They may have existing orders.';
                $_SESSION['flash_type'] = 'error';
            }
            header('Location: ' . BASE_URL . '/index.php?page=customers');
            exit;

        case 'view':
            $id = (int)($_GET['id'] ?? 0);
            $customer = $customerModel->findById($id);
            if (!$customer) {
                $_SESSION['flash_message'] = 'Customer not found.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=customers');
                exit;
            }
            $orderHistory = $customerModel->getOrderHistory($id);
            $pageTitle = 'Customer Details';
            include __DIR__ . '/../views/customers/view.php';
            break;

        default:
            $customers = $customerModel->getAll();
            $pageTitle = 'Customers';
            include __DIR__ . '/../views/customers/index.php';
            break;
    }
}

/**
 * Validate customer input fields.
 */
function validateCustomerInput(array $data): array
{
    $errors = [];

    if (empty(trim($data['full_name'] ?? ''))) {
        $errors[] = 'Full name is required.';
    }

    $contact = trim($data['contact_number'] ?? '');
    if ($contact !== '' && !preg_match('/^[0-9+\-\s()]+$/', $contact)) {
        $errors[] = 'Contact number contains invalid characters.';
    }

    return $errors;
}
