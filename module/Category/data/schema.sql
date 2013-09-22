CREATE TABLE category
(
    cat_id      INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
    cat_name    VARCHAR(100) NOT NULL,
    description   TEXT DEFAULT NULL,
    state         SMALLINT
);


