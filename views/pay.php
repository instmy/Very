<?php include 'header.php'; ?>

<header>
	<a href="index.php">入门</a>
	<a href="member.php" class="active">账户</a>
</header>

<main>
	<div class="card">
		<div class="card-header">
			<span class="right"><a href="member.php?action=home">&gt;&gt; 返回</a></span>
			缴费
		</div>
		<div class="card-content">
			<?php if (count($message)) { ?>
			<div>
				您需要先解决以下错误才能继续
				<ul>
					<?php foreach ($message as $line) {
						echo "<li>$line</li>";
					}?>
				</ul>
			</div>
			<?php } ?>
			<form method="post" action="member.php?action=pay">
				<input type="text" placeholder="测试页面 暂未开放" autofocus="autofocus" id="tradeno" name="tradeno" />
				<input type="submit" class="button" />
			</form>
			<p>每 1 元人民币可以兑换 1 Fire Card</p>
			<ol>
				<li>向 <a href="http://" target="_blank"><code>账户</code></a> 转账一个整数，小数部分将被忽略，使用手机支付宝可以免除手续费</li>
				<li>访问 <a href="http://" target="_blank"><code>https://</code></a> 查看支付宝流水号粘贴到输入框中</li>
			</ol>
			<p>客服电话是 <span class="badge">100 0000 0000</span> ，服务时间是 08：00 至 20：00</p>
		</div>
		<div class="card-footer small-text">
			<p>&copy; <?php echo date('Y'); ?> <a href="http://very.azurewebsites.net/">Very</a></p>
		</div>
	</div>
</main>

<?php include 'footer.php'; ?>