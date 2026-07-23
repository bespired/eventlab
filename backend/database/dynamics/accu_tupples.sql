CREATE TABLE `accu_tupples` (
  `prospect`    varchar(32) NOT NULL, -- owner of this data
  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

-- loop 64
  `tupp_@` varchar(8) NULL DEFAULT NULL,
  `time_@` datetime NULL DEFAULT NULL,
--

  PRIMARY KEY (`prospect`),
  UNIQUE KEY `unique_prospect` (`prospect`),

  CONSTRAINT `fk_prospect_accu_tupples`
    FOREIGN KEY (`prospect`) REFERENCES `prospects` (`handle`)
    ON DELETE CASCADE ON UPDATE CASCADE


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
