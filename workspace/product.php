<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<!-- Content -->
<div class="px-5 py-6 mb-20">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-xl font-black text-slate-800 tracking-tight">All Products</h2>
        <button onclick="document.getElementById('filter-modal').classList.remove('hidden')" class="bg-white border border-slate-100 shadow-sm px-4 py-2 rounded-xl text-sm font-bold text-slate-700 hover:text-orange-500 hover:border-orange-200 transition-colors flex items-center">
            <i class="fa-solid fa-filter mr-2"></i> Filter
        </button>
    </div>

    <div class="grid grid-cols-2 gap-5" id="product-list">
        <?php
        if ($db_selected) {
            $cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'new';
            
            $query = "SELECT * FROM products";
            if ($cat_id > 0) $query .= " WHERE cat_id = $cat_id";
            if ($sort == 'price_low') $query .= " ORDER BY price ASC";
            else if ($sort == 'price_high') $query .= " ORDER BY price DESC";
            else $query .= " ORDER BY id DESC";

            $res = $conn->query($query);
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
                echo "<p class='text-sm text-slate-500 col-span-2 text-center py-8 bg-white rounded-2xl border border-slate-100'>No products found.</p>";
            }
        }
        ?>
    </div>
</div>

<!-- Filter Modal -->
<div id="filter-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center">
    <div class="absolute inset-0 bg-slate-900 bg-opacity-20 backdrop-blur-sm" onclick="document.getElementById('filter-modal').classList.add('hidden')"></div>
    <div class="bg-white w-full sm:w-96 rounded-t-3xl sm:rounded-3xl p-6 relative z-10 transform transition-transform">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-black text-xl text-slate-800">Filters</h3>
            <button onclick="document.getElementById('filter-modal').classList.add('hidden')" class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <form method="GET" action="product.php">
            <div class="mb-5">
                <label class="block text-sm font-bold text-slate-700 mb-2">Category</label>
                <select name="cat_id" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all font-medium text-slate-700 appearance-none">
                    <option value="0">All Categories</option>
                    <?php
                    if ($db_selected) {
                        $cats = $conn->query("SELECT * FROM categories");
                        while($c = $cats->fetch_assoc()) {
                            $sel = (isset($_GET['cat_id']) && $_GET['cat_id'] == $c['id']) ? 'selected' : '';
                            echo "<option value='{$c['id']}' $sel>{$c['name']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-8">
                <label class="block text-sm font-bold text-slate-700 mb-2">Sort By</label>
                <select name="sort" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all font-medium text-slate-700 appearance-none">
                    <option value="new" <?php echo (isset($_GET['sort']) && $_GET['sort']=='new')?'selected':'';?>>Newest First</option>
                    <option value="price_low" <?php echo (isset($_GET['sort']) && $_GET['sort']=='price_low')?'selected':'';?>>Price: Low to High</option>
                    <option value="price_high" <?php echo (isset($_GET['sort']) && $_GET['sort']=='price_high')?'selected':'';?>>Price: High to Low</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-orange-500 text-white font-bold py-3.5 rounded-2xl hover:bg-orange-600 shadow-md shadow-orange-500/20 transition-all">Apply Filters</button>
        </form>
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
        } catch(e) {}
        hideLoader();
    }
</script>

<?php require_once 'common/bottom.php'; ?>
