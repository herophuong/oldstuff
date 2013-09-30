CREATE TABLE vote
{
	user_id INT NOT NULL,
	voted_user_id INT NOT NULL,
	ratescore INT NOT NULL,
	PRIMARY KEY (user_id, voted_user_id)
};

CREATE TABLE userrate
{
	user_id INT PRIMARY KEY,
	avgrate FLOAT,
	numofvote INT
};