<?php

function haya_post_like_create($arr) {
	$r = db_create('post_like', $arr);
	return $r;
}

function haya_post_like_count($cond = array()) {
	$n = db_count('post_like', $cond);
	return $n;
}

function haya_post_like_read_by_uid_and_pid($uid, $pid) {
	$like = db_find_one('post_like', array('pid' => $pid, 'uid' => $uid));
	return $like;
}

function haya_post_like_find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$post_likes = db_find('post_like', $cond, $orderby, $page, $pagesize);
	
	if (!empty($post_likes)) {
		foreach ($post_likes as & $post_like) {
			$post_like['post'] = post_read($post_like['pid']);
			$post_like['user'] = user_read($post_like['uid']);
		}
	}	
	
	return $post_likes;
}

function haya_post_like_find_by_uid_and_pid($uid, $pid) {
	$r = db_find('post_like', array('pid' => $pid, 'uid' => $uid));
	return $r;
}

function haya_post_like_find_by_uid_and_tid($uid, $tid, $num = 20) {
	$r = haya_post_like_find(array('tid' => $tid, 'uid' => $uid), array('create_date' => -1), 1, $num);
	return $r;
}

function haya_post_like_find_by_pid($pid, $num = 20) {
	$haya_post_likes = haya_post_like_find(array('pid' => $pid), array('create_date' => -1), 1, $num); 
	
	return $haya_post_likes;
}

function haya_post_like_find_by_pids($pids, $num = 1000) {
	if (!$pids) {
		return array();
	}

	$orderby = array('create_date' => -1);
	$r = db_find('post_like', array('pid' => $pids), $orderby, 1, $num, 'pid');
	return $r;
}

function haya_post_like_find_by_pids_and_uid($pids, $uid, $num = 1000) {
	if (!$pids) {
		return array();
	}

	$orderby = array('create_date' => -1);
	$r = db_find('post_like', array('pid' => $pids, 'uid' => $uid), $orderby, 1, $num, 'pid');
	return $r;
}

function haya_post_like_delete_by_tid($tid) {
	$r = db_delete('post_like', array('tid' => $tid));
	return $r;
}

function haya_post_like_delete_by_pid($pid) {
	$r = db_delete('post_like', array('pid' => $pid));
	return $r;
}

function haya_post_like_delete_by_uid($uid) {
	$r = db_delete('post_like', array('uid' => $uid));
	return $r;
}

function haya_post_like_delete_by_pid_and_uid($pid, $uid) {
	$r = db_delete('post_like', array('pid' => $pid, 'uid' => $uid));
	return $r;
}

// haya_post_likes + 1
function haya_post_like_loves($pid, $n = 1) {
	if ($n < 0) {
		$post = post__read($pid);
		if ($post['likes'] <= 0) {
			return true;
		}
	}
	
	$r = db_update('post', array('pid' => $pid), array('likes+' => $n));
	return $r;
}

function haya_post_like_find_hot_loves_by_tid($tid, $num = 5, $thumbup = 10) {
	$haya_post_likes = post_find(array('tid' => $tid, 'likes' => array(">=" => $thumbup)), array('likes' => -1, 'create_date' => -1), 1, $num); 
	
	return $haya_post_likes;
}


?>
