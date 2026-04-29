<?php

/*
	Xiuno BBS 4.0 插件实例：广告插件设置
	admin/plugin-setting-xn_ad.htm
*/

!defined('DEBUG') and exit('Access Denied.');

$setting = setting_get('till_theme_nekoha_shizuku_setting');

if ($method == 'GET') {

	$input = array();
	$input['below_avatar'] = form_radio('below_avatar', array('none' => '无', 'username' => '用户名', 'uid' => 'UID', 'usergroup' => '用户组'), $setting['below_avatar']);
	$input['threadlist_ajax'] = form_radio_yes_no('threadlist_ajax', $setting['threadlist_ajax']);

	include _include(APP_PATH . 'plugin/till_theme_nekoha_shizuku/setting.htm');
} else {

	$setting['below_avatar'] = param('below_avatar', '');
	$setting['threadlist_ajax'] = param('threadlist_ajax', false);

	setting_set('till_theme_nekoha_shizuku_setting', $setting);

	message(0, '修改成功');
}