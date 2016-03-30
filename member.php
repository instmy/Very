<?php

// $start_time = microtime();

include 'library/medoo.php';
include 'library/region.php';
$db = new Medoo();
$message = array();

// ini_set('display_errors', true);
// error_reporting(E_ALL);

function format_transfer($t) {
	/* 本函数来自 https://github.com/mengskysama/MakeDieSS/blob/master/function.php */
	
	$units = array('Byte', 'KByte', 'MByte', 'GByte', 'TByte', 'PByte');
	$level = 0;
	while ($t > 1024 && $level < count($units)-1) {
		$t /= 1024.0;
		$level++;
	}
	return sprintf("%01.2f ", $t) . $units[$level];

	/* 精确到 MByte */
	//	return sprintf("%01.2f", $t / 1024 / 1024).' MByte';
}

function generate_string($length=16) {
	// 密码字符集，可任意添加你需要的字符
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$password = '';
	for ($i=0; $i<$length; $i++) {
		// 这里提供两种字符获取方式
		// 第一种是使用 substr 截取$chars中的任意一位字符；
		// 第二种是取字符数组 $chars 的任意元素
		// $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		$password .= $chars[mt_rand(0, strlen($chars)-1)];
	}
	return $password;
}

function generate_token($username, $deep=0) {
	global $db;
	$deep++;
	if ($deep > 3) {
		return false;
	}
	$token_string = generate_string(64);
	// 检测有效性
	$token = $db->get('token', array('token', 'username', 'expired_time'), array('AND'=>array('token'=>$token_string, 'expired_time[>]'=>time())));

	if ($token) {
		return generate_token($username, $deep);
	} else {
		$result = $db->insert('token', array('token'=>$token_string, 'username'=>$username, 'expired_time'=>time()+3600*2));
		$active = $db->insert('active', array(
			'content' => "登录创建 token：{$token_string} 经过 {$deep} 次",
			'username' => $username,
			'time' => date('Y-m-d H:i:s', time())
		));
		return $token_string;
	}
}

function check_token($token_string='') {
	global $db;
	if (!$token_string && isset($_COOKIE['token'])) {
		$token_string = $_COOKIE['token'];
	}

	if ($token_string) {
		return $db->get('token', array('token', 'username', 'expired_time'), array('AND'=>array('token'=>$token_string, 'expired_time[>]'=>time())));
	} else {
		return false;
	}
}

if (isset($_GET['action'])) {
	$action = &$_GET['action'];
	if ($action == 'login') {
		if (check_token()) {
			// 登录过了
			header('Location: member.php?action=home');
			unset($message);
		} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (empty($_POST['username'])) {
				$message[] = '请键入账号';
			} else {
				$_POST['username'] = trim($_POST['username']);
				if (!ctype_alnum($_POST['username'])) {
					$message[] = '账号必须是字母与数字的组合';
				}
			}
			if (empty($_POST['password'])) {
				$message[] = '请键入密码';
			}

			if (!count($message)) {
				// 如果不存在错误则查询数据库
				$member = $db->get('member', array('id', 'name', 'email', 'phone', 'password', 'salt'), array('name'=>$_POST['username']));
				if (!$member) {
					$message[] = '无法找到该用户';
				} else {
					if (strcmp($member['password'], md5($_POST['password'].$member['salt'])) === 0) {
						// 验证账户成功
						$token = generate_token($member['name']);
						setcookie('token', $token, time()+3600*2);

						header('Location: member.php?action=home');
						unset($message);
					} else {
						$message[] = '验证密码失败';
					}
				}
			}
		}
		if (isset($message)) {
			$title = '账户';
			include 'views/login.php';
		}

	} else if ($action == 'register') {
		if (check_token()) {
			// 登录过了
			header('Location: member.php?action=home');
			unset($message);
		} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (empty($_POST['username'])) {
				$message[] = '请键入账号';
			} else {
				$_POST['username'] = trim($_POST['username']);
				$_POST['username'] = htmlentities($_POST['username']);
				if (!ctype_alnum($_POST['username'])) {
					$message[] = '账号是字母与数字的组合';
				} else if (strlen($_POST['username']) > 32) {
					$message[] = '账号需小于等于 32 字符';
				}
			}
			if (empty($_POST['password'])) {
				$message[] = '请键入密码';
			}
			if (empty($_POST['email'])) {
				$message[] = '请键入邮箱';
			}
			if (empty($_POST['phone'])) {
				// 防止未提交 phone 字段导致出错
				$_POST['phone'] = '';
			}

			if (!count($message)) {
				// 如果不存在错误则查询数据库
				$member = $db->get('member', array('id', 'name', 'email', 'phone', 'password', 'salt'), array('name'=>$_POST['name']));
				if (!$member) {
					$salt = rand(100000, 999999);
					$md5_password = md5($_POST['password'].$salt);
					$member = $db->insert('member', array(
						'name' => $_POST['username'],
						'email' => htmlentities($_POST['email']),
						'phone' => intval($_POST['phone']),
						'password' => $md5_password,
						'salt' => $salt,
						'money' => 20
						// 初始注册赠送20币 2016年3月29日21:42:12
					));

					if ($member) {
						$active = $db->insert('active', array(
							'content' => '注册',
							'username' => $_POST['username'],
							'time' => date('Y-m-d H:i:s', time())
						));
						header('Location: member.php?action=login');
						unset($message);
					} else {
						$message = '无法完成注册，请重试';
					}
				} else {
					$message[] = '该账号已被注册';
				}
			}
		}
		if (isset($message)) {
			$title = '账户';
			include 'views/register.php';
		}
	} else if ($action == 'home') {
		$token = check_token();
		if (!$token) {
			header('Location: member.php?action=login');
		} else {
			$region = $db->select('region', array('id', 'name', 'introduction'));
			$member = $db->get('member', array('id', 'name', 'email', 'money', 'phone', 'password', 'salt'), array('name'=>$token['username']));
			if (!$member) {
				header('Location: member.php?action=logout');
			} else {
				include 'library/region.php';
				$title = '账户';
				include 'views/home.php';
			}
		}
	} else if ($action == 'home_region_ajax') {
		// 通过 Ajax 查询当前 Token 用户在提供区域的账号信息
		$token = check_token();
		if (!$token) {
			echo json_encode(array('message'=>'token 授权失败', 'error'=>true));
		} else {
			if (isset($_GET['region'], $region_connect[$_GET['region']])) {
				$region_db = new Medoo($region_connect[$_GET['region']]);
				$region_member = $region_db->get('user', array('id', 'passwd', 'transfer_enable', 'port', 'enable', 'u', 'd', 'switch', 't', 'expired_time', 'username'), array('username'=>$token['username']));
				if (!$region_member || $region_member['expired_time'] < time()) {
					$region_member = array('message'=>'区域中不存在该用户');
				} else {
					$region_member['message'] = 'success';
					$region_member["transfer"] = format_transfer($region_member["transfer_enable"] - $region_member["u"] - $region_member["d"]);
				}
				echo json_encode($region_member);
			} else {
				// 少提交了参数，不返回错误信息
			}
		}
	} else if ($action == 'logout') {
		// 退出，消除 Token 有效性
		$token = check_token();
		if ($token) {
			setcookie('token', '', 0);
			$db->delete('token', array('token'=>$_COOKIE['token']));
			$active = $db->insert('active', array(
				'content' => "退出消除 {$token['token']}：{$token_string} 过期时间：{$token['expired_time']}",
				'username' => $token['username'],
				'time' => date('Y-m-d H:i:s', time())
			));
		}
		header('Location: member.php?action=login');
	} else if ($action == 'order') {
		// 购买页面
		$token = check_token();
		if ($token) {
			if (!empty($_GET['id'])) {
				$commodity = $db->get('commodity', array('id', 'name', 'introduction', 'time', 'price', 'transfer', 'region'), array('id'=>$_GET['id']));
				if ($commodity) {
					// 记得检测可用用户数量
					$region = $db->get('region', array('id', 'name', 'introduction', 'max_member'), array('id'=>$commodity['region']));
					$region_db = new Medoo($region_connect[$region['id']]);
					$region['active_member'] = $region_db->count('user', array('expired_time[>]'=>time()));
				}

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					// 购买套餐
					if ($commodity && $region && $region['max_member'] > $region['active_member']) {
						// 如果套餐与区域可用
						$count = (int)$_POST['count'];
						if ($count < 0) {
							$count = 0;
						}
						// 最终价格
						$discounted_price = $commodity["price"] * $count;
						$discount_code = $_POST['discount_code'];
						if (!empty($discount_code)) {
							$discount = $db->get('discount',
							array('card', 'md5', 'create_time', 'used_member', 'discount_price', 'min_price', 'username'),
							array('AND' =>
								array('card' => $discount_code,
									'md5' => md5($discount_code)
								)
							));
							if (!$discount || $discount['used_member'] || ($discount['username'] && $discount['username'] != $token['username'])) {
								$message[] = '折扣码不可用，请您重新确认订单';
							} else {
								// 满足折扣码最低消费金额
								if ($discount["min_price"] < $discounted_price) {
									// 进行折扣
									$use_discount_code = true;
									$discounted_price = $commodity['price'] * $count - $discount["discount_price"];
								} else {
									$message[] = "折扣码需要满 {$discount['min_price']} Fire Card 使用";
								}
							}
						} else {
							// 没有使用折扣码
						}
						// 可用流量
						$transfer_enable = 1024*1024*1024*$commodity['transfer']*$count;
						$formated_transfer = format_transfer($transfer_enable);
						// 套餐过期时间
						$expired_time = time() + $commodity['time'];

						if (!count($message)) {
							// 没有套餐错误，检查余额
							$member = $db->get('member', array('id', 'name', 'email', 'money', 'phone', 'password', 'salt'), array('name'=>$token['username']));
							if (!$member) {
								$message[] = '请重新登录后选择套餐';
							} else if ($member['money'] >= $discounted_price) {
								// 插入尝试扣款日志
								$active = $db->insert('active', array(
									'content' => "[未扣款]尝试购买 {$count} 个 {$region['name']} 区域的 {$commodity['name']} 套餐共 {$formated_transfer}，支付 {$discounted_price} Fire Card，余额 {$member['money']} Fire Card",
									'username' => $token['username'],
									'time' => date('Y-m-d H:i:s', time())
								));
								// 标记已使用
								$db->update('discount',
									array('used_member' => $token['username']),
									array('card' => $discount_code)
								);
								//if ($discount_price == 0 || $db->update('member', array('money[-]'=>$discounted_price), array('id'=>$member['id']))) {
									//修正支付扣款程序 2016年3月29日21:45:12
									if ($db->update('member', array('money[-]'=>$discounted_price), array('id'=>$member['id']))) {
									// 扣款成功
									$active = $db->insert('active', array(
										'content' => "[已扣款]购买 {$count} 个 {$region['name']} 区域的 {$commodity['name']} 套餐共 {$formated_transfer}，支付 {$discounted_price} Fire Card，扣款前余额 {$member['money']} Fire Card",
										'username' => $token['username'],
										'time' => date('Y-m-d H:i:s', time())
									));
									// 判断 region 中是否存在该用户的端口
									$region_member = $region_db->get('user', array('id', 'passwd', 'transfer_enable', 'port', 'enable', 'u', 'd', 'switch', 't', 'expired_time', 'username'), array('username'=>$token['username']));
									if (!$region_member) {
										// 分配新账号
										$active = $db->insert('active', array(
											'content' => "[已扣款]尝试购买 {$count} 个 {$region['name']} 区域的 {$commodity['name']} 套餐共 {$formated_transfer}，支付 {$discounted_price} Fire Card，余额 {$member['money']} Fire Card",
											'username' => $token['username'],
											'time' => date('Y-m-d H:i:s', time())
										));
										$last_port = $region_db->get('user', 'port', array('ORDER'=>'port DESC'));
										!$last_port && $last_port = 10000;
										$region_member = array(
											'passwd' => generate_string(6),
											'transfer_enable' => $transfer_enable,
											'port' => $last_port + 1,
											'enable' => 1,
											'u' => 0,
											'd' => 0,
											'switch' => 1,
											't' => 0,
											'expired_time' => $expired_time,
											'username' => $token['username']
										);
										$id = $region_db->insert('user', $region_member);
										if ($id) {
											$active = $db->insert('active', array(
												'content' => "分配 {$region['name']} 区域的账号，端口：{$region_member['port']}，密码：{$region_member['passwd']}",
												'username' => $token['username'],
												'time' => date('Y-m-d H:i:s', time())
											));
											unset($message);
											header('Location: member.php?action=home');
										} else {
											$active = $db->insert('active', array(
												'content' => "分配 {$region['name']} 区域的账号失败，端口：{$region_member['port']}，密码：{$region_member['passwd']}",
												'username' => $token['username'],
												'time' => date('Y-m-d H:i:s', time())
											));
											$message[] = '分配账户失败，请您联系管理员';
										}
									} else {
										// 存在该用户的账号，为该用户增加流量
										$region_member['transfer_enable'] = ($region_member['transfer_enable'] - $region_member['u'] - $region_member['d']);
										if ($region_member['transfer_enable'] > 0) {
											// 流量为负
											$transfer_enable += $region_member['transfer_enable'] - $region_member['u'] - $region_member['d'];
										}
										if ($region_db->update('user', array(
											'transfer_enable' => $transfer_enable,
											'expired_time' => $expired_time
										), array('id'=>$region_member['id']))) {
											// 如果更新成功
											$active = $db->insert('active', array(
												'content' => "向 {$region['name']} 区域的 {$region_member['username']} 增加 {$formated_transfer} 流量",
												'username' => $token['username'],
												'time' => date('Y-m-d H:i:s', time())
											));
											$message[] = '购买成功，流量已经增加';
											unset($message);
											header('Location: member.php?action=home');
										} else {
											$active = $db->insert('active', array(
												'content' => "向 {$region['name']} 区域的 {$region_member['username']} 增加 {$formated_transfer} 流量失败",
												'username' => $token['username'],
												'time' => date('Y-m-d H:i:s', time())
											));
											$message[] = '增加流量失败，请联系管理员';
										}
									}
								} else {
									$message[] = '扣款失败，请重试';
								}
							} else {
								$active = $db->insert('active', array(
									'content' => "购买 {$count} 个 {$region['name']} 区域的 {$commodity['name']} 套餐共 {$formated_transfer}，余额 {$member['money']} Fire Card 不足以支付 {$discounted_price} Fire Card",
									'username' => $token['username'],
									'time' => date('Y-m-d H:i:s', time())
								));
								$message[] = "余额不足以支付 {$discounted_price} Fire Card";
							}
						}
					}
					// 不需要 else，order 页面有处理
				}
				if (isset($message)) {
					$title = '订购';
					include 'views/order.php';
				}
			} else {
				header('Location: index.php');
			}
		} else {
			header('Location: member.php?action=login');
		}
	} else if ($action == 'region') {
		$token = check_token();
		if (!$token) {
			header('Location: member.php?action=login');
		} else {
			$region = $db->get('region', array('id', 'name', 'introduction'), array('id'=>$_GET['id']));
			if (!$region) {
				header('Location: member.php?action=home');
			} else {
				$region_db = new Medoo($region_connect[$region['id']]);
				$region_member = $region_db->get('user', array('id', 'passwd', 'transfer_enable', 'port', 'enable', 'u', 'd', 'switch', 't', 'expired_time', 'username'), array('username'=>$token['username']));
				if ($region_member) {
					$node_list = $db->select('node', array('name', 'introduction', 'address', 'type'), array('region'=>$_GET['id']));
					$title = '区域';
					include 'views/region.php';
				} else {
					header('Location: member.php?action=home');
				}
			}
		}
	} else if ($action == 'quick_config') {
		$token = check_token();
		if ($token) {
			if (isset($_GET['id'], $region_connect[$_GET['id']])) {
				$region_db = new Medoo($region_connect[$_GET['id']]);
				$region_member = $region_db->get('user', array('id', 'passwd', 'transfer_enable', 'port', 'enable', 'u', 'd', 'switch', 't', 'expired_time', 'username'), array('username'=>$token['username']));
				if (!$region_member || $region_member['expired_time'] < time()) {
					header('Location: member.php?action=home');
				} else {
					$server = $db->select('node', array('name', 'address', 'type'), array('region'=>$_GET['id']));
					$gui_config = array('configs'=>array(),
						'strategy' => 'com.shadowsocks.strategy.ha',
						'index' => -1,
						'global' => true, // 全局
						'enabled' => true, // 启用
						'shareOverLan' => false, // 共享
						'isDefault' => false,
						'localPort' => 1080,
						'pacUrl' => null,
						'useOnlinePac' => false,
						'availabilityStatistics' => false
					);
					foreach ($server as $node) {
						$gui_config['configs'][] = array(
							'server' => $node['address'],
							'server_port' => $region_member['port'],
							'password' => $region_member['passwd'],
							'method' => strtolower($node['type']),
							'remarks' => $node['name']
						);
					}
					header('Content-type: application/octet-stream');
					header('Content-Disposition: attachment; filename="gui-config.json"');

					echo json_encode($gui_config);
				}
			} else {
				// 少提交了参数，不返回错误信息
				header('Location: member.php?action=home');
			}
		} else {
			header('Location: member.php?action=login');
		}
	} else if ($action == 'pay') {
		// 付款
		$token = check_token();
		if ($token) {
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tradeno'])) {
				$tradeno = urlencode($_POST['tradeno']);
				$data = json_decode(file_get_contents('http:///api/pay.php?application=2&apikey=W6sz7qFxcfoYdCoAf03PDqptWhBVUZCPrcgIzPb5wv8avfrx3vJFlHT09MMAoZF9&method=check_order&update=1&tradeNo='.$tradeno), true);
				if (isset($data['error'])) {
					// 表明正确获取
					if (!$data['error']) {
						switch ($data['message_id']) {
							case 1:
								$message[] = '该订单不存在';
								break;
							case 2:
								// 订单被用于其他 appid
								$message[] = '订单无效，请联系管理员';
								break;
							case 3:
								$message[] = '订单已被使用';
								break;
							case 4:
								// 该状态不应出现
								$message[] = '订单没有被使用';
								break;
							case 5:
								$amount = (int)$data['data']['amount'];
								if ($db->update('member', array('money[+]'=>$amount), array('name'=>$token['username']))) {
									$active = $db->insert('active', array(
										'content' => "订单号 {$tradeno} 充值 {$amount} 个 Fire Card",
										'username' => $token['username'],
										'time' => date('Y-m-d H:i:s', time())
									));
									$message[] = '充值成功';
								} else {
									$active = $db->insert('active', array(
										'content' => "订单号 {$tradeno} 充值失败 {$amount} 个 Fire Card",
										'username' => $token['username'],
										'time' => date('Y-m-d H:i:s', time())
									));
									$message[] = '充值失败，请联系客服';
								}
								break;
							default:
								$message[] = '未知错误，请联系管理员';
								break;
						}
						$active = $db->insert('active', array(
							'content' => "获取订单 {$tradeno} 返回状态码 {$data['message_id']} 内容 {$message[0]}",
							'username' => $token['username'],
							'time' => date('Y-m-d H:i:s', time())
						));
					} else {
						// 显示错误信息
						$message[] = '检查订单失败，请联系管理员';
					}
				}
			}
			$title = '缴费';
			include 'views/pay.php';
		} else {
			header('Location: member.php?action=login');
		}
	} else if ($action == 'discount') {
		// Ajax 折扣码查询
		$token = check_token();
		if ($token) {
			if (isset($_GET['commodity_id'], $_GET['code'])) {
				$commodity = $db->get('commodity', array('id', 'name', 'introduction', 'time', 'price', 'transfer', 'region'), array('id'=>$_GET['commodity_id']));
				if ($commodity) {
					/*$code = generate_string(32);
					$db->insert('discount', array(
						'card' => $code,
						'md5' => md5($code),
						'create_time' => date('Y-m-d H:i:s'),
						'discount_price' => 5
					));*/
					$discount_code = $db->get('discount',
						array('card', 'md5', 'create_time', 'used_member', 'discount_price', 'min_price', 'username'),
						array('AND'=>
							array('card'=>$_GET['code'],
								'md5'=>md5($_GET['code']),
								'used_member'=>array('', null))
						));
					if ($discount_code) {
						echo json_encode(array('commodity'=>$commodity, 'discount'=>$discount_code));
					} else {
						echo json_encode(array('commodity'=>$commodity));
					}
				} else {
					echo json_encode(array('message'=>'套餐不存在', 'error'=>true));
				}
			} else {
				echo json_encode(array('message'=>'请输入套餐 id 与折扣码', 'error'=>true));
			}
		} else {
			echo json_encode(array('message'=>'请重新登录', 'error'=>true));
		}
	} else {
		header('Location: member.php?action=login');
	}
} else {
	header('Location: member.php?action=login');
}