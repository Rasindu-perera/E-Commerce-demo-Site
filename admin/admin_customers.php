<?php
session_start();
require_once '../config/db.php';

// Auth check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Fetch customers (all users who are not admin - or simply all users for now)
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$customers = $stmt->fetchAll();

$pageTitle = "Manage Customers";
$activeMenu = "customers";

ob_start();
?>

<div class="p-6 md:p-8 relative z-10 w-full max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-white mb-1">Customers</h2>
            <p class="text-slate-400 text-sm">View and manage all registered customers.</p>
        </div>
        <button class="btn-gradient bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium px-5 py-2.5 rounded-lg shadow-lg shadow-indigo-500/25 flex items-center space-x-2 transition-transform hover:-translate-y-0.5">
            <i class="fa-solid fa-download text-sm"></i>
            <span>Export CSV</span>
        </button>
    </div>

    <div class="glass-panel rounded-2xl border border-slate-700/50 shadow-xl overflow-hidden">
        <!-- Table Toolbar -->
        <div class="p-4 border-b border-slate-700/50 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-800/30">
            <div class="relative w-full sm:w-64">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" placeholder="Search customers..." class="w-full bg-slate-900 border border-slate-700/50 text-sm text-white rounded-lg pl-9 pr-4 py-2 outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all">
            </div>
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <select class="bg-slate-900 border border-slate-700/50 text-sm text-slate-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500/50 transition-all">
                    <option>All Statuses</option>
                    <option>Active</option>
                    <option>Inactive</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-800/50 border-b border-slate-700/50 text-xs uppercase tracking-wider text-slate-400">
                        <th class="py-4 px-6 font-semibold">ID</th>
                        <th class="py-4 px-6 font-semibold">Customer Name</th>
                        <th class="py-4 px-6 font-semibold">Email</th>
                        <th class="py-4 px-6 font-semibold">Joined Date</th>
                        <th class="py-4 px-6 font-semibold">Status</th>
                        <th class="py-4 px-6 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-800/50">
                    <?php if(empty($customers)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No customers found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                        <tr class="hover:bg-slate-800/30 transition-colors group">
                            <td class="py-3 px-6 text-slate-400">#<?= $customer['id'] ?></td>
                            <td class="py-3 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-lg">
                                        <?= strtoupper(substr($customer['first_name'], 0, 1)) ?>
                                    </div>
                                    <span class="text-white font-medium group-hover:text-indigo-300 transition-colors">
                                        <?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-6 text-slate-300">
                                <?= htmlspecialchars($customer['email']) ?>
                            </td>
                            <td class="py-3 px-6 text-slate-400">
                                <?= date('M j, Y', strtotime($customer['registration_date'] ?? 'now')) ?>
                            </td>
                            <td class="py-3 px-6">
                                <?php if (($customer['status'] ?? 'Active') === 'Active'): ?>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border text-emerald-400 bg-emerald-500/10 border-emerald-500/20">Active</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border text-slate-400 bg-slate-500/10 border-slate-500/20">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-6">
                                <div class="flex items-center justify-center space-x-2">
                                    <button class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white transition-colors flex items-center justify-center" title="View Details">
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
            <p class="text-xs text-slate-400">Showing <span class="font-medium text-white">1</span> to <span class="font-medium text-white"><?= count($customers) ?></span> of <span class="font-medium text-white"><?= count($customers) ?></span> results</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'admin_layout.php';
?>
