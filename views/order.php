<?php include 'header.php'; ?>

<header>
	<a href="index.php">入门</a>
	<a href="member.php" class="active">账户</a>
</header>

<main>
	<div class="card">
		<div class="card-header">
			<span class="right"><a href="index.php">&gt;&gt; 返回</a></span>
			订购
			<p class="small-text" id="order_t">
				选择 Fire 中的套餐进行购买
			</p>
		</div>
		<div class="card-content">
			<?php
			if (count($message)) {
			?>
			<div>
				请您阅读
				<ul>
					<?php foreach ($message as $line) {
						echo "<li>$line</li>";
					}?>
				</ul>
			</div>
			<?php
			}
			?>
			<?php
			if ($commodity && $region && $region['max_member'] > $region['active_member']) {
			?>
			<p><?php echo $region['name']; ?> 区域的<?php echo $commodity['name']; ?>套餐售价是 <span class="firecard"><?php echo $commodity['price']; ?> Fire Card</span>，包括 <?php echo $commodity['transfer']; ?> GByte 流量，如果现在购买该区域将可以使用至 <?php echo date('Y 年 m 月 d 日', time()+$commodity['time']); ?></p>
			<form method="POST" action="member.php?action=order&id=<?php echo $commodity['id']; ?>">
				<p>
					<p>购买 <select id="count" name="count">
						<option value="1">1 个</option>
						<option value="2">2 个</option>
						<option value="3">3 个</option>
						<option value="4">4 个</option>
						<option value="5">5 个</option>
					</select>，可用时间不会因购买数量而延长，只有流量会叠加</p>
					<input type="hidden" id="commodity_id" value="<?php echo $commodity['id']; ?>" />
					<input type="text" placeholder="折扣码" id="discount_code" name="discount_code" />
				</p>
				<p><input type="submit" class="button" value="提交订单" id="submit" /></p>
				<p id="message">价格是 <span class="firecard"><?php echo $commodity["price"]; ?> Fire Card</span></p>
			</form>
			<?php
			} else {
			?>
			<p>无法获取该套餐的信息，可能不存在或该区域已经满员</p>
			<?php
			}
			?>
		</div>
		<div class="card-footer small-text">
			<p>&copy; <?php echo date('Y'); ?> <a href="http://very.azurewebsites.net/">Very</a></p>
		</div>
	</div>
</main>

<?php include 'footer.php'; ?>