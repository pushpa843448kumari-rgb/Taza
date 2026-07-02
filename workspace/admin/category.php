<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $name = $conn->real_escape_string($_POST['name']);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        $img = "";
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $img = uniqid().".".$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/categories/".$img);
        }
        
        if ($action === 'add') {
            $conn->query("INSERT INTO categories (name, image) VALUES ('$name', '$img')");
        } else {
            if ($img != "") {
                $conn->query("UPDATE categories SET name='$name', image='$img' WHERE id=$id");
            } else {
                $conn->query("UPDATE categories SET name='$name' WHERE id=$id");
            }
        }
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM categories WHERE id=$id");
        echo json_encode(['status' => 'success']);
        exit;
    }
}
?>
<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Categories</h2>
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
                        <th class="p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($db_selected) {
                        $res = $conn->query("SELECT * FROM categories ORDER BY id DESC");
                        while($row = $res->fetch_assoc()) {
                            $img = !empty($row['image']) ? "../assets/images/categories/".$row['image'] : "https://via.placeholder.com/50";
                            echo "<tr class='border-b border-slate-50 hover:bg-slate-50/50 transition-colors'>";
                            echo "<td class='p-5'><img src='{$img}' class='w-12 h-12 rounded-xl shadow-sm object-cover border border-slate-100'></td>";
                            echo "<td class='p-5 font-bold text-slate-800'>{$row['name']}</td>";
                            echo "<td class='p-5 text-right'>";
                            echo "<button onclick='openModal(\"edit\", {$row['id']}, \"".addslashes($row['name'])."\")' class='text-blue-500 hover:text-blue-600 bg-blue-50 hover:bg-blue-100 w-10 h-10 rounded-xl inline-flex items-center justify-center mr-2 transition-colors'><i class='fa-solid fa-edit'></i></button>";
                            echo "<button onclick='deleteCat({$row['id']})' class='text-red-500 hover:text-red-600 bg-red-50 hover:bg-red-100 w-10 h-10 rounded-xl inline-flex items-center justify-center transition-colors'><i class='fa-solid fa-trash'></i></button>";
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
<div id="cat-modal" class="hidden fixed inset-0 bg-slate-900 bg-opacity-20 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 id="modal-title" class="font-black text-xl text-slate-800 tracking-tight">Add Category</h3>
            <button onclick="closeModal()" class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors"><i class="fa-solid fa-times text-sm"></i></button>
        </div>
        <form id="cat-form" onsubmit="saveCat(event)" class="p-6">
            <input type="hidden" name="action" id="modal-action" value="add">
            <input type="hidden" name="id" id="modal-id" value="">
            
            <div class="mb-5">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Category Name</label>
                <input type="text" name="name" id="modal-name" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
            </div>
            
            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Image</label>
                <input type="file" name="image" accept="image/*" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-500 font-medium outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100">
            </div>
            
            <button type="submit" class="w-full bg-slate-800 text-white font-black py-4 rounded-2xl shadow-lg hover:bg-slate-900 transition-all text-lg">Save Category</button>
        </form>
    </div>
</div>

<script>
function openModal(action, id='', name='') {
    document.getElementById('modal-action').value = action;
    document.getElementById('modal-id').value = id;
    document.getElementById('modal-name').value = name;
    document.getElementById('modal-title').textContent = action === 'add' ? 'Add Category' : 'Edit Category';
    document.getElementById('cat-modal').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('cat-modal').classList.add('hidden');
    document.getElementById('cat-form').reset();
}
async function saveCat(e) {
    e.preventDefault();
    showLoader();
    try {
        const res = await fetch('category.php', { method: 'POST', body: new FormData(e.target) });
        const data = await res.json();
        if(data.status==='success') location.reload();
    } catch(e) {}
    hideLoader();
}
async function deleteCat(id) {
    if(!confirm('Are you sure?')) return;
    showLoader();
    try {
        const fd = new FormData();
        fd.append('action', 'delete'); fd.append('id', id);
        const res = await fetch('category.php', { method: 'POST', body: fd });
        const data = await res.json();
        if(data.status==='success') location.reload();
    } catch(e) {}
    hideLoader();
}
</script>

<?php require_once 'common/bottom.php'; ?>
