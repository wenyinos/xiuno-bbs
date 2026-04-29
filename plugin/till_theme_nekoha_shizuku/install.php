<?php

!defined('DEBUG') and exit('Forbidden');

$tablepre = $db->tablepre;
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN color varchar(64) NOT NULL default ''";

$r = db_exec($sql);

$setting = setting_get('till_theme_nekoha_shizuku_setting');
if (empty($setting)) {
    $setting = array('below_avatar' => 'none');
    setting_set('till_theme_nekoha_shizuku_setting', $setting);
}