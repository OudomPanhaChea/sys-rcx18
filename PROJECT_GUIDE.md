# Project Developer Guide & Cheat-Sheet

> A plain-English map of this codebase for someone who is new to PHP and this
> framework. Read this first before changing anything.

---

## 1. What this project is

An all-in-one **CRM / Project-Management SaaS** web application. It handles
projects, invoices, estimates, leads, expenses, products, staff attendance,
leaves, meetings, chat, notes, to-dos, support tickets, reports, and
subscription/billing plans. It can host multiple client companies (that's the
"SaaS" part).

## 2. The technology behind it

| Layer         | Technology                          | Role |
|---------------|-------------------------------------|------|
| Language      | **PHP**                             | Everything server-side |
| Framework     | **CodeIgniter 3.1.11**              | Organizes the code (`system/` + `application/`) |
| Database      | **MySQL** (via `mysqli` driver)     | Stores all data |
| Auth          | **Ion Auth**                        | Login, users, roles/permissions |
| PDF export    | **dompdf**                          | Invoice/estimate PDFs |
| Frontend      | Bootstrap + jQuery + plugins        | UI (see `assets/modules/`): FullCalendar, Chart.js, Select2, SweetAlert, Dropzone, Gantt, etc. |
| Web server    | Apache (`.htaccess`) or IIS (`web.config`) | Routes URLs into `index.php` |

> ⚠️ **This is NOT Laravel.** Ignore Laravel tutorials. CodeIgniter 3 is
> simpler — its official docs: https://codeigniter.com/userguide3/

## 3. Folder map

```
sys\
├── index.php            ← entry point; EVERY request starts here
├── application\         ← ★ ALL YOUR CODE LIVES HERE ★
│   ├── controllers\     ← receive a request, check permissions, decide what happens
│   ├── models\          ← read/write the database
│   ├── views\           ← the HTML pages users see
│   │   └── includes\    ← shared page parts: head.php, navbar.php, footer.php, js.php
│   ├── config\          ← settings (database, routes, autoload, ion_auth)
│   ├── helpers\         ← reusable functions → custom_helper.php
│   ├── language\        ← 50+ translations of every text label
│   ├── libraries\       ← Ion_auth, Pdf (dompdf wrapper)
│   ├── migrations\      ← database schema changes over time
│   └── logs\            ← error logs (check here when something breaks)
├── system\              ← ⛔ FRAMEWORK ENGINE — NEVER EDIT
├── assets\              ← css/, js/, img/, modules/ (plugins), uploads/
├── vendor\              ← third-party libraries
└── Code.zip             ← a full backup of the codebase (keep it!)
```

**Rule #1: never edit `system/` or `vendor/`.** Those are the engine.

## 4. How the code works (MVC)

Every page is built by three cooperating files — **M**odel, **V**iew,
**C**ontroller. Example: loading the Projects page.

1. **Controller** `controllers/Projects.php` — receives the request, checks
   "is this user logged in and allowed?", asks the model for data.
2. **Model** `models/Projects_model.php` — runs the database queries.
3. **View** `views/projects.php` — renders the HTML the user sees.

### The URL tells you which file to open  ← most important trick

```
yoursite.com / invoices / view / 5
               │          │      │
               │          │      └─ parameter (e.g. invoice id = 5)
               │          └─ function name inside the controller
               └─ controller file  → controllers/Invoices.php
```

So `.../invoices/view/5` runs the `view($id)` function in `Invoices.php`.
The default page (empty URL) is the **Front** controller — the public site
(set in `application/config/routes.php`).

### How a view page is assembled

Each view stitches shared pieces together:
```php
<?php $this->load->view('includes/head');   ?>   // <head> + CSS
<?php $this->load->view('includes/navbar'); ?>   // top menu
      ...page content...
<?php $this->load->view('includes/footer'); ?>
<?php $this->load->view('includes/js');     ?>   // JavaScript
```
- Change the **menu** → edit `views/includes/navbar.php`
- Change **site-wide CSS/head** → edit `views/includes/head.php`

## 5. Two things you MUST understand before editing

### a) User roles are numbers (Ion Auth groups)

Code constantly checks `is_admin()`, `in_group(3)`, `in_group(4)`, plus a
`permissions('...')` helper and `is_module_allowed('...')`. Best guess of the
group numbers (VERIFY in the MySQL `groups` table):

| Group | Likely role |
|-------|-------------|
| 1     | Admin (company owner) |
| 2     | Super-admin / SaaS owner |
| 3     | Client |
| 4     | Employee / team member |

A check like `if (!$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))`
means *"block clients and employees from doing this."* This is where you change
who is allowed to do what.

### b) Text is never hardcoded — it's translated

Instead of `"Dashboard"`, the code writes:
```php
<?=$this->lang->line('dashboard') ? $this->lang->line('dashboard') : 'Dashboard'?>
```
It looks the word up in `application/language/english/`. To change wording
site-wide, edit the language file, not the view.

## 6. Key config files

| File | What it controls |
|------|------------------|
| `application/config/database.php` | DB host/user/password/name (blank in this copy; the live server fills it in) |
| `application/config/routes.php`   | URL → controller mapping; default controller is `front` |
| `application/config/autoload.php` | What loads on every request (Ion Auth, DB, and ALL models are auto-loaded) |
| `application/config/config.php`   | `base_url`, encryption key, sessions |
| `application/config/ion_auth.php` | Auth/permission settings |
| `index.php` (line ~55)            | `ENVIRONMENT` = `production` (hides errors) vs `development` (shows them) |

Auto-loaded models mean you can call e.g. `$this->projects_model->...` in any
controller without loading it first.

## 7. Useful helper functions (`application/helpers/custom_helper.php`)

Handy globals used throughout the app: `company_details()`, `get_currency()`,
`get_tax()`, `send_mail()`, `get_notifications()`, `is_module_allowed()`,
`frontend_permissions()`, `theme_color()`, `default_language()`,
`maintenance_mode()`, `check_my_storage()`. Look here before writing new logic —
it may already exist.

## 8. Safe workflow for making a change

1. **Work on a local copy, never the live site.** Use XAMPP / Laragon (Apache +
   PHP + MySQL) locally. Import the production database into it.
2. To see errors while developing, set `ENVIRONMENT` to `development` in
   `index.php` (switch it back to `production` on the live server).
3. **Use the URL to find the file** (URL → controller → function).
4. Learn in this order: **Views (HTML) → Controllers → Models (database).**
   Views are the safest, most visual place to start.
5. Back up before editing. `Code.zip` and the DB are your safety nets.
6. Check `application/logs/` when something breaks.

## 9. Common "how do I…" starting points

| Goal | Where to look |
|------|---------------|
| Change a visible word/label | `application/language/english/…` |
| Change the top menu | `views/includes/navbar.php` |
| Change colors/logo/CSS | `views/includes/head.php`, `assets/css/`, `assets/img/` |
| Change what a page shows | `views/<name>.php` |
| Change page logic / permissions | `controllers/<Name>.php` |
| Change a database query | `models/<Name>_model.php` |
| Add/alter a DB table | `application/migrations/` (+ the live DB) |
| Email templates/sending | `custom_helper.php` → `send_mail()`, `render_email_template()` |

## 10. Composer & the `vendor/` folder (dependencies)

`vendor/` holds third-party libraries. It is **not** kept in Git in full —
instead **Composer** (PHP's package manager) restores it from two small files.

### The files that control it

| File | Purpose |
|------|---------|
| `composer.json` | Lists the dependencies we want (currently just `stripe/stripe-php`) |
| `composer.lock` | Pins the EXACT versions so every machine installs the same code |
| `.gitignore`     | Tells Git to skip `vendor/` — **except** `vendor/phpmailer/` (see below) |

### Restoring `vendor/` on a fresh copy

After a `git clone` (or on a new machine), run **one command**:
```
composer install
```
Composer reads `composer.lock` and downloads the libraries into `vendor/`.
(Use `composer install`, which respects the lock file — not `composer update`,
which would upgrade versions.)

### ⚠️ Two libraries, two different rules — important

This project loads its libraries by **manual hardcoded paths**, not Composer's
autoloader. That means:

- **Stripe** — a real Composer package. Loaded via
  `require_once('vendor/stripe/stripe-php/init.php')`. It is gitignored and
  restored by `composer install`.
- **PHPMailer** — a hand-placed legacy copy (PHPMailer 5.x), loaded via
  `require_once('vendor/phpmailer/class.phpmailer.php')`. It is **NOT** a
  Composer package, so it is kept in Git (the `.gitignore` has a
  `!/vendor/phpmailer/` exception). **Do not delete it** — email breaks without
  it, and `composer install` will not bring it back.

### Deployment gotcha

If you deploy by pulling this repo onto a **new/empty server**, that server
won't have `vendor/stripe` until you run `composer install` there. That needs
Composer installed on the server (many shared hosts, e.g. Hostinger, don't have
it by default — you'd need SSH access, a hosting-panel Composer tool, or upload
the `vendor/` folder manually via FTP). On a server that **already has** a full
`vendor/` folder (like the current live site), nothing breaks.

### If GitHub blocks a push with "secret detected"

Stripe's library ships example API keys (`sk_test_...`) in its own README, and
GitHub's scanner flags them even though they're harmless demos. Because
`vendor/` is now gitignored, those files no longer reach GitHub — so this
shouldn't recur. If a genuine secret is ever flagged, remove it from the code
(never commit real API keys or DB passwords), don't just allowlist it.

---

*Kept in the repo root as a living reference. Update it as you learn more about
the project.*