<?php
session_start();
require_once 'config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT id, first_name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Setup Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            
            // Check for admin
            if (strtolower($email) === 'admin@gmail.com') {
                $_SESSION['is_admin'] = true;
                header("Location: admin/dashboard.php");
                exit;
            } else {
                $_SESSION['is_admin'] = false;
                header("Location: index.php");
                exit;
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KWRmart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        primaryHover: '#2563eb',
                        dark: '#0f172a',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 selection:bg-primary/30 text-slate-800">

    <div class="bg-white max-w-md w-full rounded-2xl shadow-xl shadow-slate-200/50 p-8 border border-slate-100 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-10 -left-10 w-32 h-32 bg-primary/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-dark/5 rounded-full blur-2xl"></div>

        <div class="relative z-10">
            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-xl bg-dark text-white flex items-center justify-center mx-auto mb-4 shadow-lg shadow-dark/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                </div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Welcome Back</h1>
                <p class="text-slate-500 text-sm mt-2">Log in to your KWRmart account.</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-6 border border-red-100 flex items-start space-x-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <div class="bg-blue-50 text-blue-800 p-4 rounded-lg text-sm mb-6 border border-blue-200 text-left">
                <p class="font-semibold mb-1">Demo Admin Access</p>
                <p>Email: <strong>admin@gmail.com</strong></p>
                <p>Password: <strong>admin1234</strong></p>
                <p class="text-rose-600 font-semibold mt-1 text-xs">⚠️ Please do not change the admin password!</p>
            </div>

            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-sm" placeholder="you@example.com" required>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-slate-700" for="password">Password</label>
                        <a href="#" class="text-xs text-primary hover:underline">Forgot password?</a>
                    </div>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-sm" placeholder="••••••••" required>
                </div>

                <div class="flex items-center mt-2 mb-4">
                    <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary/20 border-slate-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-slate-600">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-primaryHover text-white font-medium py-2.5 rounded-lg transition-colors shadow-lg shadow-primary/25 mt-2">
                    Sign In
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-6">
                Don't have an account? <a href="register.php" class="text-primary font-medium hover:underline">Sign up</a>
            </p>
            <div class="mt-4 text-center">
                 <a href="index.php" class="text-xs text-slate-400 hover:text-slate-600 transition-colors">&larr; Back to Store</a>
            </div>
        </div>
    </div>

</body>
</html>
