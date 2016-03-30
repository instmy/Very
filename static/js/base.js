/**
 * JavaScript Code Framework
 * @website Fire
 * @author Fire
**/

$(window).ready(function () {
	// 首页选择套餐订购
	$("#region_commodity").change(function () {
		$(".spinner").css("display", "block");
		$(".spinner").nextUntil(".card-footer").remove();
		var region = $("#region_commodity option:selected").val();
		$.getJSON("index.php?action=commodity&region="+region, function (result) {
			$(".spinner").css("display", "none");
			var timestamp = Date.parse(new Date());
			var commodity = result["commodity"];
			var region_information = result["region"];
			var innerHTML = '<div class="card-content"><p>' + region_information["introduction"] + '</p>';
			if (region_information["usable_member"] && parseInt(region_information["usable_member"]) > 0) {
				innerHTML += '<p>区域中可用 ' + region_information["usable_member"] + ' 个空闲位置，满员后将停止销售</p></div>';
			} else {
				innerHTML += '<p>该区域已经满员，请选择其他套餐</p></div>';
			}
			for (var i=0; i < commodity.length; ++i) {
				var expired_time = new Date();
				expired_time.setTime(timestamp + commodity[i]["time"] * 1000);
				innerHTML += '<div class="card-content"><a href="member.php?action=order&id=' + commodity[i]["id"] + '" class="button right">订购</a><a href="member.php?action=order&id=' + commodity[i]["id"] + '">' + commodity[i]["name"] + ' <span class="small-text">/ ' + commodity[i]["price"] +' 个 Fire Card</span></a><p class="small-text">' + commodity[i]["introduction"] + '</p><p class="small-text">在现在购买后可以使用至 ' + expired_time.toLocaleDateString() + '</p></div>';
			};
			if (result.length == 0) {
				innerHTML += '<div class="card-content">该区域可能已经满员，暂无可订购套餐</div>';
			}
			$(".spinner").after(innerHTML);
		});
	});
	// 加载默认区域的信息
	$("#region_commodity").change();

	// 账号系统
	// 将账号存储到 LocalStorage
	$("#submit").click(function () {
		$("#username").val($.trim($("#username").val()));
		var username = $("#username").val();
		if (username.length > 0) {
			localStorage.username = $("#username").val();
		}
	});
	if (localStorage.username) {
		$("#username").val(localStorage.username);
	}

	// 账户
	if ($("#region")) {
		// 账户页查看自己的区域账户
		$("#region").change(function () {
			$(".spinner").css("display", "block");
			$(".spinner").nextUntil(".card-footer").remove();
			var region = $("#region option:selected").val();
			$.getJSON("member.php?action=home_region_ajax&region="+region, function (result) {
				$(".spinner").css("display", "none");
				var innerHTML = '<div class="card-content">';
				if (result["message"] == "success") {
					innerHTML += '<a class="button" href="member.php?action=quick_config&id=' + region + '">下载快速配置文件</a>';
					innerHTML += '<a class="button right" href="member.php?action=region&id=' + region + '">查看所有节点</a>';
					innerHTML += "<p>端口：" + result["port"] + "</p>";
					innerHTML += "<p>密码：" + result["passwd"] + "</p>";
					innerHTML += "<p>可用流量：" + result["transfer"] + "</p>";
					var expired_time = new Date();
					expired_time.setTime(result["expired_time"] * 1000);
					innerHTML += '<p class="small-text">您在该区域的账号将在 ' + expired_time.toLocaleDateString() + ' 过期，重新订购套餐可以为该区域续费</p>';
				} else {
					innerHTML += "<p>你不持有本区域的账号或账号已过期</p>";
				}
				innerHTML += '</div>';
				$(".spinner").after(innerHTML);
			});
		});
		$("#region").change();
	}

	// 订购页面查询折扣码有效性及预览
	if ($("#discount_code")) {
		function reset_price () {
			var commodity_id = $("#commodity_id").val();
			var discount_code = $("#discount_code").val();
			$.getJSON("member.php?action=discount&commodity_id="+encodeURIComponent(commodity_id)+"&code="+encodeURIComponent(discount_code), function (result) {
				$("#message").remove();
				var innerHTML = '<p id="message">';
				if (result["error"]) {
					innerHTML += result["message"];
				} else {
					var commodity = result["commodity"];
					var discount = result["discount"];
					// 商品单价
					commodity["price"] = parseInt(commodity["price"]);
					// 购买数量
					var count = $("#count option:selected").val();
					if (result["discount"]) {
						// 如果折扣码存在
						// 折扣码可折扣价格
						discount["discount_price"] = parseInt(discount["discount_price"]);
						// 折后价格
						var discounted_price = commodity["price"] * count;
						// 如果存在最低消费
						if (discount["min_price"] > 0) {
							// 如果满足最低消费
							if (discount["min_price"] <= discounted_price) {
								// 设置折后价格
								var discounted_price = discounted_price - discount["discount_price"];
							} else {
								innerHTML += "满 " + discount["min_price"] + " Fird Card ";
							}
						} else {
							var discounted_price = discounted_price - discount["discount_price"];
						}
						if (discounted_price < 0) {
							var discounted_price = 0;
						}
						innerHTML += "折扣 " + discount["discount_price"] + ' Fire Card，最终价格是 <span class="firecard">' + discounted_price + ' Fird Card</span>';
					} else {
						innerHTML += '价格是 <span class="firecard">' + (commodity["price"] * count) + " Fire Card</span>";
					}
				}
				innerHTML += '</p>';
				$("#submit").after(innerHTML);
			});
		}
		$("#discount_code").change(reset_price);
		$("#count").change(reset_price);
	}
});