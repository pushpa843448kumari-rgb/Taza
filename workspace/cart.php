<?php
require_once 'config.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle AJAX POST for Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($action === 'add') {
        $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $qty;
        } else {
            $_SESSION['cart'][$product_id] = $qty;
        }
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($action === 'update') {
        $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
        if ($qty > 0) {
            $_SESSION['cart'][$product_id] = $qty;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($action === 'remove') {
        unset($_SESSION['cart'][$product_id]);
        echo json_encode(['status' => 'success']);
        exit;
    }
}
?>
<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<div class="px-5 py-6 pb-40">
    <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-8">My Cart</h2>

    <?php
    $total = 0;
    if (empty($_SESSION['cart'])) {
        echo "<div class='text-center py-16 bg-slate-50 rounded-[2rem] border border-slate-100'>";
        echo "<div class='w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto shadow-sm mb-6'>";
        echo "<i class='fa-solid fa-bag-shopping text-4xl text-slate-200'></i>";
        echo "</div>";
        echo "<h3 class='text-lg font-black text-slate-800 mb-2'>Your cart is empty</h3>";
        echo "<p class='text-slate-500 font-medium mb-6 text-sm'>Looks like you haven't added anything yet.</p>";
        echo "<a href='index.php' class='inline-block bg-orange-500 text-white px-8 py-3.5 rounded-2xl font-bold shadow-md shadow-orange-500/20 hover:bg-orange-600 hover:scale-105 transition-all'>Start Shopping</a>";
        echo "</div>";
    } else {
        $ids = implode(',', array_keys($_SESSION['cart']));
        if ($db_selected) {
            $res = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
            while ($row = $res->fetch_assoc()) {
                $pid = $row['id'];
                $qty = $_SESSION['cart'][$pid];
                $subtotal = $row['price'] * $qty;
                $total += $subtotal;
                $img = !empty($row['image']) ? "assets/images/products/".$row['image'] : "https://via.placeholder.com/100";
                
                echo "<div class='bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 p-4 flex mb-5 relative group'>";
                echo "<button onclick='removeCartItem({$pid})' class='absolute top-4 right-4 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors'><i class='fa-solid fa-trash-can text-sm'></i></button>";
                echo "<div class='w-24 h-24 bg-slate-50 rounded-2xl overflow-hidden mr-5 flex-shrink-0'>";
                echo "<img src='{$img}' class='w-full h-full object-cover mix-blend-multiply'>";
                echo "</div>";
                echo "<div class='flex-1 flex flex-col justify-between py-1'>";
                echo "<h3 class='text-base font-bold text-slate-800 pr-8 leading-tight mb-2'>{$row['name']}</h3>";
                echo "<div class='flex items-center justify-between mt-auto'>";
                echo "<span class='text-orange-500 font-black text-lg'>".formatPrice($row['price'])."</span>";
                echo "<div class='flex items-center bg-slate-50 border border-slate-200 rounded-xl h-9 w-24 justify-between px-1'>";
                echo "<button onclick='updateCartItem({$pid}, -1, {$qty})' class='w-8 h-8 flex items-center justify-center text-slate-500 font-bold hover:text-orange-500 hover:bg-slate-100 rounded-lg transition-colors'>-</button>";
                echo "<span class='text-sm font-black text-slate-800'>{$qty}</span>";
                echo "<button onclick='updateCartItem({$pid}, 1, {$qty})' class='w-8 h-8 flex items-center justify-center text-slate-500 font-bold hover:text-orange-500 hover:bg-slate-100 rounded-lg transition-colors'>+</button>";
                echo "</div></div></div></div>";
            }
        }
    }
    ?>

    <?php if ($total > 0): ?>
    <div class="fixed bottom-0 left-0 w-full bg-white/90 backdrop-blur-md border-t border-slate-100 px-6 py-5 z-30 shadow-[0_-8px_30px_rgba(0,0,0,0.04)] rounded-t-[2.5rem] pb-safe pb-[80px]">
        <div class="flex justify-between items-end mb-5 px-2">
            <span class="text-slate-500 font-bold text-sm uppercase tracking-wider">Total</span>
            <span class="text-3xl font-black text-slate-800 leading-none"><?php echo formatPrice($total); ?></span>
        </div>
        <a href="checkout.php" class="w-full flex items-center justify-center bg-gradient-to-r from-orange-500 to-amber-500 text-white font-black py-4 rounded-2xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:scale-[1.02] transition-all text-lg">
            Proceed to Checkout <i class="fa-solid fa-arrow-right ml-2"></i>
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
    async function updateCartItem(id, change, current) {
        let newQty = current + change;
        if(newQty < 1) newQty = 1;
        if(newQty === current) return;
        
        showLoader();
        try {
            const fd = new FormData();
            fd.append('action', 'update');
            fd.append('product_id', id);
            fd.append('qty', newQty);
            await fetch('cart.php', { method: 'POST', body: fd });
            location.reload();
        } catch(e) {}
        hideLoader();
    }

    async function removeCartItem(id) {
        if(!confirm('Remove this item?')) return;
        showLoader();
        try {
            const fd = new FormData();
            fd.append('action', 'remove');
            fd.append('product_id', id);
            await fetch('cart.php', { method: 'POST', body: fd });
            location.reload();
        } catch(e) {}
        hideLoader();
    }
</script>

<?php require_once 'common/bottom.php'; ?>
