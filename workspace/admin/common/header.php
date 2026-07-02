<?php require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin - Quick Kart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            -webkit-user-select: none; -khtml-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;
            overflow-x: hidden; background-color: #f8fafc;
        }
    </style>
</head>
<body class="pb-16 text-slate-800 lg:flex bg-slate-50">
    
    <!-- Loader -->
    <div id="loader" class="hidden fixed inset-0 bg-slate-900 bg-opacity-20 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-3xl shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-100 border-t-orange-500 mb-3"></div>
            <p class="text-sm font-black text-slate-800 tracking-wider uppercase">Loading...</p>
        </div>
    </div>
