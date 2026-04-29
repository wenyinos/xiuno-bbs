<?php
exit;

elseif ($action == 'post_like') {

	$header['title'] = lang('haya_post_like')." - " . $conf['sitename'];
	
	if (!$uid) {
		message(0, lang('haya_post_like_login_like_tip'));
	}
	
	// hook post_like_start.php
	
	if ($method == 'POST') {

		$pid = param('pid');

		$post = post_read($pid);
		empty($post) AND message(0, lang('post_not_exists'));

		if ($post['isfirst'] == 1) {
			if (isset($haya_post_like_config['open_thread'])
				&& $haya_post_like_config['open_thread'] != 1
			) {
				message(0, lang('haya_post_like_close_thread_tip'));
			}
		} else {
			if (isset($haya_post_like_config['open_post'])
				&& $haya_post_like_config['open_post'] != 1
			) {
				message(0, lang('haya_post_like_close_post_tip'));
			}
		}
		
		$haya_post_like_check = haya_post_like_find_by_uid_and_pid($uid, $pid);
		
		$action2 = param(2, 'create');
		if ($action2 == 'create') {
			// hook post_like_create_start.php
			
			if (!empty($haya_post_like_check)) {
				message(0, lang('haya_post_like_user_has_like_tip'));
			}
			
			haya_post_like_create(array(
				'tid' => $post['tid'], 
				'pid' => $pid, 
				'uid' => $user['uid'],
				'create_date' => time(),
				'create_ip' => $longip,
			));
			
			haya_post_like_loves($pid, 1);
			
			if (function_exists("notice_send")) {
				// hook post_like_notice_send_start.php
				
				$notice_user = '<a href="'.url('user-'.$user['uid']).'" target="_blank"><img class="avatar-1" src="'.$user['avatar_url'].'"> '.$user['username'].'</a>';
			
				$thread = thread_read($post['tid']);
				$thread['subject'] = notice_substr($thread['subject'], 20);
				$notice_thread = '<a target="_blank" href="'.url('thread-'.$post['tid']).'">【'.$thread['subject'].'】</a>';
		
				$post['message'] = htmlspecialchars(strip_tags($post['message']));
				$post['message'] = notice_substr($post['message'], 20);
				$notice_post = '<a target="_blank" href="'.url('thread-'.$post['tid']).'#'.$post['pid'].'">【'.$post['message'].'】</a>';
				
				if ($post['isfirst'] == 1) {
					$notice_msg_tpl = lang('haya_post_like_send_notice_for_thread');
					
					// hook post_like_notice_send_thread.php
				} else {
					$notice_msg_tpl = lang('haya_post_like_send_notice_for_post');
					
					// hook post_like_notice_send_post.php
				}
				
				$notice_msg = str_replace(
					array('{thread}', '{post}', '{user}'),
					array($notice_thread, $notice_post, $notice_user),
					$notice_msg_tpl
				);

				notice_send($user['uid'], $post['uid'], $notice_msg, 150);
				
				// hook post_like_notice_send_end.php
			}
			
			$haya_post_like_count = haya_post_like_count(array('pid' => $pid));
			$haya_post_like_msg = array(
				'count' => intval($haya_post_like_count),
				'msg' => lang('haya_post_like_like_success_tip'),
			);
			
			// hook post_like_add_end.php
			
			message(1, $haya_post_like_msg);
		} elseif ($action2 == 'delete') {
			// hook post_like_delete_start.php
			
			if (isset($haya_post_like_config['like_is_delete'])
				&& $haya_post_like_config['like_is_delete'] != 1
			) {
				message(0, lang('haya_post_like_no_unlike_tip'));
			}
			
			if (empty($haya_post_like_check)) {
				message(0, lang('haya_post_like_user_no_like_tip'));
			}
			
			$post_like = haya_post_like_read_by_uid_and_pid($uid, $pid);

			$delete_time = intval($haya_post_like_config['delete_time']);
			if ($post_like['create_date'] + $delete_time > time()) {
				message(0, lang('haya_post_like_no_fast_like_tip'));
			}
			
			haya_post_like_delete_by_pid_and_uid($pid, $user['uid']);
			
			haya_post_like_loves($pid, -1);
			
			$haya_post_like_count = haya_post_like_count(array('pid' => $pid));
			$haya_post_like_msg = array(
				'count' => intval($haya_post_like_count),
				'msg' => lang('haya_post_like_unlike_success_tip'),
			);
			
			// hook post_like_delete_end.php
			
			message(1, $haya_post_like_msg);
		}
		
		// hook post_like_post_end.php
		
		message(1, lang('haya_post_like_like_error_tip'));	
	}
	
	// hook post_like_end.php
	
	message(1, lang('haya_post_like_like_error_tip'));

}


?>