# 面向用户

## 简介

### 主要特点

#### 用户友好
- 一站式管理各个导航菜单；
- 轻松创建二级菜单；
- “魔法菜单项”让你轻松插入特殊菜单项，包括板块列表、用户菜单、登录按钮、发帖按钮等，省时省力；
- 附赠几款适配版菜单，基本上实现开箱即用；

#### 开发者友好
- 只需要一个`function`即可插入菜单；
- 留出了hook点，可添加自己的菜单槽位和“魔法菜单项”；

### 包里有什么

- 插件本体
- 帮助文档 *（你目前在阅读的这个）*
- 演示插件，包含：
  - 原版导航栏（左侧论坛板块，右侧用户信息）
  - 双行导航栏（第一行左侧普通菜单，右侧用户信息；第二行左侧论坛板块，右侧发新帖按钮）
  - 侧边导航栏
  - App底部栏
  - 友情链接栏

### 配置菜单
进入菜单设置后（位于管理员仪表盘顶部导航栏），先选择要进行配置的菜单，然后按“选择”按钮。

每一列的介绍：
- **ID** - 一般情况不需要动。
- **图标** - 输入Font Awesome图标，或图标图片的网址，不填写则不显示图标。
- **导航标签** - 显示在页面上的文字。
- **title属性** - 鼠标悬停在上面时会看到说明文字。
- **描述** - 若主题支持，将会显示这里显示的文字。
- **网址** - 输入网址或“魔法菜单项”；外链需要带`https://`或`https://`前缀，内链不需要前缀（如`thread-1234.htm`）。
- **排序** - 数字越小越靠前，越大越靠后；建议以10的倍数作为顺序，方便后期添加菜单项时将菜单项插入到希望出现的位置中。
  - 比如: `10 20 30 ... 100`
- **CSS类** - 实现特殊效果。
- **自定义属性** - 实现特殊效果。
- **二级菜单** - 选择一个菜单槽位即可让该菜单项拥有二级菜单，该菜单的链接将被忽略。在选择菜单槽位时，你可能会发现缺少几项，**这是特意设计的**，避免创造出“无限循环菜单”，导致网站崩溃。请尽量避免设计出“无限循环菜单”。

在调整完菜单项后，别忘了**点击保存按钮**。

### 魔法菜单项
将“魔法菜单项”输入到“链接”字段即可实现特殊效果。

点击“魔法菜单项参考”按钮查看

> 以下菜单项有特殊效果，但不是魔法菜单项：
> - `.`（小数点） - 主页

### 兼容性
所有与原版导航菜单有关的hook都将失效。新的菜单项必须通过本插件配置才会展示。

可以用“魔法菜单项”弥补，本插件已内置搜索和消息的“魔法菜单项”。

### 备份与恢复菜单槽位里的菜单项

#### 备份

进入想要备份的菜单，点击“导出”按钮，复制数据到一个安全的地方即可。

#### 恢复

进入想要备份的菜单，点击“导出”按钮，复制数据到一个安全的地方即可。

### 卸载本插件
若按照常规方式卸载本插件，本插件的数据将保留在服务器中，以便下次再使用，且防止其他相关插件无法读取菜单数据导致故障。

如果你真的想卸载本插件用于重置菜单数据等，请打开`unstall.php`文件，取消注释即可。

# 面向开发者

## 使用方法

```php
/**
 * 生成导航菜单
 *
 * @param array $args 参数
 * @param array $c_menu 指定菜单结构，若不填写，则使用全局菜单结构
 * @return string 菜单的HTML代码
 */
xn_nav_menu($args);

//定义菜单参数
//这些参数不需要全都填写，留空即使用默认值（即下方写明的值）
$args = array(
    'menu'            => 'primary_menu',   //菜单槽位；若存在$c_menu，则使用$c_menu里的槽位
    'container'       => 'div',            //菜单容器标签；若为false，则不输出容器
    'container_class' => 'menu-container', //菜单容器class
    'container_id'    => '',               //菜单容器id
    'container_attr'  => '',               //菜单容器的其他属性
    'menu_class'      => 'menu',           //菜单本身class
    'menu_id'         => '',               //菜单本身id
    'item_class'      => '',               //菜单项class
    'item_attr'       => '',               //菜单项的其他属性
    'icon_class'      => '',               //图标附加class
    'link_before'     => '',               //链接之前内容
    'link_id'         => '',               //链接id（很少用到）【预留】
    'link_class'      => '',               //链接class
    'link_attr'       => '',               //链接的其他属性
    'before'          => '',               //链接文字之前
    'after'           => '',               //链接文字之后
    'link_after'      => '',               //链接之后内容
    'desc'            => 'span',           //是否显示“描述”？false为不显示，填写一个HTML标签为显示
    'desc_class'      => '',               //描述的class
    'no_ul'           => false,            //不输出ul标签？【用于特殊情况】
    'no_li'           => false,            //不输出li标签？【用于特殊情况】
    'submenu'         => false,            //子菜单槽位；若存在$c_menu，则使用$c_menu里的槽位
    'is_submenu'      => false,            //是否为子菜单？【预留】
    '_submenu_args'   => array(            //子菜单菜单相关参数（可选）【预留】
      'container'       => 'div',          //菜单容器标签
      'container_class' => 'dropdown-menu',//菜单容器class
      'container_id'    => '',             //菜单容器id
      'container_attr'  => '',             //菜单容器的其他属性
      'menu_class'      => 'list-unstyled',//菜单本身class
      'menu_id'         => '',             //菜单本身id
      'link_before'     => '',             //链接id
      'link_class'      => 'dropdown-item',//链接class
      'item_class'      => '',             //链接的其他属性
      'before'          => '',             //链接文字之前
      'after'           => '',             //链接文字之后
      'link_after'      => '',             //链接之后内容
    ),
    '_link_class_has_submenu' => '',       //链接class-有子菜单时【用于特殊情况】
    '_bs_version'     => 4,                //Bootstrap大版本号（用于改变输出）【预留】
    'echo'            => true,             //使用echo还是return？false为return；默认为true，方便使用
);

```

### 实例 - 基本用法

用起来类似于WordPress。
```html
<!-- 这是一个简单的Bootstrap Navbar导航栏 -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <!-- 将<div class="collapse navbar-collapse" id="navbarColor01">原本的菜单</div>替换成xn_nav_menu()即可 -->
    <?php xn_nav_menu(array(
            'menu'            => 'primary_menu',             //菜单槽位
            'container'       => 'div',                      //菜单容器标签
            'container_class' => 'collapse navbar-collapse', //菜单容器class
            'container_id'    => 'navbarColor01',            //菜单容器id
            'menu_class'      => 'navbar-nav',               //菜单class
            'link_class'      => 'nav-link',                 //链接class
            'item_class'      => 'nav-item',                 //菜单项class
            '_bs_version'     => 5,                          //因为Bootstrap 5的下拉菜单写法相较4不同，所以如果使用的Bootstrap大版本号是5的话，需要增加此项，以保证结果正确
        )); ?>
</nav>
```

### 实例 - 让现有菜单栏支持本插件

我们以Xiuno BBS原装导航栏为例。导航栏的代码可以在`view/htm/header_nav.inc.htm`中找到。

#### 第一步：分析现有菜单

主要有两个菜单槽位，一个在左侧，一个在右侧。

左侧菜单显示主页和论坛板块，右侧菜单显示用户信息。

#### 第二步：进行修改

```html
    <!--{hook header_nav_start.htm}-->
    <header class="navbar navbar-expand-lg navbar-light bg-light" id="header">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#nav" aria-controls="navbar_collapse" aria-expanded="false" aria-label="<?php echo lang('toggler_menu');?>">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!--{hook header_nav_logo_before.htm}-->
            
            <a class="navbar-brand text-truncate" href="<?php echo $header['mobile_link'];?>">
                <img src="<?php echo $conf['logo_mobile_url'];?>" class="logo-2">
                <?php if($header['mobile_title']) { ?>
                    <span class="hidden-lg"><?php echo $header['mobile_title'];?></span>
                <?php } ?>
            </a>
            
            <!--{hook header_nav_logo_after.htm}-->
            

            <?php if(empty($uid)) { ?>
                <a class="navbar-brand hidden-lg" href="<?php echo url('user-login');?>" aria-label="<?php echo lang('login');?>"> <i class="icon-user icon"></i></a>
            <?php } else { ?>
                <a class="navbar-brand hidden-lg" href="<?php echo url("thread-create-$fid");?>" aria-label="<?php echo lang('thread_create');?>"><i class="icon-edit icon"></i></a>
            <?php } ?>
            
            <!--{hook header_nav_user_icon_after.htm}-->
            
            <div class="collapse navbar-collapse" id="nav">
                <!-- 左侧：版块 -->
                <!-- 直接使用；如果没有安装本插件，主题会崩溃；建议带上依赖 -->
                <?php xn_nav_menu(array(
                    'menu'            => 'primary_menu',       //菜单槽位
                    'container'       => '',                   //菜单容器标签
                    'container_class' => '',                   //菜单容器class
                    'container_id'    => '',                   //菜单容器id
                    'menu_class'      => 'navbar-nav mr-auto', //菜单class
                    'item_class'      => 'nav-item',           //菜单项class
                    'link_class'      => 'nav-link'            //链接class
                )); ?>

                <!-- 右侧：用户 -->
                <!-- 兼容性写法；如果你不确定你的用户会不会安装本插件，请这样做 -->
                <?php 
                if( function_exists("xn_nav_menu") ): /* 如果插件安装了的话，使用插件的菜单 */
                xn_nav_menu(array(
                    'menu'            => 'secondary_menu', //菜单槽位
                    'container'       => '',               //菜单容器标签
                    'container_class' => '',               //菜单容器class
                    'container_id'    => '',               //菜单容器id
                    'menu_class'      => 'navbar-nav',     //菜单class
                    'item_class'      => 'nav-item',       //菜单项class
                    'link_class'      => 'nav-link'        //链接class
                ));
                else:
                /* 否则使用之前的菜单 */
                ?>

                <ul class="navbar-nav">
					<!--{hook header_nav_user_start.htm}-->
				<?php if(empty($uid)) { ?>
					<li class="nav-item"><a class="nav-link" href="<?php echo url('user-login');?>"><i class="icon-user"></i> <?php echo lang('login');?></a></li>
					<!--<li class="nav-item"><a class="nav-link" href="<?php echo url('user-create');?>"><?php echo lang('register');?></a></li>-->
				<?php } else { ?>
					<li class="nav-item username"><a class="nav-link" href="<?php echo url('my');?>"><img class="avatar-1" src="<?php echo $user['avatar_url'];?>"> <?php echo $user['username'];?></a></li>
					<!-- 管理员 -->
					<?php if($gid == 1) { ?>
					<li class="nav-item"><a class="nav-link" href="admin/"><i class="icon-home"></i> <?php echo lang('admin_page');?></a></li>
					<?php } ?>
					<!--{hook header_nav_admin_page_after.htm}-->
					<li class="nav-item"><a class="nav-link" href="<?php echo url('user-logout');?>"><i class="icon-sign-out"></i> <?php echo lang('logout');?></a></li>
				<?php } ?>
					<!--{hook header_nav_user_end.htm}-->
				</ul>

                <?php endif; ?>
            </div>
        </div>
    </header>
    <!--{hook header_nav_end.htm}-->
```
#### 第三步：让你的插件依赖于本插件（可选）

> *“这样你就能和我永远在一起了！”* *开个玩笑*

如果你的插件离开了本插件将无法使用，请打开你的插件的`conf.json`，将`"dependencies": []`换成：
```json
"dependencies": {
    "abs_menu": "1.0"
}
```
这样，如果用户在没有安装本插件，就想安装你的插件，会出现提示：

> 依赖以下插件： 【菜单 / Menus】 ，请先安装依赖的插件

#### 第四步：让你制作的导航栏一定会替换其他主题的导航栏（可选）
打开你的插件的`conf.json`，将`"overwrites_rank": []`换成：
```json
"overwrites_rank": {
    "view/htm/header_nav.inc.htm": 1000
}
```

## 数据库中的菜单槽位结构
```php
$abs_nav_menus = array(
    //键为槽位ID，值为菜单项

    //【首先】主菜单；原装Xiuno导航菜单的左侧
    'primary_menu' => array(
        //菜单项
        array(
            'lid' => 菜单项ID, 
            'icon' => 图标, 
            'name' => 导航标签, 
            'title' => title属性, 
            'desc' => 菜单项描述（如果主题支持，将在菜单中显示）, 
            'href' => 网址, 
            'order' => 排序, 
            'class' => 菜单项CSS类,
            'attr' => 自定义属性,
            'submenu' => 自定义子菜单,
            //以下为预留参数，暂不对外开放
            'link_class' => 链接CSS类,
            ),
        //下同...
    ),

    //【其次】副菜单；原装Xiuno导航菜单的右侧
    'secondary_menu' => array(), 

    //【然后】副副菜单；双栏菜单的第二行左侧
    'tertiary_menu' => array(), 

    //【最后】副副副菜单；双栏菜单的第二行右侧
    'quaternary_menu' => array(), 
    
    //用户菜单；点击用户头像展开子菜单会用到的菜单
    'user_menu' => array(), 

    //……其他插件的菜单槽位……

    //自定义菜单位置（可以用于子菜单等）
    'custom_1' => array(), 
    'custom_2' => array(), 
    'custom_3' => array(), 
    'custom_4' => array(), 
    'custom_5' => array(), 
)
```

## 函数

### xn_nav_menu_get()

获取指定菜单槽位的菜单项。

```php

//建议使用
$slot_name = 'custom_1';
$menu = setting_get('abs_nav_menus');
$menu[$slot_name];

/**
 * 获取指定菜单槽位的菜单项
 *
 * @param string $slot_name 槽位ID
 * @return array
 */
if(function_exists("xn_nav_menu_get")) {
$menu = xn_nav_menu_get($slot_name = "custom_1");
}
```

### xn_nav_menu_slot_add()

添加菜单槽位。适合在安装插件时操作。

```php
/**
 * 添加菜单槽位
 *
 * @param string $slot_name 槽位ID
 * @param array $prefill_items 预先填充的菜单项；若不填写则为空白菜单
 */
if(function_exists("xn_nav_menu_slot_add")) {
xn_nav_menu_slot_add($slot_name = "custom_1"/* ， $prefill_items */);
}
```

### xn_nav_menu_slot_del()

删除菜单槽位。适合在卸载插件时操作。

**危险操作**——会删除整个菜单槽位，包括其中的菜单项。

```php
/**
 * 删除菜单槽位
 *
 * @param string $slot_name 槽位ID
 */
if(function_exists("xn_nav_menu_slot_del")) {
xn_nav_menu_slot_del($slot_name = "custom_1");
}
```

### xn_nav_menu_item_add() 

向指定菜单槽位添加菜单项。

```php
/**
 * 向指定菜单槽位添加菜单项
 *
 * @param string $slot_name 槽位ID
 * @param array $menu_item 菜单项
 */
if(function_exists("xn_nav_menu_item_add")) {
xn_nav_menu_item_add($slot_name = "custom_1", $menu_item);
}
//$menu_item可以是以下种类之一：
$menu_item = array('order' => 10, 'name' => '我的菜单项', 'href' => '#');

$menu_item = array(
    array('order' => 20, 'name' => '我的菜单项', 'href' => '#'),
    array('order' => 30, 'name' => '我的菜单项', 'href' => '#'),
);
```

### xn_nav_menu_item_set() 

更新指定菜单槽位的指定菜单项。

```php
/**
 * 向指定菜单槽位添加菜单项
 *
 * @param string $slot_name 槽位ID
 * @param array $menu_item 菜单项
 * @param bool $override 覆写菜单项？true: 旧菜单项将完全消失，并被新的菜单项代替；false：正常流程
 */
if(function_exists("xn_nav_menu_item_set")) {
xn_nav_menu_item_set($slot_name = "custom_1", $menu_item);
}
//$menu_item可以是以下种类之一：
$menu_item = array('lid' => 1, 'order' => 10, 'name' => '我的菜单项', 'href' => '#');

$menu_item = array(
    array('lid' => 2, 'order' => 20, 'name' => '我的菜单项', 'href' => '#'),
    array('lid' => 3, 'order' => 30, 'name' => '我的菜单项', 'href' => '#'),
);

$menu_item = 'YToxOntpOjA7YToxMDp7czozOiJsaWQiO2k6MTtzOjU6Im9yZGVyIjtpOjEwO3M6NDoiaWNvbiI7czowOiIiO3M6NDoibmFtZSI7czoxNToi5oiR55qE6I+c5Y2V6aG5IjtzOjQ6ImRlc2MiO3M6MDoiIjtzOjQ6ImhyZWYiO3M6MToiIyI7czo1OiJ0aXRsZSI7czowOiIiO3M6NToiY2xhc3MiO3M6MDoiIjtzOjQ6ImF0dHIiO3M6MDoiIjtzOjc6InN1Ym1lbnUiO3M6MDoiIjt9fQ==';
```

### xn_nav_menu_item_del() 

删除指定菜单槽位的指定菜单项。

```php
/**
 * 删除指定菜单槽位的指定菜单项
 *
 * @param string $slot_name 槽位ID
 * @param string|array $menu_item 菜单项ID
 */
if(function_exists("xn_nav_menu_item_del")) {
xn_nav_menu_item_del($slot_name = "custom_1", $menu_item);
}

$menu_item = 1;

$menu_item = array(2, 3);
```

## Hook点

### menu_magichref_case_end.php
用于添加新的“魔法菜单项”处理方式。


```php

/* =====我的菜单项===== */
elseif ($menu_item['href'] === "__newcase__") :

$r .= '任何HTML元素都可以'; //生成菜单项；必须步骤

elseif ($menu_item['href'] === "__newcase2__") :

$_this_item = array(
    'icon' => 'fa fa-cog', //图标
    'name' => '我的菜单', //可以使用lang()函数
    'href' => url('my-link') //链接；建议使用url()函数，生成符合伪静态设置的链接
);
$r .= xn_nav_menu_item($_this_item, $args); //使用内置函数生成菜单项
```

### menu_magichref_datalist_end.php
将你添加的“魔法菜单项”添加到“魔法菜单项参考”和“网址”框的自动完成列表里。
```php
'__newcase__' => '我的魔法菜单描述',
```

### menu_slot_before_primary.php、menu_slot_after_quaternary.php、menu_slot_before_custom.php、menu_slot_end.htm
让用户可以选择你添加的菜单槽位。

拿不定主意的话，选`menu_slot_before_custom.php`。
```php
'my_menu' => '我的菜单名称',
```

# 作者
- Geticer

# 特别感谢
- Tillreetree - 开发协力
- 七濑胡桃 - 精神支持

# 许可证
**MIT License**

详见插件附带的LICENSE.txt。

# 致敬插件
- App底部栏(git_appdock)
- 导航栏页面增加(rob_links)

# 如果你认为本插件对你很有用，请考虑为我买杯奶茶或你喜欢的饮料

支付宝:

![](https://menherachanfans.eu.org/assets/appreciate_g_z.png)

微信:

![](https://menherachanfans.eu.org/assets/appreciate_g_w.png)
