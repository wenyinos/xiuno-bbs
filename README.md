# Xiuno BBS

English | [中文](./README.zh-CN.md)

Xiuno BBS is a lightweight, extensible PHP forum system.  
This repository includes the core forum, admin panel, language packs, and plugin system.

## Requirements

- PHP 7.x+ (compatible with Xiuno 4 ecosystem)
- MySQL / MariaDB
- Nginx or Apache
- Writable directories: `upload/`, `plugin/`, `tmp/`, `log/`, `conf/`

## Quick Start

1. Upload project files to your web root.
2. Visit `/install/` and complete the installer.
3. Delete the `install/` directory after installation.
4. Configure production permissions for writable directories only.

## Nginx Rewrite Rule (Pseudo-static)

Add the following rule to your Nginx site config:

```nginx
location ~* \.(htm)$ {

    rewrite "^(.*)/(.+?).htm(.*?)$" $1/index.php?$2.htm$3 last;

}
```

## Project Structure

- `index.php`, `index.inc.php`: application entry and request bootstrap
- `model/`: core business/data logic
- `route/`: front-end route handlers
- `admin/`: admin panel routes and templates
- `plugin/`: plugin modules and hook points
- `view/`: templates, CSS, JS assets
- `conf/`: runtime configuration

## Security Notes

- Do not commit `conf/conf.php` or credential files.
- Keep logs/uploads out of version control backups when possible.
- Restrict write permission to runtime directories only.

