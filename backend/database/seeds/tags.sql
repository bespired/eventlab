INSERT INTO `tags`
  (`handle`, `label`, `poly`, `category` )
VALUES
-- gender
  ('x--male',    'Man',         'person', 'gender' ),
  ('x--female',  'Vrouw',       'person', 'gender' ),
  ('x--nonbin',  'Non-binair',  'person', 'gender' ),
  ('x--unknown', 'Onbekend',    'person', 'gender' ),

-- tree
  ('x--parent', 'Ouder',    'person', 'tree' ),
  ('x--child',  'Kind',     'person', 'tree' ),

-- burgerlijk (zit al in relations)
--  ('x--unmarried', 'Ongehuwd',   'person', 'civil'), -- type 'person', want het zegt iets over de persoon
--  ('x--married',   'Getrouwd',   'person', 'civil'),
--  ('x--divorced',  'Gescheiden', 'person', 'civil'), -- Let op: 'divorced' ipv 'deforced'
--  ('x--widowed',   'Weduw(e)',   'person', 'civil'), -- 'widowed' is de staat van de persoon

-- photo - properties
  ('x--color',  'Kleur',      'photo', 'props' ),
  ('x--analog', 'Analoog',    'photo', 'props' ),


-- photo - what
  ('x--portrait',  'Portretfoto',   'photo', 'what' ),
  ('x--group',     'Groepsfoto',    'photo', 'what' ),
  ('x--school',    'Scoolfoto',     'photo', 'what' ),
  ('x--document',  'Scan',          'photo', 'what' ),
  ('x--landscape', 'Omgevingsfoto', 'photo', 'what' ), -- bv standbeeld
  ('x--artifact',  'Object',        'photo', 'what' ), -- bv zakhorloge
  ('x--art',       'Kunst',         'photo', 'what' ), -- bv schilderij

-- photo - when
  ('x--era-war',    'Oorlogstijd',         'photo', 'when' ),
  ('x--era-prewar', 'Vooroorlogs',         'photo', 'when' ),
  ('x--childhood',  'Jeugdfoto',           'photo', 'when' ),
  ('x--holiday',    'Vakantie/vrije tijd', 'photo', 'when' ),

-- photo - why
  ('x--celebration', 'Feest',              'photo', 'why' ), -- (bruiloft, jubileum).
  ('x--military',    'Militaire dienst',   'photo', 'why' ),
  ('x--migration',   'Emigratie',          'photo', 'why' ),
  ('x--moving',      'Verhuizing',         'photo', 'why' ),
  ('x--memorial',    'Ter nagedachtenis' , 'photo', 'why' ),-- (begrafenis, grafsteen).

-- photo - meta
  ('x--low-res',       'Lage kwaliteit' ,    'photo', 'meta' ),  -- Lage kwaliteit, moet nog eens goed gescand worden.
  ('x--ai-colorized',  'AI-ingekleurd',      'photo', 'meta' ), -- (belangrijk om te weten wat origineel is!).
  ('x--todo-identify', 'Uitzoeken: Wie staan hierop?' , 'photo', 'meta' ); -- filter.



