// wenyinos 统一认证：改密成功后反向同步到中心（开关停用时不推送）
// 注意：此处 $password_new 已被原生流程改写为 md5(md5(明文).salt)，需从 POST 重取原始值（= md5(新明文)）
if(sso_enabled())
{
	$wysso_new_md5 = param('password_new');
	if(preg_match('/^[a-f0-9]{32}$/', (string)$wysso_new_md5))
	{
		sso_api('password', array('account' => $user['username'], 'password' => $wysso_new_md5));
	}
}
