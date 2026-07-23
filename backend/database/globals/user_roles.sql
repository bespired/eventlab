CREATE TABLE `user_roles` (
  `prospect`   varchar(32) NOT NULL, -- The tenant-specific profile identity
  `tenant`     varchar(2)  NOT NULL, -- Target project framework
  `role`       varchar(32) NOT NULL, -- Action profile string (e.g., 'admin', 'editor')
  `created_by` varchar(32) NULL DEFAULT 'system',
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_prospect_project_role` (`prospect`, `tenant`, `role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
