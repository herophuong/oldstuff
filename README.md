oldstuff
========

Old stuff trade system

User Guide (for testing purpose)

1. For this project, WAMP or LAMP server is the best for testing purpose
2. Clone this project using Github and switch to branch "stuff"
3. Copy cloned folder to /root directory/ (for example, C:\wamp\www in Windows)
4. Use Git Shell or Terminal or cmd, go to project folder (for example C:\wamp\www\oldstuff) and type "php composer.phar install"
5. Start the local web server
6. Go to "localhost/phpmyadmin", create database "oldstuff"
7. Click database "oldstuff" and choose tab SQL. Open file /data/sample.sql, copy everything and paste to SQL box, click Go
8. Do the same thing with file /module/Vote/data/schema.sql to create 2 more table
9. Open web browser and type "http://localhost/oldstuff/public"
10. Now you can do everything with this website such as sign up, sign in, add stuff, search...
