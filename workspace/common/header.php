<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Quick Kart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            overflow-x: hidden;
            background-color: #f3f4f6;
        }
    </style>
    <script>
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 85 || e.keyCode === 117)) {
                e.preventDefault();
            }
        });
    </script>
</head>
<body class="pb-20 text-slate-800 bg-slate-50 font-sans">
    <!-- Top Nav -->
    <div class="bg-white px-4 py-3 flex justify-between items-center sticky top-0 z-50 shadow-sm border-b border-slate-100">
        <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-800">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h1 class="text-orange-500 font-black text-2xl tracking-tighter italic">Quick Kart</h1>
        <a href="cart.php" class="relative w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center text-orange-600">
            <i class="fa-solid fa-cart-shopping"></i>
            <?php
            $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
            if ($cart_count > 0) {
                echo "<span class='absolute -top-1 -right-1 bg-slate-900 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center border-2 border-white'>$cart_count</span>";
            }
            ?>
        </a>
    </div>

    <!-- AJAX Loader Modal -->
    <div id="loader" class="hidden fixed inset-0 bg-slate-900 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-3xl shadow-2xl flex flex-col items-center border border-slate-100">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-200 border-t-orange-500 mb-3"></div>
            <p class="text-xs font-bold text-slate-800 uppercase tracking-widest">Loading...</p>
        </div>
    </div>
