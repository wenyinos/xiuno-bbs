<?php

!defined('DEBUG') AND exit('Access Denied.');

// hook admin_index_menu_after.php

// 只允许管理员登陆后台
// Only allow administrators to log in the background

// 对于越权访问，可以默认为黑客企图，不用友好提示。
// For unauthorized access, can default to the hacking attempt, without a friendly reminder.
if(DEBUG < 3) {
	// 管理组检查 / check admin group
	if($gid != 1) {
		setcookie('bbs_sid', '', $time - 86400);
		//http_403();
		http_location(url('../user-login'));
	}
	
	// 管理员令牌检查 / check admin token
	admin_token_check();
}

// CSRF 防护：后台 POST 的 Origin/Referer 若存在则必须同源 / check request origin for admin POST
if($method == 'POST') {
	$origin = _SERVER('HTTP_ORIGIN');
	$referer = _SERVER('HTTP_REFERER');
	$host = _SERVER('HTTP_HOST');
	$csrf_ok = TRUE;
	if($origin !== NULL) {
		$origin_host = parse_url($origin, PHP_URL_HOST);
		$csrf_ok = $origin_host !== NULL && strcasecmp($origin_host, $host) == 0;
	} elseif($referer !== NULL) {
		$referer_host = parse_url($referer, PHP_URL_HOST);
		$csrf_ok = $referer_host !== NULL && strcasecmp($referer_host, $host) == 0;
	}
	!$csrf_ok AND message(-1, 'Illegal request origin (CSRF check failed).');
}

$route = param(0, 'index');

switch ($route) {
	// hook admin_index_route_case_start.php
	case 'index':		include _include(ADMIN_PATH.'route/index.php'); 	break;
	case 'setting': 	include _include(ADMIN_PATH.'route/setting.php'); 	break;
	case 'forum': 		include _include(ADMIN_PATH.'route/forum.php'); 	break;
	case 'friendlink': 	include _include(ADMIN_PATH.'route/friendlink.php'); 	break;
	case 'group': 		include _include(ADMIN_PATH.'route/group.php'); 	break;
	case 'other':		include _include(ADMIN_PATH.'route/other.php'); 	break;
	case 'user':		include _include(ADMIN_PATH.'route/user.php'); 		break;
	case 'thread':		include _include(ADMIN_PATH.'route/thread.php'); 		break;
	case 'plugin':		include _include(ADMIN_PATH.'route/plugin.php'); 	break;
	// hook admin_index_route_case_end.php
	default: 
		// hook admin_index_route_case_default.php
		include _include(ADMIN_PATH.'route/index.php'); 	break;
		/*
		!is_word($route) AND http_404();
		$routefile = _include(ADMIN_PATH."route/$route.php");
		!is_file($routefile) AND  http_404();
		include $routefile;
		*/
}

?>
