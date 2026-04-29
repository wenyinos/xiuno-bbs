<?php

!defined('DEBUG') and exit('Access Denied.');

/**
 * @var array $xn_nav_menus 菜单数据
 */
$xn_nav_menus = setting_get('abs_nav_menus');

/**
 * @var array $_this_menu 当前编辑的菜单槽位 数组
 */
$_this_menu = array();

/**
 * @var string $_this_menu_parent 当前编辑的菜单槽位 的上级菜单
 */
$_this_menu_parent = '';

/**
 * @var int $maxid 该菜单槽位内含多少个菜单项
 */
$maxid = 0;

/**
 * @var string $selected_menu 当前编辑的菜单ID
 */
$selected_menu = param('selected_menu', '');


$action = param('action', '');

/**
 * @var bool $not_selected_menu 没有选中菜单? false为选中了菜单
 */
$not_selected_menu = true;

/**
 * 可选二级菜单 去除父菜单；解决“无限循环菜单”问题
 *
 * @param array $_this_menu_available_submenu_slots
 * @param string $selected_menu
 * @param array $found_arr 
 * @return array
 */
function xn_nav_menu_check_parent($_this_menu_available_submenu_slots, $selected_menu, $found_arr) {
	foreach ($found_arr as $key => $value) {

		$found_key = array_search($selected_menu, $value);

		if ($found_key === false) { //如果没选子菜单，则跳过
			continue;
		} else {
			unset($_this_menu_available_submenu_slots[$key]); //去除父菜单,否则会死循环
			$_this_menu_parent = $key; //父菜单的key

			$_this_menu_available_submenu_slots = xn_nav_menu_check_parent($_this_menu_available_submenu_slots, $key, $found_arr); //重复此过程，直到没有父菜单选择了此菜单为止
		}
	}
	return $_this_menu_available_submenu_slots;
}

if ($method == 'GET') {
	//查
	$header['title'] = lang('menu') . ' ' . lang('setting');
	$header['mobile_title'] = lang('menu');
	if (param(3, '') === 'export') {
		if (!empty($selected_menu)) {
			message(0, lang('copy the following data') . '<pre class="text-wrap break-all" style="user-select:all">' . xn_nav_menu_slot_items_to_string($selected_menu) . '</pre>');
		} else {
			message(1, lang('Menu not exist'));
		}
	} elseif (param(3, '') === 'import') {
		$r = '<form action="' . url('plugin-setting-abs_menu-import') . '" method="post">'
			. '<input type="hidden" name="selected_menu" value="' . $selected_menu . '">'
			. '<label for="new_menu_data">' . lang('paste the menu data here') . '</label>'
			. form_textarea('new_menu_data', '')
			. '<button type="submit" class="btn btn-primary btn-block mt-3">' . lang('menu_import') . '</button>'
			. '</form>';
		message(-101, $r);
	} elseif (!empty($selected_menu)) { //如果选择了菜单

		if (isset($xn_nav_menus[$selected_menu]) && empty($xn_nav_menus[$selected_menu])) { //如果菜单存在，但是空的，则添加一个菜单项
			$not_selected_menu = false;
			$_this_menu = array(array('lid' => 1, 'icon' => '🙁', 'name' => '该菜单没有菜单项', 'title' => '提示', 'href' => '#添加一个菜单项吧！', 'desc' => '保存后本项将消失', 'order' => 0, 'class' => '', 'submenu' => ''));
			$maxid = count($_this_menu);
		} elseif (isset($xn_nav_menus[$selected_menu]) && !is_null($xn_nav_menus[$selected_menu])) { //如果菜单存在
			$not_selected_menu = false;
			$_this_menu = $xn_nav_menus[$selected_menu];
			$maxid = count($_this_menu);
		} else { //如果菜单不存在
			message(1, lang('Menu not exist'));
		}

		/**
		 * @var array $_this_menu_available_submenu_slots 当前菜单槽位可以选择的二级菜单
		 */
		$_this_menu_available_submenu_slots = $abs_nav_menus_slots;
		unset($_this_menu_available_submenu_slots[$selected_menu]);  //不能选择当前编辑的菜单,否则会死循环

		$found_arr = array();
		foreach ($xn_nav_menus as $key => $value) {
			$found_arr[$key] = array_column($value, 'submenu');  //查找所有菜单槽位里,选择了子菜单的菜单项
		}

		$_this_menu_available_submenu_slots = xn_nav_menu_check_parent($_this_menu_available_submenu_slots, $selected_menu, $found_arr); //去除父菜单

	}

	include _include(APP_PATH . 'plugin/abs_menu/setting.htm');
} elseif ($method == 'POST') {
	if ($action == 'edit_menu' && !empty($selected_menu)) {

		$s_lid_arr = param('lid', array(0));
		$s_order_arr = param('order', array(0));
		$s_icon_arr = param('icon', array(''));
		$s_name_arr = param('name', array(''));
		$s_href_arr = param('href', array());
		$s_title_arr = param('title', array(''));
		$s_desc_arr = param('desc', array(''));
		$s_class_arr = param('class', array(''));
		$s_attr_arr = param('attr', array(''));
		$s_submenu_arr = param('submenu', array(''));
		$menu_save_arr = array();
		//var_dump($s_submenu_arr);
		$xn_current_menu = $xn_nav_menus[$selected_menu];

		foreach ($s_lid_arr as $key => $value) {

			//$key = $value;
			if (empty($s_href_arr[$key])) {
				continue;
			}
			$new_menu_item = array(
				'lid' => $key,
				'order' => $s_order_arr[$key],
				'icon' => $s_icon_arr[$key],
				'name' => $s_name_arr[$key],
				'desc' => $s_desc_arr[$key],
				'href' => $s_href_arr[$key],
				'title' => $s_title_arr[$key],
				'class' => $s_class_arr[$key],
				'attr' => $s_attr_arr[$key],
				'submenu' => isset($s_submenu_arr[$key]) ? ($s_submenu_arr[$key] == '_none' ? '' : $s_submenu_arr[$key]) : '',
			);

			if (!isset($xn_current_menu[$key])) {
				//增
				array_push($xn_current_menu, $new_menu_item);
			} else {
				//改
				$xn_current_menu[$key] = $new_menu_item;
			}
		}
		//删
		foreach ($xn_current_menu as $key => $value) {
			if (!isset($s_lid_arr[$key])) {
				unset($xn_current_menu[$key]);
			}
		}

		$xn_current_menu = array_values($xn_current_menu);

		//消除不连续lid编号
		$xn_current_menu_lid = 1;
		foreach ($xn_current_menu as &$value) {
			$value['lid'] = $xn_current_menu_lid;
			$xn_current_menu_lid++;
		}
		unset($xn_current_menu_lid);

		$xn_nav_menus[$selected_menu] = $xn_current_menu;

		setting_set('abs_nav_menus', $xn_nav_menus);

		message(0, '修改成功');
	} elseif (($action == 'import' || param(3, '') == 'import') && !empty($selected_menu)) {
		$new_menu_data = param('new_menu_data', '');
		$r = xn_nav_menu_item_set($selected_menu, $new_menu_data, true);
		if ($r) {
			message(0, jump(lang('Menu items imported successfully'), 'back', 1));
		} else {
			message(-1, lang('Incomplete or incorrect menu data'));
		}
	}
} else {
	message(-1, 'Bad Request');
}
