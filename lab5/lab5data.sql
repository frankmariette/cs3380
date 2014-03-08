-- The double dashes are comments within .sql files

-- Drop, create and set schema for this example
DROP SCHEMA IF EXISTS lab5 CASCADE;

CREATE SCHEMA lab5;

SET search_path = lab5;

CREATE TABLE baseball_team (
	tid serial PRIMARY KEY,
	league varchar(2) NOT NULL,
  	city varchar(35) NOT NULL,
  	name varchar(50) NOT NULL
);


-- The only relationship that needs to be enforced within this table
-- is the 'm-to-1' relationship between player and baseball_team
CREATE TABLE player (
        pid serial PRIMARY KEY,
	-- On delete cascade will delete any player refrencing a team that is being deleted
	tid integer REFERENCES baseball_team ON DELETE CASCADE,
        fname varchar(25) NOT NULL,
        lname varchar(25) NOT NULL
);

-- The statistics table is a weak entity with a '1-to-1' relationship with the table, player.
-- As a result, its primary key depends upon the primary key of player.
CREATE TABLE statistics (
        pid integer PRIMARY KEY REFERENCES player ON DELETE CASCADE,
        ab integer NOT NULL,
	runs integer NOT NULL,
        hits integer NOT NULL,
        hr integer NOT NULL,
	rbi integer NOT NULL
); 


-- The position table relates to the player table through a 'm-to-n' relationship.
-- This is enforced with use of a differenct table to enforce the foreign keys.

--The position table looks like 
CREATE TABLE position (
	pos varchar(2) PRIMARY KEY, --pos is a simple abbreviation of name
	name varchar(50)
);

--The table 'played_by' is used to enforce the 'm-to-n' relationship
CREATE TABLE played_by (
	pid integer REFERENCES player,
	pos varchar(2) REFERENCES position,
	PRIMARY KEY(pid, pos)
);

-- Inserts within the baseball_team table
INSERT INTO baseball_team VALUES (DEFAULT, 'NL', 'St. Louis', 'Cardinals');
INSERT INTO baseball_team VALUES (DEFAULT, 'NL', 'Los Angeles', 'Dodgers');
INSERT INTO baseball_team VALUES (DEFAULT, 'NL', 'Chicago', 'Cubs');

INSERT INTO baseball_team VALUES (DEFAULT, 'AL', 'Kansas City', 'Royals');
INSERT INTO baseball_team VALUES (DEFAULT, 'AL', 'New York', 'Yankees');

-- Inserts within the player table
INSERT INTO player VALUES (DEFAULT, 1, 'Yadier', 'Molina'); 
INSERT INTO player VALUES (DEFAULT, 1, 'Allen', 'Craig'); 
INSERT INTO player VALUES (DEFAULT, 1, 'Matt', 'Carpenter'); 

INSERT INTO player VALUES (DEFAULT, 2, 'A.J.', 'Ellis');
INSERT INTO player VALUES (DEFAULT, 2, 'Adrian', 'Gonzalez');
INSERT INTO player VALUES (DEFAULT, 2, 'Mark', 'Ellis');

INSERT INTO player VALUES (DEFAULT, 4, 'Salvador', 'Perez'); 
INSERT INTO player VALUES (DEFAULT, 4, 'Eric', 'Hosmer'); 
INSERT INTO player VALUES (DEFAULT, 4, 'Alex', 'Gordon'); 

INSERT INTO player VALUES (DEFAULT, NULL, 'Ozzie', 'Smith'); 
INSERT INTO player VALUES (DEFAULT, NULL, 'George', 'Brett'); 

-- Inserts within the stats table 
INSERT INTO statistics VALUES 
(1, 505, 68, 161, 12, 80), (2, 508, 71, 160, 13, 97), (3, 626, 126, 199, 11, 78);

INSERT INTO statistics VALUES 
(4, 390, 43, 93, 10, 52), (5, 583, 69, 171, 22, 100), (6, 433, 46, 117, 6, 48);

INSERT INTO statistics VALUES 
(7, 496, 48, 145, 13, 79), (8, 623, 86, 118, 17, 79), (9, 633, 90, 168, 20, 81);



--Inserts within the position table
INSERT INTO position VALUES 
('P', 'Pitcher'), 
('C', 'Catcher'), 
('1B', 'First baseman'), 
('2B', 'Second baseman'), 
('3B', 'Third baseman'), 
('SS', 'Shortstop'), 
('LF', 'Left Field'),
('CF', 'Center Field'),
('RF', 'Right Field'),
('DH', 'Designated hitter');

--Inserts within the played_by table to tie together player with position
INSERT INTO played_by VALUES 
(1, 'C'), 
(2, '1B'), (2, 'LF'), 
(3, '2B'), (3, '3B'), 
(4, 'C'),
(5, '1B'),
(6, '2B'), (6, '3B'),
(7, 'C'), (7, '1B'),
(8, '1B'), (8, 'DH'), (8, 'RF'),
(9, 'LF')
;

