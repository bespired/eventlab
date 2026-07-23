CREATE TABLE `puls_forms` (
  `handle`   varchar(32) NOT NULL,
  `prospect` varchar(32) NOT NULL, -- owner of this data

  `name`        varchar(64) NOT NULL, -- formname
  `formhandle`  varchar(32) NOT NULL, -- formhandle

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`),

  CONSTRAINT `fk_prospect_puls_forms`
    FOREIGN KEY (`prospect`) REFERENCES `prospects` (`handle`)
    ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


