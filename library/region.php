<?php

/**
 * 区域信息的 Medoo 连接信息
 * @author cenegd<cenegd@live.com>
**/

$region_connect = array(
	/**
	 * 请进行严格的授权
	**/

	// Fire 区域
	1 => array(
		'database_type' => 'mysql',
		'database_name' => 'region_fire',
		'server' => '127.0.0.1',
		'username' => 'region_fire',
		'password' => 'region_fire',
		'charset' => 'utf8',
		'port' => 3306
	),
	// Very 区域，暂用 Fire 区域代替
	2 => array(
		'database_type' => 'mysql',
		'database_name' => 'region_fire',
		'server' => '127.0.0.1',
		'username' => 'region_fire',
		'password' => 'region_fire',
		'charset' => 'utf8',
		'port' => 3306
	)
);