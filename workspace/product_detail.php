<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>
<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
if ($db_selected) {
    $res = $conn->query("SELECT * FROM products WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $product = $res->fetch_assoc();
    }
}
if (!$product) {
    echo "<div class='p-6 text-center text-red-500 font-bold'>Product not found!</div>";
    require_once 'common/bottom.php';
    exit;
}
$img = !empty($product['image']) ? "assets/images/products/".$product['image'] : "https://via.placeholder.com/400";
?>

<div class="pb-24">
    <div class="w-full h-80 bg-slate-50 relative overflow-hidden">
        <button onclick="history.back()" class="absolute top-4 left-4 w-10 h-10 bg-white/80 backdrop-blur-sm rounded-full shadow-sm flex items-center justify-center z-10 text-slate-700 hover:text-orange-500 hover:scale-105 transition-all">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <img src="<?php echo $img; ?>" class="w-full h-full object-cover">
    </div>
    
    <div class="px-6 py-8 bg-white rounded-t-[2.5rem] -mt-8 relative z-10 shadow-[0_-8px_20px_rgba(0,0,0,0.02)] min-h-[50vh]">
        <div class="flex justify-between items-start mb-3">
            <h1 class="text-2xl font-black text-slate-800 leading-tight w-2/3 tracking-tight"><?php echo $product['name']; ?></h1>
            <span class="text-2xl font-black text-orange-500"><?php echo formatPrice($product['price']); ?></span>
        </div>
        
        <div class="mb-6">
            <?php if($product['stock'] > 0): ?>
                <span class="bg-emerald-100 text-emerald-700 text-[11px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full inline-flex items-center"><i class="fa-solid fa-check-circle mr-1.5"></i> In Stock (<?php echo $product['stock']; ?>)</span>
            <?php else: ?>
                <span class="bg-red-100 text-red-700 text-[11px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full inline-flex items-center"><i class="fa-solid fa-times-circle mr-1.5"></i> Out of Stock</span>
            <?php endif; ?>
        </div>

        <h3 class="font-black text-slate-800 mt-8 mb-3 text-lg">Description</h3>
        <p class="text-sm text-slate-600 leading-relaxed font-medium"><?php echo nl2br($product['description']); ?></p>

        <!-- Quantity & Add to Cart Fixed Bottom -->
        <div class="fixed bottom-0 left-0 w-full bg-white/90 backdrop-blur-md border-t border-slate-100 px-5 py-4 flex items-center justify-between z-30 pb-safe pb-[70px]">
            <div class="flex items-center bg-slate-50 border border-slate-200 rounded-2xl h-14 w-36 justify-between px-2">
                <button onclick="updateQty(-1)" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-orange-500 font-bold text-xl hover:bg-slate-100 rounded-xl transition-colors">-</button>
                <input type="number" id="qty" value="1" min="1" max="<?php echo $product['stock']; ?>" class="w-10 text-center outline-none font-black text-slate-800 bg-transparent text-lg" readonly>
                <button onclick="updateQty(1)" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-orange-500 font-bold text-xl hover:bg-slate-100 rounded-xl transition-colors">+</button>
            </div>
            <button onclick="addToCartDetail()" class="ml-4 flex-1 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-black h-14 rounded-2xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:scale-[1.02] transition-all flex items-center justify-center">
                <i class="fa-solid fa-bag-shopping mr-2"></i> Add to Cart
            </button>
        </div>
    </div>
</div>

<script>
    function updateQty(change) {
        let input = document.getElementById('qty');
        let val = parseInt(input.value) + change;
        let max = parseInt(input.getAttribute('max'));
        if (val >= 1 && val <= max) input.value = val;
    }

    async function addToCartDetail() {
        let qty = document.getElementById('qty').value;
        showLoader();
        try {
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', <?php echo $product['id']; ?>);
            formData.append('qty', qty);
            const res = await fetch('cart.php', { method: 'POST', body: formData });
            const data = await res.json();
            if(data.status === 'success') {
                alert('Added to cart!');
                location.reload();
            } else {
                alert(data.msg || 'Error adding to cart');
            }
        } catch(e) {}
        hideLoader();
    }
</script>

<?php require_once 'common/bottom.php'; ?>
