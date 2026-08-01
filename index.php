<?php
// --- KWRmart Front Controller ---

// Parse the route from the URL. 
// If using Apache mod_rewrite, we can pass it via $_GET['route'] (see .htaccess).
$route = $_GET['route'] ?? 'home';

// Basic sanitization: strip .php extension if it was included in the URL for backward compatibility
$route = str_replace('.php', '', $route);

// Remove leading/trailing slashes
$route = trim($route, '/');

// Default to home page
if ($route === '') {
    $route = 'home';
}

// Security: Prevent directory traversal by only allowing valid characters (alphanumeric, dashes, underscores)
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $route)) {
    // Render a simple 404 page for invalid route formats
    http_response_code(404);
    echo "<!DOCTYPE html><html lang='en'><head><title>404 Not Found - KWRmart</title>";
    echo "<script src='https://cdn.tailwindcss.com'></script></head>";
    echo "<body class='bg-slate-950 text-white flex items-center justify-center h-screen flex-col'>";
    echo "<h1 class='text-6xl font-bold text-rose-500 mb-4'>404</h1>";
    echo "<h2 class='text-2xl font-medium mb-8 text-slate-300'>Page Not Found</h2>";
    echo "<a href='/' class='bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3 rounded-lg font-medium transition-colors'>Return Home</a>";
    echo "</body></html>";
    exit;
}

// Check if the requested view file exists
$viewFile = __DIR__ . '/views/pages/' . $route . '.php';

if (file_exists($viewFile)) {
    // Include the requested page
    require_once $viewFile;
} else {
    // Render a 404 page if the file doesn't exist
    http_response_code(404);
    echo "<!DOCTYPE html><html lang='en'><head><title>404 Not Found - KWRmart</title>";
    echo "<script src='https://cdn.tailwindcss.com'></script></head>";
    echo "<body class='bg-slate-950 text-white flex items-center justify-center h-screen flex-col'>";
    echo "<h1 class='text-6xl font-bold text-rose-500 mb-4'>404</h1>";
    echo "<h2 class='text-2xl font-medium mb-8 text-slate-300'>Page Not Found</h2>";
    echo "<p class='text-slate-500 mb-8'>The page '$route' does not exist.</p>";
    echo "<a href='/' class='bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3 rounded-lg font-medium transition-colors'>Return Home</a>";
    echo "</body></html>";
    exit;
}
