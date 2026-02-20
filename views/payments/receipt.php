<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo (int) $payment['id']; ?> - Aqua B Water Refilling Station</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #333; padding: 20px; max-width: 400px; margin: 0 auto; }
        .receipt-header { text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 15px; margin-bottom: 15px; }
        .receipt-header h1 { font-size: 18px; margin-bottom: 5px; }
        .receipt-header p { font-size: 12px; color: #666; margin: 2px 0; }
        .receipt-title { text-align: center; font-size: 16px; font-weight: bold; margin: 10px 0; text-transform: uppercase; letter-spacing: 1px; }
        .receipt-meta { font-size: 13px; margin-bottom: 15px; }
        .receipt-meta p { margin: 3px 0; }
        .items-table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 13px; }
        .items-table th, .items-table td { padding: 6px 4px; text-align: left; }
        .items-table th { border-bottom: 1px solid #333; font-weight: bold; }
        .items-table td { border-bottom: 1px dashed #ddd; }
        .items-table .text-right { text-align: right; }
        .total-row { border-top: 2px solid #333; font-weight: bold; font-size: 15px; }
        .total-row td { padding-top: 8px; }
        .payment-info { margin: 15px 0; padding: 10px 0; border-top: 2px dashed #ccc; font-size: 13px; }
        .payment-info p { margin: 3px 0; }
        .thank-you { text-align: center; margin: 20px 0; padding-top: 15px; border-top: 2px dashed #ccc; font-size: 14px; font-style: italic; color: #555; }
        .print-btn { display: block; margin: 20px auto; padding: 10px 30px; background: #0d6efd; color: #fff; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; }
        .print-btn:hover { background: #0b5ed7; }
        @media print {
            .print-btn { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <h1>Aqua B Water Refilling Station</h1>
        <p>123 Main Street, Barangay Centro</p>
        <p>Contact: (02) 123-4567</p>
    </div>

    <div class="receipt-title">Official Receipt</div>

    <div class="receipt-meta">
        <p><strong>Receipt #:</strong> <?php echo (int) $payment['id']; ?></p>
        <p><strong>Date:</strong> <?php echo date('M j, Y g:i A', strtotime($payment['created_at'])); ?></p>
        <p><strong>Order #:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name'] ?? $order['full_name'] ?? ''); ?></p>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orderItems)): ?>
                <?php foreach ($orderItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td class="text-right"><?php echo (int) $item['quantity']; ?></td>
                    <td class="text-right">₱<?php echo number_format((float) $item['unit_price'], 2); ?></td>
                    <td class="text-right">₱<?php echo number_format((float) $item['unit_price'] * (int) $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr class="total-row">
                <td colspan="3" class="text-right">Total:</td>
                <td class="text-right">₱<?php echo number_format((float) $order['total_amount'], 2); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="payment-info">
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment['payment_method']); ?></p>
        <p><strong>Amount Paid:</strong> ₱<?php echo number_format((float) $payment['amount'], 2); ?></p>
    </div>

    <div class="thank-you">
        Thank you for your purchase!
    </div>

    <button class="print-btn" onclick="window.print()">
        🖨️ Print Receipt
    </button>
</body>
</html>
