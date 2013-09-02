CREATE TABLE user
(
    user_id       INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
    email         VARCHAR(255) DEFAULT NULL UNIQUE,
    display_name  VARCHAR(50) DEFAULT NULL,
    password      VARCHAR(128) NOT NULL,
    state         SMALLINT
); 
