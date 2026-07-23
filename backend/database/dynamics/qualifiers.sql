CREATE TABLE `qualifiers` (
  `handle`      varchar(32) NOT NULL,

  `type`        varchar(32) NOT NULL, -- qualifier type: segments, scores, facits, funnels, reports

  `created_by`  varchar(16) NULL DEFAULT 'system',
  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
