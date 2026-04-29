# Repository Guidelines

## Project Structure & Module Organization
This repository is a Xiuno BBS (PHP) application with a flat, function-oriented structure.

- `index.php`, `index.inc.php`, `model.inc.php`: app bootstrap and main request flow.
- `model/`: core business/data functions (`*.func.php`), usually split by domain (`thread`, `post`, `user`).
- `route/` and `admin/route/`: front-site and admin route handlers.
- `view/htm/`, `view/css/`, `view/js/`: templates and static assets.
- `plugin/`: extension modules, each plugin with its own `hook/`, config, and install scripts.
- `conf/`: runtime config (`conf.php` is local, not committed).
- `tmp/`, `log/`, `upload/`: runtime/cache/log/user content.

## Build, Test, and Development Commands
There is no Node/Composer build pipeline in this repo; use PHP runtime checks and local server startup.

- `php -S 127.0.0.1:8000 -t /path/to/xiuno-bbs`: run locally.
- `php -l model/thread.func.php`: lint a changed PHP file.
- `find . -name "*.php" -not -path "./upload/*" -print0 | xargs -0 -n1 php -l`: batch syntax check.
- Install flow: visit `/install/`, complete setup, then remove the `install/` directory.

## Coding Style & Naming Conventions
- Follow existing style: tabs for indentation, K&R-style braces, snake_case function names.
- Keep low-level CRUD helpers in `*_func.php` using existing patterns like `thread__read()` and `thread_update()`.
- Preserve hook points (`// hook ...`) and plugin extension contracts.
- Template updates belong in `view/htm/` (or plugin `overwrite/`) and should keep existing `.htm` naming.

## Testing Guidelines
Automated tests are not configured in this repository; rely on linting plus manual regression checks.

- Run PHP lint on all touched files before commit.
- Smoke-test key flows: login, thread create/reply/delete, admin settings save, and affected plugin hooks.
- If DB/query logic changes, verify behavior in both front routes and matching admin screens.

## Commit & Pull Request Guidelines
- Current history is minimal; use clear, imperative commit messages going forward (recommended: Conventional Commits, e.g., `fix(thread): validate fid before update`).
- One logical change per commit; include config/schema notes when relevant.
- PRs should include: summary, changed paths, manual test steps/results, rollback notes, and screenshots for UI/template changes.

## Security & Configuration Tips
- Never commit `conf/conf.php`, SMTP credentials, or user uploads.
- Treat `conf/conf.default.php` as template only; keep environment-specific secrets local.
- Restrict write permissions to runtime directories only (`upload/`, `tmp/`, `log/`, `conf/`, `plugin/` when needed).
