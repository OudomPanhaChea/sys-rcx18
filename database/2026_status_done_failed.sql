-- Project status rework (2026-07-08)
-- 1) Rename "Finished" -> "Done"
-- 2) Add new "Failed" status (red)
-- 3) Recolour "Not Started" to yellow so red uniquely means Failed
--
-- Safe to run once on production (u189356587_sys). Idempotent-ish: the INSERT
-- is guarded so re-running does not duplicate the Failed row.

UPDATE project_status SET title = 'Done' WHERE id = 3 AND title = 'Finished';

UPDATE project_status SET class = 'warning' WHERE id = 1 AND title = 'Not Started';

INSERT INTO project_status (id, title, class)
SELECT 4, 'Failed', 'danger'
WHERE NOT EXISTS (SELECT 1 FROM project_status WHERE id = 4 OR title = 'Failed');
