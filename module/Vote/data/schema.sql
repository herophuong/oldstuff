CREATE TABLE vote
(
	`user_id` INT NOT NULL,
	`voted_user_id` INT NOT NULL,
	`ratescore` INT NOT NULL,
	PRIMARY KEY (`user_id`, `voted_user_id`)
);

CREATE TABLE IF NOT EXISTS `userrate`
(
	`user_id` int(11) NOT NULL,
	`avgrate` FLOAT NOT NULL,
	`numofvote` int(11) NOT NULL,
	PRIMARY KEY (`user_id`)
);