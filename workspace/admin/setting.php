<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $id = $_SESSION['admin_id'];
    $user = $conn->real_escape_string($_POST['username']);
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    
    $admin = $conn->query("SELECT * FROM admin WHERE id=$id")->fetch_assoc();
    if (password_verify($old, $admin['password'])) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE admin SET username='$user', password='$hash' WHERE id=$id");
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Incorrect old password']);
    }
    exit;
}
$admin = $conn->query("SELECT * FROM admin WHERE id=".$_SESSION['admin_id'])->fetch_assoc();
?>
<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<div class="p-6 max-w-lg">
    <h2 class="text-2xl font-black text-slate-800 mb-8 tracking-tight">Settings</h2>

    <form id="setting-form" onsubmit="updateSettings(event)" class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 p-8">
        <div id="msg" class="hidden mb-6 p-4 rounded-xl text-sm text-center font-bold"></div>
        
        <div class="mb-5">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Username</label>
            <div class="relative">
                <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="username" value="<?php echo $admin['username']; ?>" required class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:ring-2 focus:ring-orange-200 focus:border-orange-500 outline-none transition-all">
            </div>
        </div>
        
        <div class="mb-5">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Old Password</label>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="password" name="old_password" required class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:ring-2 focus:ring-orange-200 focus:border-orange-500 outline-none transition-all">
            </div>
        </div>
        
        <div class="mb-8">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">New Password</label>
            <div class="relative">
                <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="password" name="new_password" required class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:ring-2 focus:ring-orange-200 focus:border-orange-500 outline-none transition-all">
            </div>
        </div>
        
        <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-500 text-white font-black py-4 rounded-2xl shadow-lg shadow-orange-500/30 hover:scale-105 transition-all text-lg">Update Credentials</button>
    </form>
</div>

<script>
async function updateSettings(e) {
    e.preventDefault();
    showLoader();
    let msg = document.getElementById('msg');
    msg.classList.add('hidden');
    try {
        let fd = new FormData(e.target);
        let res = await fetch('setting.php', {method:'POST', body:fd});
        let data = await res.json();
        if(data.status==='success') {
            msg.className = 'mb-6 p-4 rounded-xl text-sm text-center font-bold bg-emerald-50 text-emerald-600 border border-emerald-100';
            msg.textContent = 'Settings updated successfully!';
            msg.classList.remove('hidden');
            e.target.reset();
        } else {
            msg.className = 'mb-6 p-4 rounded-xl text-sm text-center font-bold bg-red-50 text-red-600 border border-red-100';
            msg.textContent = data.msg;
            msg.classList.remove('hidden');
        }
    } catch(err) {}
    hideLoader();
}
</script>

<?php require_once 'common/bottom.php'; ?>
