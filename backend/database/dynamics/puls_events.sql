CREATE TABLE `puls_events` (
  `handle`   varchar(32) NOT NULL,
  `prospect` varchar(32) NOT NULL, -- owner of this data
  `on_visit` varchar(32) NOT NULL, -- what visit did this happen

  `category` varchar(64) NOT NULL, -- matomo category
  `action`   varchar(64) NOT NULL, -- matomo action
  `value`    varchar(64) NOT NULL, -- matomo name/value

  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`),

  CONSTRAINT `fk_prospect_puls_events`
    FOREIGN KEY (`prospect`) REFERENCES `prospects` (`handle`)
    ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

