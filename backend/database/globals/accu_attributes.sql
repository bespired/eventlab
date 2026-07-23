CREATE TABLE `accu_attributes` (
  `handle`      varchar(32) NOT NULL,
  `label`       varchar(64) NOT NULL, -- human readable label

  `accu`        varchar(16) NOT NULL, -- word | bit | tupple
  `slot`        varchar(16) NOT NULL, -- column in accu
  `type`        varchar(16) NOT NULL, -- key, system, tag, field, option, consent

  `rules`       varchar(64) NULL, -- convert rules
  `sane`        varchar(64) NULL, -- sanity rules

  `owner`       varchar(32) NOT NULL, -- made by

  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
