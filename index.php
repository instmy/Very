<?php

// $start_time = microtime();

include 'library/medoo.php';
include 'library/region.php';
$db = new Medoo();

if (isset($_GET['action'])) {
	$action = &$_GET['action'];
	if ($action == 'commodity') {
		$where = array();
		$region_where = array();
		if (isset($_GET['region'])) {
			$where = array('region'=>&$_GET['region']);
			$region_where = array('id'=>&$_GET['region']);
		}
		$region = $db->get('region', array('id', 'name', 'introduction', 'max_member'), $region_where);
		$region_db = new Medoo($region_connect[$region['id']]);
		$region['active_member'] = $region_db->count('user', array('expired_time[>]'=>time()));
		$region['usable_member'] = $region['max_member'] - $region['active_member'];
		unset($region['active_member'], $region['max_member']);
		
		$commodity = $db->select('commodity', array('id', 'name', 'introduction', 'time', 'price'), $where);
		echo json_encode(array('commodity'=>$commodity, 'region'=>$region));
	} else {
		header('Location: index.php');
	}
} else {
	$title = '入门';
	$region = $db->select('region', array('id', 'name'));
	include 'views/index.php';
}