// wenyinos 统一认证：关闭前台自助注册，引导至认证中心注册（开关停用时恢复原生注册）
if(sso_enabled())
{
	$wysso_cfg = sso_config();
	$wysso_reg = !empty($wysso_cfg['login_url']) ? str_replace('login.php', 'register.php', $wysso_cfg['login_url']) : 'https://wenyinos.com/auth/register.php';
	message(-1, '本站注册已关闭：请前往玟茵社区统一认证中心注册账号（' . $wysso_reg . '）');
}
