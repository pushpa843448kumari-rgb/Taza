<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Handle Order Placement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $total = 0;
    
    // Update user info if needed
    $conn->query("UPDATE users SET address='$address', phone='$phone' WHERE id=$user_id");

    $ids = implode(',', array_keys($_SESSION['cart']));
    $res = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = [];
    while($r = $res->fetch_assoc()) {
        $qty = $_SESSION['cart'][$r['id']];
        $total += ($r['price'] * $qty);
        $products[] = $r;
    }

    $conn->query("INSERT INTO orders (user_id, total_amount, status) VALUES ($user_id, $total, 'Placed')");
    $order_id = $conn->insert_id;

    foreach ($products as $p) {
        $pid = $p['id'];
        $qty = $_SESSION['cart'][$pid];
        $price = $p['price'];
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $pid, $qty, $price)");
    }

    $_SESSION['cart'] = [];
    header("Location: order.php?success=1");
    exit;
}

// Get User details
$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
?>
<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<div class="px-5 py-6 pb-32">
    <div class="flex items-center mb-8">
        <button onclick="history.back()" class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center mr-4 text-slate-700 hover:text-orange-500 hover:scale-105 transition-all"><i class="fa-solid fa-arrow-left"></i></button>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Checkout</h2>
    </div>

    <form method="POST" class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 mb-6">
        <h3 class="font-black text-slate-800 mb-5 text-lg flex items-center"><i class="fa-solid fa-location-dot text-orange-500 mr-2"></i> Delivery Details</h3>
        
        <div class="mb-5">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Full Name</label>
            <input type="text" value="<?php echo $user['name']; ?>" readonly class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-500 font-medium outline-none">
        </div>
        
        <div class="mb-5">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Phone Number</label>
            <input type="tel" name="phone" value="<?php echo $user['phone']; ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
        </div>
        
        <div class="mb-6">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Delivery Address</label>
            <textarea name="address" rows="3" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all resize-none"><?php echo $user['address']; ?></textarea>
        </div>

        <h3 class="font-black text-slate-800 mt-8 mb-5 text-lg flex items-center"><i class="fa-solid fa-credit-card text-orange-500 mr-2"></i> Payment Method</h3>
        <label class="border-2 border-orange-500 bg-orange-50 rounded-2xl p-4 flex items-center mb-8 cursor-pointer relative overflow-hidden group">
            <div class="absolute inset-0 bg-orange-500 opacity-0 group-hover:opacity-5 transition-opacity"></div>
            <input type="radio" checked class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-slate-300">
            <span class="ml-3 font-bold text-slate-800">Cash on Delivery (COD)</span>
            <i class="fa-solid fa-money-bill-wave ml-auto text-emerald-500 text-2xl"></i>
        </label>

        <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-500 text-white font-black py-4 rounded-2xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:scale-[1.02] transition-all text-lg flex items-center justify-center">
            Confirm Order <i class="fa-solid fa-check ml-2"></i>
        </button>
    </form>
</div>

<?php require_once 'common/bottom.php'; ?>
