<?php

!defined('DEBUG') and exit('Forbidden');

$setting = setting_get('abs_nav_menus');
if (empty($setting)) {

    $setting = array(
        'primary_menu' => array(
            array('lid' => 1, 'icon' => 'icon-home', 'name' => '首页', 'title' => '', 'href' => '.', 'order' => 0, 'class' => ''),
            array('lid' => 2, 'icon' => 'icon-comments', 'name' => '话题', 'href' => '__forumlist_submenu__', 'order' => 100, 'class' => ''),
        ),
        'secondary_menu' => array(
            array('lid' => 1, 'icon' => 'icon-user', 'name' => '用户', 'href' => '__user_submenu__', 'order' => 50, 'class' => ''),
            array('lid' => 2, 'icon' => 'icon-user', 'name' => '登录', 'href' => '__btn_login__', 'order' => 100, 'class' => ''),
            array('lid' => 3, 'icon' => 'icon-search', 'name' => '搜索', 'href' => '__search__', 'order' => 10, 'class' => ''),
            array('lid' => 4, 'icon' => 'icon-bell', 'name' => '通知', 'href' => '__user_notice__', 'order' => 49, 'class' => ''),
        ),
        'tertiary_menu' => array(
            array('lid' => 1, 'icon' => 'icon-comments', 'name' => '0', 'href' => '__forumlist__', 'order' => 0, 'class' => ''),
        ),
        'quaternary_menu' => array(
            array('lid' => 4, 'icon' => 'icon-search', 'name' => '搜索', 'href' => '__search_box__', 'order' => 10, 'class' => ''),
            array('lid' => 3, 'icon' => 'icon-plus', 'name' => '发新帖', 'href' => '__btn_thread_new__', 'order' => 100, 'class' => ''),
        ),

        'user_menu' => array(
            array('lid' => 1, 'icon' => 'icon-home', 'name' => '我的主页', 'href' => url('my'), 'order' => 0),
            array('lid' => 2, 'icon' => 'icon-comments', 'name' => '我的帖子', 'href' => url('my-thread'), 'order' => 0),
            array('lid' => 3, 'icon' => 'icon-circle-o', 'name' => '——', 'href' => '__divider__', 'order' => 50),
            array('lid' => 4, 'icon' => 'icon-cog', 'name' => '后台', 'href' => '__admin__', 'order' => 100),
            array('lid' => 5, 'icon' => 'icon-user', 'name' => '退出', 'href' => '__user_logout__', 'order' => 100),
        ),

        'footer_menu' => array(),

        'custom_1' => array(),
        'custom_2' => array(),
        'custom_3' => array(),
        'custom_4' => array(),
        'custom_5' => array(),
    );


    setting_set('abs_nav_menus', $setting);
}
