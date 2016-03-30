<?php include 'header.php'; ?>

<header>
	<a href="index.php">入门</a>
	<a href="member.php" class="active">账户</a>
</header>

<main>
	<div class="card">
		<div class="card-header">
			注册 <span class="right"><a href="member.php?action=login">&gt;&gt; 登录</a></span>
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
			} else {
			?>
			<p>在开始前，我希望你阅读 Fire 的基本条款</p>
			<p>网络审查是围绕着我们生活中存在的一种现象，是与非不可轻易区分，但是不需要商榷的是<b>通过避免网络审查进行犯罪活动是违背道德的</b>。Fire 遵守中华人民共和国的法律，所以请您遵守<a href="http://www.gov.cn/gongbao/content/2011/content_1860856.htm" target="_blank">《计算机信息网络国际联网安全保护管理办法》</a>。</p>
			<?php } ?>
			<form method="POST" action="member.php?action=register">
				<p class="small-text">* 账号由英文字母与数字组成并小于 32 字符</p>
				<p>
					<input type="text" placeholder="账号" id="username" name="username" />
				</p>
				<p class="small-text">* 密码是识别您身份的必要信息</p>
				<p>
					<input type="password" placeholder="密码" id="password" name="password" />
				</p>
				<p class="small-text">* 电子邮箱将可能用于提醒您续费</p>
				<p>
					<input type="email" placeholder="邮箱" id="email" name="email" />
				</p>
				<p class="small-text">可选，在您有需要时我们将会与您联系</p>
				<p>
					<input type="text" placeholder="电话" id="phone" name="phone" />
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