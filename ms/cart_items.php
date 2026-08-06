<?php
// IMPORTANT: Set $editable BEFORE including this file
// Usage: < ?php $editable = true; include 'cart_items.php'; ? >

$editable = $editable ?? false; // Default to false if not set
$_SESSION['has_unavailable_items'] = false; // Reset the flag
?>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th class="right-align">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($_SESSION['cart'] as $id => $qty): 
                    $stmt = $pdo->prepare("SELECT name, price, is_active FROM {$tab_prefix}_products WHERE id = ?");
                    $stmt->execute([$id]);
                    $product = $stmt->fetch();
                    if ($product):
                        $is_sold_out = !$product['is_active'];
                        if (!$is_sold_out):
                            $subtotal = $product['price'] * $qty;
                            $grand_total += $subtotal;
                        endif;
                ?>
                <tr id="row-<?php echo $id; ?>">
                    <td>
                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                        <?php if ($is_sold_out): ?>
                            <br><span class="sold-out-cart-badge">Sold Out</span>
                            <?php $_SESSION['has_unavailable_items'] = true; // Set the flag if there's an unavailable item ?>
                        <?php else: ?>
                            <br><small>$<?php echo number_format($product['price'], 2); ?> ea</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="qty-controls">
                            <?php if ($editable && !$is_sold_out): ?>
                                <nobr>
                                    <input type="number" value="<?php echo $qty; ?>" min="1" max="99" 
                                        class="qty-input ajax-qty" data-id="<?php echo $id; ?>">
                                    <a href="#" class="btn-remove ajax-remove" data-id="<?php echo $id; ?>">❌</a>
                                </nobr>
                            <?php else: ?>
                                <nobr>
                                    <span><?php echo $qty; ?></span>
                                    <?php if ($editable): ?>
                                        <a href="#" class="btn-remove ajax-remove" data-id="<?php echo $id; ?>">❌</a>
                                    <?php endif; ?>
                                </nobr>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="right-align" id="subtotal-<?php echo $id; ?>">
                        <?php if ($is_sold_out): ?>
                            <span class="sold-out-cart-badge">Unavailable</span>
                        <?php else: ?>
                            $<?php echo number_format($subtotal, 2); ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; endforeach; ?>
            </tbody>
        </table>

        <div class="total-row">Grand Total: $<span id="grand-total-display"><?php echo number_format($grand_total, 2); ?></span></div>