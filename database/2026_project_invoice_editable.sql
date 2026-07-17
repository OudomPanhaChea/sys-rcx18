-- 2026-07: Editable project invoices
-- Run once on production (already applied to local rcx_sys).
--
-- The invoice modal snapshots project data into its own tables the first
-- time it is saved. Edits live here only — the original project row is
-- never touched, and later project edits never overwrite a saved invoice.

CREATE TABLE IF NOT EXISTS `project_invoices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `saas_id` INT(11) NOT NULL,
  `project_id` INT(11) NOT NULL,
  `invoice_no` VARCHAR(50) NOT NULL DEFAULT '',
  `issued_date` DATE NULL,
  `client_name` VARCHAR(191) NOT NULL DEFAULT '',
  `client_company` VARCHAR(191) NOT NULL DEFAULT '',
  `client_email` VARCHAR(191) NOT NULL DEFAULT '',
  `client_phone` VARCHAR(100) NOT NULL DEFAULT '',
  `booking` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` VARCHAR(10) NOT NULL DEFAULT 'amount', -- 'amount' | 'percent'
  `discount_value` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT NULL,
  `created` DATETIME NULL,
  `updated` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_project` (`project_id`),
  KEY `idx_saas` (`saas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `project_invoice_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` INT(11) NOT NULL,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `details` VARCHAR(255) NOT NULL DEFAULT '',
  `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `sort_order` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_invoice` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
