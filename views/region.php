<?php include 'header.php'; ?>

<header>
	<a href="index.php">入门</a>
	<a href="member.php" class="active">账户</a>
</header>

<main>
	<div class="card">
		<div class="card-header">
			<span class="right"><a href="member.php?action=home">&gt;&gt; 返回</a></span>
			区域
		</div>
		<?php foreach ($node_list as $node) {
		?>
		<div class="card-content">
			<?php echo $node['name']; ?>
			<?php echo $node['type']; ?>
			<?php echo $node['address']; ?>
			<p class="small-text"><?php echo $node['introduction']; ?></p>
		</div>
		<?php } ?>
		<div class="card-footer small-text">
			<p>&copy; <?php echo date('Y'); ?> <a href="http://very.azurewebsites.net/">Very</a></p>
		</div>
	</div>
</main>

<?php include 'footer.php'; ?>