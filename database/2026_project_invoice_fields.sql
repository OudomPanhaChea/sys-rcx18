-- 2026-07: Project invoice / client account fields
-- Run once on production (already applied to local rcx_sys).

-- Projects: booking amount (deposit the client booked with), the account URL
-- the team works on, and the account username/ID.
ALTER TABLE `projects`
  ADD COLUMN `booking` TEXT NULL AFTER `budget`,
  ADD COLUMN `account_url` TEXT NULL AFTER `booking`,
  ADD COLUMN `account_username` VARCHAR(191) NULL AFTER `account_url`;

-- Users (clients): link to the client's account, captured on the
-- "Create New Client" form on the Clients page.
ALTER TABLE `users`
  ADD COLUMN `account_link` TEXT NULL AFTER `company`;
