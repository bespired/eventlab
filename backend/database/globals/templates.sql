CREATE TABLE `templates` (
  `handle`     varchar(32) NOT NULL,
  `key`        varchar(32) NOT NULL,    -- om te vinden

  `type`       varchar(16) NOT NULL,    -- email | page
  `subject`    varchar(64) NOT NULL,    -- voor mail, of label
  `html`       text DEFAULT NULL,       -- de content

  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
