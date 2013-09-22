CREATE TABLE stuff
(
    stuff_id      INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
    user_id       INTEGER NOT NULL,
    cat_id      INTEGER NOT NULL,
    stuff_name    VARCHAR(100) NOT NULL,
    description   TEXT DEFAULT NULL,
    image         TEXT,
    price         FLOAT,
    purpose       VARCHAR(5),
    desired_stuff VARCHAR(100),
    state         SMALLINT
);
