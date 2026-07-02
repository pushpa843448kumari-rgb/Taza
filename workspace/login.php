<?php
require_once 'config.php';

// Handle AJAX Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        $res = $conn->query("SELECT * FROM users WHERE email='$email'");
        
        if ($res && $res->num_rows > 0) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Invalid password']);
            }
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'User not found']);
        }
        exit;
    }
    
    if ($action === 'signup') {
        $name = $conn->real_escape_string($_POST['name']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $check = $conn->query("SELECT id FROM users WHERE email='$email'");
        if ($check && $check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Email already exists']);
            exit;
        }
        
        $sql = "INSERT INTO users (name, phone, email, password) VALUES ('$name', '$phone', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['user_name'] = $name;
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Database error']);
        }
        exit;
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen overflow-hidden select-none relative">
    <!-- Decorative blobs -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-orange-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-80 h-80 bg-amber-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 translate-x-1/3 translate-y-1/3"></div>

    <!-- Loader -->
    <div id="loader" class="hidden fixed inset-0 bg-slate-900 bg-opacity-20 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-3xl shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-100 border-t-orange-500 mb-3"></div>
            <p class="text-sm font-black text-slate-800 tracking-wider uppercase">Loading...</p>
        </div>
    </div>

    <div class="bg-white shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-slate-100 rounded-[2.5rem] w-full max-w-md p-8 mx-5 relative z-10">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-tr from-orange-500 to-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-orange-500/30 transform rotate-3">
                <i class="fa-solid fa-bolt text-2xl text-white"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Quick Kart</h1>
            <p class="text-slate-500 font-medium text-sm mt-2">Welcome back! Please enter your details.</p>
        </div>
        
        <!-- Tabs -->
        <div class="flex mb-8 bg-slate-50 rounded-2xl p-1.5 border border-slate-100">
            <button id="tab-login" onclick="switchTab('login')" class="w-1/2 py-2.5 text-sm font-black rounded-xl bg-white shadow-sm text-slate-800 transition-all uppercase tracking-wider">Login</button>
            <button id="tab-signup" onclick="switchTab('signup')" class="w-1/2 py-2.5 text-sm font-bold rounded-xl text-slate-500 hover:text-slate-700 transition-all uppercase tracking-wider">Sign Up</button>
        </div>

        <!-- Error Msg -->
        <div id="error-msg" class="hidden bg-red-50 text-red-600 border border-red-100 text-xs font-black uppercase tracking-wider p-4 rounded-2xl mb-6 text-center flex items-center justify-center"><i class="fa-solid fa-circle-exclamation mr-2"></i> <span></span></div>

        <!-- Login Form -->
        <form id="form-login" onsubmit="handleAuth(event, 'login')">
            <input type="hidden" name="action" value="login">
            <div class="mb-5">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
            </div>
            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Password</label>
                <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-500 text-white font-black py-4 rounded-2xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:scale-[1.02] transition-all text-lg">Sign In</button>
        </form>

        <!-- Signup Form -->
        <form id="form-signup" onsubmit="handleAuth(event, 'signup')" class="hidden">
            <input type="hidden" name="action" value="signup">
            <div class="mb-4">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Full Name</label>
                <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Phone</label>
                <input type="tel" name="phone" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Email</label>
                <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
            </div>
            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Password</label>
                <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-500 text-white font-black py-4 rounded-2xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:scale-[1.02] transition-all text-lg">Create Account</button>
        </form>
    </div>

    <script>
        function switchTab(tab) {
            document.getElementById('error-msg').classList.add('hidden');
            if (tab === 'login') {
                document.getElementById('form-login').classList.remove('hidden');
                document.getElementById('form-signup').classList.add('hidden');
                document.getElementById('tab-login').classList.add('bg-white', 'shadow-sm', 'text-slate-800', 'font-black');
                document.getElementById('tab-login').classList.remove('text-slate-500', 'font-bold');
                document.getElementById('tab-signup').classList.remove('bg-white', 'shadow-sm', 'text-slate-800', 'font-black');
                document.getElementById('tab-signup').classList.add('text-slate-500', 'font-bold');
            } else {
                document.getElementById('form-signup').classList.remove('hidden');
                document.getElementById('form-login').classList.add('hidden');
                document.getElementById('tab-signup').classList.add('bg-white', 'shadow-sm', 'text-slate-800', 'font-black');
                document.getElementById('tab-signup').classList.remove('text-slate-500', 'font-bold');
                document.getElementById('tab-login').classList.remove('bg-white', 'shadow-sm', 'text-slate-800', 'font-black');
                document.getElementById('tab-login').classList.add('text-slate-500', 'font-bold');
            }
        }

        async function handleAuth(e, type) {
            e.preventDefault();
            document.getElementById('loader').classList.remove('hidden');
            document.getElementById('error-msg').classList.add('hidden');
            
            const formData = new FormData(e.target);
            try {
                const res = await fetch('login.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                
                if (data.status === 'success') {
                    window.location.href = 'index.php';
                } else {
                    document.querySelector('#error-msg span').textContent = data.msg;
                    document.getElementById('error-msg').classList.remove('hidden');
                    document.getElementById('loader').classList.add('hidden');
                }
            } catch (err) {
                document.querySelector('#error-msg span').textContent = "Something went wrong!";
                document.getElementById('error-msg').classList.remove('hidden');
                document.getElementById('loader').classList.add('hidden');
            }
        }
    </script>
</body>
</html>
