-- =====================================================================
--  Project Categories & Issues
--  Adds per-tenant (saas_id scoped) categories and their issues, and
--  links each project to one category + one issue.
--
--  Safe/additive: creates two new tables and adds two nullable columns
--  to `projects`. No existing data is modified or removed.
--
--  Apply on PRODUCTION once (local rcx_sys already applied during dev):
--    mysql -u <user> -p <database> < database/2026_categories_issues.sql
-- =====================================================================

-- ---------------------------------------------------------------------
--  project_categories  (e.g. "Facebook")
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saas_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `class` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `saas_id` (`saas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------------
--  project_issues  (e.g. "Account Banned", belongs to a category)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saas_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `saas_id` (`saas_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------------
--  projects: link to one category + one issue (nullable)
-- ---------------------------------------------------------------------
ALTER TABLE `projects`
  ADD COLUMN `category_id` int(11) NULL DEFAULT NULL AFTER `status`,
  ADD COLUMN `issue_id` int(11) NULL DEFAULT NULL AFTER `category_id`;
