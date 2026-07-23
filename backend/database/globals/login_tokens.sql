CREATE TABLE `login_tokens` (
  `handle`     varchar(32) NOT NULL,
  `user`       varchar(32) NOT NULL, -- Links directly to login_users.handle
  `token`      varchar(48) NOT NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_user_token` (`user`, `token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
