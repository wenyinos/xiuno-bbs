# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Xiuno BBS 4.0 — a lightweight, function-oriented PHP forum system. No Composer, no MVC framework, no automated test suite. The custom framework library lives in `xiunophp/`.

## Development Commands

```bash
# Run locally (PHP built-in server)
php -S 127.0.0.1:8000 -t /path/to/xiuno-bbs

# Lint a single PHP file
php -l model/thread.func.php

# Batch lint all PHP files (excluding upload/)
find . -name "*.php" -not -path "./upload/*" -print0 | xargs -0 -n1 php -l

# Install: visit /install/ in browser, then delete the install/ directory
```

No build pipeline, no package manager, no test framework. Rely on `php -l` syntax checks and manual smoke testing.

## Architecture & Request Flow

**Entry:** `index.php` → loads `conf/conf.php` → includes `xiunophp/xiunophp.php` (or `.min.php` in production) → `model.inc.php` (loads all model files) → `index.inc.php` (session, auth, routing).

**Routing** (`index.inc.php`): URL parameter `$route` maps via switch/case to files in `route/` (e.g., `thread`, `forum`, `user`, `post`, `my`, `attach`, `mod`). Admin routes are in `admin/route/`.

**Model layer** (`model/`): All files are `*.func.php`, loaded eagerly by `model.inc.php`. Naming convention:
- `thread__read()`, `thread__create()` — double-underscore = raw CRUD (no side effects)
- `thread_create()`, `thread_read()` — single-underscore = business logic (cascading updates, validation)

**Database:** Abstracted via `xiunophp/db.func.php` with drivers in `xiunophp/db_*.class.php`. Supports MySQL, PDO MySQL, PDO SQLite, PDO MongoDB. Reads/writes auto-split for master-slave configs.

**Cache:** `xiunophp/cache.func.php` with backends: Redis, Memcached, APC, Xcache, Yac, MySQL. Configured in `conf/conf.php`.

**Templates** (`view/htm/`): Plain `.htm` files with embedded PHP. Shared includes: `header.inc.htm`, `footer.inc.htm`. Admin templates in `admin/view/htm/`.

**Languages** (`lang/`): Three locales — `zh-cn`, `zh-tw`, `en-us`. Each contains PHP files returning arrays.

## Plugin System

Plugins live in `plugin/<plugin_name>/`. Each plugin has:
- `conf.json` — metadata, version, dependencies, hook priorities
- `hook/` — PHP and HTM files named after hook points (e.g., `model_thread_create_end.php`, `index_site_brief_after.htm`)
- `install.php` / `unstall.php` — lifecycle scripts
- Optional: `model/`, `route/`, `htm/`, `setting.php`

**Hook mechanism:** Source files contain `// hook <hookpoint_name>.php` comments. The `_include()` function in `model/plugin.func.php` compiles source files by injecting plugin hook code at these points, writing merged output to `tmp/`. In `DEBUG > 1` mode, recompilation happens every request.

**Plugin CRUD:** `plugin.func.php` manages install/uninstall/enable/disable. Plugin data stored in DB `kv` table.

## Key Conventions

- **Indentation:** Tabs, K&R brace style
- **Functions:** snake_case, prefixed by domain (e.g., `thread_create`, `user_read`, `forum_list_cache`)
- **Globals:** Heavy use of `global` keyword for `$conf`, `$uid`, `$user`, `$gid`, `$group`, `$fid`, `$route`
- **Hook points:** Always preserve `// hook ...` comments — they are the plugin extension contract
- **Templates:** Use `_include()` wrapper (not raw `include`) so plugin hooks apply
- **Security gate:** All files start with `!defined('DEBUG') AND exit('Access Denied.');` or similar

## Configuration

- `conf/conf.php` — runtime config (DB, cache, site settings). **Never commit.**
- `conf/conf.default.php` — template with all available options
- `conf/smtp.conf.php` — mail server settings. **Never commit.**
- Writable directories (need web server write access): `upload/`, `plugin/`, `tmp/`, `log/`, `conf/`

## DEBUG Modes

Defined in `index.php`:
- `0` — Production (uses minified xiunophp, caches model files)
- `1` — Development (full source includes)
- `2` — Plugin development (full source + recompiles hooks every request)
