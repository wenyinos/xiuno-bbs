# For Users

## Introduction

### Main Features

#### User friendly
- One-stop management of various navigation menus;
- Easily create secondary menus;
- "Magic Menu Items" let you easily insert special menu items, including Forum lists, User menu, Login button, New Post buttons, etc., saving time and effort;
- With several adaptation menus, for out-of-the-box use;

#### Developer friendly
- Insert menus with just one `function`;
- leaves hooks for adding your own menu slots and "Magic Menu Items".

### What's in the pack

- The plugin itself
- Help documentation *(the one you are currently reading)*
- Demo plugin, containing:
  - Original styled navigation bar (forums list on the left, user info on the right)
  - Two-line styled navigation bar (first line with general menu on the left and user info on the right; second line with forums list on the left and posting button on the right)
  - Side navigation bar
  - App Bar (at the bottom)
  - Links widget

### Configure menu items
After entering the menu settings (located in the navigation bar at the top of the admin dashboard), first select the menu slot to be configured, and then press the "Select" button.

Description of each column:
- **ID** - Normally not necessary to change.
- **Icon** - Enter Font Awesome icon class, or the url of the icon image, or leave it blank and no icon will be displayed.
- **Navigation label** - The text displayed on the page.
- **Title attribute** - The text that will be displayed when the mouse is hovered over.
- **Description** - If the theme supports it, the text displayed here will be displayed.
- **URL** - Enter the URL or "Magic Menu Item"; external links need to be prefixed with `https://` or `https://`, internal links don't need a prefix (e.g. `thread-1234.htm`).
- **Order** - The smaller the number the further forward, the larger the further back; A multiple of 10 is recommended as an order to make it easier to insert menu items into the desired position when adding them later.
  - e.g. `10 20 30 ... 100`
- **CSS class** - for special effects.
- **Custom attributes** - for special effects.
- **Subenu** - Select a menu slot to give the menu item a secondary menu and the link to this menu will be ignored. When selecting a menu slot you may find a few items missing, **this is deliberately designed** to avoid creating "infinite loop menus" which can cause your website to crash. Please try to avoid creating 'infinite loop menus'.

After adjusting the menu item, don't forget to **click the Save button**.

### Magic Menu Item
Enter the "Magic Menu Item" into the "URL" field to insert special menu item.

Click on the "Magic Menu Item Reference" button to view the items that can be used.

```
__forumlist__
Forum Boards (first level menu)
[At the "Navigation Label", enter the board ID (separated by a comma ",") to display the specified forum, "0" to display all forums.

__forumlist_submenu__
Forum boards (secondary menu)
[At the "Navigation Label", enter a vertical line "|" after the normal navigation label, then enter the board ID (separated by a comma ",") to display the specified forum, otherwise display All forums.]

__user__
Avatar + username

__user_submenu__
Avatar + username + user menu (secondary menu)

__user_logout__
User logout

__admin__
Admin dashboard

__btn_login__
Login button

__btn_register__
Register button

__btn_thread_new__
Post New Button

__user_notice__
Notifications

__search__
Search (link)

__search_box__
Search box

__divider__
Divider

```

> The following menu items have special effects but are not Magic Menu Items:
> - . (Dot) - Homepage

### Compatibility

All hooks related to the original navigation menu will be disabled. New menu items must be configured with this plugin in order to be displayed.

Disabled menu items can be re-added using the "magic menu items", which are already built into this plugin for search and notices.

### Uninstalling the plugin
If you uninstall this plugin in the usual way, the plugin's data will be retained on the server for the next time you use it and to prevent other related plugins from failing to read the menu data.

If you really want to uninstall this plugin for resetting menu data etc., please open the `unstall.php` file and uncomment it.

# For Developers

## Usage

```php
/**
 * Generate a navigation menu
 *
 * @param array $args Parameters
 * @param array $c_menu Specifies the menu structure, if not filled in, the global menu structure will be used
 * @return string HTML code for the menu
 */
xn_nav_menu($args);

// Define the menu parameters
// these parameters don't all need to be filled in, leave them blank to use the default values (i.e. the values written below)
$args = array(
    'menu' => 'primary_menu',              // menu slots; if $c_menu exists, use the slots in $c_menu
    'container' => 'div',                  // the menu container tag; if it's false, no container is output
    'container_class' => 'menu-container', // the menu container class
    'container_id' => '',                  // the menu container ID
    'container_attr' => '',                // other attributes of the menu container
    'menu_class' => 'menu',                // the class of the menu itself
    'menu_id' => '',                       // the ID of the menu itself
    'item_class' => '',                    // the menu item class
    'item_attr' => '',                     // other attributes of the menu item
    'icon_class' => '',                    // Icon's additional class
    'link_before' => '',                   // content before the link
    'link_id' => '',                       // link ID (rarely used) [reserved]
    'link_class' => '',                    // link class
    'link_attr' => '',                     // other attributes of the link
    'before' => '',                        // before the link text
    'after' => '',                         // after the link text
    'link_after' => '',                    // content after the link
    'desc' => 'span',                      // whether to show description or not?
    'desc_class' => '',                    // class of the description
    'no_ul' => false,                      // don't output the ul tag? [used in special cases]
    'no_li' => false,                      // don't output li tags? [used in special cases]
    'submenu' => false,                    // submenu slots; if $c_menu exists, use the slots in $c_menu
    'is_submenu' => false,                 // Is this menu a submenu? [reserved].
    '_submenu_args'   => array(            // Submenu related parameters (optional) [Reserved]
      'container'       => 'div',          // submenu container element
      'container_class' => 'dropdown-menu',// submenu container class
      'container_id'    => '',             // submenu container id
      'container_attr'  => '',             // other attributes of the submenu container
      'menu_class'      => 'list-unstyled',// the ID of the menu itself
      'menu_id'         => '',             // the ID of the menu itself
      'link_before'     => '',             // link ID
      'link_class'      => 'dropdown-item',// link class
      'item_class'      => '',             // other attributes of the link
      'before'          => '',             // before the link text
      'after'           => '',             // after the link text
      'link_after'      => '',             // content after the link
    ),
    '_link_class_has_submenu' => '',       // link class - when there is a submenu [for special cases]
    '_bs_version' => 4,                    // Bootstrap major version number (used to change output) [Reserved]
    'echo' => true,                        // use echo or return? false for return; default is true for ease of use
);
```

### Example - Basic Usage
It is similar to WordPress.


```html
<!-- This is a simple Bootstrap Navbar navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Replace the <div class="collapse navbar-collapse" id="navbarColor01">Original Menu</div> with xn_nav_menu() -->
    <?php xn_nav_menu(array(
            'menu'            => 'primary_menu',             //Menu slot
            'container'       => 'div',                      //Menu container label
            'container_class' => 'collapse navbar-collapse', //Menu container class
            'container_id'    => 'navbarColor01',            //Menu container id
            'menu_class'      => 'navbar-nav',               //Menu class
            'link_class'      => 'nav-link',                 //Menu item class
            'item_class'      => 'nav-item',                 //Link class
            _bs_version'      => 5,                          //Because Bootstrap 5 has a different way of writing the drop-down menu than 4, so if you are using Bootstrap 5, you will need to add this to ensure that the results are correct
        )); ?>
</nav>
```

### Making an existing navbar support this plugin

Let's take the Xiuno BBS original navigation bar as an example. The navigation bar code can be found in `view/htm/header_nav.inc.htm`.

#### Step 1: Analyze the existing menu

There are mainly two menu slots, one on the left and one on the right.

The left menu shows the home page and forums list, and the right menu shows user info.

#### Step 2: Make changes

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
                <!-- Left side: Forums -->
                <!-- Use directly; if this plugin is not installed, the theme will crash; it is recommended to bring dependencies -->
                <?php xn_nav_menu(array(
                    'menu'            => 'primary_menu',       //Menu slot
                    'container'       => '',                   //Menu container label
                    'container_class' => '',                   //Menu container class
                    'container_id'    => '',                   //Menu container id
                    'menu_class'      => 'navbar-nav mr-auto', //Menu class
                    'item_class'      => 'nav-item',           //Menu item class
                    'link_class'      => 'nav-link'            //Link class
                )); ?>

                <!-- Right side: User -->
                <!-- For compatibility; if you are not sure whether your user will install this plug-in, please do so -->
                <?php 
                if( function_exists("xn_nav_menu") ): /* If the plugin is installed, use the plugin menu */
                xn_nav_menu(array(
                    'menu'            => 'secondary_menu', //Menu slot
                    'container'       => '',               //Menu container label
                    'container_class' => '',               //Menu container class
                    'container_id'    => '',               //Menu container id
                    'menu_class'      => 'navbar-nav',     //Menu class
                    'item_class'      => 'nav-item',       //Menu item class
                    'link_class'      => 'nav-link'        //Link class
                )); 
                else:
                /* Otherwise use the old menu */
                ?>

                <ul class="navbar-nav">
					<!--{hook header_nav_user_start.htm}-->
				<?php if(empty($uid)) { ?>
					<li class="nav-item"><a class="nav-link" href="<?php echo url('user-login');?>"><i class="icon-user"></i> <?php echo lang('login');?></a></li>
					<!--<li class="nav-item"><a class="nav-link" href="<?php echo url('user-create');?>"><?php echo lang('register');?></a></li>-->
				<?php } else { ?>
					<li class="nav-item username"><a class="nav-link" href="<?php echo url('my');?>"><img class="avatar-1" src="<?php echo $user['avatar_url'];?>"> <?php echo $user['username'];?></a></li>
					<!-- admin -->
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
#### Step 3: Make your plugin dependent on this plugin (optional)

> "So you can be with me forever!" *just kidding*

If your plugin will not work without this plugin, open your plugin's `conf.json` and replace `"dependencies": []` with
```json
"dependencies": {
    "abs_menu": "1.0"
}
```

This way, if a user tries to install your plugin without this one installed, a prompt will appear saying

> depends on the following plugins: Menus, please install the dependent plugins first

#### Step 4: Make sure the navigation bar you create replaces the navigation bar of other themes (optional)
Open your plugin's `conf.json` and replace `"overwrites_rank": []` with
```json
"overwrites_rank": {
    "view/htm/header_nav.inc.htm": 1000
}
```

## Menu slot structure in database
```php
$abs_nav_menus = array(
    //keys are slot IDs, values are menu items

    //primary menu; left side of original navigation menu
    'primary_menu' => array(
        //menu items
        array(
            'lid' => menu item ID, 
            'icon' => icon, 
            'name' => navigation label, 
            'title' => title attribute, 
            'desc' => menu item description (will be displayed in the menu if the theme supports it), 
            'href' => url, 
            'order' => menu item order, 
            'class' => menu item CSS class,
            'attr' => custom attribute,
            'submenu' => custom submenu,
            // The following parameters are reserved and not available to the public at this time
            'link_class' => link CSS class,
            ),
        // same as below...
    ),

    //secondary menu; the right side of the original navigation menu
    'secondary_menu' => array(), 

    //secondary secondary menu; left side of the second row of the two-lined menu
    'tertiary_menu' => array(), 

    //secondary secondary menu; second right-hand side of the two-lined menu
    'quaternary_menu' => array(), 
    
    //user menu; the menu that will be used to expand the submenu by clicking on the user's avatar
    'user_menu' => array(), 

    //...... menu slots for other plugins ......

    // custom menu location (can be used for submenus etc)
    'custom_1' => array(), 
    'custom_2' => array(), 
    'custom_3' => array(), 
    'custom_4' => array(), 
    'custom_5' => array(), 
)
```

## Functions

### xn_nav_menu_get()

Gets the menu items for the specified menu slot.

```php

// Suggested use
$slot_name = 'custom_1';
$menu = setting_get('abs_nav_menus');
$menu[$slot_name];

/**
 * Get the menu item for the specified menu slot
 *
 * @param string $slot_name Slot ID
 * @return array
 */
if(function_exists("xn_nav_menu_get")) {
$menu = xn_nav_menu_get($slot_name = "custom_1");
}
```

### xn_nav_menu_slot_add()

Adds a menu slot. Suitable for operation during plugin installation.

```php
/**
 * Add a menu slot
 *
 * @param string $slot_name Slot ID
 * @param array $prefill_items pre-populated menu items; blank menu if not filled
 */
if(function_exists("xn_nav_menu_slot_add")) {
xn_nav_menu_slot_add($slot_name = "custom_1"/* , $prefill_items */);
}
```

### xn_nav_menu_slot_del()

Delete a menu slot. Suitable for operation when uninstalling plugins.

**DANGER OPERATION** - Will delete the entire menu slot, including the menu items in it.

```php
/**
 * Deletes a menu slot
 *
 * @param string $slot_name Slot ID
 */
if(function_exists("xn_nav_menu_slot_del")) {
xn_nav_menu_slot_del($slot_name = "custom_1");
}
```

### xn_nav_menu_item_add() 

Adds a menu item to the specified menu slot.

``` php
/**
 * Adds a menu item to the specified menu slot
 *
 * @param string $slot_name Slot ID
 * @param array $menu_item menu item
 */
if(function_exists("xn_nav_menu_item_add")) {
xn_nav_menu_item_add($slot_name = "custom_1", $menu_item);
}
//$menu_item can be one of the following kinds.
$menu_item = array('order' => 10, 'name' => 'My new menu item', 'href' => '#');

$menu_item = array(
    array('order' => 20, 'name' => 'My new menu items', 'href' => '#'),
    array('order' => 30, 'name' => 'My new menu items', 'href' => '#'),
);
```

### xn_nav_menu_item_set() 

Updates the specified menu item for the specified menu slot.

``` php
/**
 * Adds a menu item to the specified menu slot
 *
 * @param string $slot_name Slot ID
 * @param array $menu_item menu item
 * @param bool $override Override menu items? true: old menu items will disappear completely and be replaced by new menu items
 */
if(function_exists("xn_nav_menu_item_set")) {
xn_nav_menu_item_set($slot_name = "custom_1", $menu_item);
}
//$menu_item can be one of the following kinds.
$menu_item = array('lid' => 1, 'order' => 10, 'name' => 'My Menu Item', 'href' => '#');

$menu_item = array(
    array('lid' => 2, 'order' => 20, 'name' => 'My menu items', 'href' => '#'),
    array('lid' => 3, 'order' => 30, 'name' => 'My menu items', 'href' => '#'),
);

$menu_item = 'YToxOntpOjA7YToxMDp7czozOiJsaWQiO2k6MTtzOjU6Im9yZGVyIjtpOjEwO3M6NDoiaWNvbiI7czowOiIiO3M6NDoibmFtZSI7czoxMjoiTXkgTWVudSBJdGVtIjtzOjQ6ImRlc2MiO3M6MDoiIjtzOjQ6ImhyZWYiO3M6MToiIyI7czo1OiJ0aXRsZSI7czowOiIiO3M6NToiY2xhc3MiO3M6MDoiIjtzOjQ6ImF0dHIiO3M6MDoiIjtzOjc6InN1Ym1lbnUiO3M6MDoiIjt9fQ==';
```

### xn_nav_menu_item_del() 

Deletes the specified menu item from the specified menu slot.

``` php
/**
 * Deletes the specified menu item for the specified menu slot
 *
 * @param string $slot_name Slot ID
 * @param string|array $menu_item menu item ID
 */
if(function_exists("xn_nav_menu_item_del")) {
xn_nav_menu_item_del($slot_name = "custom_1", $menu_item);
}

$menu_item = 1;

$menu_item = array(2, 3);
```

## Hook points

### menu_magichref_case_end.php
Used to add a new "magic menu item" handler.

```php

/* ===== my menu item ===== */
elseif ($menu_item['href'] === "__newcase__") :

$r . = 'Any HTML element will do'; // generate menu item; required step

elseif ($menu_item['href'] === "__newcase2__") :

$_this_item = array(
    'icon' => 'fa fa-cog', // icon
    'name' => 'my menu', // you can use the lang() function
    'href' => url('my-link') // link; it is recommended to use the url() function to generate a link that matches the permalink settings
);
$r . = xn_nav_menu_item($_this_item, $args); // use built-in functions to generate menu items
```

### menu_magichref_datalist_end.php
Add the "magic menu items" you have added to the autocomplete list in the "URL" box and to the "magic menu item reference".
```php
'__newcase__' => 'My magic menu description',
```

### menu_slot_before_primary.php, menu_slot_after_quaternary.php, menu_slot_before_custom.php, menu_slot_end.htm
Allow the user to choose which menu slots you add.

Choose `menu_slot_before_custom.php` if you can't decide.
```php
'my_menu' => 'My menu name',
```

# Author
- Geticer

# Special thanks to
- Tillreetree - development collaboration
- Nanase Kurumi(Menhera-chan.) - Spiritual Support

# License
**MIT License**

See LICENSE.txt for details.

# Homage to the plugin
- git_appdock
- rob_links

# If you think this plugin is useful to you, please consider buying me a cup of #C0FFEE or your favorite drink

Alipay:

![](https://menherachanfans.eu.org/assets/appreciate_g_z.png)

> the real coffee color is #6f4e37 :)