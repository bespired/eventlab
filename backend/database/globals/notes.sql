CREATE TABLE `notes` (
  `handle`      varchar(32) NOT NULL,
  `owner`       varchar(32) NOT NULL,     -- wie heeft alle rechten op website over notitie
  `addressed`   varchar(32) DEFAULT NULL, -- wie wil je iets vragen (optioneel)

  `title`       varchar(255) NOT NULL,    -- titel van verhaal
  `story`       text DEFAULT NULL,        -- het verhaal

  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
