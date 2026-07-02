<!-- Bottom Nav -->
<div class="fixed bottom-0 w-full bg-white border-t border-slate-100 flex justify-around p-3 z-40 pb-safe">
    <a href="index.php" class="text-center w-1/3 group">
        <i class="fa-solid fa-house text-2xl text-slate-400 group-hover:text-orange-500 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-orange-500' : ''; ?>"></i>
        <div class="text-[10px] mt-1 font-bold text-slate-500 group-hover:text-orange-500 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-orange-500' : ''; ?>">Home</div>
    </a>
    <a href="cart.php" class="text-center w-1/3 group relative">
        <i class="fa-solid fa-bag-shopping text-2xl text-slate-400 group-hover:text-orange-500 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'text-orange-500' : ''; ?>"></i>
        <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <span class="absolute top-0 right-8 bg-orange-500 text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center font-bold">
            <?php echo count($_SESSION['cart']); ?>
        </span>
        <?php endif; ?>
        <div class="text-[10px] mt-1 font-bold text-slate-500 group-hover:text-orange-500 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'text-orange-500' : ''; ?>">Cart</div>
    </a>
    <a href="profile.php" class="text-center w-1/3 group">
        <i class="fa-solid fa-user text-2xl text-slate-400 group-hover:text-orange-500 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'text-orange-500' : ''; ?>"></i>
        <div class="text-[10px] mt-1 font-bold text-slate-500 group-hover:text-orange-500 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'text-orange-500' : ''; ?>">Profile</div>
    </a>
</div>

<script>
    function showLoader() { document.getElementById('loader').classList.remove('hidden'); }
    function hideLoader() { document.getElementById('loader').classList.add('hidden'); }
</script>
</body>
</html>
