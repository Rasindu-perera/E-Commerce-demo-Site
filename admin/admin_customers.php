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
        <button id="exportCsvBtn" class="btn-gradient bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium px-5 py-2.5 rounded-lg shadow-lg shadow-indigo-500/25 flex items-center space-x-2 transition-transform hover:-translate-y-0.5">
            <i class="fa-solid fa-download text-sm"></i>
            <span>Export CSV</span>
        </button>
    </div>

    <div class="glass-panel rounded-2xl border border-slate-700/50 shadow-xl overflow-hidden">
        <!-- Table Toolbar -->
        <div class="p-4 border-b border-slate-700/50 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-800/30">
            <div class="relative w-full sm:w-64">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="searchInput" placeholder="Search customers..." class="w-full bg-slate-900 border border-slate-700/50 text-sm text-white rounded-lg pl-9 pr-4 py-2 outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all">
            </div>
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <select id="statusFilter" class="bg-slate-900 border border-slate-700/50 text-sm text-slate-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500/50 transition-all">
                    <option value="">All Statuses</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap" id="customersTable">
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
                        <tr class="hover:bg-slate-800/30 transition-colors group customer-row" data-status="<?= htmlspecialchars($customer['status'] ?? 'Active') ?>">
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
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border text-emerald-400 bg-emerald-500/10 border-emerald-500/20 status-badge">Active</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border text-slate-400 bg-slate-500/10 border-slate-500/20 status-badge">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-6">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="viewCustomerDetails(<?= $customer['id'] ?>)" class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white transition-colors flex items-center justify-center" title="View Details">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button onclick="deleteCustomer(<?= $customer['id'] ?>, this)" class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white transition-colors flex items-center justify-center" title="Deactivate User">
                                        <i class="fa-solid fa-trash text-xs"></i>
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

<!-- Customer Details Modal -->
<div id="customerModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl relative">
        <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800/50">
            <h3 class="text-xl font-bold text-white">Customer Profile</h3>
            <button onclick="closeCustomerModal()" class="text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto">
            <div id="modalCustomerInfo" class="mb-6 flex items-center space-x-4">
                <!-- Injected via JS -->
            </div>
            <h4 class="text-lg font-bold text-white mb-3">Order History</h4>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-700 text-sm text-slate-400">
                        <th class="py-2 font-medium">Order ID</th>
                        <th class="py-2 font-medium">Date</th>
                        <th class="py-2 font-medium">Total</th>
                        <th class="py-2 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody id="modalOrdersTbody" class="text-white text-sm divide-y divide-slate-800/50">
                    <!-- Items injected via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // 1. Search & Filter JS
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.customer-row');

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

    // 2. CSV Export
    document.getElementById('exportCsvBtn').addEventListener('click', function() {
        let csvContent = "data:text/csv;charset=utf-8,";
        
        // Headers
        csvContent += "ID,Customer Name,Email,Joined Date,Status\n";
        
        // Rows
        rows.forEach(row => {
            if(row.style.display !== 'none') {
                const cells = row.querySelectorAll('td');
                const id = cells[0].innerText.replace('#','');
                const name = cells[1].innerText.trim();
                const email = cells[2].innerText.trim();
                const date = cells[3].innerText.trim();
                const status = cells[4].innerText.trim();
                
                csvContent += `${id},"${name}","${email}","${date}","${status}"\n`;
            }
        });
        
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "customers_report.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // 3. Customer Details Modal (AJAX)
    const modal = document.getElementById('customerModal');
    function viewCustomerDetails(id) {
        document.getElementById('modalCustomerInfo').innerHTML = '<div class="text-slate-400">Loading profile...</div>';
        document.getElementById('modalOrdersTbody').innerHTML = '';
        modal.classList.remove('hidden');
        
        fetch(`../api/get_customer_details.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const u = data.user;
                    document.getElementById('modalCustomerInfo').innerHTML = `
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                            ${u.first_name.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">${u.first_name} ${u.last_name}</h2>
                            <p class="text-slate-400">${u.email}</p>
                            <p class="text-xs text-slate-500 mt-1">Status: ${u.status}</p>
                        </div>
                    `;
                    
                    let ordersHtml = '';
                    if(data.orders.length === 0) {
                        ordersHtml = '<tr><td colspan="4" class="py-4 text-center text-slate-500">No orders placed yet.</td></tr>';
                    } else {
                        data.orders.forEach(o => {
                            ordersHtml += `
                                <tr>
                                    <td class="py-3 text-indigo-400">#${String(o.id).padStart(5, '0')}</td>
                                    <td class="py-3">${new Date(o.order_date).toLocaleDateString()}</td>
                                    <td class="py-3 font-medium text-emerald-400">$${parseFloat(o.total_amount).toFixed(2)}</td>
                                    <td class="py-3">${o.status}</td>
                                </tr>
                            `;
                        });
                    }
                    document.getElementById('modalOrdersTbody').innerHTML = ordersHtml;
                } else {
                    document.getElementById('modalCustomerInfo').innerHTML = `<div class="text-rose-400">Error: ${data.message}</div>`;
                }
            }).catch(err => {
                document.getElementById('modalCustomerInfo').innerHTML = `<div class="text-rose-400">Network Error</div>`;
            });
    }

    function closeCustomerModal() {
        modal.classList.add('hidden');
    }

    // 4. Deactivate Customer
    function deleteCustomer(id, btnEl) {
        if(confirm("Are you sure you want to deactivate this user? They will not be able to log in, but their order history will remain intact.")) {
            fetch('../api/delete_customer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: id })
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    const row = btnEl.closest('.customer-row');
                    row.setAttribute('data-status', 'Inactive');
                    const badge = row.querySelector('.status-badge');
                    badge.className = "px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border text-slate-400 bg-slate-500/10 border-slate-500/20 status-badge";
                    badge.innerText = "Inactive";
                    alert("User deactivated successfully.");
                } else {
                    alert('Error: ' + data.message);
                }
            }).catch(err => alert('Network error.'));
        }
    }
</script>

<?php
$content = ob_get_clean();
require_once 'admin_layout.php';
?>
