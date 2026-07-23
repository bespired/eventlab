CREATE TABLE `lut_referrers` (
  `handle`      varchar(32) NOT NULL,

  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
