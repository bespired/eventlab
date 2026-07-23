CREATE TABLE `puls_visits` (
  `handle`   varchar(32) NOT NULL,
  `prospect` varchar(32) NOT NULL, -- owner of this data

  `agent`    varchar(32) NOT NULL, -- lookup
  `location` varchar(32) NOT NULL, -- lookup
  `referrer` varchar(32) NOT NULL, -- lookup

  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`),

  CONSTRAINT `fk_prospect_puls_visits`
    FOREIGN KEY (`prospect`) REFERENCES `prospects` (`handle`)
    ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

