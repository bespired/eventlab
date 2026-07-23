CREATE TABLE `puls_signals` (
  `handle`   varchar(32) NOT NULL,
  `prospect` varchar(32) NOT NULL, -- owner of this data

  `bucket`   varchar(64) NOT NULL, -- bucketname

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`),

  CONSTRAINT `fk_prospect_puls_signals`
    FOREIGN KEY (`prospect`) REFERENCES `prospects` (`handle`)
    ON DELETE CASCADE ON UPDATE CASCADE


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

