CREATE TABLE `migrationseeds` (
  `type` varchar(12) NOT NULL, -- migration or seed
  `db` varchar(64) NOT NULL,   -- name of the DB the migration has run into

  `name` varchar(80) NOT NULL,

  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

