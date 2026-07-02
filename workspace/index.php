<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<!-- Content -->
<div class="px-5 py-6 mb-20">
    <!-- Search Bar (Optional) -->
    <div class="mb-8 relative">
        <input type="text" placeholder="Search for products..." class="w-full bg-white rounded-2xl py-3.5 px-6 shadow-sm border border-slate-100 outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium text-slate-700 transition-shadow">
        <i class="fa-solid fa-search absolute right-5 top-4 text-orange-400"></i>
    </div>

    <!-- Top Categories (Horizontal Scroll) -->
    <div class="mb-10">
        <div class="flex justify-between items-end mb-5">
            <h2 class="text-xl font-black text-slate-800 tracking-tight">Categories</h2>
            <a href="product.php" class="text-sm text-orange-500 font-bold hover:text-orange-600 transition-colors">View All</a>
        </div>
        <div class="flex overflow-x-auto space-x-5 pb-3 scrollbar-hide">
            <?php
            if ($db_selected) {
                $res = $conn->query("SELECT * FROM categories LIMIT 6");
                if ($res && $res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $img = !empty($row['image']) ? "assets/images/categories/".$row['image'] : "https://via.placeholder.com/100";
                        echo "<a href='product.php?cat_id={$row['id']}' class='flex-shrink-0 flex flex-col items-center group'>";
                        echo "<div class='w-16 h-16 rounded-2xl bg-gradient-to-tr from-orange-100 to-amber-50 shadow-sm border border-orange-100 flex items-center justify-center overflow-hidden mb-3 group-hover:scale-105 transition-transform duration-300'>";
                        echo "<img src='{$img}' class='w-full h-full object-cover mix-blend-multiply'>";
                        echo "</div>";
                        echo "<span class='text-xs font-bold text-slate-600 group-hover:text-orange-500 transition-colors'>{$row['name']}</span>";
                        echo "</a>";
                    }
                } else {
                    echo "<p class='text-sm text-slate-500'>No categories found.</p>";
                }
            }
            ?>
        </div>
    </div>

    <!-- Featured Products Grid -->
    <div>
        <div class="flex justify-between items-end mb-5">
            <h2 class="text-xl font-black text-slate-800 tracking-tight">Featured Products</h2>
            <a href="product.php" class="text-sm text-orange-500 font-bold hover:text-orange-600 transition-colors">See All</a>
        </div>
        <div class="grid grid-cols-2 gap-5">
            <?php
            if ($db_selected) {
                $res = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 10");
                if ($res && $res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $img = !empty($row['image']) ? "assets/images/products/".$row['image'] : "https://via.placeholder.com/200";
                        $price = formatPrice($row['price']);
                        echo "<div class='bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col group'>";
                        echo "<a href='product_detail.php?id={$row['id']}' class='flex-grow'>";
                        echo "<div class='relative overflow-hidden bg-slate-50 aspect-square'>";
                        echo "<img src='{$img}' class='w-full h-full object-cover group-hover:scale-105 transition-transform duration-500'>";
                        echo "</div>";
                        echo "<div class='p-4 pb-2'>";
                        echo "<h3 class='text-sm font-bold text-slate-800 leading-tight mb-1'>{$row['name']}</h3>";
                        echo "<p class='text-orange-500 font-black text-base'>{$price}</p>";
                        echo "</div></a>";
                        echo "<div class='px-4 pb-4 mt-auto'>";
                        echo "<button onclick='addToCart({$row['id']})' class='w-full bg-orange-50 text-orange-600 text-xs font-bold py-2.5 rounded-xl hover:bg-orange-500 hover:text-white transition-colors flex items-center justify-center gap-2'><i class='fa-solid fa-cart-plus'></i> Add</button>";
                        echo "</div></div>";
                    }
                } else {
                    echo "<p class='text-sm text-slate-500 col-span-2 text-center py-6 bg-white rounded-2xl border border-slate-100'>No products available.</p>";
                }
            }
            ?>
        </div>
    </div>
</div>

<script>
    async function addToCart(id) {
        showLoader();
        try {
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', id);
            formData.append('qty', 1);
            
            const res = await fetch('cart.php', { method: 'POST', body: formData });
            const data = await res.json();
            if(data.status === 'success') {
                alert('Added to cart!');
                location.reload();
            }
        } catch(e) {
            console.error(e);
        }
        hideLoader();
    }
</script>

<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>

<?php require_once 'common/bottom.php'; ?>
