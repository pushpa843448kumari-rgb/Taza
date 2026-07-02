<!-- Sidebar Mobile -->
<div class="lg:hidden bg-white text-slate-800 p-4 flex justify-between items-center shadow-sm sticky top-0 z-40 border-b border-slate-100">
    <button onclick="document.getElementById('admin-sidebar').classList.toggle('-translate-x-full')" class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
        <i class="fa-solid fa-bars"></i>
    </button>
    <h1 class="text-xl font-black text-slate-800 tracking-tight">Admin</h1>
    <a href="setting.php" class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 hover:text-orange-500 transition-colors"><i class="fa-solid fa-cog"></i></a>
</div>

<!-- Sidebar Menu -->
<div id="admin-sidebar" class="fixed inset-y-0 left-0 bg-white w-64 shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 lg:h-screen lg:shadow-none lg:border-r lg:border-slate-100 lg:sticky lg:top-0 flex flex-col">
    <div class="bg-slate-50 text-slate-800 p-5 flex justify-between items-center lg:hidden border-b border-slate-100">
        <h2 class="text-lg font-black tracking-tight">Menu</h2>
        <button onclick="document.getElementById('admin-sidebar').classList.add('-translate-x-full')" class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center text-slate-600">
            <i class="fa-solid fa-times text-sm"></i>
        </button>
    </div>
    <div class="hidden lg:flex items-center justify-center p-6 border-b border-slate-100">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Quick Kart<span class="text-[10px] font-black uppercase tracking-wider bg-orange-100 text-orange-600 px-2 py-0.5 rounded-md ml-2 align-middle">Admin</span></h2>
    </div>
    
    <ul class="py-4 flex-1 space-y-1 px-3">
        <li><a href="index.php" class="flex items-center px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF'])=='index.php'?'bg-orange-50 text-orange-600 font-bold':'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-800'; ?>"><i class="fa-solid fa-chart-pie w-6"></i> Dashboard</a></li>
        <li><a href="category.php" class="flex items-center px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF'])=='category.php'?'bg-orange-50 text-orange-600 font-bold':'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-800'; ?>"><i class="fa-solid fa-tags w-6"></i> Categories</a></li>
        <li><a href="product.php" class="flex items-center px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF'])=='product.php'?'bg-orange-50 text-orange-600 font-bold':'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-800'; ?>"><i class="fa-solid fa-box w-6"></i> Products</a></li>
        <li><a href="order.php" class="flex items-center px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF'])=='order.php'?'bg-orange-50 text-orange-600 font-bold':'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-800'; ?>"><i class="fa-solid fa-shopping-cart w-6"></i> Orders</a></li>
        <li><a href="user.php" class="flex items-center px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF'])=='user.php'?'bg-orange-50 text-orange-600 font-bold':'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-800'; ?>"><i class="fa-solid fa-users w-6"></i> Users</a></li>
        <li><a href="setting.php" class="flex items-center px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF'])=='setting.php'?'bg-orange-50 text-orange-600 font-bold':'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-800'; ?>"><i class="fa-solid fa-cog w-6"></i> Settings</a></li>
    </ul>
    
    <div class="p-4 border-t border-slate-100">
        <a href="login.php?logout=1" class="flex justify-center items-center w-full bg-red-50 text-red-600 font-bold py-3.5 rounded-xl hover:bg-red-100 transition-colors"><i class="fa-solid fa-power-off mr-2"></i> Logout</a>
    </div>
</div>

<div class="flex-1 w-full min-h-screen relative">
