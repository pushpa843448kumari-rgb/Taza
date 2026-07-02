<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 bg-white w-64 shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out border-r border-slate-100">
    <div class="bg-slate-50 text-slate-800 p-5 flex justify-between items-center border-b border-slate-100">
        <h2 class="text-lg font-black uppercase tracking-widest text-orange-500">Menu</h2>
        <button onclick="document.getElementById('sidebar').classList.add('-translate-x-full')" class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center text-slate-600">
            <i class="fa-solid fa-times"></i>
        </button>
    </div>
    <ul class="py-4 text-sm font-bold text-slate-600">
        <li><a href="index.php" class="block px-6 py-4 hover:bg-slate-50 transition-colors"><i class="fa-solid fa-house mr-4 text-slate-400"></i> Home</a></li>
        <li><a href="product.php" class="block px-6 py-4 hover:bg-slate-50 transition-colors"><i class="fa-solid fa-box mr-4 text-slate-400"></i> Shop</a></li>
        <li><a href="cart.php" class="block px-6 py-4 hover:bg-slate-50 transition-colors"><i class="fa-solid fa-bag-shopping mr-4 text-slate-400"></i> Cart</a></li>
        <li><a href="order.php" class="block px-6 py-4 hover:bg-slate-50 transition-colors"><i class="fa-solid fa-list mr-4 text-slate-400"></i> My Orders</a></li>
        <li><a href="profile.php" class="block px-6 py-4 hover:bg-slate-50 transition-colors"><i class="fa-solid fa-user mr-4 text-slate-400"></i> Profile</a></li>
        <?php if(isset($_SESSION['user_id'])): ?>
        <li><a href="login.php?logout=1" class="block px-6 py-4 text-orange-500 hover:bg-orange-50 transition-colors"><i class="fa-solid fa-power-off mr-4"></i> Logout</a></li>
        <?php else: ?>
        <li><a href="login.php" class="block px-6 py-4 text-green-500 hover:bg-green-50 transition-colors"><i class="fa-solid fa-right-to-bracket mr-4"></i> Login</a></li>
        <?php endif; ?>
    </ul>
</div>
<!-- Overlay -->
<div id="sidebar-overlay" onclick="document.getElementById('sidebar').classList.add('-translate-x-full')" class="hidden fixed inset-0 z-40 bg-slate-900 bg-opacity-20 backdrop-blur-sm"></div>
