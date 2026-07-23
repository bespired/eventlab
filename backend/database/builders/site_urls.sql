CREATE TABLE `site_urls` (
  `handle`      varchar(32) NOT NULL,

  `url`         varchar(256) NOT NULL,
  `type`        varchar(24)  NULL DEFAULT "page",  -- page or redirect

  `redirect`    varchar(24)  NULL DEFAULT NULL,    -- 301, 302 or ...
  `destination` varchar(256)  NULL DEFAULT NULL,   -- redirect where to

  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`)


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

