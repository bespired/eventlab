CREATE TABLE `result_segments` (
  `qualifier`   varchar(32) NOT NULL, -- to what qualifier does this belong
  `prospect`    varchar(32) NOT NULL, -- owner of this data

  `aggrigated`  bool NULL DEFAULT NULL, -- in segment
  `depth`       bool NULL DEFAULT NULL, -- dieptste boolean

-- loop 48
  `bool_@`      bool NULL DEFAULT NULL,
--

  `added_at`    datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`qualifier`, `prospect`),

  CONSTRAINT `fk_prospect_result_segments`
    FOREIGN KEY (`prospect`) REFERENCES `prospects` (`handle`)
    ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

