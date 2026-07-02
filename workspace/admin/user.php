<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM users WHERE id=$id");
        echo json_encode(['status' => 'success']);
        exit;
    }
}
?>
<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<div class="p-6">
    <h2 class="text-2xl font-black text-slate-800 mb-8 tracking-tight">Registered Users</h2>

    <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase tracking-wider font-black">
                        <th class="p-5">User ID</th>
                        <th class="p-5">Name</th>
                        <th class="p-5">Email</th>
                        <th class="p-5">Phone</th>
                        <th class="p-5">Joined Date</th>
                        <th class="p-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($db_selected) {
                        $res = $conn->query("SELECT * FROM users ORDER BY id DESC");
                        while($row = $res->fetch_assoc()) {
                            echo "<tr class='border-b border-slate-50 hover:bg-slate-50/50 transition-colors'>";
                            echo "<td class='p-5 font-black text-slate-500'>#{$row['id']}</td>";
                            echo "<td class='p-5 font-bold text-slate-800 flex items-center'><div class='w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-black mr-3'>".strtoupper(substr($row['name'], 0, 1))."</div>{$row['name']}</td>";
                            echo "<td class='p-5 text-sm font-medium text-slate-600'>{$row['email']}</td>";
                            echo "<td class='p-5 text-sm font-medium text-slate-600'>{$row['phone']}</td>";
                            echo "<td class='p-5 text-xs font-black uppercase tracking-wider text-slate-400'>".date('d M Y', strtotime($row['created_at']))."</td>";
                            echo "<td class='p-5 text-right'>";
                            echo "<button onclick='deleteUser({$row['id']})' class='text-red-500 hover:text-red-600 bg-red-50 w-10 h-10 rounded-xl inline-flex items-center justify-center hover:bg-red-100 transition-colors'><i class='fa-solid fa-trash'></i></button>";
                            echo "</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function deleteUser(id) {
    if(!confirm('Are you sure you want to delete this user? This will also delete their orders.')) return;
    showLoader();
    try {
        let fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        let res = await fetch('user.php', {method:'POST', body:fd});
        let data = await res.json();
        if(data.status==='success') location.reload();
    } catch(e) {}
    hideLoader();
}
</script>

<?php require_once 'common/bottom.php'; ?>
