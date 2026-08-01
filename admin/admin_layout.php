<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';
// Fetch pending orders for notifications
$notifStmt = $pdo->query("
    SELECT o.id, o.total_amount, o.order_date, u.first_name, u.last_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.status = 'Pending' 
    ORDER BY o.order_date DESC 
    LIMIT 5
");
$pendingNotifications = $notifStmt->fetchAll();

$notifCountStmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'");
$pendingCount = $notifCountStmt->fetchColumn();

// Default values if not set
$pageTitle = $pageTitle ?? "Dashboard";
$activeMenu = $activeMenu ?? "overview";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Admin</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: '#0f172a',
                        darker: '#020617',
                        card: '#1e293b'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass-panel {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
    </style>
</head>
<body class="flex h-screen overflow-hidden selection:bg-indigo-500/30">

    <!-- Sidebar -->
    <aside class="w-64 glass-panel flex-col hidden md:flex z-20 border-r border-slate-700/50 shadow-2xl relative">
        <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/5 to-transparent pointer-events-none"></div>
        <div class="h-20 flex items-center justify-center border-b border-slate-700/50 relative z-10">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-chart-line text-white text-sm"></i>
            </div>
            <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-300">AdminPanel</span>
        </div>
        
        <nav class="flex-grow p-4 space-y-2 overflow-y-auto relative z-10">
            <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-4">Main Menu</p>
            
            <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all <?= $activeMenu === 'overview' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 shadow-inner' : 'text-slate-400 hover:text-white hover:bg-slate-800/80 group' ?>">
                <i class="fa-solid fa-layer-group <?= $activeMenu === 'overview' ? '' : 'group-hover:text-indigo-400 transition-colors' ?>"></i>
                <span class="font-medium">Overview</span>
            </a>
            
            <a href="admin_products.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all <?= $activeMenu === 'products' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 shadow-inner' : 'text-slate-400 hover:text-white hover:bg-slate-800/80 group' ?>">
                <i class="fa-solid fa-box <?= $activeMenu === 'products' ? '' : 'group-hover:text-indigo-400 transition-colors' ?>"></i>
                <span class="font-medium">Products</span>
            </a>
            
            <a href="admin_customers.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all <?= $activeMenu === 'customers' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 shadow-inner' : 'text-slate-400 hover:text-white hover:bg-slate-800/80 group' ?>">
                <i class="fa-solid fa-users <?= $activeMenu === 'customers' ? '' : 'group-hover:text-indigo-400 transition-colors' ?>"></i>
                <span class="font-medium">Customers</span>
            </a>
            
            <a href="admin_orders.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all <?= $activeMenu === 'orders' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 shadow-inner' : 'text-slate-400 hover:text-white hover:bg-slate-800/80 group' ?>">
                <i class="fa-solid fa-cart-shopping <?= $activeMenu === 'orders' ? '' : 'group-hover:text-indigo-400 transition-colors' ?>"></i>
                <span class="font-medium">Orders</span>
            </a>
            
            <a href="admin_reports.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all <?= $activeMenu === 'reports' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 shadow-inner' : 'text-slate-400 hover:text-white hover:bg-slate-800/80 group' ?>">
                <i class="fa-solid fa-chart-pie <?= $activeMenu === 'reports' ? '' : 'group-hover:text-indigo-400 transition-colors' ?>"></i>
                <span class="font-medium">Reports</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-slate-700/50 relative z-10">
            <a href="../index.php" class="flex items-center space-x-3 px-4 py-3 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-xl transition-colors">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span class="font-medium">Exit to Storefront</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <main class="flex-1 flex flex-col relative overflow-y-auto overflow-x-hidden bg-slate-900">
        <!-- Ambient Glow -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600/10 rounded-full mix-blend-screen filter blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-purple-600/10 rounded-full mix-blend-screen filter blur-[100px] pointer-events-none"></div>

        <!-- Header -->
        <header class="h-20 glass-panel sticky top-0 z-30 flex items-center justify-between px-8 border-b border-slate-700/50 shadow-sm backdrop-blur-xl">
            <h1 class="text-2xl font-bold text-white tracking-tight"><?= htmlspecialchars($pageTitle) ?></h1>
            <div class="flex items-center space-x-5">
                <div class="relative group">
                    <button class="relative text-slate-400 hover:text-white transition-colors py-2">
                        <i class="fa-regular fa-bell text-xl"></i>
                        <?php if ($pendingCount > 0): ?>
                            <span class="absolute top-1 right-0 bg-rose-500 text-[9px] font-bold text-white px-1.5 py-0.5 rounded-full border-2 border-slate-900 shadow-lg"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </button>
                    <!-- Notification Dropdown -->
                    <div class="absolute right-0 mt-2 w-80 bg-slate-800 border border-slate-700/50 rounded-2xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 overflow-hidden backdrop-blur-xl">
                        <div class="p-4 border-b border-slate-700/50 bg-slate-800/80">
                            <h3 class="text-white font-semibold flex items-center justify-between">
                                Notifications
                                <?php if ($pendingCount > 0): ?>
                                    <span class="bg-rose-500/20 text-rose-400 text-xs px-2 py-1 rounded-md"><?= $pendingCount ?> New</span>
                                <?php endif; ?>
                            </h3>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            <?php if (empty($pendingNotifications)): ?>
                                <div class="p-6 text-center text-slate-400">
                                    <i class="fa-regular fa-bell-slash text-2xl mb-2 opacity-50"></i>
                                    <p class="text-sm">You're all caught up!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($pendingNotifications as $notif): ?>
                                    <a href="admin_orders.php" class="block p-4 border-b border-slate-700/30 hover:bg-slate-700/50 transition-colors">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-sm font-medium text-white">New Order #<?= $notif['id'] ?></span>
                                            <span class="text-xs text-indigo-400 font-bold">$<?= number_format($notif['total_amount'], 2) ?></span>
                                        </div>
                                        <p class="text-xs text-slate-400 mb-2">By <?= htmlspecialchars($notif['first_name'] . ' ' . $notif['last_name']) ?></p>
                                        <span class="text-[10px] text-slate-500"><i class="fa-regular fa-clock mr-1"></i><?= date('M d, H:i', strtotime($notif['order_date'])) ?></span>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <a href="admin_orders.php" class="block p-3 text-center text-xs font-medium text-indigo-400 hover:text-indigo-300 hover:bg-slate-700/50 transition-colors bg-slate-800/80">
                            View All Orders
                        </a>
                    </div>
                </div>
                <a href="../profile.php" class="flex items-center space-x-3 pl-4 border-l border-slate-700/50 cursor-pointer group">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=6366f1&color=fff" alt="Admin" class="w-9 h-9 rounded-full ring-2 ring-transparent group-hover:ring-indigo-500/50 transition-all">
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium text-white leading-none mb-1">Admin</p>
                        <p class="text-xs text-slate-400 leading-none">Superuser</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-slate-500 text-xs ml-2"></i>
                </a>
            </div>
        </header>

        <!-- Dynamic Page Content -->
        <?= $content ?? '' ?>

    </main>
</body>
</html>
