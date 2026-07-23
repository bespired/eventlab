CREATE TABLE `user_tenants` (
  `user`     varchar(32) NOT NULL, -- user that can login
  `tenant`   varchar(2)  NOT NULL, -- what project
  `prospect` varchar(32) NOT NULL, -- prospect op een project
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_user_tentant_prospect` (`user`, `tenant`, `prospect` )

  -- Foreign Key voor de prospect kan niet ivm mixed databases

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
