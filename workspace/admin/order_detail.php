<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $id = (int)$_POST['id'];
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE orders SET status='$status' WHERE id=$id");
    echo json_encode(['status'=>'success']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($db_selected) {
    $order = $conn->query("SELECT o.*, u.name as user_name, u.phone, u.address, u.email FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=$id")->fetch_assoc();
    $items = $conn->query("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$id");
}
if (!$order) {
    echo "Order not found!";
    exit;
}
?>
<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center">
            <a href="order.php" class="mr-4 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-slate-500 hover:text-orange-600 hover:bg-orange-50 transition-colors border border-slate-100"><i class="fa-solid fa-arrow-left"></i></a>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Order #<?php echo $id; ?></h2>
        </div>
        <div>
            <select id="order-status" onchange="updateStatus(<?php echo $id; ?>)" class="bg-white border border-slate-200 rounded-xl px-5 py-2.5 font-bold text-slate-800 outline-none shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all cursor-pointer">
                <option value="Placed" <?php if($order['status']=='Placed') echo 'selected'; ?>>Placed</option>
                <option value="Dispatched" <?php if($order['status']=='Dispatched') echo 'selected'; ?>>Dispatched</option>
                <option value="Delivered" <?php if($order['status']=='Delivered') echo 'selected'; ?>>Delivered</option>
                <option value="Cancelled" <?php if($order['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Customer Info -->
        <div class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 p-6 h-max">
            <h3 class="font-black text-slate-800 mb-5 pb-4 border-b border-slate-100 text-lg flex items-center"><div class="w-8 h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center mr-3"><i class="fa-solid fa-user"></i></div> Customer</h3>
            <div class="space-y-4">
                <div>
                    <strong class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Name</strong>
                    <p class="font-bold text-slate-800"><?php echo $order['user_name']; ?></p>
                </div>
                <div>
                    <strong class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Email</strong>
                    <p class="font-medium text-slate-600"><?php echo $order['email']; ?></p>
                </div>
                <div>
                    <strong class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Phone</strong>
                    <p class="font-medium text-slate-600"><?php echo $order['phone']; ?></p>
                </div>
                <div class="pt-4 border-t border-slate-50">
                    <strong class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">Delivery Address</strong>
                    <p class="text-sm font-medium text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100 leading-relaxed"><?php echo nl2br($order['address']); ?></p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="md:col-span-2 bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 p-6">
            <h3 class="font-black text-slate-800 mb-5 pb-4 border-b border-slate-100 text-lg flex items-center"><div class="w-8 h-8 bg-orange-50 text-orange-500 rounded-lg flex items-center justify-center mr-3"><i class="fa-solid fa-box-open"></i></div> Order Items</h3>
            <div class="space-y-5">
                <?php while($item = $items->fetch_assoc()): 
                    $img = !empty($item['image']) ? "../assets/images/products/".$item['image'] : "https://via.placeholder.com/100";
                ?>
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-all hover:border-orange-200 hover:shadow-sm">
                    <div class="flex items-center">
                        <img src="<?php echo $img; ?>" class="w-16 h-16 object-cover rounded-xl mr-4 shadow-sm">
                        <div>
                            <h4 class="font-bold text-slate-800 mb-1"><?php echo $item['name']; ?></h4>
                            <p class="text-[11px] font-black uppercase tracking-wider text-slate-500">Qty: <?php echo $item['quantity']; ?> × <span class="text-slate-600"><?php echo formatPrice($item['price']); ?></span></p>
                        </div>
                    </div>
                    <div class="font-black text-lg text-slate-800">
                        <?php echo formatPrice($item['quantity'] * $item['price']); ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <div class="mt-6 border-t border-slate-100 pt-6 flex justify-end">
                <div class="bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-2xl p-5 flex items-center shadow-lg shadow-orange-500/20">
                    <span class="text-sm font-bold opacity-90 mr-4 uppercase tracking-wider">Total Amount</span>
                    <span class="text-3xl font-black tracking-tight"><?php echo formatPrice($order['total_amount']); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function updateStatus(id) {
    let status = document.getElementById('order-status').value;
    showLoader();
    try {
        let fd = new FormData();
        fd.append('id', id);
        fd.append('status', status);
        let res = await fetch('order_detail.php', {method:'POST', body:fd});
        let data = await res.json();
        if(data.status==='success') { location.reload(); }
    } catch(e) {}
    hideLoader();
}
</script>

<?php require_once 'common/bottom.php'; ?>
