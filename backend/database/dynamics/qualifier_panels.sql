CREATE TABLE `qualifier_panels` (
  `handle`      varchar(32) NOT NULL,
  `order`       int NOT NULL  DEFAULT 1, -- also slot in result_{type}
  `type`        varchar(32) NOT NULL, -- qualifier type: segments, scores, facits, funnels, reports



  `created_by`  varchar(16) NULL DEFAULT 'system',
  `created_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`handle`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*
{
  "constants": {
    "c1": "marijn.van.herpt@centagon.com",
    "c2": "leon.van.herpt@centagon.com"
  },
  "times": {
    "t1": { "start": "2021-01-01", "end": "2021-12-31" }
  },
  "conditions": {
    "r1": { "attribute": "field--email", "compare": "EQUALS", "value": "@c1", "timed": "@t1" },
    "r2": { "attribute": "field--email", "compare": "EQUALS", "value": "@c2",  },
    "r3": { "attribute": "field--name",  "compare": "LIKE",   "value": "@c2",  }
  },
  "expression": "( r1 OR r2 ) AND r3"
}
*/
