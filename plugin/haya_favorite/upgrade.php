<?php

/**
 * 帖子收藏 更新文件
 *
 * @create 2018-2-24
 * @author deatil
 */
 
!defined('DEBUG') AND exit('Forbidden');

function haya_favorite_db_field_exists($tablename, $fieldname) {
	$isexists = db_sql_find_one("DESCRIBE " . $tablename . " `{$fieldname}`");
	return !empty($isexists) ? true : false;
}

$tablepre = $db->tablepre;

if (haya_favorite_db_field_exists("{$tablepre}thread", "haya_favorites")) {
	$r = db_exec("ALTER TABLE `{$tablepre}thread` CHANGE COLUMN `haya_favorites` `favorites` int(11) NULL DEFAULT '0' COMMENT '收藏数';");
}

// 更新插件配置
$haya_favorite_config = array(
	"user_favorite" => 10,
	"user_favorite_count" => 20,
	"user_favorite_sort" => 'desc',
	"thread_show_favorite" => 0,
	"show_hot_favorite" => 0,
	"hot_favorite_count" => 10,
	"hot_favorite_find_time" => 30,
	"hot_favorite_cache_time" => 86400,
);
setting_set('haya_favorite', $haya_favorite_config); 

?>