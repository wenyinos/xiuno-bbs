<?php

/*
	Xiuno BBS 4.0 插件：wenyinos 统一认证 设置
	admin/plugin-setting-xn_sso.htm
*/

!defined('DEBUG') AND exit('Access Denied.');

if($method == 'GET') {

	$kv = kv_get('xn_sso');

	$input = array();
	$input['enabled'] = form_radio('enabled', array(1 => '启用', 0 => '停用（恢复 BBS 原生登录/注册）'), isset($kv['enabled']) ? $kv['enabled'] : 1);
	$input['api_url'] = form_text('api_url', isset($kv['api_url']) ? $kv['api_url'] : '');
	$input['app_id'] = form_text('app_id', isset($kv['app_id']) ? $kv['app_id'] : 'forum');
	$input['secret'] = form_text('secret', isset($kv['secret']) ? $kv['secret'] : '');
	$input['timeout'] = form_text('timeout', isset($kv['timeout']) ? $kv['timeout'] : 3);
	$input['login_url'] = form_text('login_url', isset($kv['login_url']) ? $kv['login_url'] : '');
	$input['logout_url'] = form_text('logout_url', isset($kv['logout_url']) ? $kv['logout_url'] : '');

	include _include(APP_PATH.'plugin/xn_sso/setting.htm');

} else {

	$kv = array();
	$kv['enabled'] = param('enabled', 0) ? 1 : 0;
	$kv['api_url'] = param('api_url');
	$kv['app_id'] = param('app_id', 'forum');
	$kv['secret'] = param('secret');
	$kv['timeout'] = param('timeout', 3);
	$kv['login_url'] = param('login_url');
	$kv['logout_url'] = param('logout_url');

	kv_set('xn_sso', $kv);

	message(0, '保存成功');
}

?>
