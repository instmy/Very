<?php include 'header.php'; ?>

<header>
	<a href="index.php" class="active">入门</a>
	<a href="member.php">账户</a>
</header>

<main>
	<div class="card">
		<div class="card-header">
			入门 <span class="right">区域 <select id="region_commodity">
					<?php
					foreach ($region as $node) {
					?>
					<option value="<?php echo $node['id']; ?>"><?php echo "{$node['name']}"; ?></option>
					<?php
					}
					?>
				</select></span>
			<p class="small-text">
				选择一个可用区域并挑好套餐
			</p>
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