/* =====通知===== */
elseif ($menu_item['href'] === "__user_notice__") :
if ($uid != 0 && isset($user['unread_notices'])) {
$_this_item = array(
'icon' => $menu_item['icon'] ,
'name' => $menu_item['name'],
'href' => url('my-notice'),
'before' => '<span class="d-none d-md-inline">',
'after' => ($user['unread_notices'] != 0 ? '</span> <b class="badge badge-danger">'.$user['unread_notices'].'</b>' : '</span>')
);
$r .= xn_nav_menu_item($_this_item, $args);
} else {
continue;
}

/* =====搜索（链接）===== */
elseif ($menu_item['href'] === "__search__") :
$_this_item = array(
'icon' => $menu_item['icon'],
'name' => $menu_item['name'],
'href' => url('search'),
'class' => $menu_item['class'],
'before' => '<span class="d-none d-md-inline">',
'after' => '</span>'
);
$r .= xn_nav_menu_item($_this_item, $args);

/* =====搜索（框）===== */
elseif ($menu_item['href'] === "__search_box__") :
$r .= '<form action="'.url('search').'" method="get" class="form-inline my-2 my-md-0 '.$menu_item['class'].'">'
    .'<div class="input-group m-0">'
        .'<input class="form-control" type="text" name="keyword" placeholder="'.$menu_item['name'].'">'
        .'<div class="input-group-append">'
            .'<button class="btn btn-primary" type="submit"><i class="'.$menu_item['icon'].'"></i></button>'
            .'</div>'
        .'</div>'
    .'</form>';
/* =====搜索（按钮）===== */
/*
elseif ($menu_item['href'] === "__btn_search__") :

$_this_item = array(
'icon' => $menu_item['icon'],
'name' => $menu_item['name'],
'href' => url('search'),
'link_class' => 'btn btn-secondary',
'class' => $menu_item['class']
);

$r .= xn_nav_menu_item($_this_item, $args);
//*/