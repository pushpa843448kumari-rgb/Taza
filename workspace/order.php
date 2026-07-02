<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
?>
<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<div class="px-5 py-6 pb-24">
    <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-6">My Orders</h2>

    <!-- Tabs -->
    <div class="flex border-b border-slate-200 mb-6 bg-white rounded-t-2xl px-2 pt-2">
        <button onclick="switchOrderTab('active')" id="tab-active" class="flex-1 py-3 font-black text-orange-500 border-b-2 border-orange-500 transition-colors uppercase tracking-wider text-sm">Active</button>
        <button onclick="switchOrderTab('history')" id="tab-history" class="flex-1 py-3 font-bold text-slate-400 border-b-2 border-transparent hover:text-slate-600 transition-colors uppercase tracking-wider text-sm">History</button>
    </div>

    <!-- Active Orders -->
    <div id="active-orders">
        <?php
        if ($db_selected) {
            $res = $conn->query("SELECT * FROM orders WHERE user_id=$user_id AND status IN ('Placed','Dispatched') ORDER BY id DESC");
            if ($res && $res->num_rows > 0) {
                while ($order = $res->fetch_assoc()) {
                    renderOrderCard($conn, $order);
                }
            } else {
                echo "<div class='text-center py-16 bg-slate-50 rounded-[2rem] border border-slate-100'>";
                echo "<i class='fa-solid fa-box-open text-4xl text-slate-200 mb-4'></i>";
                echo "<h3 class='text-lg font-black text-slate-800 mb-2'>No active orders</h3>";
                echo "<p class='text-slate-500 font-medium text-sm'>You don't have any ongoing orders.</p>";
                echo "</div>";
            }
        }
        ?>
    </div>

    <!-- Order History -->
    <div id="history-orders" class="hidden">
        <?php
        if ($db_selected) {
            $res = $conn->query("SELECT * FROM orders WHERE user_id=$user_id AND status IN ('Delivered','Cancelled') ORDER BY id DESC");
            if ($res && $res->num_rows > 0) {
                while ($order = $res->fetch_assoc()) {
                    renderOrderCard($conn, $order);
                }
            } else {
                echo "<div class='text-center py-16 bg-slate-50 rounded-[2rem] border border-slate-100'>";
                echo "<i class='fa-solid fa-clock-rotate-left text-4xl text-slate-200 mb-4'></i>";
                echo "<h3 class='text-lg font-black text-slate-800 mb-2'>No order history</h3>";
                echo "<p class='text-slate-500 font-medium text-sm'>Your completed orders will appear here.</p>";
                echo "</div>";
            }
        }
        ?>
    </div>
</div>

<?php
function renderOrderCard($conn, $order) {
    $oid = $order['id'];
    $items = $conn->query("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$oid");
    
    // Get first item for preview
    $first_item = $items->fetch_assoc();
    $img = !empty($first_item['image']) ? "assets/images/products/".$first_item['image'] : "https://via.placeholder.com/100";
    
    // Tracker logic
    $s = $order['status'];
    $s1 = ($s == 'Placed' || $s == 'Dispatched' || $s == 'Delivered') ? 'text-orange-500' : 'text-slate-300';
    $s2 = ($s == 'Dispatched' || $s == 'Delivered') ? 'text-orange-500' : 'text-slate-300';
    $s3 = ($s == 'Delivered') ? 'text-orange-500' : 'text-slate-300';
    
    $bg1 = ($s == 'Placed' || $s == 'Dispatched' || $s == 'Delivered') ? 'bg-orange-500' : 'bg-slate-200';
    $bg2 = ($s == 'Dispatched' || $s == 'Delivered') ? 'bg-orange-500' : 'bg-slate-200';
    
    echo "<div class='bg-white rounded-[2rem] shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 mb-6 overflow-hidden'>";
    echo "<div class='p-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center'>";
    echo "<div><span class='text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1'>Order ID</span><span class='font-black text-slate-800 bg-white px-2 py-1 rounded-lg border border-slate-100 shadow-sm'>#{$oid}</span></div>";
    echo "<div><span class='text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1 text-right'>Total</span><span class='font-black text-orange-500 text-lg'>".formatPrice($order['total_amount'])."</span></div>";
    echo "</div>";
    
    echo "<div class='p-5 flex items-center'>";
    echo "<div class='w-16 h-16 rounded-2xl bg-slate-50 overflow-hidden mr-4 flex-shrink-0 border border-slate-100'>";
    echo "<img src='{$img}' class='w-full h-full object-cover mix-blend-multiply'>";
    echo "</div>";
    echo "<div class='flex-1'>";
    echo "<h3 class='text-sm font-bold text-slate-800 line-clamp-1 mb-1'>{$first_item['name']}</h3>";
    $more = $items->num_rows;
    if ($more > 0) echo "<span class='text-xs font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md'>+ $more more</span>";
    echo "</div></div>";

    if ($s != 'Cancelled') {
        echo "<div class='px-8 pb-6 pt-2'>";
        echo "<div class='relative flex items-center justify-between w-full'>";
        // Line
        echo "<div class='absolute left-0 top-2.5 w-full h-1.5 bg-slate-100 rounded-full z-0'></div>";
        echo "<div class='absolute left-0 top-2.5 h-1.5 rounded-full transition-all duration-700 ease-out z-0 {$bg1}' style='width: ".($s=='Placed'?'50%':($s=='Dispatched'?'100%':'100%')).";'></div>";
        
        // Dots
        echo "<div class='relative z-10 flex flex-col items-center {$s1}'><div class='w-6 h-6 rounded-full bg-white border-[5px] border-current shadow-sm flex items-center justify-center mb-2 transition-colors duration-500'></div><span class='text-[10px] font-black uppercase tracking-wide mt-1'>Placed</span></div>";
        echo "<div class='relative z-10 flex flex-col items-center {$s2}'><div class='w-6 h-6 rounded-full bg-white border-[5px] border-current shadow-sm flex items-center justify-center mb-2 transition-colors duration-500'></div><span class='text-[10px] font-black uppercase tracking-wide mt-1 text-center'>Dispatched</span></div>";
        echo "<div class='relative z-10 flex flex-col items-center {$s3}'><div class='w-6 h-6 rounded-full bg-white border-[5px] border-current shadow-sm flex items-center justify-center mb-2 transition-colors duration-500'></div><span class='text-[10px] font-black uppercase tracking-wide mt-1'>Delivered</span></div>";
        echo "</div></div>";
    } else {
        echo "<div class='px-5 pb-5'><span class='bg-red-50 text-red-600 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider inline-flex items-center border border-red-100'><i class='fa-solid fa-ban mr-2'></i> Cancelled</span></div>";
    }

    echo "</div>";
}
?>

<script>
function switchOrderTab(tab) {
    if(tab === 'active') {
        document.getElementById('active-orders').classList.remove('hidden');
        document.getElementById('history-orders').classList.add('hidden');
        
        const tabActive = document.getElementById('tab-active');
        tabActive.classList.replace('text-slate-400', 'text-orange-500');
        tabActive.classList.replace('font-bold', 'font-black');
        tabActive.classList.replace('border-transparent', 'border-orange-500');
        
        const tabHistory = document.getElementById('tab-history');
        tabHistory.classList.replace('text-orange-500', 'text-slate-400');
        tabHistory.classList.replace('font-black', 'font-bold');
        tabHistory.classList.replace('border-orange-500', 'border-transparent');
    } else {
        document.getElementById('history-orders').classList.remove('hidden');
        document.getElementById('active-orders').classList.add('hidden');
        
        const tabHistory = document.getElementById('tab-history');
        tabHistory.classList.replace('text-slate-400', 'text-orange-500');
        tabHistory.classList.replace('font-bold', 'font-black');
        tabHistory.classList.replace('border-transparent', 'border-orange-500');
        
        const tabActive = document.getElementById('tab-active');
        tabActive.classList.replace('text-orange-500', 'text-slate-400');
        tabActive.classList.replace('font-black', 'font-bold');
        tabActive.classList.replace('border-orange-500', 'border-transparent');
    }
}
</script>

<?php require_once 'common/bottom.php'; ?>
