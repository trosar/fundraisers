<?php
require_once 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin.php");
    exit;
}

$order = null;
$orderItems = [];

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // UPDATE ORDER
    if (isset($_POST['update_order'])) {
        $orderId = $_POST['orig_order_id'];
        try {
            $pdo->beginTransaction();

            // Update order-level fields
            $stmt = $pdo->prepare("UPDATE {$tab_prefix}_orders 
                                   SET customer_name = ?, email = ?, scout_name = ?, payment_mode = ?, 
                                       address = ?, comments = ?, status = ?
                                   WHERE id = ?");
            $stmt->execute([
                $_POST['customer_name'], $_POST['email'], $_POST['scout_name'], $_POST['payment_mode'],
                $_POST['address'], $_POST['comments'], $_POST['status'], $orderId
            ]);

            // Delete existing items and re-insert
            $pdo->prepare("DELETE FROM {$tab_prefix}_order_items WHERE order_id = ?")->execute([$orderId]);

            $productNames = $_POST['item_product_name'] ?? [];
            $quantities = $_POST['item_quantity'] ?? [];
            $prices = $_POST['item_price'] ?? [];
            $newTotal = 0;

            $itemStmt = $pdo->prepare("INSERT INTO {$tab_prefix}_order_items (order_id, product_name, quantity, price_per_item, subtotal) VALUES (?, ?, ?, ?, ?)");
            for ($i = 0; $i < count($productNames); $i++) {
                if (!empty($productNames[$i]) && !empty($quantities[$i])) {
                    $qty = (int)$quantities[$i];
                    $price = (float)($prices[$i] ?? 0);
                    $subtotal = $qty * $price;
                    $itemStmt->execute([$orderId, $productNames[$i], $qty, $price, $subtotal]);
                    $newTotal += $subtotal;
                }
            }

            // Update total amount
            $pdo->prepare("UPDATE {$tab_prefix}_orders SET total_amount = ? WHERE id = ?")
                ->execute([$newTotal, $orderId]);

            $pdo->commit();
            $_SESSION['success_msg'] = "Order #{$orderId} updated successfully.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['success_msg'] = "Failed to update order: " . $e->getMessage();
        }
        header("Location: orders.php");
        exit;
    }

    // DELETE ORDER
    if (isset($_POST['delete_order'])) {
        $orderId = $_POST['order_id'];
        // Log the record before deletion
        $fetchStmt = $pdo->prepare("SELECT * FROM {$tab_prefix}_orders WHERE id = ? LIMIT 1");
        $fetchStmt->execute([$orderId]);
        $record = $fetchStmt->fetch();
        if ($record) {
            $logStmt = $pdo->prepare("INSERT INTO {$tab_prefix}_delete_log (log_timestamp, page, log_message) VALUES (CURRENT_TIMESTAMP, 'orders.php', ?)");
            $logStmt->execute([generateInsertSql("{$tab_prefix}_orders", $record, $pdo)]);
        }
        // Also log order items
        $itemFetchStmt = $pdo->prepare("SELECT * FROM {$tab_prefix}_order_items WHERE order_id = ?");
        $itemFetchStmt->execute([$orderId]);
        while ($itemRecord = $itemFetchStmt->fetch()) {
            $logStmt = $pdo->prepare("INSERT INTO {$tab_prefix}_delete_log (log_timestamp, page, log_message) VALUES (CURRENT_TIMESTAMP, 'orders.php', ?)");
            $logStmt->execute([generateInsertSql("{$tab_prefix}_order_items", $itemRecord, $pdo)]);
        }

        // Delete order (CASCADE will remove order_items)
        $stmt = $pdo->prepare("DELETE FROM {$tab_prefix}_orders WHERE id = ? LIMIT 1");
        $stmt->execute([$orderId]);
        $_SESSION['success_msg'] = "Order #{$orderId} deleted.";
        header("Location: orders.php");
        exit;
    }
}

// Fetch editing order data via GET
$editOrderId = $_GET['edit'] ?? null;
if ($editOrderId) {
    $stmt = $pdo->prepare("SELECT * FROM {$tab_prefix}_orders WHERE id = ?");
    $stmt->execute([$editOrderId]);
    $order = $stmt->fetch();
    if ($order) {
        $itemStmt = $pdo->prepare("SELECT * FROM {$tab_prefix}_order_items WHERE order_id = ?");
        $itemStmt->execute([$editOrderId]);
        $orderItems = $itemStmt->fetchAll();
    }
}

// Fetch all orders for the list
$orders = $pdo->query("SELECT * FROM {$tab_prefix}_orders ORDER BY order_date DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<?php $page_title = 'Manage Orders'; include 'header-html.php'; ?>

<div class="main-container">
    <?php $nav_no_print = true; include 'menu.php'; ?>

    <?php if (isset($_SESSION['success_msg'])): ?>
        <div class="success-message no-print">
            ✅ <?php echo htmlspecialchars($_SESSION['success_msg']); unset($_SESSION['success_msg']); ?>
        </div>
    <?php endif; ?>

    <?php if ($order): ?>
        <!-- Edit Order Form -->
        <div class="order-card no-print">
            <h2 class="headings">Edit Order #<?php echo $order['id']; ?></h2>
            <form id="edit_order_form" method="POST">
                <input type="hidden" name="orig_order_id" value="<?php echo $order['id']; ?>">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" value="<?php echo htmlspecialchars($order['customer_name']); ?>" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($order['email']); ?>" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Scout Name</label>
                        <input type="text" name="scout_name" value="<?php echo htmlspecialchars($order['scout_name']); ?>">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Payment Mode</label>
                        <select name="payment_mode">
                            <option value="Venmo" <?php echo $order['payment_mode'] === 'Venmo' ? 'selected' : ''; ?>>Venmo</option>
                            <option value="Cash" <?php echo $order['payment_mode'] === 'Cash' ? 'selected' : ''; ?>>Cash</option>
                            <option value="Check" <?php echo $order['payment_mode'] === 'Check' ? 'selected' : ''; ?>>Check</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Status</label>
                        <select name="status">
                            <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Paid" <?php echo $order['status'] === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Address</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($order['address'] ?? ''); ?>">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Comments</label>
                        <input type="text" name="comments" value="<?php echo htmlspecialchars($order['comments'] ?? ''); ?>">
                    </div>
                </div>

                <h3 style="margin-top: 25px; color: var(--accent-color);">Order Items</h3>
                <div id="items_container" style="overflow-x: auto;">
                    <table class="item-table" id="items_table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th class="right-align">Quantity</th>
                                <th class="right-align">Price Per Item</th>
                                <th class="right-align">Subtotal</th>
                                <th class="right-align">Action</th>
                            </tr>
                        </thead>
                        <tbody id="items_tbody">
                            <?php foreach ($orderItems as $item): ?>
                                <tr class="item-row">
                                    <td><input type="text" name="item_product_name[]" value="<?php echo htmlspecialchars($item['product_name']); ?>" required style="width: 100%;"></td>
                                    <td><input type="number" name="item_quantity[]" value="<?php echo $item['quantity']; ?>" min="1" required class="item-qty" style="width: 70px;" oninput="updateSubtotal(this)"></td>
                                    <td><input type="number" step="0.01" name="item_price[]" value="<?php echo $item['price_per_item']; ?>" min="0" required class="item-price" style="width: 90px;" oninput="updateSubtotal(this)"></td>
                                    <td class="right-align item-subtotal">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                    <td class="right-align"><button type="button" class="btn-remove" onclick="this.closest('tr').remove(); updateOrderTotal();" style="border:none; background:none; cursor:pointer;">Remove</button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="text-align: right; margin-top: 10px;">
                    <button type="button" class="btn btn-back" onclick="addItemRow()">+ Add Item</button>
                </div>

                <div class="total-row" style="margin-top: 15px;">
                    Total: $<span id="order_total"><?php echo number_format($order['total_amount'], 2); ?></span>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <a href="orders.php" class="btn btn-back">Cancel</a>
                    <button type="submit" name="update_order" class="btn btn-confirm">Save Changes</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Orders List -->
    <div class="order-card">
        <h2 class="headings">All Orders</h2>
        <div style="overflow-x: auto;">
            <table class="stack-mobile">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Scout</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th class="right-align">Total</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $row): ?>
                        <tr>
                            <td data-label="Date"><strong><?php echo date('M j, Y', strtotime($row['order_date'])); ?></strong></td>
                            <td data-label="Order #"><?php echo $row['id']; ?></td>
                            <td data-label="Customer"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td data-label="Scout"><?php echo htmlspecialchars($row['scout_name']); ?></td>
                            <td data-label="Payment"><?php echo htmlspecialchars($row['payment_mode']); ?></td>
                            <td data-label="Status">
                                <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                    <?php echo strtoupper($row['status']); ?>
                                </span>
                            </td>
                            <td data-label="Total" class="right-align">$<?php echo number_format($row['total_amount'], 2); ?></td>
                            <td data-label="Action" style="text-align: right; white-space: nowrap;">
                                <a href="orders.php?edit=<?php echo $row['id']; ?>" class="btn-edit" style="text-decoration:none; font-weight:bold; margin-right:10px;">Edit</a>

                                <form method="POST" onsubmit="return confirm('Delete Order #<?php echo $row['id']; ?> and all its items?');" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_order" class="btn-remove" style="border:none; background:none; cursor:pointer;">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Calculate subtotal for a row
function updateSubtotal(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const subtotal = qty * price;
    row.querySelector('.item-subtotal').textContent = '$' + subtotal.toFixed(2);
    updateOrderTotal();
}

// Recalculate the full order total
function updateOrderTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        total += qty * price;
    });
    document.getElementById('order_total').textContent = total.toFixed(2);
}

// Add a new item row
function addItemRow() {
    const tbody = document.getElementById('items_tbody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td><input type="text" name="item_product_name[]" placeholder="Product name" required style="width: 100%;"></td>
        <td><input type="number" name="item_quantity[]" value="1" min="1" required class="item-qty" style="width: 70px;" oninput="updateSubtotal(this)"></td>
        <td><input type="number" step="0.01" name="item_price[]" value="0.00" min="0" required class="item-price" style="width: 90px;" oninput="updateSubtotal(this)"></td>
        <td class="right-align item-subtotal">$0.00</td>
        <td class="right-align"><button type="button" class="btn-remove" onclick="this.closest('tr').remove(); updateOrderTotal();" style="border:none; background:none; cursor:pointer;">Remove</button></td>
    `;
    tbody.appendChild(row);
    updateOrderTotal();
}
</script>

</body>
</html>