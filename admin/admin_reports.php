<?php
session_start();
require_once '../config/db.php';

// Auth check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Total Lifetime Stats
$stmtTotal = $pdo->query("SELECT COUNT(id) as total_orders, COALESCE(SUM(total_amount), 0) as total_revenue FROM orders WHERE status = 'Completed'");
$totals = $stmtTotal->fetch();

// Sales by Category
$stmtCategory = $pdo->query("
    SELECT p.category, SUM(oi.quantity) as items_sold, SUM(oi.quantity * oi.unit_price) as revenue 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    JOIN orders o ON oi.order_id = o.id 
    WHERE o.status = 'Completed' 
    GROUP BY p.category 
    ORDER BY revenue DESC
");
$categorySales = $stmtCategory->fetchAll();

// Monthly Revenue Table
$stmtMonthly = $pdo->query("
    SELECT DATE_FORMAT(order_date, '%Y-%m') as month_str, SUM(total_amount) as revenue, COUNT(id) as orders 
    FROM orders 
    WHERE status = 'Completed' 
    GROUP BY month_str 
    ORDER BY month_str DESC 
    LIMIT 12
");
$monthlySales = $stmtMonthly->fetchAll();

$pageTitle = "Business Reports";
$activeMenu = "reports";

ob_start();
?>

<div class="p-6 md:p-8 relative z-10 w-full max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-white mb-1">Reports & Analytics</h2>
            <p class="text-slate-400 text-sm">Detailed breakdown of your store's performance.</p>
        </div>
        <button id="exportPdfBtn" class="btn-gradient bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium px-5 py-2.5 rounded-lg shadow-lg shadow-indigo-500/25 flex items-center space-x-2 transition-transform hover:-translate-y-0.5">
            <i class="fa-solid fa-file-pdf text-sm"></i>
            <span>Export to PDF</span>
        </button>
    </div>

    <!-- Top Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-500/20 rounded-full blur-xl group-hover:bg-indigo-500/30 transition-all"></div>
            <div class="flex justify-between items-start mb-2 relative z-10">
                <div>
                    <p class="text-sm font-medium text-slate-400 mb-2">Lifetime Revenue</p>
                    <h3 class="text-3xl font-bold text-white">$<?= number_format($totals['total_revenue'], 2) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-indigo-500/5 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shadow-inner">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-slate-500 relative z-10 mt-2">Calculated from all completed orders</p>
        </div>
        <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/20 rounded-full blur-xl group-hover:bg-emerald-500/30 transition-all"></div>
            <div class="flex justify-between items-start mb-2 relative z-10">
                <div>
                    <p class="text-sm font-medium text-slate-400 mb-2">Total Orders Delivered</p>
                    <h3 class="text-3xl font-bold text-white"><?= number_format($totals['total_orders']) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shadow-inner">
                    <i class="fa-solid fa-box-open text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-slate-500 relative z-10 mt-2">Calculated from all completed orders</p>
        </div>
    </div>

    <!-- Reports Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Category Sales -->
        <div class="glass-panel rounded-2xl border border-slate-700/50 shadow-xl overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-700/50 bg-slate-800/30">
                <h3 class="text-lg font-bold text-white">Sales by Category</h3>
            </div>
            <div class="overflow-x-auto flex-grow">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-800/50 border-b border-slate-700/50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="py-4 px-6 font-semibold">Category</th>
                            <th class="py-4 px-6 font-semibold text-right">Items Sold</th>
                            <th class="py-4 px-6 font-semibold text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-800/50">
                        <?php if(empty($categorySales)): ?>
                            <tr><td colspan="3" class="py-6 text-center text-slate-400">No data available.</td></tr>
                        <?php else: ?>
                            <?php foreach($categorySales as $cat): ?>
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3 px-6 font-medium text-white"><?= htmlspecialchars($cat['category']) ?></td>
                                    <td class="py-3 px-6 text-right text-slate-300"><?= number_format($cat['items_sold']) ?> units</td>
                                    <td class="py-3 px-6 text-right font-bold text-emerald-400">$<?= number_format($cat['revenue'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Breakdown -->
        <div class="glass-panel rounded-2xl border border-slate-700/50 shadow-xl overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-700/50 bg-slate-800/30">
                <h3 class="text-lg font-bold text-white">Monthly Breakdown (Last 12)</h3>
            </div>
            <div class="overflow-x-auto flex-grow">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-800/50 border-b border-slate-700/50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="py-4 px-6 font-semibold">Month</th>
                            <th class="py-4 px-6 font-semibold text-right">Orders</th>
                            <th class="py-4 px-6 font-semibold text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-800/50">
                        <?php if(empty($monthlySales)): ?>
                            <tr><td colspan="3" class="py-6 text-center text-slate-400">No data available.</td></tr>
                        <?php else: ?>
                            <?php foreach($monthlySales as $month): ?>
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3 px-6 font-medium text-slate-300">
                                        <?= date('F Y', strtotime($month['month_str'] . '-01')) ?>
                                    </td>
                                    <td class="py-3 px-6 text-right text-slate-300"><?= number_format($month['orders']) ?></td>
                                    <td class="py-3 px-6 text-right font-bold text-emerald-400">$<?= number_format($month['revenue'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- JS PDF Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        doc.setFontSize(18);
        doc.text("Business Analytics Report", 14, 22);
        doc.setFontSize(11);
        doc.text("Generated on: " + new Date().toLocaleString(), 14, 30);
        
        // Add Category Sales Table
        doc.setFontSize(14);
        doc.text("Sales by Category", 14, 45);
        
        const categoryData = [];
        const catRows = document.querySelectorAll('.glass-panel:nth-child(1) tbody tr');
        catRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if(cells.length === 3) {
                categoryData.push([cells[0].innerText, cells[1].innerText, cells[2].innerText]);
            }
        });
        
        doc.autoTable({
            startY: 50,
            head: [['Category', 'Items Sold', 'Revenue']],
            body: categoryData,
            theme: 'grid',
            headStyles: { fillColor: [99, 102, 241] }
        });
        
        // Add Monthly Breakdown Table
        let currentY = doc.lastAutoTable.finalY + 15;
        doc.setFontSize(14);
        doc.text("Monthly Breakdown (Last 12 Months)", 14, currentY);
        
        const monthlyData = [];
        const monthRows = document.querySelectorAll('.glass-panel:nth-child(2) tbody tr');
        monthRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if(cells.length === 3) {
                monthlyData.push([cells[0].innerText, cells[1].innerText, cells[2].innerText]);
            }
        });
        
        doc.autoTable({
            startY: currentY + 5,
            head: [['Month', 'Orders', 'Revenue']],
            body: monthlyData,
            theme: 'grid',
            headStyles: { fillColor: [16, 185, 129] } // Emerald 500
        });
        
        doc.save("business_report.pdf");
    });
</script>

<?php
$content = ob_get_clean();
require_once 'admin_layout.php';
?>
