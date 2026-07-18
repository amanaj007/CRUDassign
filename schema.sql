SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS Education;
DROP TABLE IF EXISTS osition;
DROP TABLE IF EXISTS Profile;
DROP TABLE IF EXISTS Institution;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    user_id INTEGER NOT NULL AUTO_INCREMENT,
    name VARCHAR(128),
    email VARCHAR(128),
    password VARCHAR(128),
    PRIMARY KEY (user_id),
    UNIQUE(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Profile (
    profile_id INTEGER NOT NULL AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    first_name TEXT,
    last_name TEXT,
    email TEXT,
    headline TEXT,
    summary TEXT,
    PRIMARY KEY(profile_id),
    CONSTRAINT profile_ibfk_1
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Position` (
    profile_id INTEGER,
    `rank` INTEGER,
    year INTEGER,
    description TEXT,
    CONSTRAINT position_ibfk_1
        FOREIGN KEY (profile_id)
        REFERENCES Profile(profile_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY(profile_id, `rank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Institution (
    institution_id INTEGER NOT NULL AUTO_INCREMENT,
    name VARCHAR(255),
    PRIMARY KEY(institution_id),
    UNIQUE(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Education (
    profile_id INTEGER,
    institution_id INTEGER,
    `rank` INTEGER,
    year INTEGER,
    CONSTRAINT education_ibfk_1
        FOREIGN KEY (profile_id)
        REFERENCES Profile(profile_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT education_ibfk_2
        FOREIGN KEY (institution_id)
        REFERENCES Institution(institution_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY(profile_id, institution_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Institution (name) VALUES ('University of Michigan');
INSERT INTO Institution (name) VALUES ('University of Virginia');
INSERT INTO Institution (name) VALUES ('University of Oxford');
INSERT INTO Institution (name) VALUES ('University of Cambridge');
INSERT INTO Institution (name) VALUES ('Stanford University');
INSERT INTO Institution (name) VALUES ('Duke University');
INSERT INTO Institution (name) VALUES ('Michigan State University');
INSERT INTO Institution (name) VALUES ('Mississippi State University');
INSERT INTO Institution (name) VALUES ('Montana State University');

INSERT INTO users (name, email, password)
VALUES ('Chuck Severance', 'csev@umich.edu', '1a52e17fa899cf40fb04cfc42e6352f1');
