<?php
exit;

elseif ($action == 'favorite') {

	$header['title'] = lang('haya_favorite_my_favorite') . " - " . $conf['sitename'];

	$haya_favorite_config = setting_get('haya_favorite');
	
	// hook favorite_start.php
	
	if ($method == 'GET') {
		// hook favorite_detail_start.php
		
		$pagesize = intval($haya_favorite_config['user_favorite']);
		$page = param(2, 1);
		$cond['uid'] = $uid; 
		
		$haya_favorite_count = haya_favorite_count($cond);
		$threadlist = haya_favorite_find($cond, array('create_date' => -1), $page, $pagesize);
		$pagination = pagination(url("my-favorite-{page}"), $haya_favorite_count, $page, $pagesize);
		
		// hook favorite_detail_end.php
		
		include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorite.htm');

	} else {

		$action = param(2, 'add');
		$tid = param('tid');
		if (!$user) {
			message(0, lang('haya_favorite_user_favorite_error_tip'));
		}

		$thread = thread_read($tid);
		empty($thread) AND message(0, lang('thread_not_exists'));
		$haya_check_favorite = haya_favorite_find_by_uid_and_tid($uid, $tid);
		
		$haya_favorite_user_favorite_count = isset($haya_favorite_config['user_favorite_count']) ? intval($haya_favorite_config['user_favorite_count']) : 20;
		
		// hook favorite_start.php
		
		if ($action == 'create') {
			// hook favorite_create_start.php
			
			if (!empty($haya_check_favorite)) {
				message(0, lang('haya_favorite_user_have_favorite_tip'));
			}
			
			haya_favorite_create(array(
				'tid' => $tid, 
				'uid' => $user['uid'],
				'create_date' => time(),
				'create_ip' => $longip,
			));
			
			haya_favorite_thread_user_favorites($tid, 1);
			
			$haya_favorite_count = haya_favorite_count(array('tid' => $tid));

			$haya_favorite_users = haya_favorite_find_by_tid($tid, $haya_favorite_user_favorite_count);
			ob_start();
			include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorite_users.htm');	
			$haya_favorite_user_html = ob_get_clean();
			
			$haya_favorite_msg = array(
				'count' => $haya_favorite_count,
				'users' => $haya_favorite_user_html,
				'msg' => lang('haya_favorite_user_favorite_success_tip'),
			);
			
			if (function_exists("notice_send")) {
				
				// hook favorite_notice_send_before.php
				
				$thread = thread_read($thread['tid']);
				$thread['subject'] = notice_substr($thread['subject'], 20);
				
				$notice_thread_subject = $thread['subject'];
				$notice_thread_substr_subject = htmlspecialchars(strip_tags($thread['subject']));
				$notice_thread_substr_subject = notice_substr($notice_thread_substr_subject, 20);
				$notice_thread_url = url('thread-'.$thread['tid']);
				$notice_thread = '<a target="_blank" href="'.$notice_thread_url.'">《'.$notice_thread_subject.'》</a>';
				
				$notice_user_url = url('user-'.$user['uid']);
				$notice_user_avatar_url = $user['avatar_url'];
				$notice_user_username = $user['username'];
				$notice_user = '<a href="'.$notice_user_url.'" target="_blank"><img class="avatar-1" src="'.$notice_user_avatar_url.'"> '.$notice_user_username.'</a>';
				
				// hook favorite_notice_send.php
				
				$notice_msg = str_replace(
					array(
						'{thread_subject}', '{thread_substr_subject}', '{thread_url}', '{thread}', 
						'{user_url}', '{user_avatar_url}', '{user_username}', '{user}'
					),
					array(
						$notice_thread_subject, $notice_thread_substr_subject, $notice_thread_url, $notice_thread, 
						$notice_user_url, $notice_user_avatar_url, $notice_user_username, $notice_user
					),
					lang('haya_favorite_send_notice_for_thread')
				);
				notice_send($user['uid'], $thread['uid'], $notice_msg, 155);

				// hook favorite_notice_send_end.php				
			}
			
			// hook favorite_create_end.php
			
			message(1, $haya_favorite_msg);
		} elseif ($action == 'delete') {
			// hook favorite_delete_start.php
			
			if (empty($haya_check_favorite)) {
				message(0, lang('haya_favorite_user_no_favorite_error_tip'));
			}
			
			haya_favorite_delete_by_tid_and_uid($tid, $user['uid']);
			
			haya_favorite_thread_user_favorites($tid, -1);
			
			$haya_favorite_count = haya_favorite_count(array('tid' => $tid));
			
			$haya_favorite_users = haya_favorite_find_by_tid($tid, $haya_favorite_user_favorite_count);
			ob_start();
			include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorite_users.htm');	
			$haya_favorite_user_html = ob_get_clean();
			
			$haya_favorite_msg = array(
				'count' => $haya_favorite_count,
				'users' => $haya_favorite_user_html,
				'msg' => lang('haya_favorite_user_delete_favorite_success_tip'),
			);
			
			// hook favorite_delete_end.php
			
			message(1, $haya_favorite_msg);
		}
		
	}

}

elseif ($action == 'favorites') {
	
	$header['title'] = lang('haya_favorite_my_favorite') . " - " . $conf['sitename'];
	
	$haya_favorite_config = setting_get('haya_favorite');
	
	if (strtolower($haya_favorite_config['user_favorite_sort']) == 'asc') {
		$user_favorite_sort = 'asc';
	} else {
		$user_favorite_sort = 'desc';
	}
	
	$orderby = param('orderby', $user_favorite_sort);
	if (strtolower($orderby) == 'asc') {
		$orderby_config = array('create_date' => 1);
	} else {
		$orderby_config = array('create_date' => -1);
	}
	
	$pagesize = intval($haya_favorite_config['user_favorite']);
	$page = param(2, 1);
	$cond['uid'] = $uid; 
	
	$haya_favorite_count = haya_favorite_count($cond);
	$threadlist = haya_favorite_find($cond, $orderby_config, $page, $pagesize);
	$pagination = pagination(url("my-favorites-{page}", array("orderby" => $orderby)), $haya_favorite_count, $page, $pagesize);
	
	include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorites.htm');	
}


?>