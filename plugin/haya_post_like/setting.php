<?php

!defined('DEBUG') and exit('Access Denied.');

$header['title'] = lang('haya_post_like').lang('setting');

if ($method == 'GET') {
	
	$config = setting_get('haya_post_like');
	
	include _include(APP_PATH.'plugin/haya_post_like/view/htm/setting.htm');
	
} else {
	
	$config = array();
	
	$config['open_thread'] = param('open_thread', 0);
	$config['open_post'] = param('open_post', 1);
	$config['hot_like_post_low_count'] = param('hot_like_post_low_count', 10);
	$config['hot_like_post_size'] = param('hot_like_post_size', 5);
	$config['hot_like_isfirst'] = param('hot_like_isfirst', 0);
	$config['list_show_likes'] = param('list_show_likes', 0);
	$config['like_is_delete'] = param('like_is_delete', 1);
	$config['delete_time'] = param('delete_time', 10);
	setting_set('haya_post_like', $config); 
	
	message(0, jump(lang('haya_post_like_setting_success_tip'), url('plugin-setting-haya_post_like')));
}

?>