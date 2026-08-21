-- Calls Per Hour dashboard rollup table.
--
-- Apply this to the GoAutoDial database before enabling the generator:
--   mysql -u <user> -p goautodial < sql/dashboard_calls_per_hour_rollup.sql
--
-- One row stores all chart series for one completed hour and user group.
-- ADMIN is the unfiltered user group. Other values are VICIdial user group IDs.

CREATE TABLE IF NOT EXISTS `go_dashboard_calls_per_hour` (
    `date` DATE NOT NULL,
    `user_group` VARCHAR(64) NOT NULL,
    `hour_of_day` TINYINT UNSIGNED NOT NULL,
    `inbound_calls` INT UNSIGNED NOT NULL DEFAULT 0,
    `outbound_calls` INT UNSIGNED NOT NULL DEFAULT 0,
    `dropped_calls` INT UNSIGNED NOT NULL DEFAULT 0,
    `generated_at` DATETIME NOT NULL,
    PRIMARY KEY (`date`, `user_group`, `hour_of_day`),
    KEY `idx_generated_at` (`generated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Migrate existing installations without recreating the table or losing data.
DELIMITER //

DROP PROCEDURE IF EXISTS `rename_dashboard_calls_per_hour_columns` //

CREATE PROCEDURE `rename_dashboard_calls_per_hour_columns`()
BEGIN
    IF EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'go_dashboard_calls_per_hour'
          AND column_name = 'report_date'
    ) THEN
        ALTER TABLE `go_dashboard_calls_per_hour`
            CHANGE COLUMN `report_date` `date` DATE NOT NULL;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'go_dashboard_calls_per_hour'
          AND column_name = 'reporting_scope'
    ) THEN
        ALTER TABLE `go_dashboard_calls_per_hour`
            CHANGE COLUMN `reporting_scope` `user_group` VARCHAR(64) NOT NULL;
    END IF;
END //

CALL `rename_dashboard_calls_per_hour_columns`() //
DROP PROCEDURE `rename_dashboard_calls_per_hour_columns` //

DELIMITER ;
