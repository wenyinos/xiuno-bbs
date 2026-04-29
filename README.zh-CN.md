# Xiuno BBS

[English](./README.md) | 中文

Xiuno BBS 是轻量且可扩展的 PHP 论坛系统。  
本仓库包含论坛核心、后台管理、语言包与插件机制。

## 环境要求

- PHP 7.x+（兼容 Xiuno 4 生态）
- MySQL / MariaDB
- Nginx 或 Apache
- 可写目录：`upload/`、`plugin/`、`tmp/`、`log/`、`conf/`

## 快速开始

1. 将项目文件上传到网站根目录。
2. 访问 `/install/` 完成安装流程。
3. 安装完成后删除 `install/` 目录。
4. 仅为运行时目录配置写权限。

## 最近更新

- 已将兼容性目标升级到 PHP 8.4+（升级者：`wenyinos`）。
- 仓库地址：<https://github.com/wenyinos/xiuno-bbs>

## Nginx 伪静态规则

将以下规则加入 Nginx 站点配置：

```nginx
location ~* \.(htm)$ {

    rewrite "^(.*)/(.+?).htm(.*?)$" $1/index.php?$2.htm$3 last;

}
```

## 项目结构

- `index.php`、`index.inc.php`：应用入口与请求引导
- `model/`：核心业务与数据逻辑
- `route/`：前台路由处理
- `admin/`：后台路由与模板
- `plugin/`：插件模块与 Hook 扩展点
- `view/`：模板、CSS、JS 静态资源
- `conf/`：运行时配置

## 安全建议

- 不要提交 `conf/conf.php` 或任何密钥配置。
- 建议将日志和上传文件排除在代码备份之外。
- 仅给运行时目录授予写权限，避免全站可写。
