<?php
$appCfg = require __DIR__ . '/../../config/app.php';
$base = $appCfg['base_url'];
$roleLabel = $appCfg['roles'][$currentUser['rol'] ?? ''] ?? ($currentUser['rol'] ?? '');
$currentRoute = $currentRoute ?? '';
?>
<aside class="sidebar">
	<div class="logo">ContaTech</div>
	<div class="role-badge"><?= htmlspecialchars($roleLabel) ?></div>
	<ul class="menu">
		<?php foreach ($menuItems as $item): ?>
			<li>
        		<a href="<?= $base ?>/views/<?= htmlspecialchars($item['route']) ?>/index.php" class="<?= $currentRoute === $item['route'] ? 'active' : '' ?>">
          		<?php if (!empty($item['icon'])): ?>
					<img class="icon" src="<?= $base ?>/public/assets/img/menu/<?= htmlspecialchars($item['icon']) ?>" alt=""><?php endif; ?>
          		<?= htmlspecialchars($item['label']) ?>
        		</a>
      		</li>
    	<?php endforeach; ?>
		<div class="sub-menu">
			<li style="margin-top:20px;">
				<button type="button" onclick="logout()"><img class="icon" src="../../public/assets/img/menu/log-out.png" alt=""> Cerrar sesión</button>
				<button type="button" onclick="logout()"><img class="icon" src="../../public/assets/img/menu/profile.png" alt=""> Perfil</button>
			</li>
		</div>
	</ul>
</aside>

<script>
	function logout(){ 
		ContaAPI.post('auth/logout').catch(()=>{}); 
		ContaAPI.clearSession(); 
		window.location.href='<?= $base ?>/views/auth/login.php'; 
	}
</script>
