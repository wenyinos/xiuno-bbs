// wenyinos 统一认证：登录态继承（未登录且持有中心票据时兑换；开关停用时跳过）
// 注入点位于 index.inc.php 的 $uid/$user 判定之后、路由分发之前，同作用域可直接覆盖变量
// 票据处理三态：
//   - 兑换成功：静默建立登录态
//   - 中心业务拒绝（1004 未开通 / 1003 禁用 / 1001 不存在）：定向回中心面板查看提示；
//     按票据值缓存（本会话内后续访问不再重复请求中心，直接回中心）；
//     在中心退出登录（票据清除/更换）后恢复游客浏览
//   - 票据无效/过期（2xxx）/协议异常（3xxx）/中心不可达：静默游客，不缓存
$wysso_ticket = isset($_COOKIE['wy_auth']) ? $_COOKIE['wy_auth'] : '';
$wysso_conf   = sso_config();
if(sso_enabled() && empty($uid) && $wysso_ticket !== '')
{
	// 本会话已被中心业务拒绝 → 每次访问直接回中心（中心退出后票据清除，此分支不再进入）
	if(isset($_SESSION['wy_sso_denied_ticket']) && $_SESSION['wy_sso_denied_ticket'] === $wysso_ticket)
	{
		header('Location: ' . $wysso_conf['login_url']);
		exit;
	}

	$wysso_resp = sso_api('ticket', array('ticket' => $wysso_ticket));

	if($wysso_resp !== NULL)
	{
		$wysso_code = intval($wysso_resp['code']);
		if($wysso_code === 0)
		{
			$wysso_uid = sso_upsert_user($wysso_resp['data']);   // ticket 场景：不带密码
			if($wysso_uid)
			{
				unset($_SESSION['wy_sso_denied_ticket']);
				$uid = $wysso_uid;
				$_SESSION['uid'] = $uid;
				user_token_set($uid);

				// 重载当前请求的用户上下文（与 index.inc.php 17-28 行一致）
				$user = user_read($uid);
				$gid = intval($user['gid']);
				$group = isset($grouplist[$gid]) ? $grouplist[$gid] : $grouplist[0];
				$forumlist_show = forum_list_access_filter($forumlist, $gid);
				$forumarr = arrlist_key_values($forumlist_show, 'fid', 'name');
			}
		}
		elseif($wysso_code > 0 && $wysso_code < 2000)
		{
			// 中心业务拒绝（未开通/禁用/不存在）：记录并定向回中心，退出中心后恢复游客
			$_SESSION['wy_sso_denied_ticket'] = $wysso_ticket;
			header('Location: ' . $wysso_conf['login_url']);
			exit;
		}
		// 2xxx（票据无效/过期）与 3xxx（协议异常）：静默继续游客浏览，不缓存
	}
	// NULL（中心不可达）→ 静默，不影响游客浏览
}

// 单点登出轻量校验（M-2）：已登录用户进行写操作（POST）时每 30 分钟校验一次中心票据有效性。
// 中心已登出/撤票（2xxx）→ 静默本地登出；站点准入被撤销（1xxx）→ 本地登出并定向回中心
if(sso_enabled() && !empty($uid) && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST'
	&& (!isset($_SESSION['wy_ticket_checked_at']) || time() - intval($_SESSION['wy_ticket_checked_at']) > 1800))
{
	$_SESSION['wy_ticket_checked_at'] = time();
	$wysso_ck = sso_api('ticket', array('ticket' => isset($_COOKIE['wy_auth']) ? $_COOKIE['wy_auth'] : ''));
	if($wysso_ck !== NULL)
	{
		$wysso_ck_code = intval($wysso_ck['code']);
		if($wysso_ck_code !== 0)
		{
			// 本地登出（同全域登出的本地部分；票据已失效无需 revoke）
			$uid = 0;
			$_SESSION['uid'] = 0;
			user_token_clear();
			unset($_SESSION['wy_ticket_checked_at']);

			if($wysso_ck_code > 0 && $wysso_ck_code < 2000)
			{
				header('Location: ' . $wysso_conf['login_url']);
				exit;
			}
			// 2xxx（中心已登出/撤票）：静默降级为游客继续本请求
		}
	}
}
