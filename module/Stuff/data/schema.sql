CREATE TABLE stuff
(
    stuff_id       INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
    user_id       INTEGER NOT NULL,
    cat_id         INTEGER NOT NULL,
    stuff_name         VARCHAR(100) NOT NULL,
    description         VARCHAR(300) DEFAULT NULL,
    price           FLOAT   NOT NULL,
    state         SMALLINT
);
