CREATE TABLE `accu_bits` (
  `prospect`    varchar(32) NOT NULL, -- owner of this data
  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

-- loop 64
  `bit_@`  bool NULL DEFAULT 0,
  `time_@` datetime NULL DEFAULT NULL,
--

  PRIMARY KEY (`prospect`),
  UNIQUE KEY `unique_prospect` (`prospect`),

  CONSTRAINT `fk_prospect_accu_bits`
    FOREIGN KEY (`prospect`) REFERENCES `prospects` (`handle`)
    ON DELETE CASCADE ON UPDATE CASCADE


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
