<?php
session_start();
require_once 'config/db.php';

// Auto-migrate reviews and description
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN description TEXT AFTER category");
} catch (PDOException $e) {}
try {
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT,
        user_id INT,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        review_text TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
} catch (PDOException $e) {}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: products.php");
    exit;
}

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}

// Fetch reviews
$revStmt = $pdo->prepare("
    SELECT r.*, u.first_name, u.last_name 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.product_id = ? 
    ORDER BY r.created_at DESC
");
$revStmt->execute([$id]);
$reviews = $revStmt->fetchAll();

// Calculate average rating
$avgRating = 0;
if (count($reviews) > 0) {
    $sum = 0;
    foreach ($reviews as $rev) $sum += $rev['rating'];
    $avgRating = round($sum / count($reviews), 1);
} else {
    $avgRating = rand(40, 50) / 10; // Dummy visual if no real reviews
}

$imagePath = !empty($product['image_path']) ? $product['image_path'] : "https://picsum.photos/seed/{$product['id']}/800/800";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - KWRmart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="public/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/cart.js" defer></script>
</head>
<body class="antialiased min-h-screen flex flex-col selection:bg-indigo-500/30">

    <!-- Navigation Bar -->
    <?php
    $header_file = file_get_contents(__DIR__ . '/home.php');
    if(preg_match('/<nav class="sticky.*?<\/nav>/s', $header_file, $matches)) {
        echo $matches[0];
    }
    ?>
    
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
        <!-- Breadcrumbs & Back Button -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div class="text-sm text-slate-400">
                <a href="/" class="hover:text-indigo-400">Home</a>
                <span class="mx-2">/</span>
                <a href="/products?category=<?= urlencode($product['category']) ?>" class="hover:text-indigo-400"><?= htmlspecialchars($product['category']) ?></a>
                <span class="mx-2">/</span>
                <span class="text-slate-200"><?= htmlspecialchars($product['name']) ?></span>
            </div>
            <a href="javascript:history.back()" class="inline-flex items-center space-x-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white px-4 py-2 rounded-lg border border-slate-700 transition-colors text-sm font-medium w-fit">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>

        <div class="glass-panel p-8 rounded-3xl border border-slate-700/50 shadow-2xl mb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Image -->
                <div class="bg-slate-900 rounded-2xl p-6 flex items-center justify-center relative group overflow-hidden">
                    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-[500px] object-contain group-hover:scale-105 transition-transform duration-500 drop-shadow-2xl">
                    <?php if(!empty($product['discount_percentage']) && $product['discount_percentage'] > 0): ?>
                        <div class="absolute top-4 left-4 bg-rose-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg shadow-rose-500/30">
                            -<?= $product['discount_percentage'] ?>%
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="flex flex-col">
                    <span class="text-indigo-400 text-sm font-semibold tracking-wider uppercase mb-2"><?= htmlspecialchars($product['category']) ?></span>
                    <h1 class="text-4xl font-extrabold text-white mb-4"><?= htmlspecialchars($product['name']) ?></h1>
                    
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex items-center text-amber-400">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="fa-<?= $i <= round($avgRating) ? 'solid' : 'regular' ?> fa-star text-sm mr-1"></i>
                            <?php endfor; ?>
                            <span class="text-white font-medium ml-2"><?= $avgRating ?></span>
                        </div>
                        <span class="text-slate-500 text-sm">(<?= count($reviews) ?> Reviews)</span>
                    </div>

                    <div class="mb-8 border-t border-b border-slate-700/50 py-6">
                        <div class="flex items-end space-x-4">
                            <span class="text-4xl font-bold text-white flex items-baseline">
                                <span class="text-2xl text-indigo-400 mr-1">$</span><?= number_format($product['selling_price'], 2) ?>
                            </span>
                            <?php if(!empty($product['discount_percentage']) && $product['discount_percentage'] > 0): ?>
                                <?php $original = $product['selling_price'] / (1 - ($product['discount_percentage']/100)); ?>
                                <span class="text-xl text-slate-500 line-through mb-1">$<?= number_format($original, 2) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="prose prose-invert max-w-none text-slate-300 leading-relaxed mb-10">
                        <?php if(!empty($product['description'])): ?>
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        <?php else: ?>
                            <p>Premium quality <?= htmlspecialchars($product['category']) ?> built with the latest technology. This product comes with a standard 1-year warranty and excellent customer support.</p>
                        <?php endif; ?>
                    </div>

                    <div class="mt-auto">
                        <button class="add-to-cart-btn btn-gradient w-full text-white font-bold text-lg px-8 py-4 rounded-xl flex items-center justify-center space-x-3 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-1 transition-all" data-id="<?= $product['id'] ?>" data-price="<?= $product['selling_price'] ?>">
                            <i class="fa-solid fa-cart-plus text-xl"></i>
                            <span>Add to Cart</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-white mb-8 border-b border-slate-700/50 pb-4">Customer Reviews</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Add Review Form -->
                <div class="lg:col-span-1">
                    <div class="bg-slate-800/50 p-6 rounded-2xl border border-slate-700/50">
                        <h3 class="text-lg font-semibold text-white mb-4">Write a Review</h3>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <form id="reviewForm" class="space-y-4">
                                <input type="hidden" id="rev_product_id" value="<?= $product['id'] ?>">
                                <div>
                                    <label class="block text-sm text-slate-400 mb-2">Rating</label>
                                    <div class="flex space-x-2 text-2xl text-slate-600" id="starRating">
                                        <i class="fa-solid fa-star cursor-pointer hover:text-amber-400 transition-colors" data-val="1"></i>
                                        <i class="fa-solid fa-star cursor-pointer hover:text-amber-400 transition-colors" data-val="2"></i>
                                        <i class="fa-solid fa-star cursor-pointer hover:text-amber-400 transition-colors" data-val="3"></i>
                                        <i class="fa-solid fa-star cursor-pointer hover:text-amber-400 transition-colors" data-val="4"></i>
                                        <i class="fa-solid fa-star cursor-pointer hover:text-amber-400 transition-colors" data-val="5"></i>
                                    </div>
                                    <input type="hidden" id="rev_rating" value="0">
                                </div>
                                <div>
                                    <label class="block text-sm text-slate-400 mb-2">Your Review</label>
                                    <textarea id="rev_text" rows="4" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white focus:border-indigo-500 outline-none placeholder-slate-500" placeholder="Share your experience..."></textarea>
                                </div>
                                <div id="rev_msg" class="text-sm hidden"></div>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2 rounded-lg font-medium transition-colors w-full">Submit Review</button>
                            </form>
                        <?php else: ?>
                            <p class="text-slate-400 mb-4">You must be logged in to post a review.</p>
                            <a href="login.php" class="bg-slate-700 hover:bg-slate-600 text-white px-6 py-2 rounded-lg font-medium transition-colors inline-block text-center w-full">Login to Review</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Review List -->
                <div class="lg:col-span-2 space-y-6">
                    <?php if(empty($reviews)): ?>
                        <div class="text-center py-12 bg-slate-800/30 rounded-2xl border border-slate-700/30">
                            <i class="fa-regular fa-comment text-4xl text-slate-600 mb-3"></i>
                            <p class="text-slate-400">No reviews yet. Be the first to review this product!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($reviews as $rev): ?>
                            <div class="bg-slate-800/50 p-6 rounded-2xl border border-slate-700/50">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold">
                                            <?= strtoupper(substr($rev['first_name'], 0, 1) . substr($rev['last_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h4 class="text-white font-medium"><?= htmlspecialchars($rev['first_name'] . ' ' . $rev['last_name']) ?></h4>
                                            <div class="text-amber-400 text-xs mt-0.5">
                                                <?php for($i=1; $i<=5; $i++): ?>
                                                    <i class="fa-<?= $i <= $rev['rating'] ? 'solid' : 'regular' ?> fa-star"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-xs text-slate-500"><?= date('M j, Y', strtotime($rev['created_at'])) ?></span>
                                </div>
                                <p class="text-slate-300 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($rev['review_text'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php
    if(preg_match('/<!-- Footer -->.*?<\/html>/s', $header_file, $matches)) {
        echo $matches[0];
    }
    ?>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Star Rating UI
        const stars = document.querySelectorAll('#starRating i');
        const ratingInput = document.getElementById('rev_rating');
        
        stars.forEach(star => {
            star.addEventListener('click', (e) => {
                const val = parseInt(e.target.getAttribute('data-val'));
                ratingInput.value = val;
                
                stars.forEach(s => {
                    if(parseInt(s.getAttribute('data-val')) <= val) {
                        s.classList.remove('text-slate-600');
                        s.classList.add('text-amber-400');
                    } else {
                        s.classList.remove('text-amber-400');
                        s.classList.add('text-slate-600');
                    }
                });
            });
        });

        // Submit Review
        const form = document.getElementById('reviewForm');
        if(form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = form.querySelector('button');
                const msg = document.getElementById('rev_msg');
                const rating = ratingInput.value;
                const text = document.getElementById('rev_text').value;
                const pid = document.getElementById('rev_product_id').value;

                if(rating == 0) {
                    msg.innerHTML = 'Please select a rating.';
                    msg.className = 'text-sm text-rose-400 block mb-2';
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = 'Submitting...';

                const fd = new FormData();
                fd.append('product_id', pid);
                fd.append('rating', rating);
                fd.append('review_text', text);

                try {
                    const res = await fetch('api/add_review.php', {
                        method: 'POST',
                        body: fd
                    });
                    const data = await res.json();
                    if(data.success) {
                        window.location.reload();
                    } else {
                        msg.innerHTML = data.message;
                        msg.className = 'text-sm text-rose-400 block mb-2';
                    }
                } catch(err) {
                    msg.innerHTML = 'An error occurred.';
                    msg.className = 'text-sm text-rose-400 block mb-2';
                }
                
                btn.disabled = false;
                btn.innerHTML = 'Submit Review';
            });
        }
    });
    </script>
</body>
</html>
