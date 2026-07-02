<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
?>
<?php require_once 'common/header.php'; ?>
<?php require_once 'common/sidebar.php'; ?>

<div class="p-6">
    <h2 class="text-2xl font-black text-slate-800 mb-8 tracking-tight">Orders</h2>

    <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase tracking-wider font-black">
                        <th class="p-5">Order ID</th>
                        <th class="p-5">User</th>
                        <th class="p-5">Date</th>
                        <th class="p-5">Total Amount</th>
                        <th class="p-5">Status</th>
                        <th class="p-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($db_selected) {
                        $res = $conn->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.id DESC");
                        while($row = $res->fetch_assoc()) {
                            $s = $row['status'];
                            $bg = 'bg-slate-100 text-slate-700';
                            if($s=='Placed') $bg = 'bg-blue-50 text-blue-600 border border-blue-100';
                            if($s=='Dispatched') $bg = 'bg-orange-50 text-orange-600 border border-orange-100';
                            if($s=='Delivered') $bg = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                            if($s=='Cancelled') $bg = 'bg-red-50 text-red-600 border border-red-100';
                            
                            echo "<tr class='border-b border-slate-50 hover:bg-slate-50/50 transition-colors'>";
                            echo "<td class='p-5 font-black text-slate-800'>#{$row['id']}</td>";
                            echo "<td class='p-5 font-bold text-slate-600'>{$row['user_name']}</td>";
                            echo "<td class='p-5 text-sm font-medium text-slate-500'>".date('d M Y, h:i A', strtotime($row['created_at']))."</td>";
                            echo "<td class='p-5 font-black text-orange-500'>".formatPrice($row['total_amount'])."</td>";
                            echo "<td class='p-5'><span class='px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider {$bg}'>{$s}</span></td>";
                            echo "<td class='p-5 text-right'><a href='order_detail.php?id={$row['id']}' class='bg-slate-50 text-slate-600 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-orange-50 hover:text-orange-600 transition-colors border border-slate-100 hover:border-orange-200'>View Details</a></td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'common/bottom.php'; ?>
