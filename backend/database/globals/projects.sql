CREATE TABLE `projects` (
  `handle`       varchar(32) NOT NULL,
  `tenant`       varchar(2)  NULL DEFAULT '00',  -- a0, s3, d8, etc.

  `clientname`   varchar(48) NULL DEFAULT '',
  `projectname`  varchar(48) NULL DEFAULT '',

  `databasename`  varchar(128) NULL DEFAULT '', -- database for scoring
  `buildersname`  varchar(128) NULL DEFAULT '', -- database for form automation site email building
                                                -- probably just website_tenant_web or web_databasename

  -- columns for routing lookup
  `custom_domain` varchar(255) NULL DEFAULT NULL, -- e.g., 'www.client.com' or 'client.nl'
  `fallback_slug` varchar(64)  NULL DEFAULT NULL, -- e.g., 'beef-farmers' (for local/subfolder routing)

  `active`       bool DEFAULT 1,

  `created_at`   datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`),
  UNIQUE KEY `unique_handle` (`handle`),
  UNIQUE KEY `unique_domain` (`custom_domain`),  -- Prevents routing collisions
  UNIQUE KEY `unique_slug`   (`fallback_slug`)   -- Prevents subfolder routing collisions

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
