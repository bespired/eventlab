CREATE TABLE `result_scores` (
  `qualifier`   varchar(32) NOT NULL, -- to what qualifier does this belong
  `prospect`    varchar(32) NOT NULL, -- owner of this data

  `aggrigated`  int NULL DEFAULT NULL,
  `depth`       bool NULL DEFAULT NULL, -- dieptste value

-- loop 48
  `value_@`     int NULL DEFAULT NULL,
--

  `added_at`    datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`qualifier`, `prospect`),

  CONSTRAINT `fk_prospect_result_scores`
    FOREIGN KEY (`prospect`) REFERENCES `prospects` (`handle`)
    ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

