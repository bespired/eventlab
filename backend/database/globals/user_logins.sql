CREATE TABLE `user_logins` (
  `handle`     varchar(32) NOT NULL,

  `email`      varchar(256) NOT NULL,
  `hash`       varchar(256) NOT NULL,

  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY `unique_email` (`email`)

  -- Foreign Key voor de prospect kan niet ivm mixed databases

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
