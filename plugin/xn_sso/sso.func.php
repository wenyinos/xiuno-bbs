<?php
// wenyinos 认证中心接入 · 函数库（由 hook/model_inc_end.php 载入）

// 插件配置（kv 存储；后台插件设置页维护）
function sso_config()
{
	static $conf = NULL;
	if($conf === NULL)
	{
		$kv = kv_get('xn_sso');
		$conf = array(
			'enabled' => isset($kv['enabled']) ? intval($kv['enabled']) : 1,
			'api_url' => isset($kv['api_url']) ? $kv['api_url'] : '',
			'app_id'  => isset($kv['app_id']) ? $kv['app_id'] : 'forum',
			'secret'  => isset($kv['secret']) ? $kv['secret'] : '',
			'timeout' => isset($kv['timeout']) ? intval($kv['timeout']) : 3,
			'login_url'  => isset($kv['login_url']) ? $kv['login_url'] : '',
			'logout_url' => isset($kv['logout_url']) ? $kv['logout_url'] : '',
		);
	}
	return $conf;
}

// 统一认证总开关：启用时登录/注册/退出/票据兑换全部走中心；禁用时恢复 BBS 原生行为
function sso_enabled()
{
	$conf = sso_config();
	return !empty($conf['enabled']);
}

// 调用中心 API；返回响应数组；不可达/异常返回 NULL（降级语义）
function sso_api($action, $data)
{
	$conf = sso_config();
	if(empty($conf['api_url']) || empty($conf['secret'])) return NULL;

	$body = json_encode(array('action' => $action, 'data' => $data), JSON_UNESCAPED_UNICODE);
	if($body === FALSE) return NULL;

	$timestamp = time();
	$nonce = xn_rand(16);
	$sign = hash_hmac('sha256', $conf['app_id'] . '|' . $action . '|' . md5($body) . '|' . $timestamp . '|' . $nonce, $conf['secret']);

	$headers = array(
		'Content-Type: application/json',
		'X-Wy-App: ' . $conf['app_id'],
		'X-Wy-Timestamp: ' . $timestamp,
		'X-Wy-Nonce: ' . $nonce,
		'X-Wy-Sign: ' . $sign,
	);

	$response = FALSE;
	if(function_exists('curl_init'))
	{
		$ch = curl_init($conf['api_url']);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, $conf['timeout']);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $conf['timeout']);
		$response = curl_exec($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		if($errno !== 0 || $response === FALSE) return NULL;
	}
	else
	{
		$ctx = stream_context_create(array('http' => array(
			'method' => 'POST',
			'header' => implode("\r\n", $headers),
			'content' => $body,
			'timeout' => $conf['timeout'],
			'ignore_errors' => TRUE,
		)));
		$response = @file_get_contents($conf['api_url'], FALSE, $ctx);
		if($response === FALSE) return NULL;
	}

	$result = xn_json_decode($response);
	if(empty($result)) return NULL;
	return $result;
}

// 中心用户 → 本地 upsert；返回本地 uid 或 0
// $password_md5：本次登录的 md5(明文)（仅 verify 场景携带；ticket 场景为空）
function sso_upsert_user($data, $password_md5 = '')
{
	global $longip, $time;

	$username = isset($data['username']) ? trim($data['username']) : '';
	if($username === '') return 0;

	$bbs_gid = isset($data['bbs_gid']) ? intval($data['bbs_gid']) : 0;
	$email = isset($data['email']) ? trim($data['email']) : '';
	$realname = isset($data['realname']) ? trim($data['realname']) : '';

	$user = user_read_by_username($username);

	if(empty($user))
	{
		// 邮箱防御：为空或已被占用则不写入
		if($email === '' || user_read_by_email($email)) $email = '';

		$salt = xn_rand(16);
		// ticket 场景无密码：生成不可猜测占位（本地密码路径不可用，须依赖中心）
		$password = $password_md5 !== '' ? md5($password_md5 . $salt) : md5(xn_rand(32) . $salt);

		$arr = array(
			'username' => $username,
			'email' => $email,
			'password' => $password,
			'salt' => $salt,
			'gid' => $bbs_gid > 0 ? $bbs_gid : 101,
			'realname' => $realname,
			'create_ip' => $longip,
			'create_date' => $time,
			'logins' => 0,
			'login_date' => $time,
			'login_ip' => $longip,
		);
		$uid = user_create($arr);
		return $uid === FALSE ? 0 : intval($uid);
	}

	$uid = intval($user['uid']);
	$update = array();

	// 密码同步（仅 verify 场景携带 md5(明文) 时）
	if($password_md5 !== '') $update['password'] = md5($password_md5 . $user['salt']);

	// gid 下发：管理类组（<100）由中心权威；普通组（>=100）保留 BBS 等级自治
	if($bbs_gid > 0 && $bbs_gid < 100 && intval($user['gid']) != $bbs_gid) $update['gid'] = $bbs_gid;

	// 中心权威回传：昵称/邮箱以中心为准同步到本地（邮箱查重防撞唯一键；realname 列宽 16 截断）
	if($realname !== '' && $realname !== $user['realname']) $update['realname'] = mb_substr($realname, 0, 16);
	if($email !== '' && $email !== $user['email'])
	{
		$occupied = user_read_by_email($email);
		if(empty($occupied) || intval($occupied['uid']) === $uid) $update['email'] = $email;
	}

	// 基本资料回传（手机号/QQ 非空才覆盖；列宽截断）
	$mobile = isset($data['mobile']) ? trim($data['mobile']) : '';
	$qq = isset($data['qq']) ? trim($data['qq']) : '';
	if($mobile !== '' && $mobile !== $user['mobile']) $update['mobile'] = substr($mobile, 0, 11);
	if($qq !== '' && $qq !== $user['qq']) $update['qq'] = substr($qq, 0, 15);

	if($update) user_update($uid, $update);
	return $uid;
}
