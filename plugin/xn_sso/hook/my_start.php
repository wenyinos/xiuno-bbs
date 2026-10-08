// wenyinos 统一认证：改密等资料修改统一定向中心账号设置页（避免多端修改造成误解；总开关停用时恢复原生）
if(sso_enabled() && $action === 'password')
{
	$wysso_cfg = sso_config();
	$wysso_target = !empty($wysso_cfg['login_url']) ? str_replace('login.php', 'profile.php', $wysso_cfg['login_url']) : 'https://wenyinos.com/auth/profile.php';
	http_location($wysso_target);
	exit;
}
