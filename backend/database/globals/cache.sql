CREATE TABLE `cache` (
  `cache_key`  varchar(255) NOT NULL,
  `payload`    text NOT NULL,

  `expires_at` datetime NOT NULL,

  PRIMARY KEY (`cache_key`),
  INDEX `idx_expiry` (`expires_at`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
