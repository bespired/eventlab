CREATE TABLE `tags` (
  `handle`   varchar(32) NOT NULL,
  `label`    varchar(64) NOT NULL,
  `poly`     varchar(64) NOT NULL, -- photo|story|person
  `category` varchar(32) NOT NULL, -- what|when|why|meta

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

