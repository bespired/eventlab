INSERT INTO `accu_attributes`
  (`handle`, `accu`, `slot`, `type`, `owner`, `label`, `sane`, `rules`)
VALUES
  ('email',      'word', 1, 'key',    'sys', 'Email'   ,   'email',  NULL),
  ('username',   'word', 2, 'system', 'prospect', 'Username',   'name',   'firstname-infix-lastname'),

  ('firstname',  'word', 3, 'field', 'sys', 'Firstname',   'name',   NULL),
  ('infix',      'word', 4, 'field', 'sys', 'Infix',       'name',   NULL),
  ('lastname',   'word', 5, 'field', 'sys', 'Lastname',    'name',   NULL),

  ('fullname',   'word', 6, 'system', 'sys', 'Fullname',   'name',   'Firstname Infix Lastname'),
  ('avatarname', 'word', 7, 'system', 'sys', 'Avatarname', 'name',   'Firstname:1;Lastname:1'),

  ('birthday',   'word', 8, 'field', 'sys', 'Birthday',    'date',   NULL),
  ('passingday', 'word', 9, 'field', 'sys', 'Passingday',  'date',   NULL),

  ('newsletter', 'bit',  1, 'tag', 'sys', 'Newsletter',    'bool',   NULL),

  ('marketing',  'tupp', 1, 'consent', 'sys', 'Marketing consent', 'tupple', NULL),
  ('contact',    'tupp', 2, 'consent', 'sys', 'Contact consent',   'tupple', NULL);



