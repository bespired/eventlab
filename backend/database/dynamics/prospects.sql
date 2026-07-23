CREATE TABLE `prospects` (
  `handle`      varchar(32) NOT NULL,

  `email`       varchar(255) NULL DEFAULT NULL,
  `elmi`        varchar(255) NULL DEFAULT NULL,
  -- eventlab email identifier token.
  -- some random token to add to email click-to-pages so
  -- whe can bind visitor id to prospect. (even track what button was pressed)
  -- {elmi}--{mail-handle}--{btn-handle}
  -- https://website.com/url/to/go?elmi={elmi}--{mail-handle}--{btn-handle}

  `code`        varchar(10) DEFAULT NULL,
  `valid`       datetime NULL DEFAULT NULL,
    -- aanvraag van inlog code via mail.

  `username`    varchar(255) DEFAULT NULL,

  `blacklisted` bool DEFAULT NULL,        -- wil geen nieuwsbrief

  `is_backend`  bool DEFAULT FALSE,        -- kan inloggen backend
  `is_manager`  bool DEFAULT FALSE,        -- is lid van signals
  `is_member`   bool DEFAULT FALSE,        -- kan inloggen frontend

  `is_contact`  bool DEFAULT NULL,        -- als email dan true.
  `has_visits`  bool DEFAULT NULL,        -- kan met import een false zijn.
  `first_visit` datetime DEFAULT NULL,    -- waneer dan
  `last_visit`  datetime DEFAULT NULL,    -- waneer dan

  `created_by`  varchar(16) NULL DEFAULT 'system',
  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_email` (`email`)
    -- Zorgt dat een email (als die er is) uniek blijft

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
