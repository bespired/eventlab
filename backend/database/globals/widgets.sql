CREATE TABLE `widgets` (
  `handle`    varchar(32) NOT NULL,  -- ze hebben er allemaal een
  `order`     int(11) DEFAULT 1,     --

  `label`     varchar(64) NOT NULL,  -- zo heet de widget
  `component` varchar(64) NOT NULL,  -- zo heet de vue component
  `svgicon`   varchar(64) NOT NULL,  -- icon

  `fields`    text DEFAULT NULL,     -- array met field omschrijvingen
  `otml`      text DEFAULT NULL,     -- html met mustage voor mail

  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
