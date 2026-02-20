<?php
/**
 * Product Controller
 *
 * Handles CRUD operations and stock-in for products.
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/StockMovement.php';

function handleProducts($action)
{
    $productModel = new Product();
    $isAdmin = (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1);

    switch ($action) {
        case 'create':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Access denied. Admin only.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=products');
                exit;
            }
            $categories = $productModel->getCategories();
            $pageTitle = 'Add Product';
            include __DIR__ . '/../views/products/create.php';
            break;

        case 'store':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Access denied. Admin only.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=products');
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=products');
                exit;
            }

            $errors = validateProductInput($_POST);
            if (!empty($errors)) {
                $_SESSION['flash_message'] = implode('<br>', $errors);
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=products&action=create');
                exit;
            }

            $productModel->create([
                'product_name'      => trim($_POST['product_name']),
                'category'          => trim($_POST['category']),
                'quantity_in_stock' => (int)$_POST['quantity_in_stock'],
                'reorder_level'     => (int)$_POST['reorder_level'],
                'cost_price'        => (float)$_POST['cost_price'],
                'selling_price'     => (float)$_POST['selling_price'],
            ]);

            $_SESSION['flash_message'] = 'Product created successfully.';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . BASE_URL . '/index.php?page=products');
            exit;

        case 'edit':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Access denied. Admin only.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=products');
                exit;
            }
            $id = (int)($_GET['id'] ?? 0);
            $product = $productModel->findById($id);
            if (!$product) {
                $_SESSION['flash_message'] = 'Product not found.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=products');
                exit;
            }
            $categories = $productModel->getCategories();
            $pageTitle = 'Edit Product';
            include __DIR__ . '/../views/products/edit.php';
            break;

        case 'update':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Access denied. Admin only.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=products');
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=products');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            $errors = validateProductInput($_POST);
            if (!empty($errors)) {
                $_SESSION['flash_message'] = implode('<br>', $errors);
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=products&action=edit&id=' . $id);
                exit;
            }

            $productModel->update($id, [
                'product_name'      => trim($_POST['product_name']),
                'category'          => trim($_POST['category']),
                'quantity_in_stock' => (int)$_POST['quantity_in_stock'],
                'reorder_level'     => (int)$_POST['reorder_level'],
                'cost_price'        => (float)$_POST['cost_price'],
                'selling_price'     => (float)$_POST['selling_price'],
            ]);

            $_SESSION['flash_message'] = 'Product updated successfully.';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . BASE_URL . '/index.php?page=products');
            exit;

        case 'delete':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Access denied. Admin only.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=products');
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/index.php?page=products');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            try {
                $productModel->delete($id);
                $_SESSION['flash_message'] = 'Product deleted successfully.';
                $_SESSION['flash_type'] = 'success';
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = 'Cannot delete product. It may be referenced by orders.';
                $_SESSION['flash_type'] = 'error';
            }
            header('Location: ' . BASE_URL . '/index.php?page=products');
            exit;

        case 'stock_in':
            $id = (int)($_GET['id'] ?? ($_POST['product_id'] ?? 0));

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productId = (int)($_POST['product_id'] ?? 0);
                $quantity  = (int)($_POST['quantity'] ?? 0);
                $notes     = trim($_POST['notes'] ?? '');

                if ($productId <= 0 || $quantity <= 0) {
                    $_SESSION['flash_message'] = 'Please select a product and enter a valid quantity.';
                    $_SESSION['flash_type'] = 'error';
                    header('Location: ' . BASE_URL . '/index.php?page=products&action=stock_in&id=' . $productId);
                    exit;
                }

                $productModel->updateStock($productId, $quantity, 'add');

                $stockModel = new StockMovement();
                $stockModel->create([
                    'product_id' => $productId,
                    'type'       => 'stock_in',
                    'quantity'   => $quantity,
                    'reference'  => 'STOCK-IN-' . date('Ymd-His'),
                    'notes'      => $notes,
                    'created_by' => $_SESSION['user_id'],
                ]);

                $_SESSION['flash_message'] = 'Stock added successfully.';
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . BASE_URL . '/index.php?page=products');
                exit;
            }

            $product = $productModel->findById($id);
            if (!$product) {
                $_SESSION['flash_message'] = 'Product not found.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . BASE_URL . '/index.php?page=products');
                exit;
            }
            $products = $productModel->getAll();
            $pageTitle = 'Stock In';
            include __DIR__ . '/../views/products/stock_in.php';
            break;

        default:
            $products = $productModel->getAll();
            $pageTitle = 'Products';
            include __DIR__ . '/../views/products/index.php';
            break;
    }
}

/**
 * Validate product input fields.
 */
function validateProductInput(array $data): array
{
    $errors = [];

    if (empty(trim($data['product_name'] ?? ''))) {
        $errors[] = 'Product name is required.';
    }
    if (empty(trim($data['category'] ?? ''))) {
        $errors[] = 'Category is required.';
    }
    if (!isset($data['quantity_in_stock']) || (int)$data['quantity_in_stock'] < 0) {
        $errors[] = 'Quantity in stock must be 0 or greater.';
    }
    if (!isset($data['reorder_level']) || (int)$data['reorder_level'] < 0) {
        $errors[] = 'Reorder level must be 0 or greater.';
    }
    if (!isset($data['cost_price']) || (float)$data['cost_price'] < 0) {
        $errors[] = 'Cost price must be 0 or greater.';
    }
    if (!isset($data['selling_price']) || (float)$data['selling_price'] < 0) {
        $errors[] = 'Selling price must be 0 or greater.';
    }

    return $errors;
}
