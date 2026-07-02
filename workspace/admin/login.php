<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $res = $conn->query("SELECT * FROM admin WHERE username='$username'");
    if ($res && $res->num_rows > 0) {
        $admin = $res->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Invalid password']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Admin not found']);
    }
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Quick Kart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen overflow-hidden select-none relative">
    
    <!-- Decorative blobs -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-orange-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-80 h-80 bg-amber-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 translate-x-1/3 translate-y-1/3"></div>

    <div id="loader" class="hidden fixed inset-0 bg-slate-900 bg-opacity-20 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-3xl shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-100 border-t-orange-500 mb-3"></div>
            <p class="text-sm font-black text-slate-800 tracking-wider uppercase">Loading...</p>
        </div>
    </div>

    <div class="bg-white shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-slate-100 rounded-[2.5rem] w-full max-w-sm p-8 mx-5 relative z-10">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-tr from-slate-800 to-slate-900 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl shadow-lg shadow-slate-900/20 transform -rotate-3">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Admin Login</h1>
            <p class="text-slate-500 font-medium text-sm mt-2">Manage your store</p>
        </div>
        
        <div id="error-msg" class="hidden bg-red-50 text-red-600 border border-red-100 text-xs font-black uppercase tracking-wider p-4 rounded-2xl mb-6 text-center flex items-center justify-center"><i class="fa-solid fa-circle-exclamation mr-2"></i> <span></span></div>

        <form id="form-login" onsubmit="handleAuth(event)">
            <div class="mb-5">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Username</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-4 top-4 text-slate-400"></i>
                    <input type="text" name="username" required class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
                </div>
            </div>
            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-4 text-slate-400"></i>
                    <input type="password" name="password" required class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
                </div>
            </div>
            <button type="submit" class="w-full bg-slate-800 text-white font-black py-4 rounded-2xl shadow-lg shadow-slate-800/30 hover:bg-slate-900 hover:scale-[1.02] transition-all text-lg flex items-center justify-center">Sign In <i class="fa-solid fa-arrow-right ml-2"></i></button>
        </form>
    </div>

    <script>
        async function handleAuth(e) {
            e.preventDefault();
            document.getElementById('loader').classList.remove('hidden');
            document.getElementById('error-msg').classList.add('hidden');
            
            try {
                const res = await fetch('login.php', { method: 'POST', body: new FormData(e.target) });
                const data = await res.json();
                
                if (data.status === 'success') {
                    window.location.href = 'index.php';
                } else {
                    document.querySelector('#error-msg span').textContent = data.msg;
                    document.getElementById('error-msg').classList.remove('hidden');
                }
            } catch (err) {}
            document.getElementById('loader').classList.add('hidden');
        }
    </script>
</body>
</html>
