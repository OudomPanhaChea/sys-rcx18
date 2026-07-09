---
name: verify
description: Build/launch/drive recipe for verifying changes to this CodeIgniter 3 app locally (XAMPP MySQL + PHP built-in server)
---

# Verifying this app locally

CodeIgniter 3 app, normally deployed to Hostinger (sys.rcx18.com). Locally it uses
XAMPP's MySQL (`rcx_sys` DB, user `root`, no password) — the `application/config/is_local.flag`
file switches DB config to local. Apache does NOT serve this directory; use PHP's built-in server.

## Launch

PHP built-in server needs a router script (CI pretty URLs, no .htaccess support):

```php
<?php // router.php
$root = 'E:/RCX/public_html/sys';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path !== '/' && file_exists($root . $path) && !is_dir($root . $path)) return false;
chdir($root);
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $root . '/index.php';
```

```bash
C:/xampp/php/php.exe -S 127.0.0.1:8090 path/to/router.php   # run in background
```

`base_url` auto-detects from HTTP_HOST, so http://127.0.0.1:8090 just works.

## Login for driving endpoints

No known dev credentials; real user passwords are bcrypt. Do NOT modify existing user
rows (denied by permissions and wrong anyway). Instead insert a throwaway admin and
delete it afterwards:

```bash
MYSQL="C:/xampp/mysql/bin/mysql.exe -u root rcx_sys -N -e"
# hash via PowerShell (php not on Git Bash PATH):  php -r "echo password_hash('...', PASSWORD_BCRYPT);"
$MYSQL "INSERT INTO users (saas_id, ip_address, username, password, email, created_on, active, first_name, last_name) VALUES (3,'127.0.0.1','verify_test','<HASH>','claude-verify-test@example.com',UNIX_TIMESTAMP(),1,'CLAUDE','VERIFYTEST')"
$MYSQL "INSERT INTO users_groups (user_id, group_id) VALUES (<ID>, 1)"   # group 1 = admin
```

Login is AJAX, CSRF is off, sessions are files:

```bash
curl -s -c cookies.txt -d "identity=claude-verify-test@example.com&password=..." http://127.0.0.1:8090/auth/login
# returns {"error":false,...}; then drive with -b cookies.txt
```

## Flows worth driving

- Task detail modal: `projects/tasks/<project_id>` page; `.modal-task-detail[data-edit=<task_id>]`
  links are inside dropdowns — trigger via page jQuery: `window.jQuery('.modal-task-detail[data-edit="53"]').trigger('click')`.
- Comments JSON: `POST projects/get_comments` with `type=task_comment&to_id=<task_id>` (+ optional `last_id` for delta).
- Post comment: `POST projects/create-comment` with `comment_task_id, is_comment=true, message`.
- Live-update check: insert a `messages` row directly in MySQL while the modal is open; the 3s poll must render it.
- Notifications: `POST notifications/get_live_notifications`.

Playwright works (install in scratchpad: `npm init -y && npm install playwright`; browsers already in `$LOCALAPPDATA/ms-playwright`).

## Gotchas

- Git Bash has no `php` on PATH — use PowerShell for `php -r`, or `C:/xampp/php/php.exe` explicitly.
- Task 53 / project 51 ("Submit #J25") has a rich comment history — good test subject.
- Clean up ALL inserted rows (users, users_groups, messages, notifications) when done.
- Kill the PHP server after (`taskkill //F //IM php.exe` — check nothing else uses php.exe first).
