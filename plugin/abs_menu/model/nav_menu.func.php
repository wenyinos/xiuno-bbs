<?php

/**
 * @var array $abs_nav_menus_slots 菜单槽位(下拉框的选项)
 */
$abs_nav_menus_slots = array(
    '_none' => '无',

    // hook menu_slot_before_primary.php

    'primary_menu' => '【首先】主菜单丨左侧',
    'secondary_menu' => '【其次】副菜单丨右侧',
    'tertiary_menu' => '【然后】副副菜单丨第二行左侧',
    'quaternary_menu' => '【最后】副副副菜单丨第二行右侧',

    // hook menu_slot_after_quaternary.php

    'user_menu' => '用户菜单',

    // hook menu_slot_before_custom.php

    'custom_1' => '自定义菜单 1',
    'custom_2' => '自定义菜单 2',
    'custom_3' => '自定义菜单 3',
    'custom_4' => '自定义菜单 4',
    'custom_5' => '自定义菜单 5',

    // hook menu_slot_end.php

);

/**
 * @var array $abs_nav_menus_magic_href 魔法菜单项
 */
$abs_nav_menus_magic_href = array(
    '__forumlist__' => '论坛板块（一级菜单）<br>【“导航标签”处，输入板块ID（使用英文逗号“,”分隔）则显示指定板块，“0”显示全部板块。】',
    '__forumlist_submenu__' => '论坛板块（二级菜单）<br>【“导航标签”处，在正常导航标签后输入竖线“|”，然后输入板块ID（使用英文逗号“,”分隔）则显示指定板块，否则显示全部板块。】',

    '__user__' => '头像+用户名（个人中心）',
    '__user_submenu__' => '头像+用户名+用户菜单（二级菜单）',
    '__user_logout__' => '用户登出',
    '__admin__' => '后台管理',

    '__btn_login__' => '登录按钮',
    '__btn_register__' => '注册按钮',
    '__btn_thread_new__' => '发新帖按钮',

    // hook menu_magichref_datalist_end.php

    '__divider__' => '分隔线',
    '.' => '首页',
);

//========== 【核心功能开始】 ==========//

/**
 * 生成导航菜单
 *
 * @param array $args 参数，详见函数内$defaults
 * @param array $c_menu 指定菜单结构
 * @return string 菜单的HTML代码
 */
function xn_nav_menu($args, $c_menu = NULL) {

    if (!is_null($c_menu)) {
        $xn_nav_menus = $c_menu;
    } else {
        /** 
         * @var array $xn_nav_menus 导航菜单
         */
        global $xn_nav_menus;
        if (!isset($xn_nav_menus)) {
            $xn_nav_menus = setting_get('abs_nav_menus');
        }
        $c_menu = $xn_nav_menus;
    }

    global $fid;
    global $user;
    global $uid;

    /**
     * @var string $r 输出结果
     */
    $r = "";

    /**
     * @var array $args_defaults 默认参数
     */
    $args_defaults = array(
        'menu'                    => 'primary_menu',   //菜单槽位；若存在$c_menu，则使用$c_menu里的槽位
        'container'               => 'div',            //菜单容器标签；若为false，则不输出容器
        'container_class'         => 'menu-container', //菜单容器class
        'container_id'            => '',               //菜单容器id
        'container_attr'          => '',               //菜单容器的其他属性
        'menu_class'              => 'menu',           //菜单本身class
        'menu_id'                 => '',               //菜单本身id
        'item_class'              => '',               //菜单项class
        'item_attr'               => '',               //菜单项的其他属性
        'icon_class'              => '',               //图标附加class
        'link_before'             => '',               //链接之前内容
        'link_id'                 => '',               //链接id（很少用到）【预留】
        'link_class'              => '',               //链接class
        '_link_class_has_submenu' => '',               //链接class-有子菜单时【用于特殊情况】
        'link_attr'               => '',               //链接的其他属性
        'before'                  => '',               //链接文字之前
        'after'                   => '',               //链接文字之后
        'link_after'              => '',               //链接之后内容
        'desc'                    => false,            //是否显示“描述”？false为不显示，填写一个HTML标签为显示
        'desc_class'              => '',               //描述的class
        'no_ul'                   => false,            //不输出ul标签？【用于特殊情况】
        'no_li'                   => false,            //不输出li标签？【用于特殊情况】
        'submenu'                 => false,            //子菜单槽位；若存在$c_menu，则使用$c_menu里的槽位
        'is_submenu'              => false,            //是否为子菜单？【预留】
        '_bs_version'             => 4,                //Bootstrap大版本号（用于改变输出）【预留】
        'echo'                    => true,             //使用echo还是return？false为return；默认为true，方便使用
    );

    $args = array_merge($args_defaults, $args);


    /* 如果没写container，则container元素不存在 */
    if ($args['container'] != false) {
        $r .= '<' . $args['container']
            . ' class="' . $args['container_class'] . '"'
            . (empty($args['container_id']) ? '' : ' id="' . $args['container_id'] . '"')
            . (empty($args['container_attr']) ? '' : $args['container_attr'])
            . ' >';
    }

    if (!$args['no_ul']) {
        $r .= '<ul class="' . $args['menu_class'] . '"' . (empty($args['menu_id']) ?  '>' : ' id="' . $args['menu_id'] . '">');
    }


    $the_menu = $c_menu[$args['menu']];
    $the_menu_order = array_column($the_menu, 'order');
    array_multisort($the_menu_order, SORT_ASC, $the_menu);

    /**
     * @var string $link_attr_add 附加链接属性
     */
    $link_attr_add = '';

    foreach ($the_menu as $menu_item) {
        /**
         * @var array $_this_item 当前菜单项
         */
        $_this_item = array();
        /* =====如果必要参数缺失，则跳过===== */
        if (
            /*( !isset($menu_item['name']) || empty($menu_item['name'])) || */
            (!isset($menu_item['href']) || empty($menu_item['href']))
        ) {
            continue;
        }
        /* =====魔法菜单项【开始】===== */
        /* =====论坛板块列表===== */
        if (function_exists("forum_list_cache") && $menu_item['href'] === "__forumlist__") :

            $forumlist_nav = array('_forumlist_menu' => array());
            $forumlist_temp = forum_list_cache();
            if (intval($menu_item['name']) == 0) {
                $forumlist_selected = '';
            } else {
                $forumlist_selected = explode(',', $menu_item['name']);
            }

            foreach ($forumlist_temp as $key => $value) {
                if (empty($forumlist_selected) || in_array($key, $forumlist_selected)) {
                    $forumlist_nav['_forumlist_menu'][] = array('lid' => $value['fid'], 'icon' => $value['icon_url'], 'name' => $value['name'], 'href' => url('forum-' . $value['fid']), 'order' => $value['rank'], 'class' => $menu_item['class'], 'attr' => 'fid="' . $value['fid'] . '" data-active="fid-' . $value['fid'] . '" ');
                } else {
                    continue;
                }
            }
            $r .= xn_nav_menu(
                array(
                    'menu'            => '_forumlist_menu',
                    'container'       => '',
                    'container_class' => '',
                    'container_id'    => '',
                    'no_ul'           => true,
                    'no_li'           => $args['no_li'],
                    'menu_class'      => '',
                    'menu_id'         => '',
                    'before'          => '',
                    'after'           => '',
                    'link_before'     => '',
                    'link_after'      => '',
                    'link_class'      => 'nav-link ' . $args['link_class'],
                    'item_class'      => 'nav-item ' . $args['item_class'],
                    'echo'            => false,
                ),
                $forumlist_nav
            );

        /* =====论坛板块列表-含二级菜单===== */
        elseif (function_exists("forum_list_cache") && $menu_item['href'] === "__forumlist_submenu__") :
            if ($args['_bs_version'] === 5) {
                $link_attr_add .= ' data-bs-toggle="dropdown" ';
            } else {
                $link_attr_add .= ' data-toggle="dropdown" ';
            }
            $menu_item['attr'] .= ' ' . $link_attr_add;
            $forumlist_nav = array('_forumlist_menu' => array());
            $forumlist_temp = forum_list_cache();
            $forumlist_selected = array();

            if (strpos($menu_item['name'], "|")  !== false) {
                $forumlist_selected = explode('|', $menu_item['name']); //分割竖线
                $menu_item['name'] = $forumlist_selected[0]; //更新导航菜单名称
                $forumlist_selected = explode(',', $forumlist_selected[1]);
            }

            foreach ($forumlist_temp as $key => $value) {
                if (empty($forumlist_selected) || in_array($key, $forumlist_selected)) {
                    $forumlist_nav['_forumlist_menu'][] = array('lid' => $value['fid'], 'icon' => $value['icon_url'], 'name' => $value['name'], 'href' => url('forum-' . $value['fid']), 'order' => $value['rank'], 'class' => $menu_item['class'], 'attr' => 'fid="' . $value['fid'] . '" data-active="fid-' . $value['fid'] . '" ');
                } else {
                    continue;
                }
            }

            $_this_item = array(
                'icon' => $menu_item['icon'],
                'name' => $menu_item['name'],
                'class' => $menu_item['class'],
                'href' => "#",
                'submenu' => '_forumlist_menu',
                'link_id' => '_forumlist_menu',
                'link_class' => $args['link_class'],
                'link_attr' => 'role="button" aria-haspopup="true" aria-expanded="false"' . $menu_item['attr']
            );
            $r .= xn_nav_menu_item($_this_item, $args, $forumlist_nav);

        /* =====用户===== */
        elseif ($menu_item['href'] === "__user__") :

            if ($uid != 0) {

                $_this_item = array(
                    'icon' => $user['avatar_url'],
                    'name' => $user['username'],
                    'href' => url('my')
                );
                $r .= xn_nav_menu_item($_this_item, $args);
            } else {
                continue;
            }

        /* =====用户-含二级菜单===== */
        elseif ($menu_item['href'] === "__user_submenu__") :
            if ($uid != 0) {
                if ($args['_bs_version'] === 5) {
                    $link_attr_add .= ' data-bs-toggle="dropdown" ';
                } else {
                    $link_attr_add .= ' data-toggle="dropdown" ';
                }
                $menu_item['attr'] .= ' ' . $link_attr_add;
                $_this_item = array(
                    'icon' => $user['avatar_url'],
                    'name' => $user['username'],
                    'class' => $menu_item['class'],
                    'href' => "#",
                    'submenu' => 'user_menu',
                    'link_id' => 'user_menu',
                    'link_class' => $args['link_class'],
                    'link_attr' => 'role="button" aria-haspopup="true" aria-expanded="false"' . $menu_item['attr']
                );

                $r .= xn_nav_menu_item($_this_item, $args, $c_menu);
            } else {
                continue;
            }

        /* =====后台页面===== */
        elseif ($menu_item['href'] === "__admin__") :
            if ($uid != 0 && (isset($user) && $user['gid'] == 1)) {

                $_this_item = array(
                    'icon' => $menu_item['icon'],
                    'name' => lang('admin_page'),
                    'href' => 'admin/'
                );
                $r .= xn_nav_menu_item($_this_item, $args);
            } else {
                continue;
            }

        /* =====用户登出===== */
        elseif ($menu_item['href'] === "__user_logout__") :

            if ($uid != 0) {
                $_this_item = array(
                    'icon' => $menu_item['icon'],
                    'name' => lang('logout'),
                    'href' => url('user-logout')
                );
                $r .= xn_nav_menu_item($_this_item, $args);
            } else {
                continue;
            }

        /* =====登录按钮===== */
        elseif ($menu_item['href'] === "__btn_login__") :

            if ($uid == 0) {
                $_this_item = array(
                    'icon' => $menu_item['icon'],
                    'name' => lang('login'),
                    'href' => url('user-login'),
                    'link_class' => 'btn btn-primary',
                    'link_attr' => 'role="button"',
                );
                $r .= xn_nav_menu_item($_this_item, $args);
            } else {
                $r .= '';
            }


        /* =====注册按钮===== */
        elseif ($menu_item['href'] === "__btn_register__") :
            if ($uid == 0) {
                $_this_item = array(
                    'icon' => $menu_item['icon'],
                    'name' => lang('register'),
                    'href' => url('user-create'),
                    'link_class' => 'btn btn-secondary',
                    'link_attr' => 'role="button"',

                );
                $r .= xn_nav_menu_item($_this_item, $args);
            } else {
                $r .= '';
            }

        /* =====发新帖按钮===== */
        elseif ($menu_item['href'] === "__btn_thread_new__") :
            if ($uid != 0) {
                $_this_item = array(
                    'icon' => $menu_item['icon'],
                    'name' => $menu_item['name'],
                    'href' => url('thread-create-' . $fid),
                    'link_class' => 'btn btn-primary ' . $menu_item['class'],
                    'link_attr' => 'role="button"',

                );

                $r .= xn_nav_menu_item($_this_item, $args);
            } else {
                $r .= '';
            }

        /* =====分隔线===== */
        elseif ($menu_item['href'] === "__divider__") :
            if ((isset($menu_item['submenu']) && !empty($menu_item['submenu'])) || (strpos($args['container_class'], 'dropdown-menu') !== false || strpos($args['menu_class'], 'dropdown-menu') !== false)) {
                $r .= '<div class="dropdown-divider"></div>';
            } else {
                $r .= '<div class="vr border-right border-dark"></div>';
            }

        /* =====其他===== */

        // hook menu_magichref_case_end.php

        /* =====魔法菜单项【结束】===== */
        /* =====标准菜单项===== */
        else :
            if (isset($menu_item['submenu']) && !empty($menu_item['submenu'])) {
                if ($args['_bs_version'] === 5) {
                    $link_attr_add .= ' data-bs-toggle="dropdown" ';
                } else {
                    $link_attr_add .= ' data-toggle="dropdown" ';
                }
                //var_dump($menu_item['href']);
                $menu_item['attr'] .= ' ' . $link_attr_add;
                $_this_item = array(
                    'icon' => $menu_item['icon'],
                    'name' => $menu_item['name'],
                    'class' => $menu_item['class'],
                    'href' => "#",
                    'href_raw' => $menu_item['href'],
                    'submenu' => $menu_item['submenu'],
                    'link_id' => $menu_item['submenu'],
                    'link_class' => $args['link_class'],
                    'link_attr' => 'role="button" aria-haspopup="true" aria-expanded="false"' . $menu_item['attr'],
                    'is_submenu' => true,
                );
                $r .= xn_nav_menu_item($_this_item, $args, $c_menu);
            } else {
                $r .= xn_nav_menu_item($menu_item, $args);
            }

        endif;
    }

    if (!$args['no_ul']) {
        $r .= '</ul>';
    }


    if ($args['container'] != false) {
        $r .= '</' . $args['container'] . '>';
    }


    if ($args['echo']) {
        echo $r;
    } else {
        return $r;
    }
}

/**
 * 生成菜单项
 *
 * @param array $menu_item 菜单项数据
 * @param array $args 来自xn_nav_menu的参数
 * @param array $c_menu 来自xn_nav_menu的全部菜单项
 * @return string 菜单项的HTML代码
 */
function xn_nav_menu_item($menu_item, $args, $c_menu = array()) {
    $_this_item_href_raw = (isset($menu_item['href_raw']) ? $menu_item['href_raw'] : $menu_item['href']) ;
    //var_dump($menu_item);

    $_submenu_args_default = array(
        'container'       => 'div',
        'container_class' => 'dropdown-menu',
        'container_id'    => '',
        'container_attr'  => '',
        'menu_class'      => 'list-unstyled',
        'menu_id'         => '',
        'icon_class'      => '',
        'link_before'     => '',
        'link_class'      => 'dropdown-item',
        'item_class'      => '',
        'before'          => '',
        'after'           => '',
        'link_after'      => '',
    );

    if (!isset($args['_submenu_args'])) {
        $args['_submenu_args'] = $_submenu_args_default;
    }

    /**
     * @todo 实现多层嵌套下拉菜单 https://github.com/mdbootstrap/bootstrap-nested-dropdown
     */

    /**
     * @var string $r 输出结果
     */
    $r = '';
    $_this_item_class = '';
    $_this_item_link_class = '';
    //var_dump($_this_item_href_raw);
    if ((isset($menu_item['class']) && !empty($menu_item['class']))) {
        $_this_item_class = $menu_item['class'] . ' ' . $args['item_class'];
    } elseif (!empty($args['item_class'])) {
        $_this_item_class = $args['item_class'];
    }
    if (isset($menu_item['submenu']) && !empty($menu_item['submenu'])) {
        $_this_item_class .= ' dropdown';
        if (isset($args['_link_class_has_submenu']) && !empty($args['_link_class_has_submenu'])) {
            $_this_item_link_class .= ' ' . $args['_link_class_has_submenu'];
        } else {
            $_this_item_link_class .= ' dropdown-toggle';
        }
        $menu_item['link_class'] = $args['link_class'] . $_this_item_link_class;
        $menu_item['href_raw'] = $menu_item['href'];
        $menu_item['href'] = "#";
    }
    
    if ($args['no_li'] == false) {
        /**
         * @var bool $is_forum 该链接是否为论坛版块
         */
        $is_forum = false;
        if (!empty($_this_item_href_raw)) {
            $is_forum = strpos($_this_item_href_raw, 'forum');
        }
        
        $r .= '<li ' . 'class="' . $_this_item_class . '" '
            . ((isset($menu_item['title']) && !empty($menu_item['title'])) ?  ' title="' . $menu_item['title'] . '" ' : '')
            . ((isset($menu_item['id']) && !empty($menu_item['id'])) ?  ' id="' . $menu_item['id'] . '" ' : '')
            . ($is_forum !== false ? 'data-active="fid-' . str_replace(['?forum-', 'forum-', 'forum/'], '', str_replace('.htm', '', $_this_item_href_raw)) . '" ' : '')
            . ((isset($menu_item['attr']) && !empty($menu_item['attr'])) ? $menu_item['attr'] : ' ')
            . '>';
        unset($is_forum);
    }


    $r .= $args['link_before'];

    if (!empty($menu_item['href'])) {
        $r .= '<a '
            . 'href="' . $menu_item['href'] . '" '
            . 'class="' . (isset($menu_item['link_class']) ? $menu_item['link_class'] : $args['link_class']) . '" '
            . (isset($menu_item['link_id']) ?  'id="' . $menu_item['link_id'] . '" ' : '')
            . (isset($menu_item['link_attr']) ? $menu_item['link_attr'] : '')
            . '>';
    } else {
        $r .= '<span '
            . 'class="' . (isset($menu_item['link_class']) ? $menu_item['link_class'] : $args['link_class']) . '" '
            . (isset($menu_item['link_id']) ?  'id="' . $menu_item['link_id'] . '" ' : '')
            . (isset($menu_item['link_attr']) ? $menu_item['link_attr'] : '')
            . '>';
    }
    // 去掉图标左右的空格，否则判断失效
    $menu_item['icon'] = trim($menu_item['icon']);
    if (strpos($menu_item['icon'], "?") !== false) {
        $menu_item['icon'] = explode('?', $menu_item['icon'])[0];
    }
    if (!empty($menu_item['icon'])) {
        if (
            strpos($menu_item['icon'], "http://") !== false
            || strpos($menu_item['icon'], "https://") !== false
            || strpos($menu_item['icon'], "view/") !== false
            || strpos($menu_item['icon'], "upload/") !== false
        ) {
            $_this_item_icon_class = $args['icon_class'] . ' navbar-icon avatar-1';
            $r .= '<img src="' . $menu_item['icon'] . '" class="' . $_this_item_icon_class  . '" /> ';
        } else {
            $r .= '<i class="' . $args['icon_class'] . ' ' . $menu_item['icon'] . '"></i> ';
        }
    }
    $r .= $args['before'];
    if (isset($menu_item['before'])) {
        $r .= $menu_item['before'];
    }
    $r .= $menu_item['name'];
    if (isset($menu_item['after'])) {
        $r .= $menu_item['after'];
    }
    $r .= $args['after'];
    if (!empty($menu_item['href'])) {
        $r .= '</a>';
    } else {
        $r .= '</span>';
    }

    if (isset($menu_item['desc']) && $args['desc'] !== false) {
        $r .= '<' . $args['desc']
            . ' class="' . $args['desc_class'] . '"'
            . ' >';
        $r .= $menu_item['desc'];
        $r .= '</' . $args['desc'] . '>';
    }

    $r .= $args['link_after'];

    if (isset($menu_item['submenu']) && !empty($menu_item['submenu'])) {

        $r .= xn_nav_menu(
            array_merge(array('menu' => $menu_item['submenu'], 'echo' => false), $args['_submenu_args']),
            $c_menu
        );
        /*
            array(
                'menu'            => $menu_item['submenu'],
                'container'       => $args['_submenu_args']['container'],
                'container_class' => 'dropdown-menu',
                'container_id'    => '',
                'container_attr'  => 'aria-labelledby="' . $menu_item['submenu'] . '"',
                'menu_class'      => 'list-unstyled',
                'menu_id'         => '',
                'before'          => '',
                'after'           => '',
                'link_before'     => '',
                'link_after'      => '',
                'link_class'      => 'dropdown-item',
                'item_class'      => '',
                'echo'            => false,
            )*/
    }

    if ($args['no_li'] == false) {
        $r .= '</li>';
    }
    return $r;
}

//========== 【核心功能结束】 ==========//

//========== 【CURD功能开始】 ==========//
/**
 * 获取指定菜单槽位的菜单项
 *
 * @param string $slot_name 槽位ID
 * @return array|bool 如果菜单槽位存在，返回数组；如果不存在，返回false
 */
function xn_nav_menu_get($slot_name) {
    if (empty($slot_name)) {
        return false;
    }

    global $xn_nav_menus;
    if (!isset($xn_nav_menus)) {
        $xn_nav_menus = setting_get('abs_nav_menus');
    }

    if (isset($xn_nav_menus[$slot_name])) {
        return $xn_nav_menus[$slot_name];
    } else {
        return false;
    }
}

/**
 * 添加菜单槽位
 *
 * @param string $slot_name 槽位ID
 * @param array|null $prefill_items 预先填充菜单项
 * @return bool
 */
function xn_nav_menu_slot_add($slot_name, $prefill_items = NULL) {
    if (in_array($slot_name, array('primary_menu', 'secondary_menu', 'tertiary_menu', 'quaternary_menu', 'user_menu'))) {
        return false; //不能删除核心菜单
    }
    $xn_nav_menus = setting_get('abs_nav_menus');
    if (!isset($xn_nav_menus[$slot_name])) { //如果菜单存在
        $xn_nav_menus[$slot_name] = (is_null($prefill_items) ? array() : $prefill_items);
        setting_set('abs_nav_menus', $xn_nav_menus);
    }
    return true;
}

/**
 * 删除菜单槽位
 *
 * @param string $slot_name 槽位ID
 * @return bool
 */
function xn_nav_menu_slot_del($slot_name) {
    if (in_array($slot_name, array('primary_menu', 'secondary_menu', 'tertiary_menu', 'quaternary_menu', 'user_menu'))) {
        return false; //不能删除核心菜单
    }
    $xn_nav_menus = setting_get('abs_nav_menus');
    if (isset($xn_nav_menus[$slot_name])) { //如果菜单存在
        unset($xn_nav_menus[$slot_name]);
        setting_set('abs_nav_menus', $xn_nav_menus);
    }
    return true;
}

/**
 * 向指定菜单槽位添加菜单项
 *
 * @param string $slot_name 槽位ID
 * @param array $menu_item 菜单项
 */
function xn_nav_menu_item_add($slot_name, $menu_item) {
    //因为不同开发者的水平参差不齐，所以得用这么多if来规范化
    if (empty($slot_name) || empty($menu_item) || !is_array($menu_item)) {
        // 需要填写槽位ID和菜单项，菜单项必须是数组
        return false;
    }

    global $xn_nav_menus;
    if (!isset($xn_nav_menus)) {
        $xn_nav_menus = setting_get('abs_nav_menus');
    }

    $xn_current_menu = array();
    if (isset($xn_nav_menus[$slot_name])) { //如果槽位不存在，则结束
        $xn_current_menu = $xn_nav_menus[$slot_name];
    } else {
        return false;
    }

    $maxid = count($xn_current_menu);
    if ($maxid === 0) { //如果总数为0，则最大ID为1
        $maxid = 1;
    }

    if (isset($menu_item[0]) && is_array($menu_item[0])) { //如果$menu_item是数组的话

        foreach ($menu_item as $value) {

            if (isset($value['name']) || isset($value['href'])) { //如果菜单项存在，则保存
                $new_menu_item = array(
                    'lid'   => $maxid,
                    'icon'  => $value['icon'],
                    'name'  => $value['name'],
                    'title' => $value['title'],
                    'desc'  => $value['desc'],
                    'href'  => $value['href'],
                    'order' => $value['order'],
                    'class' => $value['class'],
                    'attr'  => $value['attr'],
                );
                $xn_current_menu[] = $new_menu_item;
                $maxid++;
            } else { //否则结束
                continue;
            }
        }
    } else {
        if (isset($menu_item['name']) || isset($menu_item['href'])) { //如果菜单项存在，则保存
            $new_menu_item = array(
                'lid'   => $maxid,
                'icon'  => $menu_item['icon'],
                'name'  => $menu_item['name'],
                'title' => $menu_item['title'],
                'desc'  => $menu_item['desc'],
                'href'  => $menu_item['href'],
                'order' => $menu_item['order'],
                'class' => $menu_item['class'],
                'attr'  => $menu_item['attr'],
            );
            $xn_current_menu[] = $new_menu_item;
            //本来是想 $xn_nav_menus[$slot_name][] = $menu_item 的，但我不知道你会往这里写什么，所以我必须规范化
        } else { //否则结束
            return false;
        }
    }
    $xn_nav_menus[$slot_name] = $xn_current_menu;
    setting_set('abs_nav_menus', $xn_nav_menus);
    return true;
}

/**
 * 向指定菜单槽位更新菜单项
 *
 * @param string $slot_name 槽位ID
 * @param array $menu_item 菜单项
 * @param bool $override 覆写菜单项？true: 旧菜单项将完全消失，并被新的菜单项代替；false：正常流程
 */

function xn_nav_menu_item_set($slot_name, $menu_item, $override = false) {
    if (empty($slot_name) || empty($menu_item)) {
        return false;
    }
    global $xn_nav_menus;
    if (!isset($xn_nav_menus)) {
        $xn_nav_menus = setting_get('abs_nav_menus');
    }

    $xn_current_menu = array();
    if (isset($xn_nav_menus[$slot_name])) { //如果槽位不存在，则结束
        $xn_current_menu = $xn_nav_menus[$slot_name];
    } else {
        return false;
    }

    $s_lid_arr = array_column($xn_current_menu, 'lid');

    // 如果传入的是字符串，则检查是否为序列化的

    if (is_string($menu_item) && isSerialized(base64_decode($menu_item))) {
        // 反序列化，希望传入的是正确的
        try {
            $menu_item = unserialize(base64_decode($menu_item));
        } catch (\Throwable $th) {
            die;
        }
        // 安全考量，强制检查是否为数组，如果是其他类型，直接结束
        if (!is_array($menu_item)) {
            //message(-1,'');
            return false;
        }
    }
    // 继续...
    if (is_array($menu_item)) {
        if (!$override) {
            // 正常流程
            if (isset($menu_item[0]) && is_array($menu_item[0])) { //如果$menu_item是数组的话

                foreach ($menu_item as $value) {

                    $k = array_search($value['lid'], $s_lid_arr);
                    if ($k !== false) {

                        $new_menu_item = array();
                        $new_menu_item['lid'] = isset($value['lid']) ? $value['lid'] : (isset($xn_current_menu[$k]['lid']) ? $xn_current_menu[$k]['lid'] : '');
                        $new_menu_item['icon'] = isset($value['icon']) ? $value['icon'] : (isset($xn_current_menu[$k]['icon']) ? $xn_current_menu[$k]['icon'] : '');
                        $new_menu_item['name'] = isset($value['name']) ? $value['name'] : (isset($xn_current_menu[$k]['name']) ? $xn_current_menu[$k]['name'] : '');
                        $new_menu_item['title'] = isset($value['title']) ? $value['title'] : (isset($xn_current_menu[$k]['title']) ? $xn_current_menu[$k]['title'] : '');
                        $new_menu_item['desc'] = isset($value['desc']) ? $value['desc'] : (isset($xn_current_menu[$k]['desc']) ? $xn_current_menu[$k]['desc'] : '');
                        $new_menu_item['href'] = isset($value['href']) ? $value['href'] : (isset($xn_current_menu[$k]['href']) ? $xn_current_menu[$k]['href'] : '');
                        $new_menu_item['order'] = isset($value['order']) ? $value['order'] : (isset($xn_current_menu[$k]['order']) ? $xn_current_menu[$k]['order'] : '');
                        $new_menu_item['class'] = isset($value['class']) ? $value['class'] : (isset($xn_current_menu[$k]['class']) ? $xn_current_menu[$k]['class'] : '');
                        $new_menu_item['attr'] = isset($value['attr']) ? $value['attr'] : (isset($xn_current_menu[$k]['attr']) ? $xn_current_menu[$k]['attr'] : '');
                        //如果有新值，就用新值；如果没有新值，就用旧值；如果没有旧值，就用空字符串
                        $xn_current_menu[$k] = $new_menu_item;
                    } else {
                        continue;
                    }
                }
            } else {
                if (isset($menu_item['lid'])) {
                    $k = array_search($menu_item['lid'], $s_lid_arr);
                    if ($k !== false) {

                        $new_menu_item = array();
                        $new_menu_item['lid'] = isset($menu_item['lid']) ? $menu_item['lid'] : (isset($xn_current_menu[$k]['lid']) ? $xn_current_menu[$k]['lid'] : 0);
                        $new_menu_item['icon'] = isset($menu_item['icon']) ? $menu_item['icon'] : (isset($xn_current_menu[$k]['icon']) ? $xn_current_menu[$k]['icon'] : '');
                        $new_menu_item['name'] = isset($menu_item['name']) ? $menu_item['name'] : (isset($xn_current_menu[$k]['name']) ? $xn_current_menu[$k]['name'] : '');
                        $new_menu_item['title'] = isset($menu_item['title']) ? $menu_item['title'] : (isset($xn_current_menu[$k]['title']) ? $xn_current_menu[$k]['title'] : '');
                        $new_menu_item['desc'] = isset($menu_item['desc']) ? $menu_item['desc'] : (isset($xn_current_menu[$k]['desc']) ? $xn_current_menu[$k]['desc'] : '');
                        $new_menu_item['href'] = isset($menu_item['href']) ? $menu_item['href'] : (isset($xn_current_menu[$k]['href']) ? $xn_current_menu[$k]['href'] : '');
                        $new_menu_item['order'] = isset($menu_item['order']) ? $menu_item['order'] : (isset($xn_current_menu[$k]['order']) ? $xn_current_menu[$k]['order'] : 0);
                        $new_menu_item['class'] = isset($menu_item['class']) ? $menu_item['class'] : (isset($xn_current_menu[$k]['class']) ? $xn_current_menu[$k]['class'] : '');
                        $new_menu_item['attr'] = isset($menu_item['attr']) ? $menu_item['attr'] : (isset($xn_current_menu[$k]['attr']) ? $xn_current_menu[$k]['attr'] : '');
                        //如果有新值，就用新值；如果没有新值，就用旧值；如果没有旧值，就用空字符串
                        $xn_current_menu[$k] = $new_menu_item;
                    }
                } else {
                    return false;
                }
            }
            $xn_nav_menus[$slot_name] = $xn_current_menu;
        } else {
            // 强制使用新的菜单项
            $new_menu = array();
            foreach ($menu_item as $k => $v) {
                $new_menu_item = array();
                $new_menu_item['lid']   = isset($v['lid'])   ? $v['lid']   : 0;
                $new_menu_item['icon']  = isset($v['icon'])  ? $v['icon']  : '';
                $new_menu_item['name']  = isset($v['name'])  ? $v['name']  : '';
                $new_menu_item['title'] = isset($v['title']) ? $v['title'] : '';
                $new_menu_item['desc']  = isset($v['desc'])  ? $v['desc']  : '';
                $new_menu_item['href']  = isset($v['href'])  ? $v['href']  : '';
                $new_menu_item['order'] = isset($v['order']) ? $v['order'] : 0;
                $new_menu_item['class'] = isset($v['class']) ? $v['class'] : '';
                $new_menu_item['attr']  = isset($v['attr'])  ? $v['attr']  : '';
                $new_menu_item['submenu']  = isset($v['submenu']) && in_array($v['submenu'], array_keys(xn_nav_menu_get_available_slots())) ? $v['submenu']  : '';
                //如果有新值，就用新值；如果没有新值，就用空字符串
                $new_menu[$k] = $new_menu_item;
            }
            $xn_nav_menus[$slot_name] = $new_menu;
        }

        //var_dump($xn_nav_menus[$slot_name]);

        setting_set('abs_nav_menus', $xn_nav_menus);
        return true;
    }
}

/**
 * 删除指定菜单槽位的指定菜单项
 *
 * @param string $slot_name 槽位ID
 * @param string|array $menu_item 菜单项ID
 */

function xn_nav_menu_item_del($slot_name, $menu_item) {
    if (empty($slot_name) || empty($menu_item)) {
        return false;
    }
    global $xn_nav_menus;
    if (!isset($xn_nav_menus)) {
        $xn_nav_menus = setting_get('abs_nav_menus');
    }

    $xn_current_menu = array();
    if (isset($xn_nav_menus[$slot_name])) { //如果槽位不存在，则结束
        $xn_current_menu = $xn_nav_menus[$slot_name];
    } else {
        return false;
    }

    $s_lid_arr = array_column($xn_current_menu, 'lid');


    if (isset($menu_item[0]) && is_array($menu_item[0])) { //如果$menu_item是数组的话

        foreach ($menu_item as $value) {

            $k = array_search($value, $s_lid_arr);
            if ($k !== false) {
                unset($xn_current_menu[$k]);
            } else {
                continue;
            }
        }
    } else {
        if ($menu_item != 0) {
            $k = array_search($menu_item, $s_lid_arr);
            if ($k !== false) {
                unset($xn_current_menu[$k]);
            }
        } else {
            return false;
        }
    }

    $xn_nav_menus[$slot_name] = $xn_current_menu;
    setting_set('abs_nav_menus', $xn_nav_menus);
    return true;
}
//========== 【CURD功能结束】 ==========//

//========== 【杂项功能开始】 ==========//

/**
 * 获取可用菜单槽位；请尽量使用$abs_nav_menus_slots全局变量，除非无法直接获取$abs_nav_menus_slots，才用该函数
 * @return array
 */
function xn_nav_menu_get_available_slots() {
    global $abs_nav_menus_slots;
    return $abs_nav_menus_slots;
}

/**
 * 获取可用魔法菜单项；请尽量使用$abs_nav_menus_magic_href，除非无法直接获取$abs_nav_menus_magic_href，才用该函数
 * @return array
 */
function xn_nav_menu_get_available_magic_href() {
    global $abs_nav_menus_magic_href;
    return $abs_nav_menus_magic_href;
}

/**
 * 将菜单栏位里的内容转换成字符串，用于备份
 *
 * @param string $slot 槽位
 * @return string 序列化的数组
 */
function xn_nav_menu_slot_items_to_string($slot_name) {
    $r = xn_nav_menu_get($slot_name);
    return base64_encode(serialize($r));
}

if (!function_exists('isSerialized')) {
    /**
     * 是否为序列化的字符串
     *
     * @param string $str
     * @return bool
     */
    function isSerialized($str) {
        try {
            $r = ($str == serialize(false) || @unserialize($str) !== false);
        } catch (\Throwable $th) {
            return false;
        }
        return $r;
    }
}


//========== 【杂项功能结束】 ==========//