<?php
session_start();
require_once '../config/db.php';
$pageTitle = "Analytics Dashboard";
$activeMenu = "overview";
ob_start();
?>
        <div class="p-6 md:p-8 relative z-10 w-full max-w-7xl mx-auto">
            
            <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-1">Welcome back</h2>
                    <p class="text-slate-400 text-sm">Here's what's happening with your store today.</p>
                </div>
                <button id="exportPdfBtn" class="bg-white/5 hover:bg-white/10 text-white border border-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center space-x-2 shrink-0">
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
            
            // Add jsPDF dynamic script loading
            const jspdfScript = document.createElement('script');
            jspdfScript.src = "https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js";
            document.head.appendChild(jspdfScript);

            document.getElementById('exportPdfBtn').addEventListener('click', function() {
                if(window.jspdf) {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();
                    
                    doc.setFontSize(18);
                    doc.text("Analytics Dashboard Snapshot", 14, 22);
                    doc.setFontSize(11);
                    doc.text("Generated on: " + new Date().toLocaleString(), 14, 30);
                    
                    doc.setFontSize(14);
                    doc.text("Overview Metrics", 14, 45);
                    
                    const sales = document.getElementById('metric-sales').innerText;
                    const orders = document.getElementById('metric-orders').innerText;
                    const topProduct = document.getElementById('metric-top-product').innerText;
                    const topCustomer = document.getElementById('metric-top-customer').innerText;
                    
                    doc.setFontSize(12);
                    doc.text(`Monthly Revenue: ${sales}`, 14, 55);
                    doc.text(`Total Orders: ${orders}`, 14, 65);
                    doc.text(`Top Product: ${topProduct}`, 14, 75);
                    doc.text(`Top Customer: ${topCustomer}`, 14, 85);
                    
                    doc.save("dashboard_snapshot.pdf");
                } else {
                    alert("PDF Library is still loading. Please try again in a moment.");
                }
            });
            
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
                        const loader = document.getElementById('loading-indicator');
                        if (loader) {
                            loader.style.opacity = '0';
                            setTimeout(() => loader.style.display = 'none', 500);
                        }
                    }
                })
                .catch(err => {
                    console.error("Fetch Error:", err);
                    alert("Network error occurred while fetching analytics.");
                    const loader = document.getElementById('loading-indicator');
                    if (loader) {
                        loader.style.opacity = '0';
                        setTimeout(() => loader.style.display = 'none', 500);
                    }
                });
        });
    </script>
<?php
$content = ob_get_clean();
require_once 'admin_layout.php';
?>
