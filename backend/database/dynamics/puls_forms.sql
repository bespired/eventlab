CREATE TABLE `puls_forms` (
  `handle`   varchar(32) NOT NULL,
  `prospect` varchar(32) NULL,    -- owner of this data, can be null when owner not known yet

  `name`        varchar(64) NULL, -- formname

  `formdata` text NOT NULL,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`),

  CONSTRAINT `fk_prospect_puls_forms`
    FOREIGN KEY (`prospect`) REFERENCES `prospects` (`handle`)
    ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


