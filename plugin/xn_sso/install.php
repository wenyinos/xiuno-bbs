<?php

/*
	Xiuno BBS 4.0 插件：wenyinos 统一认证 安装
	admin/plugin-install-xn_sso.htm
*/

!defined('DEBUG') AND exit('Forbidden');

$kv = kv_get('xn_sso');
$kv = is_array($kv) ? $kv : array();
$kv += array(
	'api_url' => 'https://wenyinos.com/auth/api.php',
	'app_id'  => 'forum',
	'secret'  => '',
	'timeout' => 3,
);
kv_set('xn_sso', $kv);

?>
