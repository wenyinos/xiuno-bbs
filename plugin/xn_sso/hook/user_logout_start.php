// wenyinos 统一认证：退出走全域登出（开关停用时不做任何处理，原生退出流程接管）
// 1) 销毁中心票据 2) 清 BBS 本地会话 3) 定向中心（清 wy_auth cookie 后回到登录页）
if(sso_enabled())
{
	if(!empty($_COOKIE['wy_auth']))
	{
		sso_api('revoke', array('ticket' => $_COOKIE['wy_auth']));
	}

	$uid = 0;
	$_SESSION['uid'] = 0;
	user_token_clear();

	$wysso_cfg = sso_config();
	$wysso_target = !empty($wysso_cfg['logout_url']) ? $wysso_cfg['logout_url'] : './';
	http_location($wysso_target);
	exit;
}
