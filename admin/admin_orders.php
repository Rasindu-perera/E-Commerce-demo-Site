<?php
session_start();
require_once '../config/db.php';

// Auth check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Fetch orders with customer details
$stmt = $pdo->query("
    SELECT o.id, o.order_date, o.total_amount, o.status, u.first_name, u.last_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.id DESC 
    LIMIT 100
");
$orders = $stmt->fetchAll();

$pageTitle = "Manage Orders";
$activeMenu = "orders";

ob_start();
?>

<div class="p-6 md:p-8 relative z-10 w-full max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-white mb-1">Orders</h2>
            <p class="text-slate-400 text-sm">Track and manage recent customer orders.</p>
        </div>
        <button class="btn-gradient bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium px-5 py-2.5 rounded-lg shadow-lg shadow-indigo-500/25 flex items-center space-x-2 transition-transform hover:-translate-y-0.5">
            <i class="fa-solid fa-file-export text-sm"></i>
            <span>Export Orders</span>
        </button>
    </div>

    <div class="glass-panel rounded-2xl border border-slate-700/50 shadow-xl overflow-hidden">
        <!-- Table Toolbar -->
        <div class="p-4 border-b border-slate-700/50 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-800/30">
            <div class="relative w-full sm:w-64">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" placeholder="Search orders..." class="w-full bg-slate-900 border border-slate-700/50 text-sm text-white rounded-lg pl-9 pr-4 py-2 outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all">
            </div>
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <select class="bg-slate-900 border border-slate-700/50 text-sm text-slate-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500/50 transition-all">
                    <option>All Statuses</option>
                    <option>Completed</option>
                    <option>Pending</option>
                    <option>Cancelled</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-800/50 border-b border-slate-700/50 text-xs uppercase tracking-wider text-slate-400">
                        <th class="py-4 px-6 font-semibold">Order ID</th>
                        <th class="py-4 px-6 font-semibold">Customer</th>
                        <th class="py-4 px-6 font-semibold">Date</th>
                        <th class="py-4 px-6 font-semibold">Total</th>
                        <th class="py-4 px-6 font-semibold">Status</th>
                        <th class="py-4 px-6 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-800/50">
                    <?php if(empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No orders found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-slate-800/30 transition-colors group">
                            <td class="py-3 px-6 text-white font-medium">#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td class="py-3 px-6 text-slate-300">
                                <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?>
                            </td>
                            <td class="py-3 px-6 text-slate-400">
                                <?= date('M j, Y, g:i a', strtotime($order['order_date'])) ?>
                            </td>
                            <td class="py-3 px-6 text-emerald-400 font-bold">
                                $<?= number_format($order['total_amount'], 2) ?>
                            </td>
                            <td class="py-3 px-6">
                                <?php 
                                $statusColor = 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20';
                                if ($order['status'] === 'Completed') $statusColor = 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20';
                                if ($order['status'] === 'Pending') $statusColor = 'text-amber-400 bg-amber-500/10 border-amber-500/20';
                                if ($order['status'] === 'Cancelled') $statusColor = 'text-rose-400 bg-rose-500/10 border-rose-500/20';
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider <?= $statusColor ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-6">
                                <div class="flex items-center justify-center space-x-2">
                                    <button class="w-8 h-8 rounded-lg bg-slate-800/80 text-slate-400 hover:bg-slate-700 hover:text-white transition-colors flex items-center justify-center border border-slate-700/50" title="View Order">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination (Dummy) -->
        <div class="p-4 border-t border-slate-700/50 flex items-center justify-between bg-slate-800/30">
            <p class="text-xs text-slate-400">Showing <span class="font-medium text-white">1</span> to <span class="font-medium text-white"><?= count($orders) ?></span> of <span class="font-medium text-white"><?= count($orders) ?></span> results</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'admin_layout.php';
?>
