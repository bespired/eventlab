CREATE TABLE `puls_pages` (
  `handle`      varchar(32) NOT NULL,
  `prospect`    varchar(32) NULL,     -- owner of this data

  `on_visit`    varchar(32) NOT NULL, -- visit handle
  `order`       int DEFAULT 1,        -- order of visit

  `path`        varchar(255) NOT NULL, --
  `query`       varchar(255) NULL,     --
  `fragment`    varchar(255) NULL,     --
  `utms`        varchar(255) NULL,     -- special query part
  `elmi`        varchar(255) NULL,     -- special query part email

  `visited_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`),

  CONSTRAINT `fk_prospect_puls_pages`
    FOREIGN KEY (`prospect`) REFERENCES `prospects` (`handle`)
    ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- https://name.com/articles/articlename?query=file#fragment
-- └scheme └──host─ └──────path──────── └──query── └─fragment
