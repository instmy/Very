<?php include 'header.php'; ?>

<header>
	<a href="index.php">入门</a>
	<a href="member.php" class="active">账户</a>
</header>

<main>
	<div class="card">
		<div class="card-header">
			<span class="right"><a href="member.php?action=logout">&gt;&gt; 退出</a></span>
			欢迎您，<span><?php echo $member['name']; ?></span> <p class="small-text">本次登录有效至 <?php echo date('d 日 H:i:s', $token['expired_time']); ?></p>
		</div>
		<div class="card-content">
			<?php
			if (count($message)) {
			?>
			<div>
				请您阅读以下信息
				<ul>
					<?php foreach ($message as $line) {
						echo "<li>$line</li>";
					}?>
				</ul>
			</div>
			<?php
			}
			?>
			<span class="right">区域 <select id="region">
				<?php
				foreach ($region as $node) {
				?>
				<option value="<?php echo $node['id']; ?>"><?php echo $node['name']; ?></option>
				<?php
				}
				?>
				</select></span>
			我持有 <span class="firecard"><?php echo $member['money'] ;?> 个 Fire Card</span> <a href="member.php?action=pay"><span class="badge">充值</span></a>
		</div>
		<div class="spinner">
			<div class="double-bounce1"></div>
			<div class="double-bounce2"></div>
		</div>
		<div class="card-footer small-text">
			<p>&copy; <?php echo date('Y'); ?> <a href="http://very.azurewebsites.net/">Very</a></p>
		</div>
	</div>
</main>

<?php include 'footer.php'; ?>