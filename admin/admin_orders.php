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
        <button id="exportBtn" class="btn-gradient bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium px-5 py-2.5 rounded-lg shadow-lg shadow-indigo-500/25 flex items-center space-x-2 transition-transform hover:-translate-y-0.5">
            <i class="fa-solid fa-file-export text-sm"></i>
            <span>Export Orders (PDF)</span>
        </button>
    </div>

    <div class="glass-panel rounded-2xl border border-slate-700/50 shadow-xl overflow-hidden">
        <!-- Table Toolbar -->
        <div class="p-4 border-b border-slate-700/50 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-800/30">
            <div class="relative w-full sm:w-64">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="searchInput" placeholder="Search orders..." class="w-full bg-slate-900 border border-slate-700/50 text-sm text-white rounded-lg pl-9 pr-4 py-2 outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all">
            </div>
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <select id="statusFilter" class="bg-slate-900 border border-slate-700/50 text-sm text-slate-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500/50 transition-all">
                    <option value="">All Statuses</option>
                    <option value="Completed">Completed</option>
                    <option value="Pending">Pending</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap" id="ordersTable">
                <thead>
                    <tr class="bg-slate-800/50 border-b border-slate-700/50 text-xs uppercase tracking-wider text-slate-400">
                        <th class="py-4 px-6 font-semibold">Order ID</th>
                        <th class="py-4 px-6 font-semibold">Customer</th>
                        <th class="py-4 px-6 font-semibold">Date</th>
                        <th class="py-4 px-6 font-semibold">Total</th>
                        <th class="py-4 px-6 font-semibold">Status</th>
                        <th class="py-4 px-6 font-semibold text-center" data-export-ignore="true">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-800/50" id="ordersTbody">
                    <?php if(empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No orders found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-slate-800/30 transition-colors group order-row" data-status="<?= htmlspecialchars($order['status']) ?>">
                            <td class="py-3 px-6 text-white font-medium order-id">#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td class="py-3 px-6 text-slate-300 customer-name">
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
                                $statusColor = 'text-indigo-400 bg-indigo-900/30 border-indigo-500/50';
                                if ($order['status'] === 'Completed') $statusColor = 'text-emerald-400 bg-emerald-900/30 border-emerald-500/50';
                                if ($order['status'] === 'Pending') $statusColor = 'text-amber-400 bg-amber-900/30 border-amber-500/50';
                                if ($order['status'] === 'Cancelled') $statusColor = 'text-rose-400 bg-rose-900/30 border-rose-500/50';
                                ?>
                                <select onchange="updateOrderStatus(<?= $order['id'] ?>, this)" class="px-2 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider border <?= $statusColor ?> focus:outline-none appearance-none cursor-pointer">
                                    <option value="Completed" <?= $order['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </td>
                            <td class="py-3 px-6" data-export-ignore="true">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="viewOrderDetails(<?= $order['id'] ?>)" class="w-8 h-8 rounded-lg bg-slate-800/80 text-slate-400 hover:bg-slate-700 hover:text-white transition-colors flex items-center justify-center border border-slate-700/50" title="View Order">
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

<!-- Order Details Modal -->
<div id="orderModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl relative">
        <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800/50">
            <h3 class="text-xl font-bold text-white">Order Details <span id="modalOrderId" class="text-indigo-400 ml-2"></span></h3>
            <button onclick="closeOrderModal()" class="text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-700 text-sm text-slate-400">
                        <th class="py-2 font-medium">Product</th>
                        <th class="py-2 font-medium text-right">Unit Price</th>
                        <th class="py-2 font-medium text-right">Quantity</th>
                        <th class="py-2 font-medium text-right">Total</th>
                    </tr>
                </thead>
                <tbody id="modalItemsTbody" class="text-white text-sm divide-y divide-slate-800/50">
                    <!-- Items injected via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JS PDF Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
    // 1. Search and Filter Logic
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.order-row');

    function filterTable() {
        const query = searchInput.value.toLowerCase();
        const status = statusFilter.value;
        
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const rowStatus = row.getAttribute('data-status');
            const matchesSearch = text.includes(query);
            const matchesStatus = status === '' || rowStatus === status;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);

    // 2. AJAX Status Update
    function updateOrderStatus(orderId, selectEl) {
        const newStatus = selectEl.value;
        
        // Update visually instantly
        const row = selectEl.closest('.order-row');
        row.setAttribute('data-status', newStatus);
        
        const colors = {
            'Completed': 'text-emerald-400 bg-emerald-900/30 border-emerald-500/50',
            'Pending': 'text-amber-400 bg-amber-900/30 border-amber-500/50',
            'Cancelled': 'text-rose-400 bg-rose-900/30 border-rose-500/50'
        };
        selectEl.className = `px-2 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider border focus:outline-none appearance-none cursor-pointer ${colors[newStatus]}`;
        
        // Send AJAX request
        fetch('../api/update_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, status: newStatus })
        }).then(res => res.json()).then(data => {
            if(!data.success) {
                alert('Error updating status: ' + data.message);
            }
        }).catch(err => {
            console.error(err);
            alert('Failed to connect to server.');
        });
    }

    // 3. Order Details Modal
    const modal = document.getElementById('orderModal');
    function viewOrderDetails(orderId) {
        document.getElementById('modalOrderId').innerText = '#' + String(orderId).padStart(5, '0');
        document.getElementById('modalItemsTbody').innerHTML = '<tr><td colspan="4" class="py-4 text-center">Loading...</td></tr>';
        modal.classList.remove('hidden');
        
        fetch(`../api/get_order_details.php?id=${orderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    let html = '';
                    let grandTotal = 0;
                    data.items.forEach(item => {
                        const total = parseFloat(item.unit_price) * parseInt(item.quantity);
                        grandTotal += total;
                        html += `
                            <tr>
                                <td class="py-3">${item.name}</td>
                                <td class="py-3 text-right">$${parseFloat(item.unit_price).toFixed(2)}</td>
                                <td class="py-3 text-right">${item.quantity}</td>
                                <td class="py-3 text-right font-medium text-emerald-400">$${total.toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    html += `
                        <tr class="border-t border-slate-700 bg-slate-800/30">
                            <td colspan="3" class="py-3 text-right font-bold text-slate-300">Grand Total:</td>
                            <td class="py-3 text-right font-bold text-emerald-400">$${grandTotal.toFixed(2)}</td>
                        </tr>
                    `;
                    document.getElementById('modalItemsTbody').innerHTML = html;
                } else {
                    document.getElementById('modalItemsTbody').innerHTML = `<tr><td colspan="4" class="py-4 text-center text-rose-400">Error: ${data.message}</td></tr>`;
                }
            }).catch(err => {
                document.getElementById('modalItemsTbody').innerHTML = `<tr><td colspan="4" class="py-4 text-center text-rose-400">Network Error</td></tr>`;
            });
    }

    function closeOrderModal() {
        modal.classList.add('hidden');
    }

    // 4. PDF Export using jsPDF
    document.getElementById('exportBtn').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        doc.setFontSize(18);
        doc.text("Orders Report", 14, 22);
        doc.setFontSize(11);
        doc.text("Generated on: " + new Date().toLocaleString(), 14, 30);
        
        // Parse table manually to only include visible rows and exclude 'Actions' column
        const headers = [['Order ID', 'Customer', 'Date', 'Total', 'Status']];
        const data = [];
        
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const cells = row.querySelectorAll('td');
                data.push([
                    cells[0].innerText,
                    cells[1].innerText,
                    cells[2].innerText,
                    cells[3].innerText,
                    cells[4].innerText.trim().replace(/\n/g, '')
                ]);
            }
        });
        
        doc.autoTable({
            startY: 36,
            head: headers,
            body: data,
            theme: 'grid',
            headStyles: { fillColor: [99, 102, 241] } // Indigo 500
        });
        
        doc.save("orders_report.pdf");
    });
</script>

<?php
$content = ob_get_clean();
require_once 'admin_layout.php';
?>
