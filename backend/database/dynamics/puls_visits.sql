CREATE TABLE `puls_visits` (
  `handle`   varchar(32) NOT NULL,
  `prospect` varchar(32) NOT NULL, -- owner of this data

  `visit_number` int DEFAULT 1, -- lookup
  `session_id`   varchar(32) NOT NULL,
    -- something like ms36thw7-AcCjf93pTNXaJoLd before - is date bas36

  `ip4`      varchar(32) NOT NULL,
  `url`      varchar(32) NOT NULL, -- lookup in site urls
  `referrer` varchar(32) NOT NULL, -- lookup
  `device`   varchar(32) NOT NULL, -- lookup
  `location` varchar(32) NOT NULL, -- lookup
  `country`  varchar(32) NOT NULL, -- lookup
  `agent`    varchar(32) NOT NULL, -- lookup

  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`),

  CONSTRAINT `fk_prospect_puls_visits`
    FOREIGN KEY (`prospect`) REFERENCES `prospects` (`handle`)
    ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- visit
--  -- pages
--  -- events
--  -- forms
--  -- signals
