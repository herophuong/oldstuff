CREATE TABLE stuff
(
    stuff_id      INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
    user_id       INTEGER NOT NULL,
    category      VARCHAR(30) NOT NULL,
    stuff_name    VARCHAR(100) NOT NULL,
    description   VARCHAR(300) DEFAULT NULL,
    image         VARCHAR(300),
    price         FLOAT,
    purpose       VARCHAR(5),
    desired_stuff VARCHAR(100),
    state         SMALLINT
);
