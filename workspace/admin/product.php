<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $name = $conn->real_escape_string($_POST['name']);
        $cat_id = (int)$_POST['cat_id'];
        $desc = $conn->real_escape_string($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        $img = "";
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $img = uniqid().".".$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/products/".$img);
        }
        
        if ($action === 'add') {
            $conn->query("INSERT INTO products (name, cat_id, description, price, stock, image) VALUES ('$name', $cat_id, '$desc', $price, $stock, '$img')");
        } else {
            if ($img != "") {
                $conn->query("UPDATE products SET name='$name', cat_id=$cat_id, description='$desc', price=$price, stock=$stock, image='$img' WHERE id=$id");
            } else {
                $conn->query("UPDATE products SET name='$name', cat_id=$cat_id, description='$desc', price=$price, stock=$stock WHERE id=$id");
            }
        }
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM products WHERE id=$id");
        echo json_encode(['status' => 'success']);
        exit;
    }
}
?>
<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Products</h2>
        <button onclick="openModal('add')" class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black py-2.5 px-5 rounded-2xl shadow-lg shadow-orange-500/30 transition-all hover:scale-105">
            <i class="fa-solid fa-plus mr-1"></i> Add New
        </button>
    </div>

    <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase tracking-wider font-black">
                        <th class="p-5 w-24">Image</th>
                        <th class="p-5">Name</th>
                        <th class="p-5">Price</th>
                        <th class="p-5">Stock</th>
                        <th class="p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($db_selected) {
                        $res = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.cat_id=c.id ORDER BY p.id DESC");
                        while($row = $res->fetch_assoc()) {
                            $img = !empty($row['image']) ? "../assets/images/products/".$row['image'] : "https://via.placeholder.com/50";
                            echo "<tr class='border-b border-slate-50 hover:bg-slate-50/50 transition-colors'>";
                            echo "<td class='p-5'><img src='{$img}' class='w-12 h-12 rounded-xl shadow-sm object-cover border border-slate-100'></td>";
                            echo "<td class='p-5'><div class='font-bold text-slate-800'>{$row['name']}</div><div class='text-[10px] font-black uppercase tracking-wider text-slate-400 mt-1'>{$row['cat_name']}</div></td>";
                            echo "<td class='p-5 font-black text-orange-500'>".formatPrice($row['price'])."</td>";
                            echo "<td class='p-5 font-bold text-slate-600'>{$row['stock']}</td>";
                            
                            // We serialize the row to pass it easily to edit modal
                            $r = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            echo "<td class='p-5 text-right'>";
                            echo "<button onclick='openModal(\"edit\", $r)' class='text-blue-500 hover:text-blue-600 bg-blue-50 hover:bg-blue-100 w-10 h-10 rounded-xl inline-flex items-center justify-center mr-2 transition-colors'><i class='fa-solid fa-edit'></i></button>";
                            echo "<button onclick='deleteProduct({$row['id']})' class='text-red-500 hover:text-red-600 bg-red-50 hover:bg-red-100 w-10 h-10 rounded-xl inline-flex items-center justify-center transition-colors'><i class='fa-solid fa-trash'></i></button>";
                            echo "</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="prod-modal" class="hidden fixed inset-0 bg-slate-900 bg-opacity-20 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] overflow-y-auto transform transition-all scale-100">
        <div class="p-6 border-b border-slate-100 bg-white/80 backdrop-blur-md flex justify-between items-center sticky top-0 z-10">
            <h3 id="modal-title" class="font-black text-xl text-slate-800 tracking-tight">Add Product</h3>
            <button onclick="closeModal()" class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors"><i class="fa-solid fa-times text-sm"></i></button>
        </div>
        <form id="prod-form" onsubmit="saveProduct(event)" class="p-6">
            <input type="hidden" name="action" id="modal-action" value="add">
            <input type="hidden" name="id" id="modal-id" value="">
            
            <div class="mb-5">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Product Name</label>
                <input type="text" name="name" id="modal-name" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
            </div>
            
            <div class="grid grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Category</label>
                    <select name="cat_id" id="modal-cat_id" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
                        <?php
                        if ($db_selected) {
                            $cats = $conn->query("SELECT * FROM categories");
                            while($c = $cats->fetch_assoc()) {
                                echo "<option value='{$c['id']}'>{$c['name']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Price</label>
                    <input type="number" step="0.01" name="price" id="modal-price" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Stock</label>
                <input type="number" name="stock" id="modal-stock" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
            </div>
            
            <div class="mb-5">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Description</label>
                <textarea name="description" id="modal-desc" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all resize-none"></textarea>
            </div>
            
            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Image</label>
                <input type="file" name="image" accept="image/*" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-500 font-medium outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100">
            </div>
            
            <button type="submit" class="w-full bg-slate-800 text-white font-black py-4 rounded-2xl shadow-lg hover:bg-slate-900 transition-all text-lg">Save Product</button>
        </form>
    </div>
</div>

<script>
function openModal(action, data=null) {
    document.getElementById('modal-action').value = action;
    document.getElementById('modal-title').textContent = action === 'add' ? 'Add Product' : 'Edit Product';
    
    if(action === 'edit' && data) {
        document.getElementById('modal-id').value = data.id;
        document.getElementById('modal-name').value = data.name;
        document.getElementById('modal-cat_id').value = data.cat_id;
        document.getElementById('modal-price').value = data.price;
        document.getElementById('modal-stock').value = data.stock;
        document.getElementById('modal-desc').value = data.description;
    } else {
        document.getElementById('prod-form').reset();
        document.getElementById('modal-id').value = '';
        document.getElementById('modal-action').value = 'add';
    }
    
    document.getElementById('prod-modal').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('prod-modal').classList.add('hidden');
}
async function saveProduct(e) {
    e.preventDefault();
    showLoader();
    try {
        const res = await fetch('product.php', { method: 'POST', body: new FormData(e.target) });
        const data = await res.json();
        if(data.status==='success') location.reload();
    } catch(e) {}
    hideLoader();
}
async function deleteProduct(id) {
    if(!confirm('Are you sure?')) return;
    showLoader();
    try {
        const fd = new FormData();
        fd.append('action', 'delete'); fd.append('id', id);
        const res = await fetch('product.php', { method: 'POST', body: fd });
        const data = await res.json();
        if(data.status==='success') location.reload();
    } catch(e) {}
    hideLoader();
}
</script>

<?php require_once 'common/bottom.php'; ?>
