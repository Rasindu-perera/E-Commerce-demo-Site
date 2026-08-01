<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Analytics</title>
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
    <!-- Chart.js -->
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
            <a href="#" class="flex items-center space-x-3 px-4 py-3 bg-indigo-500/10 text-indigo-400 rounded-xl border border-indigo-500/20 shadow-inner">
                <i class="fa-solid fa-layer-group"></i>
                <span class="font-medium">Overview</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800/80 rounded-xl transition-all group">
                <i class="fa-solid fa-box group-hover:text-indigo-400 transition-colors"></i>
                <span class="font-medium">Products</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800/80 rounded-xl transition-all group">
                <i class="fa-solid fa-users group-hover:text-indigo-400 transition-colors"></i>
                <span class="font-medium">Customers</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800/80 rounded-xl transition-all group">
                <i class="fa-solid fa-cart-shopping group-hover:text-indigo-400 transition-colors"></i>
                <span class="font-medium">Orders</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800/80 rounded-xl transition-all group">
                <i class="fa-solid fa-chart-pie group-hover:text-indigo-400 transition-colors"></i>
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

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative overflow-y-auto overflow-x-hidden bg-slate-900">
        <!-- Ambient Glow -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600/10 rounded-full mix-blend-screen filter blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-purple-600/10 rounded-full mix-blend-screen filter blur-[100px] pointer-events-none"></div>

        <!-- Header -->
        <header class="h-20 glass-panel sticky top-0 z-30 flex items-center justify-between px-8 border-b border-slate-700/50 shadow-sm backdrop-blur-xl">
            <h1 class="text-2xl font-bold text-white tracking-tight">Analytics Dashboard</h1>
            <div class="flex items-center space-x-5">
                <div class="relative hidden sm:block">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" placeholder="Search..." class="bg-slate-800/80 border border-slate-700/50 text-sm text-white rounded-full pl-9 pr-4 py-2 w-48 outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all">
                </div>
                <button class="relative text-slate-400 hover:text-white transition-colors">
                    <i class="fa-regular fa-bell text-xl"></i>
                    <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-rose-500 border-2 border-slate-900 rounded-full"></span>
                </button>
                <div class="flex items-center space-x-3 pl-4 border-l border-slate-700/50 cursor-pointer group">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=6366f1&color=fff" alt="Admin" class="w-9 h-9 rounded-full ring-2 ring-transparent group-hover:ring-indigo-500/50 transition-all">
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium text-white leading-none mb-1">Admin</p>
                        <p class="text-xs text-slate-400 leading-none">Superuser</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-slate-500 text-xs ml-2"></i>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="p-6 md:p-8 relative z-10 w-full max-w-7xl mx-auto">
            
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-1">Welcome back</h2>
                    <p class="text-slate-400 text-sm">Here's what's happening with your store today.</p>
                </div>
                <button class="bg-white/5 hover:bg-white/10 text-white border border-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center space-x-2">
                    <i class="fa-solid fa-download text-xs"></i>
                    <span>Export Report</span>
                </button>
            </div>

            <!-- Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1 -->
                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-500/20 rounded-full blur-xl group-hover:bg-indigo-500/30 transition-all"></div>
                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div>
                            <p class="text-sm font-medium text-slate-400 mb-2">Monthly Revenue</p>
                            <h3 class="text-3xl font-bold text-white" id="metric-sales">$0.00</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-indigo-500/5 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shadow-inner">
                            <i class="fa-solid fa-dollar-sign text-xl"></i>
                        </div>
                    </div>
                    <div class="flex items-center text-xs font-medium text-emerald-400 bg-emerald-400/10 w-fit px-2 py-1 rounded-md">
                        <i class="fa-solid fa-arrow-trend-up mr-1"></i> +12.5% <span class="text-slate-500 ml-1 font-normal">vs last month</span>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/20 rounded-full blur-xl group-hover:bg-emerald-500/30 transition-all"></div>
                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div>
                            <p class="text-sm font-medium text-slate-400 mb-2">Total Orders</p>
                            <h3 class="text-3xl font-bold text-white" id="metric-orders">0</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shadow-inner">
                            <i class="fa-solid fa-cart-arrow-down text-xl"></i>
                        </div>
                    </div>
                    <div class="flex items-center text-xs font-medium text-emerald-400 bg-emerald-400/10 w-fit px-2 py-1 rounded-md">
                        <i class="fa-solid fa-arrow-trend-up mr-1"></i> +8.2% <span class="text-slate-500 ml-1 font-normal">vs last month</span>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/20 rounded-full blur-xl group-hover:bg-amber-500/30 transition-all"></div>
                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div class="pr-2">
                            <p class="text-sm font-medium text-slate-400 mb-2">Top Product</p>
                            <h3 class="text-lg font-bold text-white leading-tight mt-1 line-clamp-2 h-[2.5rem]" id="metric-top-product">Loading...</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-500/5 border border-amber-500/20 flex items-center justify-center text-amber-400 flex-shrink-0 shadow-inner">
                            <i class="fa-solid fa-crown text-xl"></i>
                        </div>
                    </div>
                    <div class="flex items-center text-xs font-medium text-slate-400 w-fit">
                        Based on volume sold
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/20 rounded-full blur-xl group-hover:bg-purple-500/30 transition-all"></div>
                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div class="pr-2">
                            <p class="text-sm font-medium text-slate-400 mb-2">Top Customer</p>
                            <h3 class="text-lg font-bold text-white leading-tight mt-1 line-clamp-2 h-[2.5rem]" id="metric-top-customer">Loading...</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500/20 to-purple-500/5 border border-purple-500/20 flex items-center justify-center text-purple-400 flex-shrink-0 shadow-inner">
                            <i class="fa-solid fa-user-astronaut text-xl"></i>
                        </div>
                    </div>
                    <div class="flex items-center text-xs font-medium text-slate-400 w-fit">
                        Based on lifetime value
                    </div>
                </div>
            </div>

            <!-- Charts Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Line Chart -->
                <div class="lg:col-span-2 glass-panel rounded-2xl p-6 shadow-lg">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Revenue Trend</h3>
                            <p class="text-xs text-slate-400 mt-1">Trailing 12 months performance</p>
                        </div>
                        <div class="bg-slate-800/50 rounded-lg p-1 flex">
                            <button class="px-3 py-1 text-xs font-medium rounded-md bg-indigo-500 text-white shadow">12M</button>
                            <button class="px-3 py-1 text-xs font-medium rounded-md text-slate-400 hover:text-white transition-colors">6M</button>
                            <button class="px-3 py-1 text-xs font-medium rounded-md text-slate-400 hover:text-white transition-colors">30D</button>
                        </div>
                    </div>
                    <div class="relative h-[300px] w-full">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Doughnut Chart -->
                <div class="glass-panel rounded-2xl p-6 shadow-lg flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Sales by Product</h3>
                            <p class="text-xs text-slate-400 mt-1">Top 5 performers</p>
                        </div>
                        <button class="text-slate-400 hover:text-white transition-colors"><i class="fa-solid fa-ellipsis"></i></button>
                    </div>
                    <div class="relative flex-grow flex items-center justify-center min-h-[250px]">
                        <canvas id="productsChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Loading Indicator Overlay -->
            <div id="loading-indicator" class="fixed inset-0 bg-slate-900/90 backdrop-blur-md z-50 flex flex-col items-center justify-center transition-opacity duration-500">
                <div class="relative">
                    <div class="w-16 h-16 border-4 border-indigo-500/30 border-t-indigo-500 rounded-full animate-spin"></div>
                    <div class="w-8 h-8 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin absolute top-4 left-4 animate-reverse"></div>
                </div>
                <p class="text-indigo-400 font-medium animate-pulse mt-6 tracking-widest uppercase text-sm">Crunching Data</p>
            </div>

        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            // Set dark theme defaults for Chart.js globally
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 23, 42, 0.95)';
            Chart.defaults.plugins.tooltip.titleColor = '#fff';
            Chart.defaults.plugins.tooltip.bodyColor = '#cbd5e1';
            Chart.defaults.plugins.tooltip.borderColor = 'rgba(255,255,255,0.1)';
            Chart.defaults.plugins.tooltip.borderWidth = 1;
            Chart.defaults.plugins.tooltip.padding = 12;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            
            fetch('../api/analytics_data.php')
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        const data = response.data;
                        
                        // --- Update Metrics ---
                        const sales = data.current_month_summary.total_sales || 0;
                        document.getElementById('metric-sales').innerText = '$' + parseFloat(sales).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        document.getElementById('metric-orders').innerText = data.current_month_summary.total_orders || 0;
                        
                        if (data.top_products && data.top_products.length > 0) {
                            document.getElementById('metric-top-product').innerText = data.top_products[0].name;
                        } else {
                            document.getElementById('metric-top-product').innerText = "N/A";
                        }

                        if (data.top_customers && data.top_customers.length > 0) {
                            const topCustomer = data.top_customers[0];
                            document.getElementById('metric-top-customer').innerText = topCustomer.first_name + ' ' + topCustomer.last_name;
                        } else {
                            document.getElementById('metric-top-customer').innerText = "N/A";
                        }
                        
                        // --- Revenue Line Chart ---
                        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
                        
                        const labels = data.monthly_revenue_trend.map(item => {
                            const date = new Date(item.month + '-01');
                            return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
                        });
                        const revenueData = data.monthly_revenue_trend.map(item => parseFloat(item.revenue));
                        
                        let gradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)'); // Indigo 500 with opacity
                        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');
                        
                        new Chart(revenueCtx, {
                            type: 'line',
                            data: {
                                labels: labels.length > 0 ? labels : ['No Data'],
                                datasets: [{
                                    label: 'Monthly Revenue',
                                    data: revenueData.length > 0 ? revenueData : [0],
                                    borderColor: '#818cf8', // Indigo 400
                                    backgroundColor: gradient,
                                    borderWidth: 3,
                                    pointBackgroundColor: '#1e293b',
                                    pointBorderColor: '#818cf8',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    pointHoverBackgroundColor: '#818cf8',
                                    pointHoverBorderColor: '#fff',
                                    fill: true,
                                    tension: 0.4 // Smooth curves
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        displayColors: false,
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) { label += ': '; }
                                                if (context.parsed.y !== null) {
                                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                                        border: { display: false },
                                        ticks: {
                                            callback: function(value) { 
                                                if (value >= 1000) return '$' + (value/1000) + 'k';
                                                return '$' + value; 
                                            },
                                            font: { size: 11 }
                                        }
                                    },
                                    x: {
                                        grid: { display: false, drawBorder: false },
                                        border: { display: false },
                                        ticks: { font: { size: 11 } }
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                    mode: 'index',
                                }
                            }
                        });

                        // --- Top Products Doughnut Chart ---
                        const productsCtx = document.getElementById('productsChart').getContext('2d');
                        
                        const productLabels = data.top_products.map(p => p.name);
                        const productData = data.top_products.map(p => parseInt(p.total_sold));
                        
                        // Vibrant colors for the doughnut
                        const bgColors = [
                            '#6366f1', // Indigo
                            '#8b5cf6', // Violet
                            '#ec4899', // Pink
                            '#f43f5e', // Rose
                            '#f59e0b'  // Amber
                        ];

                        new Chart(productsCtx, {
                            type: 'doughnut',
                            data: {
                                labels: productLabels.length > 0 ? productLabels : ['No Data'],
                                datasets: [{
                                    data: productData.length > 0 ? productData : [1],
                                    backgroundColor: productLabels.length > 0 ? bgColors : ['#334155'],
                                    borderWidth: 2,
                                    borderColor: '#1e293b', // Match card background
                                    hoverOffset: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '75%', // Modern thin doughnut
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            padding: 20,
                                            usePointStyle: true,
                                            pointStyle: 'circle',
                                            font: { size: 11 }
                                        }
                                    }
                                }
                            }
                        });

                        // Hide loading indicator with a smooth fade
                        const loader = document.getElementById('loading-indicator');
                        loader.style.opacity = '0';
                        setTimeout(() => loader.style.display = 'none', 500);

                    } else {
                        console.error("API Error:", response.message);
                        alert("Failed to load analytics data: " + response.message);
                    }
                })
                .catch(err => {
                    console.error("Fetch Error:", err);
                    alert("Network error occurred while fetching analytics.");
                });
        });
    </script>
</body>
</html>
