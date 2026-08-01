<?php
session_start();
require_once 'config/db.php';

$cartItems = [];
$subtotal = 0;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!empty($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    
    while ($row = $stmt->fetch()) {
        $id = $row['id'];
        $quantity = $_SESSION['cart'][$id]['quantity'];
        $row['cart_quantity'] = $quantity;
        $row['cart_subtotal'] = $quantity * $row['selling_price'];
        $cartItems[] = $row;
        $subtotal += $row['cart_subtotal'];
    }
}

$tax = $subtotal * 0.10; // 10% tax
$total = $subtotal + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium KWRmart</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="public/js/tailwind-config.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/cart.js" defer></script>
</head>
<body class="antialiased min-h-screen flex flex-col selection:bg-indigo-500/30">

    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 backdrop-blur-md bg-slate-900/80 border-b border-slate-700 w-full transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                
                <!-- Left: Logo -->
                <div class="flex-shrink-0 flex items-center cursor-pointer group" onclick="window.location.href='index.php'">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20 group-hover:shadow-indigo-500/40 transition-all duration-300">
                        <i class="fa-solid fa-store text-white text-lg"></i>
                    </div>
                    <span class="font-bold text-2xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">KWRmart</span>
                </div>
                
                <!-- Center: Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-white border-b-2 border-indigo-500 pb-1 text-sm font-medium hover:text-indigo-400 transition-colors">Home</a>
                    <a href="products.php" class="text-slate-400 border-b-2 border-transparent pb-1 hover:border-indigo-500 hover:text-indigo-400 text-sm font-medium transition-all duration-300">Products</a>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center space-x-4">
                    
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="hidden sm:flex items-center space-x-4">
                            <a href="login.php" class="text-slate-400 hover:text-indigo-400 transition-colors text-sm font-medium">Login</a>
                            <a href="register.php" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-lg shadow-indigo-500/20">Register</a>
                        </div>
                    <?php else: ?>
                        <div class="hidden sm:flex items-center space-x-4 pr-4 border-r border-slate-700/50">
                            <a href="profile.php" class="text-slate-300 text-sm hover:text-indigo-400 transition-colors">Hi, <span class="font-semibold text-white"><?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?></span></a>
                            <a href="logout.php" class="text-slate-400 hover:text-rose-400 transition-colors" title="Logout"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                        <a href="admin/dashboard.php" class="bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 border border-indigo-500/50 rounded-lg px-4 py-2 text-sm font-medium transition-colors flex items-center">
                            <i class="fa-solid fa-chart-line mr-2"></i> Admin Dashboard
                        </a>
                    <?php endif; ?>
                    
                    <a href="cart.php" class="relative group block ml-2">
                        <div class="flex items-center space-x-2 bg-slate-800/80 hover:bg-slate-700 border border-slate-700/50 rounded-full px-4 py-2.5 transition-all group-hover:border-indigo-500/50 shadow-sm relative">
                            <i class="fa-solid fa-cart-shopping text-slate-300 group-hover:text-white transition-colors"></i>
                            <span class="text-sm font-medium text-white hidden sm:block">Cart</span>
                            <span class="absolute -top-1 -right-1 bg-gradient-to-r from-rose-500 to-pink-500 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full shadow-lg shadow-rose-500/30 border border-slate-900">0</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 w-full relative z-10">
        <h1 class="text-4xl font-extrabold text-white mb-8 tracking-tight">Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>
            <div class="text-center py-24 glass-card rounded-3xl w-full">
                <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <i class="fa-solid fa-cart-shopping text-3xl text-slate-500"></i>
                </div>
                <h3 class="text-2xl font-medium text-white">Your cart is empty</h3>
                <p class="text-slate-400 mt-2 mb-8 max-w-md mx-auto">Looks like you haven't added anything to your cart yet.</p>
                <a href="products.php" class="btn-gradient text-white font-medium px-8 py-3.5 rounded-full shadow-lg shadow-indigo-500/25 inline-flex items-center space-x-2">
                    <span>Continue Shopping</span>
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </a>
            </div>
        <?php else: ?>
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items List -->
                <div class="flex-grow space-y-4">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="glass-card rounded-2xl p-4 flex flex-col sm:flex-row items-center gap-6 relative" id="cart-item-<?= $item['id'] ?>">
                        <!-- Image -->
                        <div class="w-24 h-24 sm:w-32 sm:h-32 bg-slate-800 rounded-xl flex-shrink-0 p-2 flex items-center justify-center">
                            <img src="https://picsum.photos/seed/<?= $item['id'] ?>/200/200" alt="<?= htmlspecialchars($item['name']) ?>" class="max-h-full max-w-full object-contain drop-shadow-lg">
                        </div>

                        <!-- Details -->
                        <div class="flex-grow flex flex-col justify-between h-full w-full">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-xs font-medium text-indigo-400 bg-indigo-500/10 px-2 py-1 rounded-md mb-2 inline-block"><?= htmlspecialchars($item['category']) ?></span>
                                    <h3 class="text-lg font-semibold text-white mb-1 leading-snug"><?= htmlspecialchars($item['name']) ?></h3>
                                    <p class="text-sm font-medium text-slate-400">$<?= number_format($item['selling_price'], 2) ?> each</p>
                                </div>
                                <button onclick="removeCartItem(<?= $item['id'] ?>)" class="text-slate-500 hover:text-rose-500 transition-colors p-2 bg-slate-800/50 hover:bg-slate-800 rounded-lg">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                            
                            <div class="flex justify-between items-end mt-4 sm:mt-0">
                                <!-- Quantity Controls -->
                                <div class="flex items-center space-x-3 bg-slate-900 border border-slate-700/50 rounded-xl p-1">
                                    <button onclick="updateQuantity(<?= $item['id'] ?>, -1, <?= $item['selling_price'] ?>)" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-minus text-xs"></i>
                                    </button>
                                    <span class="w-6 text-center text-sm font-medium text-white item-qty-<?= $item['id'] ?>"><?= $item['cart_quantity'] ?></span>
                                    <button onclick="updateQuantity(<?= $item['id'] ?>, 1, <?= $item['selling_price'] ?>)" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                </div>
                                
                                <!-- Item Total -->
                                <div>
                                    <p class="text-lg font-bold text-white flex items-baseline gap-1">
                                        <span class="text-sm text-indigo-400">$</span><span class="item-subtotal-<?= $item['id'] ?>"><?= number_format($item['cart_subtotal'], 2) ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Order Summary -->
                <aside class="w-full lg:w-80 flex-shrink-0">
                    <div class="glass-card rounded-2xl p-6 border border-slate-700/50 shadow-xl sticky top-28">
                        <h3 class="text-xl font-bold text-white mb-6">Order Summary</h3>
                        
                        <div class="space-y-4 text-sm mb-6">
                            <div class="flex justify-between items-center text-slate-300">
                                <span>Subtotal</span>
                                <span class="font-medium text-white">$<span id="summary-subtotal"><?= number_format($subtotal, 2) ?></span></span>
                            </div>
                            <div class="flex justify-between items-center text-slate-300">
                                <span>Estimated Tax (10%)</span>
                                <span class="font-medium text-white">$<span id="summary-tax"><?= number_format($tax, 2) ?></span></span>
                            </div>
                            <div class="flex justify-between items-center text-slate-300">
                                <span>Shipping</span>
                                <span class="font-medium text-emerald-400">Free</span>
                            </div>
                            <div class="border-t border-slate-700/50 pt-4 flex justify-between items-center mt-4">
                                <span class="text-base font-semibold text-white">Total</span>
                                <span class="text-2xl font-bold text-white flex items-baseline gap-1">
                                    <span class="text-lg text-indigo-400">$</span><span id="summary-total"><?= number_format($total, 2) ?></span>
                                </span>
                            </div>
                        </div>
                        
                        <a href="checkout.php" class="w-full btn-gradient text-white font-medium py-3.5 rounded-xl shadow-lg shadow-indigo-500/25 flex items-center justify-center space-x-2 transition-transform hover:-translate-y-0.5">
                            <span>Proceed to Checkout</span>
                            <i class="fa-solid fa-lock text-xs ml-1 opacity-70"></i>
                        </a>
                        
                        <div class="mt-6 pt-4 border-t border-slate-700/50 flex justify-center space-x-3 text-slate-500">
                            <i class="fa-brands fa-cc-visa text-2xl hover:text-white transition-colors"></i>
                            <i class="fa-brands fa-cc-mastercard text-2xl hover:text-white transition-colors"></i>
                            <i class="fa-brands fa-cc-paypal text-2xl hover:text-white transition-colors"></i>
                            <i class="fa-brands fa-cc-apple-pay text-2xl hover:text-white transition-colors"></i>
                        </div>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/60 bg-slate-900/80 backdrop-blur-xl mt-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8">
                <div class="lg:col-span-1">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20">
                            <i class="fa-solid fa-store text-white"></i>
                        </div>
                        <span class="font-bold text-2xl text-white tracking-tight">KWRmart</span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed mb-6">
                        Your premium destination for the latest technology. Experience innovation with our curated selection of top-tier electronics.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-indigo-500 hover:text-white transition-all shadow-sm"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-indigo-500 hover:text-white transition-all shadow-sm"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-indigo-500 hover:text-white transition-all shadow-sm"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-6">Shop Categories</h4>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Smartphones</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Laptops & PC</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Audio & Wearables</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Accessories</span></a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-6">Customer Service</h4>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Track Order</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Returns & Refunds</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>FAQ</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Contact Support</span></a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-6">Newsletter</h4>
                    <p class="text-slate-400 text-sm mb-4">Subscribe for exclusive offers and tech news.</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email..." class="bg-slate-800 border border-slate-700 text-sm text-white rounded-l-lg px-4 py-2.5 w-full outline-none focus:border-indigo-500 transition-colors">
                        <button class="btn-gradient px-4 py-2.5 rounded-r-lg text-white shadow-lg">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 text-sm">&copy; <?= date('Y') ?> Premium KWRmart. All rights reserved.</p>
                <div class="flex space-x-6 text-sm text-slate-500">
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function formatMoney(amount) {
            return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        async function updateQuantity(productId, change, price) {
            const qtyElement = document.querySelector(`.item-qty-${productId}`);
            let currentQty = parseInt(qtyElement.textContent);
            let newQty = currentQty + change;
            
            if (newQty < 1) return; // Must be at least 1, otherwise use remove button

            // Optimistic UI update
            qtyElement.textContent = newQty;
            const subtotalElement = document.querySelector(`.item-subtotal-${productId}`);
            subtotalElement.textContent = formatMoney(newQty * price);
            
            recalculateSummary();

            // Send AJAX request
            try {
                const response = await fetch('api/cart_action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'update',
                        product_id: productId,
                        quantity: newQty
                    })
                });
                
                const data = await response.json();
                if (data.require_login) {
                    alert(data.message);
                    window.location.href = 'login.php';
                    return;
                }
                
                updateCartBadge();
            } catch (error) {
                console.error("Error updating cart:", error);
                qtyElement.textContent = currentQty;
                subtotalElement.textContent = formatMoney(currentQty * price);
                recalculateSummary();
            }
        }

        async function removeCartItem(productId) {
            const itemElement = document.getElementById(`cart-item-${productId}`);
            itemElement.style.opacity = '0.5';
            itemElement.style.pointerEvents = 'none';

            try {
                const response = await fetch('api/cart_action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'remove',
                        product_id: productId
                    })
                });
                
                const data = await response.json();
                if (data.require_login) {
                    alert(data.message);
                    window.location.href = 'login.php';
                    return;
                }
                
                itemElement.remove();
                recalculateSummary();
                updateCartBadge();
                
                const remainingItems = document.querySelectorAll('[id^="cart-item-"]');
                if (remainingItems.length === 0) {
                    location.reload(); 
                }
                
            } catch (error) {
                console.error("Error removing item:", error);
                itemElement.style.opacity = '1';
                itemElement.style.pointerEvents = 'auto';
            }
        }
        
        function recalculateSummary() {
            let newSubtotal = 0;
            const itemSubtotals = document.querySelectorAll('[class^="item-subtotal-"]');
            
            itemSubtotals.forEach(el => {
                newSubtotal += parseFloat(el.textContent.replace(/,/g, ''));
            });
            
            const newTax = newSubtotal * 0.10;
            const newTotal = newSubtotal + newTax;
            
            const subtotalEl = document.getElementById('summary-subtotal');
            const taxEl = document.getElementById('summary-tax');
            const totalEl = document.getElementById('summary-total');
            
            if (subtotalEl) subtotalEl.textContent = formatMoney(newSubtotal);
            if (taxEl) taxEl.textContent = formatMoney(newTax);
            if (totalEl) totalEl.textContent = formatMoney(newTotal);
        }
        
        async function updateCartBadge() {
            try {
                const response = await fetch('api/cart_action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get' })
                });
                const data = await response.json();
                if (data.cart_count !== undefined) {
                    const badge = document.querySelector('.fa-cart-shopping').nextElementSibling.nextElementSibling;
                    if(badge) badge.textContent = data.cart_count;
                }
            } catch (e) {}
        }
    </script>
</body>
</html>
