# Xiuno BBS 升级到 PHP 8.4+ 评估报告

生成时间：2026-04-29  
最后更新：2026-04-30  
评估范围：主程序 + xiunophp 框架库 + 已安装插件（`plugin/`）  
评估方式：静态扫描 + 语法检查 + 全量 grep 搜索（不改代码）

## 1. 结论摘要

当前仓库在 PHP 8.4+ 下存在 **3 个核心阻断项（P0）**，其中 2 个会在常规主流程直接触发致命错误。  
此外发现 **4 个高风险项（P1）** 和 **7 个中低风险项（P2）**，涉及第三方库、废弃 API 调用和历史遗留代码。  
插件本体代码整体兼容性较好，但会被核心阻断项连带影响。

**当前进度：P0 全部完成（3/3），P1 待处理（0/4），P2 待处理（0/7）。**

---

## 2. P0 阻断项（必须先改）

### P0-1 `get_magic_quotes_gpc()` 已移除（PHP 8.0+） ✅ 已修复
- 文件：`xiunophp/xiunophp.php:20`
- 同步存在：`xiunophp/xiunophp.min.php:20`
- 影响：初始化阶段直接 Fatal，站点无法正常启动。
- 复现：`Call to undefined function get_magic_quotes_gpc()`
- 修复方式：移除 `set_magic_quotes_runtime()` 和 `get_magic_quotes_gpc()` 调用，硬编码 `$get_magic_quotes_gpc = FALSE`
- 修复日期：2026-04-30

### P0-2 字符串偏移花括号语法不兼容（PHP 8） ✅ 已修复
- 文件：`xiunophp/xn_html_safe.func.php`
- 位置：
  - `:785` `return $this->rawtext{$this->position++};`
  - `:938` `... $this->rawtext{$this->position} ...`
  - `:951` `... $this->rawtext{$this->position} ...`
- 影响：文件解析失败（Parse Error），而该文件由 `model/post.func.php` 直接 `include_once`，帖子模型路径受阻。
- 修复方式：将 3 处 `$this->rawtext{$this->position}` 全部改为 `$this->rawtext[$this->position]`
- 修复日期：2026-04-30

### P0-3 `each()` 已移除（PHP 8.0+） ✅ 已修复
- 文件：`xiunophp/xn_send_mail.func.php`
- 位置：
  - `:1827` `while( list(, $line) = each($lines) )`
  - `:2933` `while(list(,$line) = @each($lines))`
  - `:2962` `while(list(,$line_out) = @each($lines_out))`
- 影响：邮件发送相关路径运行时 Fatal（通知/找回密码等链路）。
- 修复方式：3 处 `while(list(...) = each(...))` 全部替换为 `foreach (... as ...)`
- 修复日期：2026-04-30

---

## 3. P1 高风险项（配置或分支触发）

### P1-1 `mysql_*` 扩展调用（已移除）
- 文件：`xiunophp/db_mysql.class.php`（整个文件，约 200 行）
- 同步存在：`xiunophp/xiunophp.min.php:39`（压缩版内嵌）
- 具体调用：`mysql_connect`, `mysql_select_db`, `mysql_query`, `mysql_fetch_assoc`, `mysql_insert_id`, `mysql_affected_rows`, `mysql_close`, `mysql_errno`, `mysql_error`
- 说明：当前 `conf/conf.php` 已配置 `db.type = pdo_mysql`，主路径通常可绕过；但 `conf/conf.default.php` 仍默认 `mysql`，新部署或重装时存在高概率踩雷。
- 修复：将 `conf/conf.default.php` 默认 DB 驱动改为 `pdo_mysql`；长期建议移除 `db_mysql.class.php` 或标注为废弃。

### P1-2 `set_magic_quotes_runtime()` / `get_magic_quotes_runtime()` 已移除（PHP 8.0+）
- 文件：`xiunophp/xn_send_mail.func.php`
- 位置：
  - `:1635-1636` 定义了兼容函数 `get_magic_quotes()` 返回 false
  - `:1640` `$magic_quotes = get_magic_quotes_runtime();` — **直接调用，无防护**
  - `:1643` `set_magic_quotes_runtime(0);` — 在 `version_compare(PHP_VERSION, '5.3.0', '<')` 分支内，实际不会执行
  - `:1645` `ini_set('magic_quotes_runtime', 0);` — 该 ini 指令在 PHP 8.0+ 已移除
  - `:1652` `set_magic_quotes_runtime($magic_quotes);` — 同上，不会执行
  - `:1654` `ini_set('magic_quotes_runtime', $magic_quotes);` — 同上
- 影响：`:1640` 行会在附件编码路径触发 Fatal。
- 修复：删除整个 `magic_quotes` 相关代码块（PHP 5.3+ 已废弃该功能）。

### P1-3 `preg_replace()` `/e` 修饰符已移除（PHP 7.0+）
- 文件：`xiunophp/misc.func.php:357`
- 代码：`$s = preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $s);`
- 说明：该代码位于已注释掉的函数 `ucs2_to_utf8()` 内部（`:346` 有 `/*` 注释块），**当前不会执行**。但如果有人取消注释或在工具脚本中调用，将触发 Fatal。
- 风险等级：P1（潜在风险，当前不触发）
- 修复：如需保留该函数，改用 `preg_replace_callback()`。

### P1-4 `Memcache` 扩展（不含 'd'）已移除
- 文件：`xiunophp/cache_memcached.class.php:22`
- 代码：`$memcache = new Memcache; $r = $memcache->connect($conf['host'], $conf['port']);`
- 同步存在：`xiunophp/xiunophp.min.php:43`
- 说明：`Memcache` 扩展（php-memcache）在 PHP 8.x 中已不可用（PECL 已停止维护）。当前代码同时支持 `Memcache` 和 `Memcached` 两个扩展，但如果用户环境只有 `Memcached` 扩展（php-memcached），代码逻辑是正确的（`:24` 分支）。风险在于：如果用户安装了旧版 `Memcache` 扩展试图使用，会触发 Fatal。
- 修复：移除 `Memcache` 扩展支持分支，仅保留 `Memcached`。

---

## 4. P2 中低风险项（警告/废弃/死代码）

### P2-1 `PEAR.php` 依赖缺失
- 文件：`xiunophp/xn_html_safe.func.php`
- 位置：
  - `:1099` `require_once('PEAR.php');`
  - `:1137` `require_once('PEAR.php');`
- 说明：`PEAR.php` 不在项目内，如果触发（`set_object` 或 `set_option` 传入非法参数），将 Fatal。正常流程不会触发。
- 修复：替换为 `trigger_error()` 或 `throw new InvalidArgumentException()`。

### P2-2 `var` 属性声明（PHP 4 风格）
- 文件：`xiunophp/xn_html_safe.func.php`
- 数量：约 20+ 处（如 `:302`, `:308`, `:343`, `:349`, `:537`, `:543`, `:638`, `:644` 等）
- 说明：`var` 在 PHP 5+ 中等价于 `public`，PHP 8.4 仍然支持，但会在严格模式下触发 Deprecation Notice。
- 修复：批量替换 `var $` 为 `public $`。

### P2-3 `=&` 引用赋值（已废弃模式）
- 文件：`xiunophp/xn_html_safe.func.php`
- 数量：约 20+ 处（如 `:316`, `:364`, `:414`, `:456`, `:835`, `:843`, `:1096` 等）
- 说明：`$this->orig_obj =& $orig_obj;` 这种在构造函数中的引用赋值在 PHP 8.4 中触发 Deprecation Notice。
- 修复：移除 `&`，改为普通赋值 `$this->orig_obj = $orig_obj;`。

### P2-4 `safe_mode` ini 指令已移除（PHP 5.4+）
- 文件：
  - `admin/route/index.php:67` `$info['safe_mode'] = ini_get('safe_mode') ? lang('yes') : lang('no');`
  - `xiunophp/xn_send_mail.func.php:744` `if ($this->Sender != '' and !ini_get('safe_mode'))`
  - `xiunophp/misc.func.php:832` `(!ini_get('safe_mode') && !ini_get('open_basedir'))`
- 说明：`safe_mode` 在 PHP 5.4 已移除。`ini_get('safe_mode')` 在 PHP 8.4 中返回空字符串（不会 Fatal），但逻辑判断结果可能与预期不同。
- 修复：移除所有 `safe_mode` 相关判断。

### P2-5 `set_error_handler()` 第二参数使用 `-1`
- 文件：`xiunophp/xiunophp.php:80`
- 代码：`DEBUG AND set_error_handler('error_handle', -1);`
- 同步存在：`xiunophp/xiunophp.min.php:78`
- 说明：PHP 8.4 中 `set_error_handler` 的 `$error_levels` 参数传 `-1` 已废弃，应使用 `E_ALL`。
- 修复：改为 `set_error_handler('error_handle', E_ALL)`。

### P2-6 历史遗留版本检查（死代码）
- 文件及位置：
  - `xiunophp/xn_send_mail.func.php:38` — 检查 PHP < 5.0.0
  - `xiunophp/xn_send_mail.func.php:1642` — 检查 PHP < 5.3.0
  - `xiunophp/xn_html_safe.func.php:519` — 检查 PHP < 4.3
  - `xiunophp/xn_html_safe.func.php:1073` — 检查 PHP < 4.3（`XML_HTMLSax3_StateParser_Lt430` 分支）
  - `xiunophp/misc.func.php:229` — 检查 PHP < 5.4.0
  - `xiunophp/misc.func.php:348` — 检查 PHP < 5.4.0
  - `xiunophp/misc.func.php:983` — 检查 PHP < 5.3.2
  - `xiunophp/xiunophp.php:19` — 检查 PHP < 5.3.0
  - `xiunophp/xiunophp.min.php:19` — 同上
- 说明：这些分支在 PHP 8.4 下永远不会执行，属于死代码。不影响运行但增加维护负担。
- 修复：删除所有 PHP < 7.0 的兼容代码分支。

### P2-7 `tool/merge.php` 中的 `set_magic_quotes_runtime` 调用
- 文件：`tool/merge.php:4`
- 代码：`function_exists('set_magic_quotes_runtime') AND set_magic_quotes_runtime(0);`
- 说明：已有 `function_exists` 防护，不会 Fatal，但属于无效代码。
- 修复：删除该行。

---

## 5. 插件评估结论（已安装/已启用）

### 5.1 已启用插件
`abs_menu, haya_favorite, haya_post_like, huux_hlight, huux_notice, huux_postlist, huux_set, till_theme_nekoha_shizuku, xn_digest, xn_forum_merge, xn_friendlink, xn_insert_code, xn_ipaccess, xn_mod_enhance, xn_mypost, xn_nav_more, xn_read_unread, xn_search, xn_syntax_hightlighter, xn_top, xn_umeditor, xn_user_recent_thread`

### 5.2 插件兼容性结论
- 非 hook 的插件 PHP 文件（model/route/setting/overwrite）语法检查通过，未发现 `mysql_* / get_magic_quotes_gpc / each / create_function` 等已移除 API 的实际命中。
- `plugin/*/hook/*` 中很多文件是"代码注入片段"（例如以 `elseif` 或数组项开头），**不能当独立 PHP 脚本 lint**；单独 lint 的 parse error 属于误报，不代表运行时必然失败。
- ~~插件运行仍会受核心 P0 问题连带影响（尤其帖子渲染与邮件链路）。~~ P0 已修复，插件链路阻断已解除。

---

## 6. 建议整改顺序

| 优先级 | 编号 | 操作 | 影响范围 | 状态 |
|--------|------|------|----------|------|
| P0 | 1 | 移除/替代 `get_magic_quotes_gpc()` 调用 | xiunophp.php, xiunophp.min.php | ✅ 已完成 |
| P0 | 2 | 修复 `xn_html_safe.func.php` 花括号字符串偏移语法（3 处） | xn_html_safe.func.php | ✅ 已完成 |
| P0 | 3 | 替换 `xn_send_mail.func.php` 中 `each()` 调用（3 处） | xn_send_mail.func.php | ✅ 已完成 |
| P1 | 4 | 将 `conf/conf.default.php` 默认 DB 驱动改为 `pdo_mysql` | conf.default.php | 待处理 |
| P1 | 5 | 删除 `xn_send_mail.func.php` 中 `magic_quotes` 相关代码块 | xn_send_mail.func.php | 待处理 |
| P1 | 6 | 移除 `cache_memcached.class.php` 中 `Memcache` 扩展支持分支 | cache_memcached.class.php, xiunophp.min.php | 待处理 |
| P2 | 7 | 将 `set_error_handler` 参数 `-1` 改为 `E_ALL` | xiunophp.php, xiunophp.min.php | 待处理 |
| P2 | 8 | 批量替换 `var $` 为 `public $` | xn_html_safe.func.php | 待处理 |
| P2 | 9 | 移除 `xn_html_safe.func.php` 中 `=&` 引用赋值 | xn_html_safe.func.php | 待处理 |
| P2 | 10 | 移除 `safe_mode` 相关判断 | admin/route/index.php, xn_send_mail.func.php, misc.func.php | 待处理 |
| P2 | 11 | 替换 `PEAR.php` 依赖为 `trigger_error` | xn_html_safe.func.php | 待处理 |
| P2 | 12 | 清理 PHP < 7.0 死代码分支 | 多个 xiunophp 文件 | 待处理 |
| P2 | 13 | 删除 `tool/merge.php` 中无效的 `set_magic_quotes_runtime` 行 | tool/merge.php | 待处理 |

完成整改后，执行一次"排除 hook 片段"的全量 `php -l` 与关键流程回归（发帖/回帖/邮件/插件入口）。

---

## 7. 变更记录

| 日期 | 操作 | 涉及文件 | 备注 |
|------|------|----------|------|
| 2026-04-30 | P0-1 修复 `get_magic_quotes_gpc` | xiunophp.php, xiunophp.min.php | 硬编码 `$get_magic_quotes_gpc = FALSE` |
| 2026-04-30 | P0-2 修复花括号字符串偏移 | xn_html_safe.func.php | 3 处 `{...}` → `[...]` |
| 2026-04-30 | P0-3 修复 `each()` 调用 | xn_send_mail.func.php | 3 处 `while+each` → `foreach` |
| 2026-04-30 | 全量 `php -l` 验证 | — | 排除 hook 片段后全部通过 |

---

## 8. 参考文档

- `get_magic_quotes_gpc()` 移除说明：<https://www.php.net/manual/en/function.get-magic-quotes-gpc.php>
- PHP 8.4 向后不兼容变更：<https://www.php.net/manual/en/migration84.incompatible.php>
- PHP 8.0 移除的函数：<https://www.php.net/manual/en/migration80.removed-functions.php>
- 花括号数组/字符串访问移除：<https://www.php.net/manual/en/migration74.deprecated.php#migration74.deprecated.core.brace-array-string-access>
- `set_error_handler` 参数变更：<https://www.php.net/manual/en/function.set-error-handler.php>
