CREATE TABLE `builder_pages` (
  `handle`   varchar(32) NOT NULL,

  `name`     varchar(128) NOT NULL,
  `label`    varchar(128) NOT NULL,

  `layout`   text NULL,

  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`)


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

