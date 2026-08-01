<?php
session_start();
require_once '../config/db.php';

// Auth check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

$pageTitle = "Business Reports";
$activeMenu = "reports";

ob_start();
?>

<div class="p-6 md:p-8 relative z-10 w-full max-w-7xl mx-auto flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="w-24 h-24 bg-indigo-500/10 rounded-full flex items-center justify-center mb-6 shadow-inner border border-indigo-500/20">
        <i class="fa-solid fa-chart-pie text-4xl text-indigo-400"></i>
    </div>
    <h2 class="text-3xl font-bold text-white mb-3">Reports & Analytics</h2>
    <p class="text-slate-400 max-w-md">This module is currently under construction. Advanced reports will be available here soon.</p>
</div>

<?php
$content = ob_get_clean();
require_once 'admin_layout.php';
?>
