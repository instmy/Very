<?php include 'header.php'; ?>

<header>
	<a href="index.php">入门</a>
	<a href="member.php" class="active">账户</a>
</header>

<main>
	<div class="card">
		<div class="card-header">
			登录 <span class="right"><a href="member.php?action=register">&gt;&gt; 注册</a></span>
		</div>
		<div class="card-content">
			<?php
			if (count($message)) {
			?>
			<div>
				您需要先解决以下错误才能继续
				<ul>
					<?php foreach ($message as $line) {
						echo "<li>$line</li>";
					}?>
				</ul>
			</div>
			<?php
			}
			?>
			<form method="POST" action="member.php?action=login">
				<p>
					<input type="text" placeholder="账号" autofocus="autofocus" id="username" name="username" />
				</p>
				<p>
					<input type="password" placeholder="密码" id="password" name="password" />
				</p>
				<p>
					<button class="button" type="submit" id="submit">提交</button>
				</p>
			</form>
		</div>
		<div class="card-footer small-text">
			<p>&copy; <?php echo date('Y'); ?> <a href="http://very.azurewebsites.net/">Very</a></p>
		</div>
	</div>
</main>

<?php include 'footer.php'; ?>