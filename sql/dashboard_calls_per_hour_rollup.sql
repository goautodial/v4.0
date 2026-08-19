-- Calls Per Hour dashboard rollup table.
--
-- Apply this to the Asterisk/VICIdial database before enabling the generator:
--   mysql -u <user> -p asterisk < sql/dashboard_calls_per_hour_rollup.sql
--
-- One row stores all chart series for one completed hour and reporting scope.
-- ADMIN is the unfiltered scope. Other scopes use the VICIdial user group ID.

CREATE TABLE IF NOT EXISTS `vicidial_dashboard_calls_per_hour` (
    `report_date` DATE NOT NULL,
    `reporting_scope` VARCHAR(64) NOT NULL,
    `hour_of_day` TINYINT UNSIGNED NOT NULL,
    `inbound_calls` INT UNSIGNED NOT NULL DEFAULT 0,
    `outbound_calls` INT UNSIGNED NOT NULL DEFAULT 0,
    `dropped_calls` INT UNSIGNED NOT NULL DEFAULT 0,
    `generated_at` DATETIME NOT NULL,
    PRIMARY KEY (`report_date`, `reporting_scope`, `hour_of_day`),
    KEY `idx_generated_at` (`generated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
