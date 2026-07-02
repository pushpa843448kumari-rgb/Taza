<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$counts = ['users'=>0, 'orders'=>0, 'revenue'=>0, 'products'=>0];
if ($db_selected) {
    $counts['users'] = $conn->query("SELECT COUNT(id) as c FROM users")->fetch_assoc()['c'] ?? 0;
    $counts['orders'] = $conn->query("SELECT COUNT(id) as c FROM orders")->fetch_assoc()['c'] ?? 0;
    $counts['revenue'] = $conn->query("SELECT SUM(total_amount) as c FROM orders WHERE status='Delivered'")->fetch_assoc()['c'] ?? 0;
    $counts['products'] = $conn->query("SELECT COUNT(id) as c FROM products")->fetch_assoc()['c'] ?? 0;
}
?>
<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<div class="p-6">
    <h2 class="text-2xl font-black text-slate-800 mb-6 tracking-tight">Dashboard Overview</h2>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-3xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 flex flex-col justify-center items-center text-center relative overflow-hidden group hover:-translate-y-1 transition-transform">
            <div class="absolute inset-0 bg-blue-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-3 relative z-10 shadow-sm">
                <i class="fa-solid fa-users"></i>
            </div>
            <span class="text-3xl font-black text-slate-800 relative z-10"><?php echo $counts['users']; ?></span>
            <span class="text-[10px] text-slate-500 font-black uppercase tracking-wider mt-1 relative z-10">Users</span>
        </div>
        
        <div class="bg-white rounded-3xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 flex flex-col justify-center items-center text-center relative overflow-hidden group hover:-translate-y-1 transition-transform">
            <div class="absolute inset-0 bg-emerald-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl mb-3 relative z-10 shadow-sm">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>
            <span class="text-2xl font-black text-slate-800 relative z-10"><?php echo formatPrice($counts['revenue']); ?></span>
            <span class="text-[10px] text-slate-500 font-black uppercase tracking-wider mt-1 relative z-10">Revenue</span>
        </div>
        
        <div class="bg-white rounded-3xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 flex flex-col justify-center items-center text-center relative overflow-hidden group hover:-translate-y-1 transition-transform">
            <div class="absolute inset-0 bg-orange-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-2xl flex items-center justify-center text-2xl mb-3 relative z-10 shadow-md shadow-orange-500/30">
                <i class="fa-solid fa-shopping-cart"></i>
            </div>
            <span class="text-3xl font-black text-slate-800 relative z-10"><?php echo $counts['orders']; ?></span>
            <span class="text-[10px] text-slate-500 font-black uppercase tracking-wider mt-1 relative z-10">Orders</span>
        </div>
        
        <div class="bg-white rounded-3xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 flex flex-col justify-center items-center text-center relative overflow-hidden group hover:-translate-y-1 transition-transform">
            <div class="absolute inset-0 bg-purple-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center text-2xl mb-3 relative z-10 shadow-sm">
                <i class="fa-solid fa-box"></i>
            </div>
            <span class="text-3xl font-black text-slate-800 relative z-10"><?php echo $counts['products']; ?></span>
            <span class="text-[10px] text-slate-500 font-black uppercase tracking-wider mt-1 relative z-10">Products</span>
        </div>
    </div>
    
    <div class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 p-6">
        <h3 class="font-black text-slate-800 mb-5 text-lg">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="product.php" class="bg-slate-50 hover:bg-orange-50 hover:text-orange-600 text-slate-700 py-4 rounded-2xl text-center font-bold text-sm transition-all border border-slate-100 hover:border-orange-200 shadow-sm group"><i class="fa-solid fa-plus mr-1 text-slate-400 group-hover:text-orange-500 transition-colors"></i> Add Product</a>
            <a href="category.php" class="bg-slate-50 hover:bg-orange-50 hover:text-orange-600 text-slate-700 py-4 rounded-2xl text-center font-bold text-sm transition-all border border-slate-100 hover:border-orange-200 shadow-sm group"><i class="fa-solid fa-tags mr-1 text-slate-400 group-hover:text-orange-500 transition-colors"></i> Categories</a>
            <a href="order.php" class="bg-slate-50 hover:bg-orange-50 hover:text-orange-600 text-slate-700 py-4 rounded-2xl text-center font-bold text-sm transition-all border border-slate-100 hover:border-orange-200 shadow-sm group"><i class="fa-solid fa-truck mr-1 text-slate-400 group-hover:text-orange-500 transition-colors"></i> Shipments</a>
            <a href="user.php" class="bg-slate-50 hover:bg-orange-50 hover:text-orange-600 text-slate-700 py-4 rounded-2xl text-center font-bold text-sm transition-all border border-slate-100 hover:border-orange-200 shadow-sm group"><i class="fa-solid fa-users-cog mr-1 text-slate-400 group-hover:text-orange-500 transition-colors"></i> Manage Users</a>
        </div>
    </div>
</div>

<?php require_once 'common/bottom.php'; ?>
