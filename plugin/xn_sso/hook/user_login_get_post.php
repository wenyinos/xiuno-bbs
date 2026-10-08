// wenyinos 统一认证：屏蔽 BBS 内置登录，一律定向认证中心（开关停用时恢复原生登录页）
if(sso_enabled())
{
	$wysso_cfg = sso_config();
	if(!empty($wysso_cfg['login_url']))
	{
		http_location($wysso_cfg['login_url']);
		exit;
	}
}
