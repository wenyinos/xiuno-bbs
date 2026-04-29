<?php

/**
 * 帖子点赞
 *
 * @create 2018-1-31
 * @author deatil
 */
 
!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

// 帖子点赞
$sql = "
CREATE TABLE {$tablepre}post_like (
	`tid` int(11) NOT NULL DEFAULT '0' COMMENT '帖子ID',
	`pid` int(11) NOT NULL DEFAULT '0' COMMENT '回帖ID',
	`uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
	`create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
	`create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
	KEY `tid_uid` (`tid`, `uid`),
	KEY `pid_uid` (`pid`, `uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$r = db_exec($sql);

$sql = "
ALTER TABLE {$tablepre}post ADD COLUMN likes int(11) NULL DEFAULT '0' COMMENT '点赞数';
";
$r = db_exec($sql);

// 添加插件配置
$haya_post_like_config = array(
	"open_thread" => 0,
	"open_post" => 1,
	"hot_like_post_low_count" => 10,
	"hot_like_post_size" => 5,
	"hot_like_isfirst" => 1,
	"delete_time" => 5,
	"list_show_likes" => 0,
	"like_is_delete" => 1,
);
setting_set('haya_post_like', $haya_post_like_config); 

?>