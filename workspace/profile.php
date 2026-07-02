<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $name = $conn->real_escape_string($_POST['name']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $address = $conn->real_escape_string($_POST['address']);
        
        $conn->query("UPDATE users SET name='$name', phone='$phone', address='$address' WHERE id=$user_id");
        echo json_encode(['status' => 'success']);
        exit;
    }
    
    if ($action === 'update_password') {
        $old = $_POST['old_password'];
        $new = $_POST['new_password'];
        $user = $conn->query("SELECT password FROM users WHERE id=$user_id")->fetch_assoc();
        if (password_verify($old, $user['password'])) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$hash' WHERE id=$user_id");
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Incorrect old password']);
        }
        exit;
    }
}

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
?>
<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<div class="px-5 py-6 pb-24">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">My Profile</h2>
        <a href="login.php?logout=1" class="text-red-500 font-bold text-sm bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition-colors"><i class="fa-solid fa-power-off mr-1"></i> Logout</a>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-amber-500 text-white rounded-[2rem] p-6 shadow-lg shadow-orange-500/20 mb-8 flex items-center relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-xl"></div>
        <div class="absolute bottom-0 left-10 w-24 h-24 bg-white/10 rounded-full -mb-10 blur-lg"></div>
        <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center text-orange-500 font-black text-3xl mr-5 shadow-inner relative z-10 border-2 border-white/50">
            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
        </div>
        <div class="relative z-10">
            <h3 class="font-black text-xl mb-1"><?php echo $user['name']; ?></h3>
            <p class="text-orange-50 text-sm font-medium opacity-90"><?php echo $user['email']; ?></p>
        </div>
    </div>

    <!-- Personal Info -->
    <form id="form-profile" onsubmit="updateProfile(event)" class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 mb-8">
        <h3 class="font-black text-slate-800 mb-5 text-lg flex items-center"><i class="fa-solid fa-user text-orange-500 mr-2"></i> Personal Details</h3>
        <input type="hidden" name="action" value="update_profile">
        <div class="mb-5">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Full Name</label>
            <input type="text" name="name" value="<?php echo $user['name']; ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
        </div>
        <div class="mb-5">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Phone Number</label>
            <input type="tel" name="phone" value="<?php echo $user['phone']; ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
        </div>
        <div class="mb-6">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Delivery Address</label>
            <textarea name="address" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all resize-none"><?php echo $user['address']; ?></textarea>
        </div>
        <button type="submit" class="w-full bg-orange-50 text-orange-600 font-black py-3.5 rounded-2xl hover:bg-orange-500 hover:text-white transition-colors">Save Changes</button>
    </form>

    <!-- Change Password -->
    <form id="form-password" onsubmit="updatePassword(event)" class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100">
        <h3 class="font-black text-slate-800 mb-5 text-lg flex items-center"><i class="fa-solid fa-lock text-orange-500 mr-2"></i> Security</h3>
        <input type="hidden" name="action" value="update_password">
        <div class="mb-5">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Old Password</label>
            <input type="password" name="old_password" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
        </div>
        <div class="mb-6">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">New Password</label>
            <input type="password" name="new_password" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-800 font-bold outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
        </div>
        <button type="submit" class="w-full bg-red-50 text-red-600 font-black py-3.5 rounded-2xl hover:bg-red-500 hover:text-white transition-colors">Update Password</button>
    </form>
</div>

<script>
    async function updateProfile(e) {
        e.preventDefault();
        showLoader();
        try {
            const res = await fetch('profile.php', { method: 'POST', body: new FormData(e.target) });
            const data = await res.json();
            if (data.status === 'success') { alert('Profile updated!'); location.reload(); }
        } catch(e) {}
        hideLoader();
    }
    async function updatePassword(e) {
        e.preventDefault();
        showLoader();
        try {
            const res = await fetch('profile.php', { method: 'POST', body: new FormData(e.target) });
            const data = await res.json();
            if (data.status === 'success') { alert('Password updated!'); e.target.reset(); }
            else { alert(data.msg); }
        } catch(e) {}
        hideLoader();
    }
</script>

<?php require_once 'common/bottom.php'; ?>
